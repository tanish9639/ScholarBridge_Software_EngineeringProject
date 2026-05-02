@extends('layouts.app', ['title' => 'Thapar LMS · '.($mode === 'create' ? 'Create Teacher' : 'Edit Teacher')])

@section('content')
    <section class="shell">
        <div class="toolbar">
            <div>
                <span class="badge">Admin · Teachers</span>
                <h1>{{ $mode === 'create' ? 'Create teacher' : 'Edit teacher' }}</h1>
                <p class="muted">Maintain teacher login, employee code, and department details.</p>
            </div>
            <a class="btn small secondary" href="{{ route('admin.staff.index') }}">Back to Teachers</a>
        </div>

        <div class="panel surface-accent">
            <form method="POST" action="{{ $mode === 'create' ? route('admin.staff.store') : route('admin.staff.update', $staff) }}">
                @csrf
                @if ($mode === 'edit')
                    @method('PUT')
                @endif

                <div class="cards-2">
                    <div>
                        <div class="field">
                            <label for="first_name">First name</label>
                            <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $profile->first_name) }}" required>
                        </div>
                        <div class="field">
                            <label for="middle_name">Middle name</label>
                            <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name', $profile->middle_name) }}">
                        </div>
                        <div class="field">
                            <label for="last_name">Last name</label>
                            <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $profile->last_name) }}">
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $staff->email) }}" required>
                        </div>
                        <div class="field">
                            <label for="password">Password {{ $mode === 'edit' ? '(leave blank to keep current)' : '' }}</label>
                            <input id="password" type="password" name="password" {{ $mode === 'create' ? 'required' : '' }}>
                        </div>
                    </div>
                    <div>
                        <div class="field">
                            <label for="employee_code">Employee code</label>
                            <input id="employee_code" type="text" name="employee_code" value="{{ old('employee_code', $profile->employee_code) }}" required>
                        </div>
                        <div class="field">
                            <label for="designation">Designation</label>
                            <input id="designation" type="text" name="designation" value="{{ old('designation', $profile->designation) }}" required>
                        </div>
                        <div class="field">
                            <label for="department_id">Department</label>
                            <select id="department_id" name="department_id">
                                <option value="">Select department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected((string) old('department_id', $profile->department_id) === (string) $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="phone">Phone</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone', $profile->phone) }}">
                        </div>
                    </div>
                </div>

                <div class="cards-2">
                    <div class="field">
                        <label for="blood_group">Blood group</label>
                        <input id="blood_group" type="text" name="blood_group" value="{{ old('blood_group', $profile->blood_group) }}">
                    </div>
                    <div class="field">
                        <label for="matriculation">Matriculation</label>
                        <input id="matriculation" type="text" name="matriculation" value="{{ old('matriculation', $profile->matriculation) }}">
                    </div>
                </div>

                <div class="cards-2">
                    <div class="field">
                        <label for="guardian_phone">Guardian phone no.</label>
                        <input id="guardian_phone" type="text" name="guardian_phone" value="{{ old('guardian_phone', $profile->guardian_phone) }}">
                    </div>
                    <div class="field">
                        <label for="mother_name">Mother's name</label>
                        <input id="mother_name" type="text" name="mother_name" value="{{ old('mother_name', $profile->mother_name) }}">
                    </div>
                </div>

                <div class="cards-2">
                    <div class="field">
                        <label for="father_name">Father's name</label>
                        <input id="father_name" type="text" name="father_name" value="{{ old('father_name', $profile->father_name) }}">
                    </div>
                    <div class="field">
                        <label for="secondary_education">Secondary education / diploma</label>
                        <input id="secondary_education" type="text" name="secondary_education" value="{{ old('secondary_education', $profile->secondary_education) }}">
                    </div>
                </div>

                <div class="field">
                    <label for="graduation">Graduation</label>
                    <input id="graduation" type="text" name="graduation" value="{{ old('graduation', $profile->graduation) }}">
                </div>

                <div class="field">
                    <label for="address">Address</label>
                    <textarea id="address" name="address">{{ old('address', $profile->address) }}</textarea>
                </div>

                <div class="cards-2">
                    <div class="field">
                        <label for="pin_code">Pin code</label>
                        <input id="pin_code" type="text" name="pin_code" value="{{ old('pin_code', $profile->pin_code) }}">
                    </div>
                    <div class="field">
                        <label for="state">State</label>
                        <input id="state" type="text" name="state" value="{{ old('state', $profile->state) }}">
                    </div>
                </div>

                <div class="field">
                    <label for="city">City</label>
                    <input id="city" type="text" name="city" value="{{ old('city', $profile->city) }}">
                </div>

                <div class="field">
                    <button type="submit">{{ $mode === 'create' ? 'Create Teacher' : 'Update Teacher' }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
