<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pengguna yang diblokir admin kehilangan semua token dan ditolak di setiap permintaan API.
 */
class EnsureNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === UserStatus::Banned) {
            $user->tokens()->delete();

            return response()->json(['message' => 'Akun Anda diblokir. Hubungi admin.'], 401);
        }

        return $next($request);
    }
}
