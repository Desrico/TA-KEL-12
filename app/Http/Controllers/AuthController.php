<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mahasiswa;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->has('ingat'))) {
        $request->session()->regenerate();

        // Redirect berdasarkan role
        if (Auth::user()->role === 'konselor') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended('beranda');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->withInput();
}

    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'nim'      => 'required|string|max:20|unique:mahasiswa,nim',
            'jurusan'  => 'required|string|max:100',
            'angkatan' => 'required|digits:4',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'mahasiswa',
        ]);

        Mahasiswa::create([
            'user_id'  => $user->id,
            'nim'      => $request->nim,
            'jurusan'  => $request->jurusan,
            'angkatan' => $request->angkatan,
        ]);

        Auth::login($user);

        return redirect('beranda');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 