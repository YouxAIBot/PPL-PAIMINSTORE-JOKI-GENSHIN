<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        if (! $user->isAdmin() || $user->status !== 'active') {
            auth()->logout();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Akun ini tidak punya akses panel admin.']);
        }

        return $next($request);
    }
}
