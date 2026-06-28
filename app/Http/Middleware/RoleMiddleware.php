<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {
            if ($request->expectsJson()) {
                abort(403, 'Akses ditolak.');
            }

            $previousUrl = url()->previous();
            $currentUrl = $request->fullUrl();

            // Jika user salah role, kembalikan ke halaman terakhir yang valid dan hindari loop ke URL yang sama.
            if ($previousUrl && $previousUrl !== $currentUrl && parse_url($previousUrl, PHP_URL_HOST) === $request->getHost()) {
                return redirect()
                    ->to($previousUrl)
                    ->with('error', 'Akses ditolak. Anda tidak memiliki izin membuka halaman tersebut.');
            }

            return redirect()
                ->to($this->fallbackUrlByRole(Auth::user()->role))
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin membuka halaman tersebut.');
        }

        return $next($request);
    }

    private function fallbackUrlByRole(?string $role): string
    {
        return match ($role) {
            'konselor' => route('admin.dashboard'),
            'mahasiswa' => route('dashboard'),
            default => route('beranda'),
        };
    }
}
