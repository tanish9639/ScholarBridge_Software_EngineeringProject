<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Elective extends Model
{
    protected $fillable = [
        'semester_id',
        'staff_user_id',
        'title',
        'code',
        'description',
        'seats',
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ElectiveEnrollment::class);
    }
}
