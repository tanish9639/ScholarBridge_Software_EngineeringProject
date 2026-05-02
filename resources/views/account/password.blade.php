@extends('layouts.app', ['title' => 'Thapar LMS · Change Password'])

@section('content')
    <section class="shell">
        <div class="toolbar">
            <div>
                <span class="badge">{{ ucfirst($user->role) }} Profile</span>
                <h1>Change password</h1>
                <p class="muted">Update your login password securely.</p>
            </div>
            <a class="btn small secondary" href="{{ route($user->role.'.profile.show') }}">Back to Personal Details</a>
        </div>

        <div class="panel surface-accent">
            <form method="POST" action="{{ route($user->role.'.profile.password.update') }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="current_password">Current password</label>
                    <input id="current_password" type="password" name="current_password" required>
                </div>
                <div class="field">
                    <label for="password">New password</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                </div>
                <div class="field">
                    <button type="submit">Update password</button>
                </div>
            </form>
        </div>
    </section>
@endsection
