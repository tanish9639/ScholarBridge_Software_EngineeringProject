<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use ZipArchive;

class SpreadsheetImportService
{
    public function rows(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'csv', 'txt' => $this->parseCsv($file->getRealPath()),
            'xlsx' => $this->parseXlsx($file->getRealPath()),
            default => throw new RuntimeException('Unsupported file format. Use CSV or XLSX.'),
        };
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            throw new RuntimeException('Unable to open CSV file.');
        }

        $headers = null;
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = $this->normalizeHeaders($data);
                continue;
            }

            if ($this->rowIsEmpty($data)) {
                continue;
            }

            $rows[] = $this->combineRow($headers, $data);
        }

        fclose($handle);

        return $rows;
    }

    private function parseXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Unable to open XLSX file.');
        }

        $sharedStrings = $this->extractSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (! $sheetXml) {
            throw new RuntimeException('Sheet1 was not found in the workbook.');
        }

        $xml = simplexml_load_string($sheetXml);
        if (! $xml) {
            throw new RuntimeException('Unable to read worksheet XML.');
        }

        $rows = [];
        $headers = null;

        foreach ($xml->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $index = $this->columnIndexFromReference($ref);
                $type = (string) $cell['t'];
                $value = isset($cell->v) ? (string) $cell->v : '';

                if ($type === 's') {
                    $value = $sharedStrings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = isset($cell->is->t) ? (string) $cell->is->t : '';
                }

                $cells[$index] = trim($value);
            }

            if ($cells === []) {
                continue;
            }

            ksort($cells);
            $ordered = [];
            $maxIndex = max(array_keys($cells));
            for ($i = 0; $i <= $maxIndex; $i++) {
                $ordered[] = $cells[$i] ?? '';
            }

            if ($headers === null) {
                $headers = $this->normalizeHeaders($ordered);
                continue;
            }

            if ($this->rowIsEmpty($ordered)) {
                continue;
            }

            $rows[] = $this->combineRow($headers, $ordered);
        }

        return $rows;
    }

    private function extractSharedStrings(ZipArchive $zip): array
    {
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if (! $sharedXml) {
            return [];
        }

        $xml = simplexml_load_string($sharedXml);
        if (! $xml) {
            return [];
        }

        $strings = [];
        foreach ($xml->si as $si) {
            if (isset($si->t)) {
                $strings[] = (string) $si->t;
                continue;
            }

            $text = '';
            foreach ($si->r as $run) {
                $text .= (string) $run->t;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = strtolower(trim((string) $header));
            $header = preg_replace('/[^a-z0-9]+/', '_', $header);

            return trim((string) $header, '_');
        }, $headers);
    }

    private function combineRow(array $headers, array $values): array
    {
        $row = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }

            $row[$header] = trim((string) ($values[$index] ?? ''));
        }

        return $row;
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function columnIndexFromReference(string $reference): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($reference));
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return max(0, $index - 1);
    }
}
