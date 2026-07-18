<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompleteCounselorSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'konselor' || ! $this->isIncompleteCounselorSession($request)) {
            return $next($request);
        }

        Auth::logout();

        $request->session()->forget('cis');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            abort(401, 'Sesi login konselor belum lengkap. Silakan login ulang.');
        }

        return redirect()->route('login')->withErrors([
            'username' => 'Sesi login konselor sebelumnya belum lengkap. Silakan login ulang.',
        ]);
    }

    private function isIncompleteCounselorSession(Request $request): bool
    {
        $user = $request->user();
        $sessionUsername = trim((string) $request->session()->get('cis.username', ''));
        $sessionToken = trim((string) $request->session()->get('cis.access_token', ''));
        $userIdentifier = trim((string) ($user?->username_cis ?: $user?->email ?: ''));

        return $sessionToken === ''
            || ($userIdentifier !== '' && $sessionUsername !== '' && strcasecmp($sessionUsername, $userIdentifier) !== 0);
    }
}
