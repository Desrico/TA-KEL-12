<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KampusApiService
{
    protected string $baseUrl;
    protected int $timeout;
    protected ?string $serviceUsername;
    protected ?string $servicePassword;
    protected ?string $staticToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.kampus_api.base_url'), '/');
        $this->timeout = (int) config('services.kampus_api.timeout', 20);
        $this->serviceUsername = filled(config('services.kampus_api.username'))
            ? (string) config('services.kampus_api.username')
            : null;
        $this->servicePassword = filled(config('services.kampus_api.password'))
            ? (string) config('services.kampus_api.password')
            : null;
        $this->staticToken = filled(config('services.kampus_api.static_token'))
            ? trim((string) config('services.kampus_api.static_token'))
            : null;
    }

    public function isConfigured(): bool
    {
        $username = Str::lower(trim((string) $this->serviceUsername));
        $password = Str::lower(trim((string) $this->servicePassword));

        return filled($this->baseUrl)
            && filled($this->serviceUsername)
            && filled($this->servicePassword)
            && ! in_array($username, ['isi_username_api', 'your_username', 'changeme'], true)
            && ! in_array($password, ['isi_password_api', 'your_password', 'changeme'], true);
    }

    public function hasStaticToken(): bool
    {
        return filled($this->baseUrl) && filled($this->staticToken);
    }

    public function getStaticToken(): ?string
    {
        return $this->hasStaticToken() ? $this->staticToken : null;
    }

    public function loginWithCredentials(string $username, string $password): array
    {
        $response = Http::asMultipart()
            ->timeout($this->timeout)
            ->post($this->baseUrl . '/jwt-api/do-auth', [
                [
                    'name' => 'username',
                    'contents' => $username,
                ],
                [
                    'name' => 'password',
                    'contents' => $password,
                ],
            ]);

        $response->throw();

        $json = $response->json();

        if (($json['result'] ?? true) === false) {
            $errorMessage = trim((string) ($json['error'] ?? 'Login CIS gagal.'));
            throw new \RuntimeException($errorMessage !== '' ? $errorMessage : 'Login CIS gagal.');
        }

        $token = $json['token']
            ?? $json['access_token']
            ?? $json['data']['token']
            ?? null;

        if (!$token) {
            throw new \RuntimeException('Token login CIS tidak ditemukan pada response.');
        }

        return [
            'token' => $token,
            'raw' => $json,
        ];
    }

    public function getMahasiswaByUsername(string $username, string $token): array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout($this->timeout)
            ->get($this->baseUrl . '/library-api/mahasiswa', [
                'nama' => '',
                'nim' => '',
                'angkatan' => '',
                'userid' => '',
                'username' => $username,
                'prodi' => '',
                'status' => 'Aktif',
                'limit' => 1,
            ]);

        $response->throw();

        return $response->json();
    }

    // Service account backend dipakai untuk fitur admin seperti autocomplete NIM tanpa meminjam sesi login mahasiswa.
    public function getServiceToken(bool $forceRefresh = false): string
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Konfigurasi service account CIS belum lengkap.');
        }

        $cacheKey = 'kampus_api.service_token';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(15), function (): string {
            return $this->loginWithCredentials(
                (string) $this->serviceUsername,
                (string) $this->servicePassword
            )['token'];
        });
    }

    public function getMahasiswa(array $filters = [], ?string $token = null, ?int $timeout = null): array
    {
        $payload = array_merge([
            'nama' => '',
            'nim' => '',
            'angkatan' => '',
            'userid' => '',
            'username' => '',
            'prodi' => '',
            'status' => 'Aktif',
            'limit' => 10,
        ], $filters);

        return $this->performMahasiswaRequest($payload, $token, $timeout);
    }

    public function getStudentByNim(string $nim, ?string $token = null): array
    {
        return $this->getMahasiswa([
            'nim' => $nim,
            'limit' => 1,
        ], $token);
    }

    public function searchActiveMahasiswaByNim(
        string $keyword,
        int $limit = 12,
        ?string $token = null,
        ?int $timeout = null
    ): array
    {
        $response = $this->getMahasiswa([
            'nim' => $keyword,
            'status' => 'Aktif',
            'limit' => $limit,
        ], $token, $timeout);

        return $this->normalizeMahasiswaRows($response);
    }

    public function findActiveMahasiswaByExactNim(string $nim, ?string $token = null): ?array
    {
        $nim = trim($nim);

        if ($nim === '') {
            return null;
        }

        $rows = $this->searchActiveMahasiswaByNim($nim, 5, $token);

        foreach ($rows as $row) {
            if (trim((string) ($row['nim'] ?? '')) === $nim) {
                return $row;
            }
        }

        return $rows[0] ?? null;
    }

    private function performMahasiswaRequest(array $filters, ?string $token = null, ?int $timeout = null): array
    {
        $usingProvidedToken = filled($token);
        $token ??= $this->getStaticToken();
        $requestTimeout = $timeout && $timeout > 0 ? $timeout : $this->timeout;

        if (! $token && $this->isConfigured()) {
            $token = $this->getServiceToken();
        }

        $response = $this->sendMahasiswaRequest($filters, $requestTimeout, $token);

        // Jika token statis/sesi kedaluwarsa, backend mencoba service account bila tersedia.
        if ($response->status() === 401 && ! $usingProvidedToken && $this->isConfigured()) {
            $refreshedToken = $this->getServiceToken(true);
            $response = $this->sendMahasiswaRequest($filters, $requestTimeout, $refreshedToken);
        }

        // Beberapa deployment CIS membuka endpoint library mahasiswa tanpa bearer token.
        // Jika mode token gagal dan request ini bukan memakai token sesi user, coba fallback guest.
        if (in_array($response->status(), [401, 403], true) && ! $usingProvidedToken) {
            $guestResponse = $this->sendMahasiswaRequest($filters, $requestTimeout, null);

            if ($guestResponse->successful()) {
                return $guestResponse->json();
            }
        }

        $response->throw();

        return $response->json();
    }

    private function sendMahasiswaRequest(array $filters, int $timeout, ?string $token = null)
    {
        $request = Http::acceptJson()->timeout($timeout);

        if (filled($token)) {
            $request = $request->withToken($token);
        }

        return $request->get($this->baseUrl . '/library-api/mahasiswa', $filters);
    }

    private function normalizeMahasiswaRows(array $payload): array
    {
        return collect($this->extractCandidateRows($payload))
            ->map(function (array $row) {
                $nim = trim((string) (
                    $row['nim']
                    ?? $row['NIM']
                    ?? $row['student_id']
                    ?? ''
                ));

                if ($nim === '') {
                    return null;
                }

                $name = trim((string) (
                    $row['name']
                    ?? $row['nama']
                    ?? $row['full_name']
                    ?? $row['student_name']
                    ?? 'Mahasiswa'
                ));

                $status = trim((string) (
                    $row['status']
                    ?? $row['status_mahasiswa']
                    ?? $row['status_akademik']
                    ?? 'Aktif'
                ));

                return [
                    'nim' => $nim,
                    'name' => $name !== '' ? $name : 'Mahasiswa',
                    'status' => $status !== '' ? $status : 'Aktif',
                    'username' => trim((string) (
                        $row['username']
                        ?? $row['user_name']
                        ?? $row['userid']
                        ?? ''
                    )),
                    'email' => trim((string) ($row['email'] ?? '')),
                    'prodi' => trim((string) (
                        $row['prodi_name']
                        ?? $row['prodi']
                        ?? $row['prodi_id']
                        ?? ''
                    )),
                    'angkatan' => trim((string) ($row['angkatan'] ?? '')),
                ];
            })
            ->filter()
            ->unique('nim')
            ->values()
            ->all();
    }

    private function extractCandidateRows(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        if ($this->looksLikeStudentRow($payload)) {
            return [$payload];
        }

        $rows = [];

        foreach ($payload as $value) {
            if (! is_array($value)) {
                continue;
            }

            if ($this->looksLikeStudentRow($value)) {
                $rows[] = $value;
                continue;
            }

            if (array_is_list($value) || $this->containsStudentHints($value)) {
                $rows = array_merge($rows, $this->extractCandidateRows($value));
            }
        }

        return $rows;
    }

    private function looksLikeStudentRow(array $row): bool
    {
        $keys = array_map(fn($key) => Str::lower((string) $key), array_keys($row));

        return in_array('nim', $keys, true)
            || in_array('nama', $keys, true)
            || in_array('name', $keys, true);
    }

    private function containsStudentHints(array $row): bool
    {
        $keys = array_map(fn($key) => Str::lower((string) $key), array_keys($row));

        foreach (['data', 'items', 'result', 'results', 'mahasiswa', 'students', 'list'] as $candidate) {
            if (in_array($candidate, $keys, true)) {
                return true;
            }
        }

        return false;
    }
}
