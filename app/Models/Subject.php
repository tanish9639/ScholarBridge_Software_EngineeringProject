<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'semester_id',
        'staff_user_id',
        'title',
        'code',
        'credits',
        'is_elective',
    ];

    protected function casts(): array
    {
        return [
            'is_elective' => 'boolean',
        ];
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function studyMaterials(): HasMany
    {
        return $this->hasMany(StudyMaterial::class);
    }
}
