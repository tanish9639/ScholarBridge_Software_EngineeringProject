<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectiveEnrollment extends Model
{
    protected $fillable = [
        'student_user_id',
        'elective_id',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function elective(): BelongsTo
    {
        return $this->belongsTo(Elective::class);
    }
}
