<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\AuxiliaryExamRequest;
use App\Models\CalendarEvent;
use App\Models\Elective;
use App\Models\ElectiveEnrollment;
use App\Models\FeePayment;
use App\Models\MedicalLeave;
use App\Models\StudyMaterial;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user()->load([
            'studentProfile.currentSemester',
            'attendanceRecords.subject',
            'markRecords.subject',
            'gradeCards.semester',
            'medicalLeaves',
            'electiveEnrollments.elective.semester',
            'feePayments.semester',
        ]);

        $currentSemester = $student->studentProfile?->currentSemester;

        $attendanceSummary = $student->attendanceRecords
            ->groupBy(fn ($record) => $record->subject?->title ?? 'Unknown Subject')
            ->map(function ($records) {
                $total = $records->count();
                $present = $records->where('status', 'present')->count();

                return [
                    'total' => $total,
                    'present' => $present,
                    'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ];
            });

        $recentMarks = $student->markRecords->sortByDesc('created_at')->take(6)->values();

        $alerts = Alert::query()
            ->where(function ($query) {
                $query->where('audience', 'all')->orWhere('audience', 'student');
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->get();

        $calendarEvents = CalendarEvent::query()
            ->where(function ($query) use ($student) {
                $query->whereNull('user_id')->orWhere('user_id', $student->id);
            })
            ->orderBy('event_date')
            ->get();

        if ($student->studentProfile?->birthday) {
            $birthday = $student->studentProfile->birthday;
            $calendarEvents->prepend((object) [
                'title' => 'Birthday',
                'event_date' => Carbon::create(now()->year, $birthday->month, $birthday->day),
                'dialog_message' => 'Happy birthday '.$student->name.'! Wishing you a joyful year ahead.',
                'type' => 'birthday',
            ]);
        }

        $birthdayDialog = null;
        if ($student->studentProfile?->birthday?->isBirthday()) {
            $birthdayDialog = 'Happy birthday '.$student->name.'! Your LMS calendar has a special reminder for you today.';
        }

        $electives = $currentSemester
            ? Elective::with('semester', 'teacher')->where('semester_id', $currentSemester->id)->get()
            : collect();

        $auxiliaryEligibleSubjects = $currentSemester
            ? Subject::where('semester_id', $currentSemester->id)->orderBy('title')->get()
            : collect();

        $studyMaterials = StudyMaterial::with('subject', 'uploader')->latest()->get();

        return view('student.dashboard', compact(
            'student',
            'currentSemester',
            'attendanceSummary',
            'recentMarks',
            'alerts',
            'calendarEvents',
            'birthdayDialog',
            'electives',
            'auxiliaryEligibleSubjects',
            'studyMaterials',
        ));
    }

    public function storeMedicalLeave(Request $request)
    {
        $validated = $request->validate([
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        MedicalLeave::create([
            ...$validated,
            'student_user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('status', 'Medical leave request submitted.');
    }

    public function storeElectiveSelection(Request $request)
    {
        $validated = $request->validate([
            'elective_id' => ['required', 'exists:electives,id'],
        ]);

        ElectiveEnrollment::firstOrCreate(
            [
                'student_user_id' => Auth::id(),
                'elective_id' => $validated['elective_id'],
            ],
            ['status' => 'selected']
        );

        return back()->with('status', 'Elective selected successfully.');
    }

    public function storeAuxiliaryExamRequest(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        AuxiliaryExamRequest::create([
            'student_user_id' => Auth::id(),
            'subject_id' => $validated['subject_id'],
            'semester_id' => Auth::user()->studentProfile?->current_semester_id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Auxiliary exam request submitted.');
    }

    public function payFee(FeePayment $feePayment)
    {
        abort_unless($feePayment->student_user_id === Auth::id(), 403);

        $feePayment->update([
            'amount_paid' => $feePayment->amount_due,
            'paid_at' => now(),
            'status' => 'paid',
            'reference_no' => 'TXN-'.strtoupper((string) str()->random(8)),
        ]);

        return back()->with('status', 'Semester fee marked as paid successfully.');
    }

    public function downloadMaterial(StudyMaterial $studyMaterial)
    {
        abort_unless(Storage::disk('local')->exists($studyMaterial->file_path), 404);

        return Storage::disk('local')->download($studyMaterial->file_path, $studyMaterial->file_name);
    }
}
