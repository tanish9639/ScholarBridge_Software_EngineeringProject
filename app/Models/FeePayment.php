<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    protected $fillable = [
        'student_user_id',
        'semester_id',
        'amount_due',
        'amount_paid',
        'due_date',
        'paid_at',
        'status',
        'reference_no',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
