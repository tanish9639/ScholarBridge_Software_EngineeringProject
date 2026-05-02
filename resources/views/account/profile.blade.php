@extends('layouts.app', ['title' => 'Thapar LMS · Personal Details'])

@section('content')
    <section class="shell">
        <div class="toolbar">
            <div>
                <span class="badge">{{ ucfirst($user->role) }} Profile</span>
                <h1>Personal details</h1>
                <p class="muted">Your department is detected from your profile record and shown automatically after login.</p>
            </div>
            <div class="actions-inline">
                <a class="btn small secondary" href="{{ route($user->dashboardRouteName()) }}">Back to Dashboard</a>
                <a class="btn small" href="{{ route($user->role.'.profile.edit') }}">Edit Details</a>
            </div>
        </div>

        <div class="grid">
            <div class="panel">
                <span class="badge">{{ ucfirst($user->role) }}</span>
                <h2>{{ $user->name }}</h2>
                <p class="muted">{{ $user->departmentName() ?? 'Department not assigned' }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                @if ($user->role === 'student')
                    <p><strong>Roll number:</strong> {{ $profile?->roll_number ?? 'N/A' }}</p>
                    <p><strong>Course:</strong> {{ $profile?->course ?? 'N/A' }}</p>
                @else
                    <p><strong>Employee code:</strong> {{ $profile?->employee_code ?? 'N/A' }}</p>
                    <p><strong>Designation:</strong> {{ $profile?->designation ?? 'N/A' }}</p>
                @endif
            </div>
        </div>

        <div class="detail-grid" style="margin-top: 1rem;">
            <div class="detail-card">
                <strong>First name</strong>
                {{ $profile?->first_name ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Middle name</strong>
                {{ $profile?->middle_name ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Last name</strong>
                {{ $profile?->last_name ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Blood group</strong>
                {{ $profile?->blood_group ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Matriculation</strong>
                {{ $profile?->matriculation ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Phone number</strong>
                {{ $profile?->phone ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Guardian phone no.</strong>
                {{ $profile?->guardian_phone ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Mother's name</strong>
                {{ $profile?->mother_name ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Father's name</strong>
                {{ $profile?->father_name ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Address</strong>
                {{ $profile?->address ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Pin code</strong>
                {{ $profile?->pin_code ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>State</strong>
                {{ $profile?->state ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>City</strong>
                {{ $profile?->city ?? 'N/A' }}
            </div>
            <div class="detail-card">
                <strong>Secondary education / diploma</strong>
                {{ $profile?->secondary_education ?? 'N/A' }}
            </div>
            @if ($isMastersStudent)
                <div class="detail-card">
                    <strong>Graduation</strong>
                    {{ $profile?->graduation ?? 'N/A' }}
                </div>
            @endif
        </div>
    </section>
@endsection
