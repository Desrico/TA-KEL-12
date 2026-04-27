<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Konselor;
use App\Services\KampusApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'konselor'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegister()
    {
        return redirect()->route('login');
    }

    public function login(Request $request, KampusApiService $kampusApi)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // 1. Login ke CIS
            $token = $kampusApi->loginWithCredentials(
                $validated['username'],
                $validated['password']
            );

            // 2. Ambil data user dari CIS
            $result = $kampusApi->getStudentByNim($validated['username'], $token);

            $cisUser = $result['data']['mahasiswa'][0] ?? null;

            if (!$cisUser) {
                return back()->withErrors([
                    'username' => 'Data pengguna tidak ditemukan di CIS.',
                ])->withInput();
            }

            DB::beginTransaction();

            $email = $cisUser['email'] ?? null;
            $nim   = $cisUser['nim'] ?? $validated['username'];
            $nama  = $cisUser['nama'] ?? $validated['username'];

            if (!$email) {
                DB::rollBack();

                return back()->withErrors([
                    'username' => 'Email pengguna dari CIS tidak ditemukan.',
                ])->withInput();
            }

            // 3. Cari user lokal berdasarkan email
            $user = User::where('email', $email)->first();

            if (!$user) {
                // kalau belum ada, buat sebagai mahasiswa default
                $user = User::create([
                    'nama'     => $nama,
                    'email'    => $email,
                    'password' => bcrypt(Str::random(16)),
                    'role'     => 'mahasiswa',
                ]);
            } else {
                // update nama jika perlu
                $user->update([
                    'nama' => $nama,
                ]);
            }

            // 4. Jika role mahasiswa, sinkronkan tabel mahasiswa
            if ($user->role === 'mahasiswa') {
                Mahasiswa::updateOrCreate(
                    ['nim' => $nim],
                    [
                        'user_id'  => $user->id,
                        'jurusan'  => (string) ($cisUser['prodi_id'] ?? '-'),
                        'angkatan' => $this->extractAngkatan($nim),
                    ]
                );
            }

            // 5. Jika role konselor, pastikan data konselor ada
            if ($user->role === 'konselor') {
                $konselor = Konselor::where('user_id', $user->id)->first();

                if (!$konselor) {
                    DB::rollBack();

                    return back()->withErrors([
                        'username' => 'Akun CIS berhasil login, tetapi akun ini belum terdaftar sebagai konselor di sistem.',
                    ])->withInput();
                }
            }

            DB::commit();

            // 6. Login ke Laravel
            Auth::login($user, $request->boolean('ingat'));
            $request->session()->regenerate();

            // 7. Redirect sesuai role
            if ($user->role === 'konselor') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'username' => 'Login CIS gagal. Username/NIM atau password salah.',
            ])->withInput();
        }
    }

    private function extractAngkatan(string $nim): ?string
    {
        if (strlen($nim) >= 2) {
            return '20' . substr($nim, 0, 2);
        }

        return null;
    }

    public function register(Request $request)
    {
        return back()->withErrors([
            'register' => 'Registrasi manual dinonaktifkan. Silakan login menggunakan akun CIS.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}