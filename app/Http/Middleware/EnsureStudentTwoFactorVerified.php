<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'mahasiswa') {
            return $next($request);
        }

        if ($request->routeIs('two-factor.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        if (! $user->hasTwoFactorAuthentication()) {
            return redirect()->route('two-factor.setup');
        }

        if (! $request->session()->has('two_factor_verified_at')) {
            if ($request->expectsJson()) {
                abort(423, 'Verifikasi Authenticator diperlukan.');
            }

            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
