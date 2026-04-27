<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Konselor;
use App\Services\KampusApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    // public function login(Request $request, KampusApiService $kampusApi)
    // {
    //     $validated = $request->validate([
    //         'username' => ['required', 'string'],
    //         'password' => ['required', 'string'],
    //     ]);

    //     try {
    //         // 1. Validasi login ke CIS
    //         $cisLogin = $kampusApi->loginWithCredentials(
    //             $validated['username'],
    //             $validated['password']
    //         );

    //         $token = $cisLogin['token'];

    //         DB::beginTransaction();

    //         // 2. Cari user lokal berdasarkan username_cis
    //         $user = User::where('username_cis', $validated['username'])->first();

    //         // 3. Kalau belum ada, coba cek apakah ini mahasiswa
    //         if (!$user) {
    //             try {
    //                 $mahasiswaResult = $kampusApi->getMahasiswaByUsername($validated['username'], $token);
    //                 $mahasiswaData = $mahasiswaResult['data']['mahasiswa'][0] ?? null;
    //             } catch (\Throwable $e) {
    //                 $mahasiswaData = null;
    //             }

    //             if ($mahasiswaData) {
    //                 $user = User::create([
    //                     'nama'         => $mahasiswaData['nama'] ?? $validated['username'],
    //                     'email'        => $mahasiswaData['email'] ?? ($validated['username'] . '@student.local'),
    //                     'username_cis' => $validated['username'],
    //                     'password'     => bcrypt(str()->random(16)),
    //                     'role'         => 'mahasiswa',
    //                 ]);

    //                 Mahasiswa::updateOrCreate(
    //                     ['nim' => $mahasiswaData['nim']],
    //                     [
    //                         'user_id'  => $user->id,
    //                         'jurusan'  => $mahasiswaData['prodi_name'] ?? (string) ($mahasiswaData['prodi_id'] ?? '-'),
    //                         'angkatan' => (string) ($mahasiswaData['angkatan'] ?? null),
    //                     ]
    //                 );
    //             } else {
    //                 DB::rollBack();

    //                 return back()->withErrors([
    //                     'username' => 'Akun CIS valid, tetapi belum terdaftar di sistem Campus Care.',
    //                 ])->withInput();
    //             }
    //         }

    //         // 4. Validasi jika user adalah konselor/admin
    //         if ($user->role === 'konselor') {
    //             $konselor = Konselor::where('user_id', $user->id)->first();

    //             if (!$konselor) {
    //                 DB::rollBack();

    //                 return back()->withErrors([
    //                     'username' => 'Akun ini login ke CIS berhasil, tetapi belum terdaftar sebagai konselor di sistem.',
    //                 ])->withInput();
    //             }
    //         }

    //         DB::commit();

    //         // 5. Login ke Laravel
    //         Auth::login($user, $request->boolean('ingat'));
    //         $request->session()->regenerate();

    //         return $this->redirectByRole($user->role);

    //     } catch (\Throwable $e) {
    //         DB::rollBack();

    //         return back()->withErrors([
    //             'username' => 'Login CIS gagal. Username atau password salah.',
    //         ])->withInput();
    //     }
    // }

    public function login(Request $request, KampusApiService $kampusApi)
{
    $validated = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    // 1. Fallback login lokal sementara
    $localCredentials = [
        'email' => $validated['username'],
        'password' => $validated['password'],
    ];

    if (Auth::attempt($localCredentials, $request->boolean('ingat'))) {
        $request->session()->regenerate();
        return $this->redirectByRole(Auth::user()->role);
    }

    try {
        // 2. Login via CIS
        $cisLogin = $kampusApi->loginWithCredentials(
            $validated['username'],
            $validated['password']
        );

        $token = $cisLogin['token'];

        DB::beginTransaction();

        // 3. Coba ambil data mahasiswa. Kalau bukan mahasiswa, tidak masalah.
        try {
            $mahasiswaResult = $kampusApi->getMahasiswaByUsername($validated['username'], $token);
            $mahasiswaData = $mahasiswaResult['data']['mahasiswa'][0] ?? null;
        } catch (\Throwable $e) {
            $mahasiswaData = null;
        }

        // 4. Tentukan role sementara berdasarkan username
        $adminUsernames = [
            'johannes',
            'tennov',
            'desy.silaban',
            'oka.simatupang',
            'albert',
            'alfriska.silalahi',
            'humasak',
            'istas.manalu',
            'eka',
            'mario',
            'yohanssen.pratama',
            'ike.fitri',
            'eka.dirgayussa',
            'ellyas.nainggolan',
            'christoper.sinaga',
            'arlinta.barus',
            'aldo',
            'chandra.simanjuntak',
        ];

        $role = in_array(strtolower($validated['username']), $adminUsernames)
            ? 'konselor'
            : 'mahasiswa';

        $nama = $mahasiswaData['nama'] ?? $validated['username'];
        $email = $mahasiswaData['email'] ?? ($validated['username'] . '@cis.local');

        // 5. Buat/update user lokal otomatis
        $user = User::updateOrCreate(
            ['username_cis' => $validated['username']],
            [
                'nama' => $nama,
                'email' => $email,
                'password' => bcrypt(str()->random(16)),
                'role' => $role,
            ]
        );

        // 6. Kalau mahasiswa, sinkronkan data mahasiswa
        if ($mahasiswaData) {
            Mahasiswa::updateOrCreate(
                ['nim' => $mahasiswaData['nim']],
                [
                    'user_id'  => $user->id,
                    'jurusan'  => $mahasiswaData['prodi_name'] ?? (string) ($mahasiswaData['prodi_id'] ?? '-'),
                    'angkatan' => (string) ($mahasiswaData['angkatan'] ?? null),
                ]
            );
        }

        DB::commit();

        Auth::login($user, $request->boolean('ingat'));
        $request->session()->regenerate();

        return $this->redirectByRole($user->role);

    } catch (\Throwable $e) {
        DB::rollBack();

        return back()->withErrors([
            'username' => 'Login gagal. Gunakan akun lokal admin atau akun CIS yang valid.',
        ])->withInput();
    }
}
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}

private function redirectByRole(?string $role)
{
    return match ($role) {
        'konselor'  => redirect()->route('admin.dashboard'),
        'mahasiswa' => redirect()->route('dashboard'),
        default     => redirect()->route('login')->withErrors([
            'username' => 'Role pengguna tidak dikenali.',
        ]),
    };
}
}