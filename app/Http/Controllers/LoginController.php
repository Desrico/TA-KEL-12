<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Konselor;
use App\Services\KampusApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            // Menyelaraskan ulang role aktif agar redirect tidak memakai role lama yang tertinggal di database/session.
            $user = $this->syncResolvedRoleForUser(
                Auth::user(),
                Auth::user()->username_cis ?? Auth::user()->email ?? '',
            );

            return $this->redirectByRole($user->role);
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

        // Login lokal tetap didukung, tetapi role user diselaraskan ulang sebelum redirect.
        $localUser = $this->attemptLocalLogin($validated['username'], $validated['password']);
        if ($localUser) {
            Auth::login($localUser, $request->boolean('ingat'));
            $request->session()->regenerate();

            return $this->redirectByRole($localUser->role);
        }

        DB::beginTransaction();

        try {
            // Login utama memakai CIS agar data user dan role tidak bergantung pada session sebelumnya.
            $cisLogin = $kampusApi->loginWithCredentials(
                $validated['username'],
                $validated['password']
            );

            $token = $cisLogin['token'];

            try {
                $mahasiswaResult = $kampusApi->getMahasiswaByUsername($validated['username'], $token);
                $mahasiswaData = $mahasiswaResult['data']['mahasiswa'][0] ?? null;
            } catch (\Throwable $e) {
                $mahasiswaData = null;
            }

            $nama = $mahasiswaData['nama'] ?? $validated['username'];
            $email = $mahasiswaData['email'] ?? ($validated['username'] . '@cis.local');

            // Sinkronisasi akun CIS tidak mengubah password lokal yang masih dipakai admin.
            $user = User::firstOrNew(['username_cis' => $validated['username']]);
            $user->nama = $nama;
            $user->email = $email;
            $user->role = 'mahasiswa';

            if (! $user->exists || empty($user->password)) {
                $user->password = bcrypt(str()->random(16));
            }

            $user->save();

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

            // Role konselor/mahasiswa ditentukan ulang dari sumber yang lebih stabil daripada role tersimpan sebelumnya.
            $user = $this->syncResolvedRoleForUser($user, $validated['username'], (bool) $mahasiswaData);

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

    private function attemptLocalLogin(string $username, string $password): ?User
    {
        // Login lokal mencari akun berdasarkan username_cis atau email agar tidak bergantung pada email saja.
        $user = User::query()
            ->where('username_cis', $username)
            ->orWhere('email', $username)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return $this->syncResolvedRoleForUser($user, $username);
    }

    private function syncResolvedRoleForUser(User $user, string $username, bool $hasMahasiswaData = false): User
    {
        // Prioritas role: data konselor lokal > daftar username admin > data mahasiswa lokal/CIS.
        $hasCounselorRelation = Konselor::query()->where('user_id', $user->id)->exists();
        $hasMahasiswaRelation = Mahasiswa::query()->where('user_id', $user->id)->exists();
        $isKnownAdminUsername = in_array(strtolower($username), $this->adminUsernames(), true);

        $resolvedRole = match (true) {
            $hasCounselorRelation => 'konselor',
            $isKnownAdminUsername => 'konselor',
            $hasMahasiswaData, $hasMahasiswaRelation => 'mahasiswa',
            in_array($user->role, ['konselor', 'mahasiswa'], true) => $user->role,
            default => 'mahasiswa',
        };

        if ($user->role !== $resolvedRole) {
            // Menimpa role lama yang tidak sinkron agar redirect sesudah login selalu benar.
            $user->forceFill(['role' => $resolvedRole])->save();
        }

        return $user->refresh();
    }

    private function adminUsernames(): array
    {
        return [
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
