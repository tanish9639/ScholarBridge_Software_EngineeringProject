<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarkRecord extends Model
{
    protected $fillable = [
        'student_user_id',
        'subject_id',
        'semester_id',
        'assessment_name',
        'max_marks',
        'marks_obtained',
        'uploaded_by',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
