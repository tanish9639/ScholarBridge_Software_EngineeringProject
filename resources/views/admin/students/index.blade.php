@extends('layouts.app', ['title' => 'Thapar LMS · Students'])

@section('content')
    <section class="shell">
        <div class="toolbar">
            <div>
                <span class="badge">Admin · Students</span>
                <h1>Manage students</h1>
                <p class="muted">Create, update, and delete student records, profile details, and semester assignment.</p>
            </div>
            <div class="actions-inline">
                <a class="btn small secondary" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                <a class="btn small secondary" href="{{ route('admin.import') }}">Bulk Import</a>
                <a class="btn small" href="{{ route('admin.students.create') }}">Create Student</a>
            </div>
        </div>

        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roll No</th>
                        <th>Course</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->studentProfile?->roll_number }}</td>
                            <td>{{ $student->studentProfile?->course }}</td>
                            <td>{{ $student->studentProfile?->departmentRelation?->name ?? $student->studentProfile?->department ?? 'N/A' }}</td>
                            <td>{{ $student->studentProfile?->currentSemester?->name ?? 'Not set' }}</td>
                            <td>
                                <div class="actions-inline">
                                    <a class="btn small secondary" href="{{ route('admin.students.edit', $student) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="small" onclick="return confirm('Delete this student?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No students available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
