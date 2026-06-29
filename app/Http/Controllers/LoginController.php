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
use Illuminate\Support\Facades\Log;

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
        ], [
            'username.required' => 'Username dan password harus diisi.',
            'password.required' => 'Password harus diisi.',
        ]);

        $localUser = $this->attemptLocalLogin($validated['username'], $validated['password']);
        if ($localUser) {
            Auth::login($localUser, $request->boolean('ingat'));
            $request->session()->regenerate();
            $request->session()->forget('cis');

            return $this->redirectByRole($localUser->role);
        }

        DB::beginTransaction();

        try {
            $cisLogin = $kampusApi->loginWithCredentials(
                $validated['username'],
                $validated['password']
            );

            $token = $cisLogin['token'];

            if ($this->isKnownCounselorLogin($validated['username'])) {
                $konselorName = env('CIS_KONSELOR_NAME', 'Ibu Laura');
                $konselorEmail = env('CIS_KONSELOR_EMAIL') ?: ($validated['username'] . '@cis.local');

                $user = User::firstOrNew([
                    'username_cis' => $validated['username'],
                ]);

                $user->nama = $konselorName;
                $user->email = $konselorEmail;
                $user->role = 'konselor';

                if (! $user->exists || empty($user->password)) {
                    $user->password = bcrypt(str()->random(32));
                }

                $user->save();

                $konselor = Konselor::query()->orderBy('id')->first();

                if (! $konselor) {
                    $konselor = new Konselor();
                }

                $konselor->user_id = $user->id;

                if (empty($konselor->spesialisasi)) {
                    $konselor->spesialisasi = 'Psikolog / Konselor';
                }

                $konselor->save();

                DB::commit();

                Auth::login($user, $request->boolean('ingat'));
                $request->session()->regenerate();
                $request->session()->put('cis', [
                    'access_token' => $token,
                    'username' => $validated['username'],
                    'logged_in_at' => now()->toIso8601String(),
                ]);

                return redirect()->route('admin.dashboard');
            }

            try {
                $mahasiswaResult = $kampusApi->getMahasiswaByUsername($validated['username'], $token);
                $mahasiswaData = $mahasiswaResult['data']['mahasiswa'][0] ?? null;
            } catch (\Throwable $e) {
                $mahasiswaData = null;
            }

            if (! $mahasiswaData) {
                DB::rollBack();

                return back()->withErrors([
                    'username' => 'Akun CIS valid, tetapi tidak memiliki akses ke aplikasi Campus Care.',
                ])->withInput();
            }

            $nama = $mahasiswaData['nama'] ?? $validated['username'];
            $email = $mahasiswaData['email'] ?? ($validated['username'] . '@cis.local');

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

            $user = $this->syncResolvedRoleForUser($user, $validated['username'], (bool) $mahasiswaData);

            DB::commit();

            Auth::login($user, $request->boolean('ingat'));
            $request->session()->regenerate();
            $request->session()->put('cis', [
                'access_token' => $token,
                'username' => $validated['username'],
                'logged_in_at' => now()->toIso8601String(),
            ]);

            return $this->redirectByRole($user->role);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Login gagal', [
                'username' => $validated['username'] ?? null,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors([
                // Detail teknis CIS/cURL cukup dicatat di log, jangan ditampilkan ke user.
                'username' => 'Username atau password yang anda masukkan salah',
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->forget('cis');
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
        $isKnownCounselorUsername = $this->isKnownCounselorLogin($username);

        $resolvedRole = match (true) {
            $hasCounselorRelation => 'konselor',
            $isKnownCounselorUsername => 'konselor',
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

    private function isKnownCounselorLogin(string $username): bool
    {
        $loginIdentifier = strtolower(trim($username));

        $allowedIdentifiers = array_filter([
            strtolower(trim((string) env('CIS_KONSELOR_USERNAME', ''))),
            strtolower(trim((string) env('CIS_KONSELOR_EMAIL', ''))),
        ]);

        return in_array($loginIdentifier, $allowedIdentifiers, true);
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
