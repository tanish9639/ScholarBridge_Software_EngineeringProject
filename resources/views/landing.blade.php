@extends('layouts.app', ['title' => 'Thapar LMS'])

@section('content')
    <section class="hero">
        <div class="hero-card">
            <span class="badge" style="background: rgba(255,255,255,0.18); color: white;">Laravel 13 · PHP 8.5</span>
            <h1 style="font-size: clamp(2.2rem, 4vw, 3.8rem); margin-bottom: 0.75rem;">Thapar LMS</h1>
            <p style="font-size: 1.1rem; font-weight: 700; letter-spacing: 0.03em;">A bold red-and-white academic workspace for students, staff, and semester operations.</p>
            <p style="max-width: 52rem; line-height: 1.7;">
                This portal includes separate student and staff logins, attendance management, marks, semester-wise grade cards,
                medical leave requests, electives, birthday calendar dialog alerts, instant notifications, auxiliary exam requests,
                study material uploads and downloads, semester fee tracking, bulk spreadsheet imports, department-aware profiles,
                and personal detail management.
            </p>
            <div class="hero-actions">
                <a class="btn secondary" href="{{ route('login.form', 'student') }}">Enter Student Portal</a>
                <a class="btn" href="{{ route('login.form', 'staff') }}">Enter Staff Portal</a>
                <a class="btn secondary" href="{{ route('login.form', 'admin') }}">Enter Admin Panel</a>
            </div>
            <div class="pillars">
                <div class="pillar">
                    <strong>Student demo</strong>
                    <p>Email: <code>student@example.com</code><br>Password: <code>password</code></p>
                </div>
                <div class="pillar">
                    <strong>Staff demo</strong>
                    <p>Email: <code>staff@example.com</code><br>Password: <code>password</code></p>
                </div>
                <div class="pillar">
                    <strong>Admin demo</strong>
                    <p>Email: <code>admin@example.com</code><br>Password: <code>password</code></p>
                </div>
                <div class="pillar">
                    <strong>Built for workflow</strong>
                    <p>Admins manage students and teachers. Staff handle academics. Students track their semester lifecycle.</p>
                </div>
            </div>
        </div>
    </section>
@endsection
