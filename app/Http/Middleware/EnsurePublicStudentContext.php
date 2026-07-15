<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePublicStudentContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'konselor') {
            return $next($request);
        }

        Auth::logout();

        $request->session()->forget('cis');
        $request->session()->forget('security_pin_verified_at');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            abort(401, 'Sesi konselor berakhir. Silakan login ulang.');
        }

        return redirect()->route('login')->withErrors([
            'username' => 'Sesi konselor sebelumnya telah diakhiri. Silakan login ulang untuk melanjutkan.',
        ]);
    }
}
