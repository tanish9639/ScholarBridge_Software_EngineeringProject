<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Thapar LMS');
    }

    public function test_student_can_access_student_dashboard(): void
    {
        $this->seed();

        $student = User::where('email', 'student@example.com')->firstOrFail();

        $response = $this->actingAs($student)->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertSee('Semester-wise grade cards');
    }

    public function test_staff_can_access_staff_dashboard(): void
    {
        $this->seed();

        $staff = User::where('email', 'staff@example.com')->firstOrFail();

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertSee('Upload subject content');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Administrative control center');
    }

    public function test_admin_can_create_a_student_record(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.students.store'), [
            'first_name' => 'Riya',
            'middle_name' => 'K',
            'last_name' => 'Malhotra',
            'email' => 'riya@example.com',
            'password' => 'password123',
            'roll_number' => 'CS2026-002',
            'course' => 'B.Tech',
            'department_id' => Department::where('code', 'CSE')->value('id'),
            'current_semester_id' => 1,
            'section' => 'B',
            'phone' => '9000000000',
            'birthday' => '2005-04-16',
            'program_level' => "Bachelor's",
        ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'riya@example.com',
            'role' => 'student',
        ]);
        $this->assertDatabaseHas('student_profiles', [
            'roll_number' => 'CS2026-002',
            'section' => 'B',
        ]);
    }

    public function test_admin_can_import_departments_from_csv(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $file = UploadedFile::fake()->createWithContent('departments.csv', "name,code\nMechanical Engineering,MEC\nCivil Engineering,CIV\n");

        $response = $this->actingAs($admin)->post(route('admin.import.store'), [
            'import_type' => 'departments',
            'sheet' => $file,
        ]);

        $response->assertRedirect(route('admin.import'));
        $this->assertDatabaseHas('departments', ['code' => 'MEC']);
        $this->assertDatabaseHas('departments', ['code' => 'CIV']);
    }

    public function test_student_can_view_personal_details_page(): void
    {
        $this->seed();

        $student = User::where('email', 'student@example.com')->firstOrFail();

        $response = $this->actingAs($student)->get(route('student.profile.show'));

        $response->assertOk();
        $response->assertSeeText("Mother's name");
        $response->assertSeeText('Computer Science');
    }
}
