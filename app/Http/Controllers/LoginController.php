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

            return $this->redirectAfterLogin($user);
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
            $request->session()->forget('security_pin_verified_at');

            return $this->redirectAfterLogin($localUser);
        }

        DB::beginTransaction();

        try {
            $cisLogin = $kampusApi->loginWithCredentials(
                $validated['username'],
                $validated['password']
            );

            $token = $cisLogin['token'];
            $counselorIdentity = $this->resolveCisCounselorIdentity(
                $validated['username'],
                $cisLogin['raw'] ?? [],
                $kampusApi,
                $token
            );

            if ($counselorIdentity) {
                $konselorName = $counselorIdentity['nama'] ?: $validated['username'];
                $konselorEmail = $counselorIdentity['email'] ?: ($validated['username'] . '@cis.local');

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

                // Jangan mengambil konselor pertama karena itu dapat memindahkan
                // relasi milik akun konselor lain ke akun yang sedang login.
                $konselor = Konselor::firstOrNew(['user_id' => $user->id]);

                if (empty($konselor->spesialisasi)) {
                    $konselor->spesialisasi = $counselorIdentity['jabatan']
                        ?: $this->adminSpecializationFallback();
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
            $request->session()->forget('security_pin_verified_at');
            $request->session()->put('cis', [
                'access_token' => $token,
                'username' => $validated['username'],
                'logged_in_at' => now()->toIso8601String(),
            ]);

            return $this->redirectAfterLogin($user);
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
        $request->session()->forget('security_pin_verified_at');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showSecurityPin(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'mahasiswa') {
            return $this->redirectByRole($user->role);
        }

        if ($user->hasSecurityPin() && $request->session()->has('security_pin_verified_at')) {
            return redirect()->route('dashboard');
        }

        return view('auth.security-pin', [
            'mode' => $user->hasSecurityPin() ? 'verify' : 'setup',
            'lockedUntil' => $user->security_pin_locked_until,
        ]);
    }

    public function showSecurityPinReset(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'mahasiswa') {
            return $this->redirectByRole($user->role);
        }

        if (! $user->hasSecurityPin()) {
            return redirect()->route('security-pin.show');
        }

        return view('auth.security-pin', [
            'mode' => 'reset',
            'lockedUntil' => $user->security_pin_locked_until,
        ]);
    }

    public function submitSecurityPin(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'mahasiswa') {
            return $this->redirectByRole($user->role);
        }

        if (! $user->hasSecurityPin()) {
            $validated = $request->validate([
                'pin' => ['required', 'digits:6', 'confirmed'],
            ], [
                'pin.required' => 'PIN keamanan wajib diisi.',
                'pin.digits' => 'PIN keamanan harus terdiri dari tepat 6 digit angka.',
                'pin.confirmed' => 'Konfirmasi PIN tidak sama.',
            ]);

            $user->forceFill([
                'security_pin_hash' => Hash::make($validated['pin']),
                'security_pin_set_at' => now(),
                'security_pin_failed_attempts' => 0,
                'security_pin_locked_until' => null,
            ])->save();

            $request->session()->put('security_pin_verified_at', now()->toIso8601String());

            return redirect()->route('dashboard')->with('success', 'PIN keamanan berhasil dibuat.');
        }

        if ($user->security_pin_locked_until && $user->security_pin_locked_until->isFuture()) {
            return back()->withErrors([
                'pin' => 'Terlalu banyak percobaan salah. Coba lagi pada ' . $user->security_pin_locked_until->format('H:i') . '.',
            ]);
        }

        $validated = $request->validate([
            'pin' => ['required', 'digits:6'],
        ], [
            'pin.required' => 'PIN keamanan wajib diisi.',
            'pin.digits' => 'PIN keamanan harus terdiri dari tepat 6 digit angka.',
        ]);

        if (! Hash::check($validated['pin'], $user->security_pin_hash)) {
            $failedAttempts = min(((int) $user->security_pin_failed_attempts) + 1, 5);
            $lockedUntil = $failedAttempts >= 5 ? now()->addMinutes(10) : null;

            $user->forceFill([
                'security_pin_failed_attempts' => $failedAttempts,
                'security_pin_locked_until' => $lockedUntil,
            ])->save();

            $message = $lockedUntil
                ? 'PIN salah 5 kali. Akun dikunci sementara selama 10 menit.'
                : 'PIN keamanan salah. Sisa percobaan: ' . (5 - $failedAttempts) . '.';

            return back()->withErrors(['pin' => $message]);
        }

        $user->forceFill([
            'security_pin_failed_attempts' => 0,
            'security_pin_locked_until' => null,
        ])->save();

        $request->session()->put('security_pin_verified_at', now()->toIso8601String());

        return redirect()->route('dashboard');
    }

    public function submitSecurityPinReset(Request $request, KampusApiService $kampusApi)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'mahasiswa') {
            return $this->redirectByRole($user->role);
        }

        if (! $user->hasSecurityPin()) {
            return redirect()->route('security-pin.show');
        }

        $validated = $request->validate([
            'password' => ['required', 'string'],
            'pin' => ['required', 'digits:6'],
        ], [
            'password.required' => 'Password CIS wajib diisi untuk reset PIN.',
            'pin.required' => 'PIN keamanan wajib diisi.',
            'pin.digits' => 'PIN keamanan harus terdiri dari tepat 6 digit angka.',
        ]);

        try {
            $kampusApi->loginWithCredentials($user->username_cis ?? '', $validated['password']);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'password' => 'Password CIS tidak valid. PIN belum diubah.',
            ])->withInput();
        }

        $user->forceFill([
            'security_pin_hash' => Hash::make($validated['pin']),
            'security_pin_set_at' => now(),
            'security_pin_failed_attempts' => 0,
            'security_pin_locked_until' => null,
        ])->save();

        $request->session()->put('security_pin_verified_at', now()->toIso8601String());

        $request->session()->forget('security_pin_verified_at');

        return redirect()->route('security-pin.reset.show', ['reset_success' => 1]);
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
        if (! $this->hasRequiredAdminAccessConfig()) {
            return false;
        }

        $loginIdentifier = strtolower(trim($username));

        $allowedIdentifiers = array_merge(
            $this->csvConfig('services.kampus_api.admin.usernames'),
            $this->csvConfig('services.kampus_api.admin.emails')
        );

        return in_array($loginIdentifier, $allowedIdentifiers, true);
    }

    private function resolveCisCounselorIdentity(
        string $username,
        array $cisLoginRaw,
        KampusApiService $kampusApi,
        string $token
    ): ?array {
        $loginIdentifiers = $this->collectLoginIdentifiers($username, $cisLoginRaw);
        $loginPegawaiIds = $this->collectLoginPegawaiIds($cisLoginRaw);
        $pegawaiIds = $this->csvConfig('services.kampus_api.admin.pegawai_ids', false);
        $jabatans = $this->adminAccessJabatans();

        // Akses admin CIS wajib dikunci oleh pegawai_id dan jabatan. Username,
        // email, dan nama hanya dipakai sebagai pengikat identitas/fallback saat
        // endpoint pejabat CIS gagal teknis setelah kredensial CIS valid.
        if (empty($pegawaiIds) || empty($jabatans)) {
            Log::warning('Konfigurasi admin CIS tidak lengkap. CIS_ADMIN_PEGAWAI_IDS wajib diisi dan salah satu dari CIS_ADMIN_JABATAN atau CIS_ADMIN_SPECIALIZATION harus valid.');

            return null;
        }

        if (! $this->isKnownCounselorLogin($username)) {
            Log::warning('Username login CIS tidak termasuk daftar admin CIS yang dikonfigurasi.', [
                'username' => $username,
            ]);

            return null;
        }

        return $this->resolvePejabatCounselorIdentity(
            $loginIdentifiers,
            $loginPegawaiIds,
            $pegawaiIds,
            $jabatans,
            $kampusApi,
            $token
        );
    }

    private function hasRequiredAdminAccessConfig(): bool
    {
        return ! empty($this->csvConfig('services.kampus_api.admin.pegawai_ids', false))
            && ! empty($this->adminAccessJabatans());
    }

    private function hasConfiguredIdentifierMatch(array $loginIdentifiers): bool
    {
        $allowedIdentifiers = array_merge(
            $this->csvConfig('services.kampus_api.admin.usernames'),
            $this->csvConfig('services.kampus_api.admin.emails')
        );

        if ((bool) array_intersect($loginIdentifiers, $allowedIdentifiers)) {
            return true;
        }

        foreach ($loginIdentifiers as $identifier) {
            if ($this->matchesConfiguredAdminName($identifier)) {
                return true;
            }
        }

        return false;
    }

    private function resolvePejabatCounselorIdentity(
        array $loginIdentifiers,
        array $loginPegawaiIds,
        array $pegawaiIds,
        array $jabatans,
        KampusApiService $kampusApi,
        string $token
    ): ?array {
        $receivedCisResponse = false;

        foreach ($this->buildPejabatFilterSets($pegawaiIds, $jabatans) as $filters) {
            try {
                $payload = $kampusApi->listPejabat($filters, $token);
                $receivedCisResponse = true;
            } catch (\Throwable $e) {
                Log::warning('Gagal mengambil data pejabat CIS untuk validasi admin.', [
                    'filters' => $filters,
                    'message' => $e->getMessage(),
                ]);

                continue;
            }

            foreach ($this->extractPejabatRows($payload) as $row) {
                if (! $this->pejabatMatchesConfiguredAccess($row, $pegawaiIds, $jabatans)) {
                    continue;
                }

                if (! $this->pejabatBelongsToAuthenticatedAdmin($row, $loginIdentifiers, $loginPegawaiIds, $pegawaiIds)) {
                    continue;
                }

                return [
                    'nama' => trim((string) ($row['nama'] ?? $row['name'] ?? ''))
                        ?: $this->firstAdminNameConfigValue(),
                    'email' => trim((string) ($row['email'] ?? ''))
                        ?: $this->firstCsvConfigValue('services.kampus_api.admin.emails'),
                    'pegawai_id' => trim((string) ($row['pegawai_id'] ?? $row['id_pegawai'] ?? '')),
                    'jabatan' => trim((string) ($row['jabatan'] ?? ''))
                        ?: $this->firstAdminAccessJabatanValue(),
                ];
            }
        }

        // Kredensial tetap sudah diverifikasi oleh do-auth CIS. Fallback ini
        // hanya berlaku saat endpoint list-pejabat gagal secara teknis, bukan
        // ketika CIS merespons sukses dengan pegawai/jabatan yang tidak cocok.
        if (! $receivedCisResponse && $this->hasConfiguredIdentifierMatch($loginIdentifiers)) {
            Log::warning('Validasi admin memakai fallback identifier karena endpoint pejabat CIS tidak tersedia.');

            return $this->configuredCounselorIdentity(
                $loginIdentifiers[0] ?? '',
                $pegawaiIds[0] ?? null,
                $jabatans[0] ?? null
            );
        }

        return null;
    }

    private function pejabatBelongsToAuthenticatedAdmin(
        array $row,
        array $loginIdentifiers,
        array $loginPegawaiIds,
        array $configuredPegawaiIds
    ): bool {
        $rowPegawaiId = trim((string) ($row['pegawai_id'] ?? $row['id_pegawai'] ?? ''));

        // Jika response login CIS membawa pegawai_id, itu menjadi bukti terkuat:
        // pegawai_id login, .env, dan row pejabat harus sama.
        if (! empty($loginPegawaiIds)) {
            return $rowPegawaiId !== ''
                && in_array($rowPegawaiId, $loginPegawaiIds, true)
                && in_array($rowPegawaiId, $configuredPegawaiIds, true);
        }

        $rowIdentifiers = $this->collectPejabatIdentifiers($row);

        if ((bool) array_intersect($loginIdentifiers, $rowIdentifiers)) {
            return true;
        }

        // Sebagian response login CIS hanya berisi token, dan response pejabat
        // kadang tidak berisi username/email. Dalam kondisi itu row pejabat
        // tetap harus cocok dengan identitas admin yang dikonfigurasi. Nama
        // dibandingkan lebih longgar agar beda titik/gelar tidak memblokir
        // pegawai_id + jabatan + username yang sudah benar.
        return $this->hasConfiguredIdentifierMatch($loginIdentifiers)
            && $this->rowMatchesConfiguredAdminIdentity($row);
    }

    private function configuredCounselorIdentity(
        string $username,
        ?string $pegawaiId = null,
        ?string $jabatan = null
    ): array {
        return [
            'nama' => $this->firstAdminNameConfigValue() ?: $username,
            'email' => $this->firstCsvConfigValue('services.kampus_api.admin.emails'),
            'pegawai_id' => $pegawaiId,
            'jabatan' => $jabatan
                ?: $this->adminSpecializationFallback(),
        ];
    }

    private function pejabatMatchesConfiguredAccess(array $row, array $pegawaiIds, array $jabatans): bool
    {
        $rowPegawaiId = trim((string) ($row['pegawai_id'] ?? $row['id_pegawai'] ?? ''));
        $rowJabatan = $this->normalizeIdentifier((string) ($row['jabatan'] ?? ''));
        $normalizedJabatans = array_map(
            fn (string $jabatan) => $this->normalizeIdentifier($jabatan),
            $jabatans
        );

        return (empty($pegawaiIds) || in_array($rowPegawaiId, $pegawaiIds, true))
            && (empty($normalizedJabatans) || in_array($rowJabatan, $normalizedJabatans, true));
    }

    private function buildPejabatFilterSets(array $pegawaiIds, array $jabatans): array
    {
        if (! empty($pegawaiIds) && ! empty($jabatans)) {
            $filters = [];

            foreach ($pegawaiIds as $pegawaiId) {
                foreach ($jabatans as $jabatan) {
                    $filters[] = [
                        'pegawai_id' => $pegawaiId,
                        'jabatan' => $jabatan,
                    ];
                }
            }

            return $filters;
        }

        if (! empty($pegawaiIds)) {
            return array_map(fn (string $pegawaiId) => ['pegawai_id' => $pegawaiId], $pegawaiIds);
        }

        return array_map(fn (string $jabatan) => ['jabatan' => $jabatan], $jabatans);
    }

    private function collectLoginIdentifiers(string $username, array $payload): array
    {
        $identifiers = [$this->normalizeIdentifier($username)];
        $candidateKeys = [
            'username',
            'user_name',
            'userid',
            'user_id',
            'uid',
            'email',
            'mail',
            'nama',
            'name',
            'full_name',
            'pegawai_id',
            'id_pegawai',
        ];

        $walk = function (array $items) use (&$walk, &$identifiers, $candidateKeys): void {
            foreach ($items as $key => $value) {
                $normalizedKey = strtolower((string) $key);

                if (is_array($value)) {
                    $walk($value);
                    continue;
                }

                if (in_array($normalizedKey, $candidateKeys, true) && is_scalar($value)) {
                    $identifiers[] = $this->normalizeIdentifier((string) $value);
                }
            }
        };

        $walk($payload);

        return array_values(array_unique(array_filter($identifiers)));
    }

    private function collectLoginPegawaiIds(array $payload): array
    {
        $pegawaiIds = [];
        $candidateKeys = [
            'pegawaiid',
            'idpegawai',
            'employee_id',
            'employeeid',
        ];

        $walk = function (array $items) use (&$walk, &$pegawaiIds, $candidateKeys): void {
            foreach ($items as $key => $value) {
                $normalizedKey = strtolower(str_replace(['-', '_', ' '], '', (string) $key));

                if (is_array($value)) {
                    $walk($value);
                    continue;
                }

                if (in_array($normalizedKey, $candidateKeys, true) && is_scalar($value)) {
                    $pegawaiIds[] = trim((string) $value);
                }
            }
        };

        $walk($payload);

        return array_values(array_unique(array_filter($pegawaiIds)));
    }

    private function collectPejabatIdentifiers(array $row): array
    {
        return array_values(array_unique(array_filter([
            $this->normalizeIdentifier((string) ($row['pegawai_id'] ?? '')),
            $this->normalizeIdentifier((string) ($row['id_pegawai'] ?? '')),
            $this->normalizeIdentifier((string) ($row['username'] ?? '')),
            $this->normalizeIdentifier((string) ($row['email'] ?? '')),
            $this->normalizeIdentifier((string) ($row['nama'] ?? '')),
            $this->normalizeIdentifier((string) ($row['name'] ?? '')),
        ])));
    }

    private function rowMatchesConfiguredAdminIdentity(array $row): bool
    {
        $rowUsernames = array_values(array_unique(array_filter([
            $this->normalizeIdentifier((string) ($row['username'] ?? '')),
            $this->normalizeIdentifier((string) ($row['user_name'] ?? '')),
            $this->normalizeIdentifier((string) ($row['userid'] ?? '')),
            $this->normalizeIdentifier((string) ($row['user_id'] ?? '')),
        ])));
        $rowEmails = array_values(array_unique(array_filter([
            $this->normalizeIdentifier((string) ($row['email'] ?? '')),
            $this->normalizeIdentifier((string) ($row['mail'] ?? '')),
        ])));
        $rowNames = array_values(array_unique(array_filter([
            (string) ($row['nama'] ?? ''),
            (string) ($row['name'] ?? ''),
            (string) ($row['full_name'] ?? ''),
        ])));

        if ((bool) array_intersect($rowUsernames, $this->csvConfig('services.kampus_api.admin.usernames'))) {
            return true;
        }

        if ((bool) array_intersect($rowEmails, $this->csvConfig('services.kampus_api.admin.emails'))) {
            return true;
        }

        foreach ($rowNames as $rowName) {
            if ($this->matchesConfiguredAdminName($rowName)) {
                return true;
            }
        }

        return false;
    }

    private function matchesConfiguredAdminName(string $name): bool
    {
        $normalizedName = $this->normalizeLooseName($name);

        if ($normalizedName === '') {
            return false;
        }

        foreach ($this->adminNameConfigValues() as $configuredName) {
            $normalizedConfiguredName = $this->normalizeLooseName($configuredName);

            if ($normalizedConfiguredName === '') {
                continue;
            }

            if ($normalizedName === $normalizedConfiguredName) {
                return true;
            }

            if (str_contains($normalizedName, $normalizedConfiguredName)
                || str_contains($normalizedConfiguredName, $normalizedName)) {
                return true;
            }
        }

        return false;
    }

    private function adminNameConfigValues(): array
    {
        $value = trim((string) config('services.kampus_api.admin.names', ''));

        if ($value === '') {
            return [];
        }

        // Nama admin sering berisi koma untuk gelar, misalnya
        // "Malino Win Krisnando Sihotang, S.Tr.Kom". Karena itu koma tidak
        // dipakai sebagai delimiter nama. Jika perlu beberapa nama, pakai titik
        // koma agar tidak bentrok dengan format gelar.
        return collect(explode(';', $value))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function firstAdminNameConfigValue(): ?string
    {
        $values = $this->adminNameConfigValues();

        return $values[0] ?? null;
    }

    private function extractPejabatRows(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        if ($this->looksLikePejabatRow($payload)) {
            return [$payload];
        }

        $rows = [];

        foreach ($payload as $value) {
            if (is_array($value)) {
                $rows = array_merge($rows, $this->extractPejabatRows($value));
            }
        }

        return $rows;
    }

    private function looksLikePejabatRow(array $row): bool
    {
        $keys = array_map(fn ($key) => strtolower((string) $key), array_keys($row));

        return in_array('pegawai_id', $keys, true)
            || in_array('id_pegawai', $keys, true)
            || (in_array('nama', $keys, true) && in_array('jabatan', $keys, true));
    }

    private function adminAccessJabatans(): array
    {
        $jabatans = $this->csvConfig('services.kampus_api.admin.jabatan', false);
        $specializations = $this->csvConfig('services.kampus_api.admin.specialization', false);

        if (! empty($jabatans) && ! empty($specializations)) {
            $normalizedJabatans = $this->normalizeConfigList($jabatans);
            $normalizedSpecializations = $this->normalizeConfigList($specializations);

            if ($normalizedJabatans !== $normalizedSpecializations) {
                Log::warning('Konfigurasi admin CIS tidak konsisten. CIS_ADMIN_JABATAN dan CIS_ADMIN_SPECIALIZATION harus sama jika keduanya diisi.');

                return [];
            }

            return $jabatans;
        }

        return ! empty($jabatans) ? $jabatans : $specializations;
    }

    private function firstAdminAccessJabatanValue(): ?string
    {
        $values = $this->adminAccessJabatans();

        return $values[0] ?? null;
    }

    private function adminSpecializationFallback(): string
    {
        return $this->firstCsvConfigValue('services.kampus_api.admin.specialization')
            ?: 'Staf Kemahasiswaan';
    }

    private function normalizeConfigList(array $values): array
    {
        $normalized = array_map(
            fn (string $value) => $this->normalizeIdentifier($value),
            $values
        );

        sort($normalized);

        return $normalized;
    }

    private function csvConfig(string $key, bool $normalize = true): array
    {
        $value = (string) config($key, '');

        if (trim($value) === '') {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn (string $item) => $normalize ? $this->normalizeIdentifier($item) : trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function firstCsvConfigValue(string $key): ?string
    {
        $values = $this->csvConfig($key, false);

        return $values[0] ?? null;
    }

    private function normalizeIdentifier(string $value): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($value)) ?? '');
    }

    private function normalizeLooseName(string $value): string
    {
        $normalized = strtolower(trim($value));
        $normalized = preg_replace('/[^\pL\pN]+/u', ' ', $normalized) ?? '';

        return preg_replace('/\s+/', ' ', trim($normalized)) ?? '';
    }

    private function redirectAfterLogin(User $user)
    {
        if ($user->role === 'mahasiswa') {
            return redirect()->route('security-pin.show');
        }

        return $this->redirectByRole($user->role);
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
