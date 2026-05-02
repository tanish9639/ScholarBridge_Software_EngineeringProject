<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'student_user_id');
    }

    public function markRecords(): HasMany
    {
        return $this->hasMany(MarkRecord::class, 'student_user_id');
    }

    public function gradeCards(): HasMany
    {
        return $this->hasMany(GradeCard::class, 'student_user_id');
    }

    public function medicalLeaves(): HasMany
    {
        return $this->hasMany(MedicalLeave::class, 'student_user_id');
    }

    public function electiveEnrollments(): HasMany
    {
        return $this->hasMany(ElectiveEnrollment::class, 'student_user_id');
    }

    public function feePayments(): HasMany
    {
        return $this->hasMany(FeePayment::class, 'student_user_id');
    }

    public function taughtSubjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'staff_user_id');
    }

    public function profile(): StudentProfile|StaffProfile|null
    {
        return match ($this->role) {
            'student' => $this->studentProfile,
            'staff' => $this->staffProfile,
            default => null,
        };
    }

    public function departmentName(): ?string
    {
        $profile = $this->profile();

        return $profile?->departmentRelation?->name ?? $profile?->department;
    }

    public function dashboardRouteName(): string
    {
        return match ($this->role) {
            'student' => 'student.dashboard',
            'staff' => 'staff.dashboard',
            'admin' => 'admin.dashboard',
            default => 'landing',
        };
    }

    public static function composeDisplayName(?string $firstName, ?string $middleName, ?string $lastName, ?string $fallback = null): string
    {
        $parts = array_filter([$firstName, $middleName, $lastName], fn ($value) => filled($value));

        return $parts !== [] ? implode(' ', $parts) : ($fallback ?: 'User '.Str::upper(Str::random(4)));
    }
}
