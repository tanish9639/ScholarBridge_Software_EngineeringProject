<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\AttendanceRecord;
use App\Models\CalendarEvent;
use App\Models\Department;
use App\Models\Elective;
use App\Models\ElectiveEnrollment;
use App\Models\FeePayment;
use App\Models\GradeCard;
use App\Models\MarkRecord;
use App\Models\Semester;
use App\Models\StaffProfile;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $computerScience = Department::create([
            'name' => 'Computer Science',
            'code' => 'CSE',
        ]);

        $electrical = Department::create([
            'name' => 'Electrical Engineering',
            'code' => 'EEE',
        ]);

        $semester1 = Semester::create([
            'name' => 'Semester 1',
            'code' => 'SEM1',
            'start_date' => now()->subMonths(5)->startOfMonth(),
            'end_date' => now()->addMonth()->endOfMonth(),
            'is_active' => true,
        ]);

        $semester2 = Semester::create([
            'name' => 'Semester 2',
            'code' => 'SEM2',
            'start_date' => now()->addMonths(2)->startOfMonth(),
            'end_date' => now()->addMonths(7)->endOfMonth(),
            'is_active' => false,
        ]);

        User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $staff = User::create([
            'name' => 'Dr. Meera Sharma',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'password' => Hash::make('password'),
        ]);

        StaffProfile::create([
            'user_id' => $staff->id,
            'employee_code' => 'STF1001',
            'designation' => 'Assistant Professor',
            'department' => 'Computer Science',
            'department_id' => $computerScience->id,
            'phone' => '9876543210',
            'first_name' => 'Meera',
            'last_name' => 'Sharma',
            'blood_group' => 'B+',
            'matriculation' => 'CBSE',
            'guardian_phone' => '9811100000',
            'mother_name' => 'Sarla Sharma',
            'father_name' => 'Raghav Sharma',
            'address' => 'Faculty Housing, Thapar Campus',
            'pin_code' => '147004',
            'state' => 'Punjab',
            'city' => 'Patiala',
            'secondary_education' => 'B.Sc. Mathematics',
            'graduation' => 'M.Tech Education Systems',
        ]);

        $student = User::create([
            'name' => 'Aarav Patel',
            'email' => 'student@example.com',
            'role' => 'student',
            'password' => Hash::make('password'),
        ]);

        StudentProfile::create([
            'user_id' => $student->id,
            'roll_number' => 'CS2026-001',
            'course' => 'B.Tech',
            'department' => 'Computer Science',
            'department_id' => $computerScience->id,
            'current_semester_id' => $semester1->id,
            'section' => 'A',
            'phone' => '9123456780',
            'birthday' => now()->toDateString(),
            'first_name' => 'Aarav',
            'last_name' => 'Patel',
            'blood_group' => 'O+',
            'matriculation' => 'CBSE',
            'guardian_phone' => '9000012345',
            'mother_name' => 'Anita Patel',
            'father_name' => 'Rajesh Patel',
            'address' => '21 Green Avenue',
            'pin_code' => '147001',
            'state' => 'Punjab',
            'city' => 'Patiala',
            'secondary_education' => 'Senior Secondary PCM',
            'graduation' => null,
            'program_level' => "Bachelor's",
        ]);

        $maths = Subject::create([
            'semester_id' => $semester1->id,
            'staff_user_id' => $staff->id,
            'title' => 'Engineering Mathematics',
            'code' => 'MTH101',
            'credits' => 4,
            'is_elective' => false,
        ]);

        $programming = Subject::create([
            'semester_id' => $semester1->id,
            'staff_user_id' => $staff->id,
            'title' => 'Programming Fundamentals',
            'code' => 'CSE101',
            'credits' => 4,
            'is_elective' => false,
        ]);

        $designThinking = Elective::create([
            'semester_id' => $semester1->id,
            'staff_user_id' => $staff->id,
            'title' => 'Design Thinking',
            'code' => 'ELC201',
            'description' => 'Creativity, rapid prototyping, and product thinking for first-year students.',
            'seats' => 40,
        ]);

        ElectiveEnrollment::create([
            'student_user_id' => $student->id,
            'elective_id' => $designThinking->id,
            'status' => 'selected',
        ]);

        foreach ([
            ['subject' => $maths, 'status' => 'present'],
            ['subject' => $maths, 'status' => 'present'],
            ['subject' => $maths, 'status' => 'absent'],
            ['subject' => $programming, 'status' => 'present'],
            ['subject' => $programming, 'status' => 'late'],
            ['subject' => $programming, 'status' => 'present'],
        ] as $index => $record) {
            AttendanceRecord::create([
                'student_user_id' => $student->id,
                'subject_id' => $record['subject']->id,
                'semester_id' => $semester1->id,
                'attendance_date' => now()->subDays(6 - $index)->toDateString(),
                'status' => $record['status'],
                'marked_by' => $staff->id,
            ]);
        }

        MarkRecord::create([
            'student_user_id' => $student->id,
            'subject_id' => $maths->id,
            'semester_id' => $semester1->id,
            'assessment_name' => 'Mid Semester',
            'max_marks' => 100,
            'marks_obtained' => 84,
            'uploaded_by' => $staff->id,
        ]);

        MarkRecord::create([
            'student_user_id' => $student->id,
            'subject_id' => $programming->id,
            'semester_id' => $semester1->id,
            'assessment_name' => 'Internal Assessment 1',
            'max_marks' => 50,
            'marks_obtained' => 42,
            'uploaded_by' => $staff->id,
        ]);

        GradeCard::create([
            'student_user_id' => $student->id,
            'semester_id' => $semester1->id,
            'gpa' => 8.65,
            'cgpa' => 8.65,
            'grade_summary' => 'Engineering Mathematics: A, Programming Fundamentals: A, English Communication: B+',
        ]);

        FeePayment::create([
            'student_user_id' => $student->id,
            'semester_id' => $semester1->id,
            'amount_due' => 45000,
            'amount_paid' => 20000,
            'due_date' => now()->addDays(10)->toDateString(),
            'status' => 'partial',
            'reference_no' => 'ADV-SEM1-2026',
        ]);

        FeePayment::create([
            'student_user_id' => $student->id,
            'semester_id' => $semester2->id,
            'amount_due' => 47000,
            'amount_paid' => 0,
            'due_date' => now()->addMonths(2)->toDateString(),
            'status' => 'pending',
        ]);

        Alert::create([
            'title' => 'Internal exam update',
            'message' => 'Internal assessment timetable has been published. Please review the calendar section.',
            'audience' => 'student',
            'level' => 'warning',
            'created_by' => $staff->id,
            'is_instant' => true,
            'expires_at' => now()->addDays(5),
        ]);

        Alert::create([
            'title' => 'Faculty meeting',
            'message' => 'Department faculty meeting scheduled for Friday at 3:00 PM.',
            'audience' => 'staff',
            'level' => 'info',
            'created_by' => $staff->id,
            'is_instant' => true,
            'expires_at' => now()->addDays(5),
        ]);

        CalendarEvent::create([
            'title' => 'Semester orientation',
            'event_date' => now()->addDays(2)->toDateString(),
            'dialog_message' => 'Orientation for project and lab work starts at 10:00 AM in Seminar Hall 2.',
            'type' => 'academic',
        ]);
    }
}
