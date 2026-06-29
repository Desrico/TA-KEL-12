<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentSecurityPinVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'mahasiswa') {
            return $next($request);
        }

        if ($request->routeIs('security-pin.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        if (! $user->hasSecurityPin() || ! $request->session()->has('security_pin_verified_at')) {
            if ($request->expectsJson()) {
                abort(423, 'PIN keamanan perlu diverifikasi.');
            }

            return redirect()->route('security-pin.show');
        }

        return $next($request);
    }
}
