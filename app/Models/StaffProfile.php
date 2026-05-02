<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id',
        'employee_code',
        'designation',
        'department',
        'department_id',
        'phone',
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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departmentRelation(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
