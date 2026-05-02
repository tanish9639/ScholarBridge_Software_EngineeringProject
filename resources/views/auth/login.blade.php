@extends('layouts.app', ['title' => 'Thapar LMS · '.ucfirst($role).' Login'])

@section('content')
    <section class="shell">
        <div class="cards-2">
            <div class="panel surface-accent">
                <span class="badge">{{ ucfirst($role) }} Login</span>
                <h1>Welcome to Thapar LMS</h1>
                <p class="muted">Use the seeded demo credentials or create more users later through the database.</p>

                <form method="POST" action="{{ route('login.attempt', $role) }}">
                    @csrf
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $role === 'student' ? 'student@example.com' : ($role === 'staff' ? 'staff@example.com' : 'admin@example.com')) }}" required>
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" value="password" required>
                    </div>
                    <div class="field">
                        <button type="submit">Login to {{ ucfirst($role) }} Portal</button>
                    </div>
                </form>
            </div>

            <div class="panel">
                <span class="badge">Portal Snapshot</span>
                <h2>What Thapar LMS covers</h2>
                <table>
                    <tr><th>Students</th><td>Attendance, marks, grade cards, medical leave, electives, auxiliary exams, alerts, fees, downloads</td></tr>
                    <tr><th>Staff</th><td>Attendance entry, marks upload, content upload, alert publishing</td></tr>
                    <tr><th>Admin</th><td>CRUD management for student and teacher accounts with profile maintenance</td></tr>
                    <tr><th>Calendar</th><td>Birthday-aware dialog plus academic events</td></tr>
                </table>
            </div>
        </div>
    </section>
@endsection
