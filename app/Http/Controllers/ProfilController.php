<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKonseling;
use App\Models\Konselor;
use App\Models\Notifikasi;
use App\Services\KampusApiService;
use Illuminate\Support\Facades\Log;

class ProfilController extends Controller
{
    public function index(KampusApiService $kampusApi)
    {
        $user = Auth::user()->load(['mahasiswa', 'konselor', 'profil']);

        if ($user->role === 'konselor') {
            $konselor = $user->konselor ?: Konselor::where('user_id', $user->id)->first();
            $counselorProfile = $this->buildCounselorProfile($user, $konselor, $kampusApi);

            return view('admin.profil', compact('user', 'konselor', 'counselorProfile'));
        }

        $mahasiswa = $user->mahasiswa;

        $totalKonseling = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
            ->count();

        $sesiBerlangsung = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
            ->where('status', 'disetujui')
            ->count();

        return view('Pages.profil', compact(
            'user',
            'mahasiswa',
            'totalKonseling',
            'sesiBerlangsung'
        ));
    }

    private function buildCounselorProfile($user, ?Konselor $konselor, KampusApiService $kampusApi): array
    {
        $configured = $this->configuredAdminIdentityFor($user);
        $cisRow = $this->fetchCisPejabatRow($kampusApi, $configured);
        $profil = $user->profil;

        $nama = $this->firstFilled(
            $cisRow['nama'] ?? null,
            $cisRow['name'] ?? null,
            $configured['name'] ?? null,
            $user->nama ?? null,
            'Konselor'
        );

        $username = $this->firstFilled(
            session('cis.username'),
            $cisRow['username'] ?? null,
            $cisRow['user_name'] ?? null,
            $cisRow['userid'] ?? null,
            $cisRow['user_id'] ?? null,
            $configured['username'] ?? null,
            $user->username_cis ?? null
        );

        $email = $this->firstFilled(
            $cisRow['email'] ?? null,
            $cisRow['mail'] ?? null,
            $configured['email'] ?? null,
            $user->email ?? null
        );

        $jabatan = $this->firstFilled(
            $cisRow['jabatan'] ?? null,
            $configured['jabatan'] ?? null,
            $konselor->jabatan ?? null
        );

        $nomorTelepon = $this->firstFilled(
            $cisRow['nomor_telepon'] ?? null,
            $cisRow['no_telepon'] ?? null,
            $cisRow['phone'] ?? null,
            $profil->nomor_telepon ?? null
        );

        return [
            'nama' => $nama,
            'username_cis' => $username,
            'email' => $email,
            'jabatan' => $jabatan,
            'nomor_telepon' => $nomorTelepon,
            'foto' => $profil->foto ?? null,
        ];
    }

    private function fetchCisPejabatRow(KampusApiService $kampusApi, array $configured): ?array
    {
        if (! filled(config('services.kampus_api.base_url'))) {
            return null;
        }

        $token = session('cis.access_token');
        $filters = $this->buildPejabatFilters($configured);

        if (empty($filters)) {
            return null;
        }

        foreach ($filters as $filter) {
            try {
                $payload = $kampusApi->listPejabat($filter, $token, 10);
            } catch (\Throwable $e) {
                Log::warning('Gagal mengambil profil pejabat CIS.', [
                    'filters' => $filter,
                    'message' => $e->getMessage(),
                ]);

                continue;
            }

            $rows = $this->extractPejabatRows($payload);
            $matched = $this->matchConfiguredPejabatRow($rows, $configured);

            if ($matched) {
                return $matched;
            }

            if (count($rows) === 1) {
                return $rows[0];
            }
        }

        return null;
    }

    private function buildPejabatFilters(array $configured): array
    {
        $pegawaiId = $configured['pegawai_id'] ?? null;
        $jabatan = $configured['jabatan'] ?? null;

        if ($pegawaiId && $jabatan) {
            return [[
                'pegawai_id' => $pegawaiId,
                'jabatan' => $jabatan,
            ]];
        }

        if ($pegawaiId) {
            return [['pegawai_id' => $pegawaiId]];
        }

        if ($jabatan) {
            return [['jabatan' => $jabatan]];
        }

        return [];
    }

    private function matchConfiguredPejabatRow(array $rows, array $configured): ?array
    {
        $pegawaiId = trim((string) ($configured['pegawai_id'] ?? ''));
        $name = $this->normalizeLooseName((string) ($configured['name'] ?? ''));

        foreach ($rows as $row) {
            $rowPegawaiId = trim((string) ($row['pegawai_id'] ?? $row['id_pegawai'] ?? ''));

            if ($pegawaiId !== '' && $rowPegawaiId === $pegawaiId) {
                return $row;
            }

            $rowName = $this->normalizeLooseName((string) ($row['nama'] ?? $row['name'] ?? ''));

            if ($name !== '' && $rowName !== '' && ($rowName === $name || str_contains($rowName, $name) || str_contains($name, $rowName))) {
                return $row;
            }
        }

        return null;
    }

    private function configuredAdminIdentityFor($user): array
    {
        $usernames = $this->csvConfig('services.kampus_api.admin.usernames');
        $emails = $this->csvConfig('services.kampus_api.admin.emails');
        $names = $this->adminNameConfigValues();
        $pegawaiIds = $this->csvConfig('services.kampus_api.admin.pegawai_ids');
        $jabatans = $this->adminJabatanConfigValues();
        $loginIdentifiers = array_filter([
            $this->normalizeIdentifier((string) session('cis.username')),
            $this->normalizeIdentifier((string) ($user->username_cis ?? '')),
            $this->normalizeIdentifier((string) ($user->email ?? '')),
            $this->normalizeLooseName((string) ($user->nama ?? '')),
        ]);

        $index = null;

        foreach ($usernames as $i => $username) {
            if (in_array($this->normalizeIdentifier($username), $loginIdentifiers, true)) {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            foreach ($emails as $i => $email) {
                if (in_array($this->normalizeIdentifier($email), $loginIdentifiers, true)) {
                    $index = $i;
                    break;
                }
            }
        }

        if ($index === null) {
            foreach ($names as $i => $name) {
                if (in_array($this->normalizeLooseName($name), $loginIdentifiers, true)) {
                    $index = $i;
                    break;
                }
            }
        }

        $index ??= 0;

        return [
            'username' => $usernames[$index] ?? $usernames[0] ?? null,
            'email' => $emails[$index] ?? $emails[0] ?? null,
            'name' => $names[$index] ?? $names[0] ?? null,
            'pegawai_id' => $pegawaiIds[$index] ?? $pegawaiIds[0] ?? null,
            'jabatan' => $jabatans[$index] ?? $jabatans[0] ?? null,
        ];
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

    private function csvConfig(string $key): array
    {
        $value = trim((string) config($key, ''));

        if ($value === '') {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function adminNameConfigValues(): array
    {
        $value = trim((string) config('services.kampus_api.admin.names', ''));

        if ($value === '') {
            return [];
        }

        return collect(explode(';', $value))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function adminJabatanConfigValues(): array
    {
        $jabatans = $this->csvConfig('services.kampus_api.admin.jabatan');

        if (! empty($jabatans)) {
            return $jabatans;
        }

        return $this->csvConfig('services.kampus_api.admin.specialization');
    }

    private function firstFilled(...$values): string
    {
        foreach ($values as $value) {
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        return '';
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

    public function update(Request $request)
    {
        return back()->with('info', 'Profil menggunakan data akun CIS, sehingga tidak dapat diubah dari aplikasi ini.');
    }

    public function toggleAnonim(Request $request)
    {
        $request->validate([
            'anonim' => 'required|boolean',
        ]);

        $user = auth()->user();

        if (! $user || $user->role !== 'mahasiswa') {
            abort(403);
        }

        $user->update([
            'is_anonim' => $request->boolean('anonim'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mode anonim berhasil diperbarui.',
            'anonim' => (bool) $user->fresh()->is_anonim,
        ]);
    }

    public function riwayat()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $riwayat = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])
            ->where('mahasiswa_id', optional($mahasiswa)->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();

        return view('Pages.riwayat', compact('riwayat'));
    }

    public function markNotificationsAsRead()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('status', 'belum')
            ->update(['status' => 'dibaca']);

        return response()->json([
            'success' => true,
        ]);
    }
}
