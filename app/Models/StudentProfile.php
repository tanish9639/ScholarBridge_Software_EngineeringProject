<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'roll_number',
        'course',
        'department',
        'department_id',
        'current_semester_id',
        'section',
        'phone',
        'birthday',
        'first_name',
        'middle_name',
        'last_name',
        'blood_group',
        'matriculation',
        'guardian_phone',
        'mother_name',
        'father_name',
        'address',
        'pin_code',
        'state',
        'city',
        'secondary_education',
        'graduation',
        'program_level',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentSemester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'current_semester_id');
    }

    public function departmentRelation(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
