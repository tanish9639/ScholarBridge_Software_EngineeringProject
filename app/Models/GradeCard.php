<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeCard extends Model
{
    protected $fillable = [
        'student_user_id',
        'semester_id',
        'gpa',
        'cgpa',
        'grade_summary',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
