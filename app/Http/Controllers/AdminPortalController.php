<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Semester;
use App\Models\StaffProfile;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\SpreadsheetImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminPortalController extends Controller
{
    public function __construct(private readonly SpreadsheetImportService $spreadsheetImportService)
    {
    }

    public function dashboard()
    {
        return view('admin.dashboard', [
            'studentCount' => User::where('role', 'student')->count(),
            'staffCount' => User::where('role', 'staff')->count(),
            'adminCount' => User::where('role', 'admin')->count(),
            'departmentCount' => Department::count(),
            'recentStudents' => User::with('studentProfile.currentSemester', 'studentProfile.departmentRelation')
                ->where('role', 'student')
                ->latest()
                ->take(5)
                ->get(),
            'recentStaff' => User::with('staffProfile.departmentRelation')
                ->where('role', 'staff')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    public function importForm()
    {
        return view('admin.import');
    }

    public function importStore(Request $request)
    {
        $validated = $request->validate([
            'import_type' => ['required', 'in:departments,students,staff'],
            'sheet' => ['required', 'file', 'mimes:csv,txt,xlsx'],
        ]);

        $rows = $this->spreadsheetImportService->rows($request->file('sheet'));

        $imported = match ($validated['import_type']) {
            'departments' => $this->importDepartments($rows),
            'students' => $this->importStudents($rows),
            'staff' => $this->importStaff($rows),
        };

        return redirect()->route('admin.import')->with('status', "Imported {$imported} {$validated['import_type']} records successfully.");
    }

    public function studentsIndex()
    {
        return view('admin.students.index', [
            'students' => User::with('studentProfile.currentSemester', 'studentProfile.departmentRelation')
                ->where('role', 'student')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function studentsCreate()
    {
        return view('admin.students.form', [
            'student' => new User(['role' => 'student']),
            'profile' => new StudentProfile(),
            'semesters' => Semester::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function studentsStore(Request $request)
    {
        $validated = $this->validateStudent($request);

        $student = User::create([
            'name' => User::composeDisplayName($validated['first_name'], $validated['middle_name'] ?? null, $validated['last_name'] ?? null, $validated['name'] ?? null),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        $student->studentProfile()->create($this->studentProfilePayload($validated));

        return redirect()->route('admin.students.index')->with('status', 'Student created successfully.');
    }

    public function studentsEdit(User $user)
    {
        abort_unless($user->role === 'student', 404);

        return view('admin.students.form', [
            'student' => $user->load('studentProfile.departmentRelation'),
            'profile' => $user->studentProfile ?? new StudentProfile(),
            'semesters' => Semester::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function studentsUpdate(Request $request, User $user)
    {
        abort_unless($user->role === 'student', 404);

        $validated = $this->validateStudent($request, $user);

        $user->update([
            'name' => User::composeDisplayName($validated['first_name'], $validated['middle_name'] ?? null, $validated['last_name'] ?? null, $validated['name'] ?? $user->name),
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->studentProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $this->studentProfilePayload($validated)
        );

        return redirect()->route('admin.students.index')->with('status', 'Student updated successfully.');
    }

    public function studentsDestroy(User $user)
    {
        abort_unless($user->role === 'student', 404);
        $user->delete();

        return redirect()->route('admin.students.index')->with('status', 'Student deleted successfully.');
    }

    public function staffIndex()
    {
        return view('admin.staff.index', [
            'staffMembers' => User::with('staffProfile.departmentRelation')
                ->where('role', 'staff')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function staffCreate()
    {
        return view('admin.staff.form', [
            'staff' => new User(['role' => 'staff']),
            'profile' => new StaffProfile(),
            'departments' => Department::orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function staffStore(Request $request)
    {
        $validated = $this->validateStaff($request);

        $staff = User::create([
            'name' => User::composeDisplayName($validated['first_name'], $validated['middle_name'] ?? null, $validated['last_name'] ?? null, $validated['name'] ?? null),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
        ]);

        $staff->staffProfile()->create($this->staffProfilePayload($validated));

        return redirect()->route('admin.staff.index')->with('status', 'Teacher created successfully.');
    }

    public function staffEdit(User $user)
    {
        abort_unless($user->role === 'staff', 404);

        return view('admin.staff.form', [
            'staff' => $user->load('staffProfile.departmentRelation'),
            'profile' => $user->staffProfile ?? new StaffProfile(),
            'departments' => Department::orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function staffUpdate(Request $request, User $user)
    {
        abort_unless($user->role === 'staff', 404);

        $validated = $this->validateStaff($request, $user);

        $user->update([
            'name' => User::composeDisplayName($validated['first_name'], $validated['middle_name'] ?? null, $validated['last_name'] ?? null, $validated['name'] ?? $user->name),
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->staffProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $this->staffProfilePayload($validated)
        );

        return redirect()->route('admin.staff.index')->with('status', 'Teacher updated successfully.');
    }

    public function staffDestroy(User $user)
    {
        abort_unless($user->role === 'staff', 404);
        $user->delete();

        return redirect()->route('admin.staff.index')->with('status', 'Teacher deleted successfully.');
    }

    private function importDepartments(array $rows): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if (blank($row['name'] ?? null) || blank($row['code'] ?? null)) {
                continue;
            }

            Department::updateOrCreate(
                ['code' => strtoupper($row['code'])],
                ['name' => $row['name']]
            );

            $count++;
        }

        return $count;
    }

    private function importStudents(array $rows): int
    {
        $count = 0;

        DB::transaction(function () use ($rows, &$count) {
            foreach ($rows as $row) {
                if (blank($row['email'] ?? null) || blank($row['roll_number'] ?? null)) {
                    continue;
                }

                $department = $this->resolveDepartment($row['department_code'] ?? null, $row['department'] ?? null);
                $semesterId = $this->resolveSemesterId($row['semester_code'] ?? null);

                $user = User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => User::composeDisplayName($row['first_name'] ?? null, $row['middle_name'] ?? null, $row['last_name'] ?? null, $row['name'] ?? null),
                        'password' => Hash::make($row['password'] ?: 'password'),
                        'role' => 'student',
                    ]
                );

                $user->studentProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'roll_number' => $row['roll_number'],
                        'course' => $row['course'] ?? 'Program',
                        'department' => $department?->name ?? ($row['department'] ?? null),
                        'department_id' => $department?->id,
                        'current_semester_id' => $semesterId,
                        'section' => $row['section'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'birthday' => $row['birthday'] ?: null,
                        'first_name' => $row['first_name'] ?? null,
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'] ?? null,
                        'blood_group' => $row['blood_group'] ?? null,
                        'matriculation' => $row['matriculation'] ?? null,
                        'guardian_phone' => $row['guardian_phone'] ?? null,
                        'mother_name' => $row['mother_name'] ?? null,
                        'father_name' => $row['father_name'] ?? null,
                        'address' => $row['address'] ?? null,
                        'pin_code' => $row['pin_code'] ?? null,
                        'state' => $row['state'] ?? null,
                        'city' => $row['city'] ?? null,
                        'secondary_education' => $row['secondary_education'] ?? null,
                        'graduation' => $row['graduation'] ?? null,
                        'program_level' => $row['program_level'] ?? null,
                    ]
                );

                $count++;
            }
        });

        return $count;
    }

    private function importStaff(array $rows): int
    {
        $count = 0;

        DB::transaction(function () use ($rows, &$count) {
            foreach ($rows as $row) {
                if (blank($row['email'] ?? null) || blank($row['employee_code'] ?? null)) {
                    continue;
                }

                $department = $this->resolveDepartment($row['department_code'] ?? null, $row['department'] ?? null);

                $user = User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => User::composeDisplayName($row['first_name'] ?? null, $row['middle_name'] ?? null, $row['last_name'] ?? null, $row['name'] ?? null),
                        'password' => Hash::make($row['password'] ?: 'password'),
                        'role' => 'staff',
                    ]
                );

                $user->staffProfile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'employee_code' => $row['employee_code'],
                        'designation' => $row['designation'] ?? 'Faculty',
                        'department' => $department?->name ?? ($row['department'] ?? null),
                        'department_id' => $department?->id,
                        'phone' => $row['phone'] ?? null,
                        'first_name' => $row['first_name'] ?? null,
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'] ?? null,
                        'blood_group' => $row['blood_group'] ?? null,
                        'matriculation' => $row['matriculation'] ?? null,
                        'guardian_phone' => $row['guardian_phone'] ?? null,
                        'mother_name' => $row['mother_name'] ?? null,
                        'father_name' => $row['father_name'] ?? null,
                        'address' => $row['address'] ?? null,
                        'pin_code' => $row['pin_code'] ?? null,
                        'state' => $row['state'] ?? null,
                        'city' => $row['city'] ?? null,
                        'secondary_education' => $row['secondary_education'] ?? null,
                        'graduation' => $row['graduation'] ?? null,
                    ]
                );

                $count++;
            }
        });

        return $count;
    }

    private function validateStudent(Request $request, ?User $student = null): array
    {
        $passwordRules = $student ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'];

        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student?->id)],
            'password' => $passwordRules,
            'roll_number' => ['required', 'string', 'max:255', Rule::unique('student_profiles', 'roll_number')->ignore($student?->studentProfile?->id)],
            'course' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'current_semester_id' => ['nullable', 'exists:semesters,id'],
            'section' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birthday' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'max:30'],
            'matriculation' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'pin_code' => ['nullable', 'string', 'max:20'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'secondary_education' => ['nullable', 'string', 'max:255'],
            'graduation' => ['nullable', 'string', 'max:255'],
            'program_level' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function validateStaff(Request $request, ?User $staff = null): array
    {
        $passwordRules = $staff ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'];

        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff?->id)],
            'password' => $passwordRules,
            'employee_code' => ['required', 'string', 'max:255', Rule::unique('staff_profiles', 'employee_code')->ignore($staff?->staffProfile?->id)],
            'designation' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'blood_group' => ['nullable', 'string', 'max:30'],
            'matriculation' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'pin_code' => ['nullable', 'string', 'max:20'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'secondary_education' => ['nullable', 'string', 'max:255'],
            'graduation' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function studentProfilePayload(array $validated): array
    {
        $department = isset($validated['department_id']) ? Department::find($validated['department_id']) : null;

        return [
            'roll_number' => $validated['roll_number'],
            'course' => $validated['course'],
            'department' => $department?->name,
            'department_id' => $department?->id,
            'current_semester_id' => $validated['current_semester_id'] ?? null,
            'section' => $validated['section'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'birthday' => $validated['birthday'] ?? null,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'matriculation' => $validated['matriculation'] ?? null,
            'guardian_phone' => $validated['guardian_phone'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'pin_code' => $validated['pin_code'] ?? null,
            'state' => $validated['state'] ?? null,
            'city' => $validated['city'] ?? null,
            'secondary_education' => $validated['secondary_education'] ?? null,
            'graduation' => $validated['graduation'] ?? null,
            'program_level' => $validated['program_level'] ?? null,
        ];
    }

    private function staffProfilePayload(array $validated): array
    {
        $department = isset($validated['department_id']) ? Department::find($validated['department_id']) : null;

        return [
            'employee_code' => $validated['employee_code'],
            'designation' => $validated['designation'],
            'department' => $department?->name,
            'department_id' => $department?->id,
            'phone' => $validated['phone'] ?? null,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'matriculation' => $validated['matriculation'] ?? null,
            'guardian_phone' => $validated['guardian_phone'] ?? null,
            'mother_name' => $validated['mother_name'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'address' => $validated['address'] ?? null,
            'pin_code' => $validated['pin_code'] ?? null,
            'state' => $validated['state'] ?? null,
            'city' => $validated['city'] ?? null,
            'secondary_education' => $validated['secondary_education'] ?? null,
            'graduation' => $validated['graduation'] ?? null,
        ];
    }

    private function resolveDepartment(?string $code, ?string $name): ?Department
    {
        if (filled($code)) {
            return Department::firstOrCreate(
                ['code' => strtoupper($code)],
                ['name' => $name ?: strtoupper($code)]
            );
        }

        if (filled($name)) {
            return Department::firstOrCreate(
                ['code' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 10)) ?: 'DEPT'],
                ['name' => $name]
            );
        }

        return null;
    }

    private function resolveSemesterId(?string $semesterCode): ?int
    {
        if (blank($semesterCode)) {
            return null;
        }

        return Semester::where('code', strtoupper($semesterCode))->value('id');
    }
}
