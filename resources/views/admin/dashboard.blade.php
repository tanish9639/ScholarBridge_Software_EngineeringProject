@extends('layouts.app', ['title' => 'Thapar LMS · Admin Dashboard'])

@section('content')
    <section class="shell">
        <div class="toolbar">
            <div>
                <span class="badge">Admin Panel</span>
                <h1>Administrative control center</h1>
                <p class="muted">Manage student and teacher records with CRUD operations and keep the LMS roster organized.</p>
            </div>
            <div class="actions-inline">
                <a class="btn small secondary" href="{{ route('admin.import') }}">Bulk Import</a>
                <a class="btn small secondary" href="{{ route('admin.students.index') }}">Manage Students</a>
                <a class="btn small" href="{{ route('admin.staff.index') }}">Manage Teachers</a>
            </div>
        </div>

        <div class="grid-3">
            <div class="panel">
                <h3>Total students</h3>
                <p class="metric">{{ $studentCount }}</p>
            </div>
            <div class="panel">
                <h3>Total teachers</h3>
                <p class="metric">{{ $staffCount }}</p>
            </div>
            <div class="panel">
                <h3>Total admins</h3>
                <p class="metric">{{ $adminCount }}</p>
            </div>
            <div class="panel">
                <h3>Total departments</h3>
                <p class="metric">{{ $departmentCount }}</p>
            </div>
        </div>

        <div class="cards-2" style="margin-top: 1rem;">
            <div class="panel">
                <div class="section-title">
                    <h2>Recent students</h2>
                    <a class="btn small secondary" href="{{ route('admin.students.create') }}">Add student</a>
                </div>
                <table>
                    <thead>
                        <tr><th>Name</th><th>Roll No</th><th>Department</th><th>Semester</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentStudents as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->studentProfile?->roll_number }}</td>
                                <td>{{ $student->studentProfile?->departmentRelation?->name ?? $student->studentProfile?->department ?? 'N/A' }}</td>
                                <td>{{ $student->studentProfile?->currentSemester?->name ?? 'Not set' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <div class="section-title">
                    <h2>Recent teachers</h2>
                    <a class="btn small secondary" href="{{ route('admin.staff.create') }}">Add teacher</a>
                </div>
                <table>
                    <thead>
                        <tr><th>Name</th><th>Employee code</th><th>Department</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentStaff as $staff)
                            <tr>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->staffProfile?->employee_code }}</td>
                                <td>{{ $staff->staffProfile?->departmentRelation?->name ?? $staff->staffProfile?->department ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No teachers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
