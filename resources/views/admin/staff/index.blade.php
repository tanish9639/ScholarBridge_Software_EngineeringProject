@extends('layouts.app', ['title' => 'Thapar LMS · Teachers'])

@section('content')
    <section class="shell">
        <div class="toolbar">
            <div>
                <span class="badge">Admin · Teachers</span>
                <h1>Manage teachers</h1>
                <p class="muted">Create, edit, and delete teacher accounts and profile records.</p>
            </div>
            <div class="actions-inline">
                <a class="btn small secondary" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                <a class="btn small secondary" href="{{ route('admin.import') }}">Bulk Import</a>
                <a class="btn small" href="{{ route('admin.staff.create') }}">Create Teacher</a>
            </div>
        </div>

        <div class="panel">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Employee Code</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staffMembers as $staff)
                        <tr>
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ $staff->staffProfile?->employee_code }}</td>
                            <td>{{ $staff->staffProfile?->designation }}</td>
                            <td>{{ $staff->staffProfile?->departmentRelation?->name ?? $staff->staffProfile?->department ?? 'N/A' }}</td>
                            <td>
                                <div class="actions-inline">
                                    <a class="btn small secondary" href="{{ route('admin.staff.edit', $staff) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="small" onclick="return confirm('Delete this teacher?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No teachers available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
