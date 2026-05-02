<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const PORTAL_ROLES = ['student', 'staff', 'admin'];

    public function landing()
    {
        return view('landing');
    }

    public function showLogin(string $role)
    {
        abort_unless(in_array($role, self::PORTAL_ROLES, true), 404);

        return view('auth.login', ['role' => $role]);
    }

    public function login(Request $request, string $role)
    {
        abort_unless(in_array($role, self::PORTAL_ROLES, true), 404);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        if (Auth::user()->role !== $role) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => sprintf('This account is not allowed in the %s portal.', $role),
            ]);
        }

        return redirect()->intended(route($role.'.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
