<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check() && auth()->user()->isAdmin() && auth()->user()->status === 'active') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($credentials)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $user = auth()->user();

        if (! $user->isAdmin()) {
            auth()->logout();

            return back()->withErrors(['email' => 'Akun ini bukan admin.'])->onlyInput('email');
        }

        if ($user->status !== 'active') {
            auth()->logout();

            return back()->withErrors(['email' => 'Akun ini dibanned. Hubungi owner.']);
        }

        $request->session()->regenerate();

        ActivityLog::record($user, 'admin.login');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        ActivityLog::record(auth()->user(), 'admin.logout');
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
