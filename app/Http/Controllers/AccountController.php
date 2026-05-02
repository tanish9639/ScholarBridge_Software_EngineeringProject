<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function show()
    {
        $user = $this->loadUser();

        return view('account.profile', [
            'user' => $user,
            'profile' => $user->profile(),
            'isMastersStudent' => $user->role === 'student' && str_contains(strtolower((string) $user->studentProfile?->program_level), 'master'),
        ]);
    }

    public function edit()
    {
        $user = $this->loadUser();

        return view('account.edit', [
            'user' => $user,
            'profile' => $user->profile(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->loadUser();
        $validated = $this->validateProfile($request, $user->role);

        $department = isset($validated['department_id']) ? Department::find($validated['department_id']) : null;

        $user->update([
            'name' => $user::composeDisplayName(
                $validated['first_name'],
                $validated['middle_name'] ?? null,
                $validated['last_name'] ?? null,
                $user->name
            ),
        ]);

        $payload = [
            'department_id' => $department?->id,
            'department' => $department?->name,
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

        if ($user->role === 'student') {
            $payload['birthday'] = $validated['birthday'] ?? null;
            $payload['program_level'] = $validated['program_level'] ?? null;
            $user->studentProfile()->updateOrCreate(['user_id' => $user->id], $payload);
        } else {
            $user->staffProfile()->updateOrCreate(['user_id' => $user->id], $payload);
        }

        return redirect()->route($user->role.'.profile.show')->with('status', 'Profile details updated successfully.');
    }

    public function editPassword()
    {
        $user = $this->loadUser();

        return view('account.password', ['user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $user = $this->loadUser();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route($user->role.'.profile.password.edit')->with('status', 'Password changed successfully.');
    }

    private function loadUser()
    {
        return Auth::user()->load('studentProfile.departmentRelation', 'staffProfile.departmentRelation');
    }

    private function validateProfile(Request $request, string $role): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
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
        ];

        if ($role === 'student') {
            $rules['birthday'] = ['nullable', 'date'];
            $rules['program_level'] = ['nullable', 'string', 'max:255'];
        }

        return $request->validate($rules);
    }
}
