<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = [
        'title',
        'message',
        'audience',
        'level',
        'created_by',
        'is_instant',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_instant' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
