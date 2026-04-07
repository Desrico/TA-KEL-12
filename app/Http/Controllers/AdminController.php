<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Halaman login admin
    public function showLogin()
    {
        // Kalau sudah login sebagai konselor, langsung ke dashboard
        if (Auth::check() && Auth::user()->role === 'konselor') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    // Proses login admin
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Cek apakah role-nya konselor
            if (Auth::user()->role !== 'konselor') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Anda tidak memiliki akses sebagai admin.',
                ])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    // Halaman dashboard admin
    public function dashboard()
    {
        // Cek role konselor
        if (!Auth::check() || Auth::user()->role !== 'konselor') {
            abort(403, 'Akses ditolak.');
        }

        return view('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}