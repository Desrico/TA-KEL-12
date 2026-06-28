<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\AiLaporanSummary;
use App\Models\JadwalKonseling;
use App\Models\Laporan;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;
use App\Models\SesiKonseling;
use App\Services\GroqSummaryService;

class LaporanController extends Controller
{
    private function jadwalHasColumn(string $column): bool
    {
        static $cache = [];

        return $cache[$column] ??= Schema::hasColumn('jadwal_konseling', $column);
    }

    private function extractTopik(?string $catatan, ?string $fallback = null): string
    {
        if (!empty($fallback)) {
            return $fallback;
        }

        if (empty($catatan)) {
            return '-';
        }

        if (preg_match('/Topik:\s*([^|]+)/i', $catatan, $match)) {
            return trim($match[1]) ?: '-';
        }

        return '-';
    }

    private function formatRiwayatStatus(?string $status): array
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'selesai' => ['label' => 'Selesai', 'class' => 'status-selesai'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'status-ditolak'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'status-dibatalkan'],
            'disetujui', 'diterima' => ['label' => 'Diterima', 'class' => 'status-diterima'],
            'menunggu', 'menunggu konfirmasi' => ['label' => 'Menunggu Konfirmasi', 'class' => 'status-menunggu'],
            'berlangsung', 'sedang berlangsung' => ['label' => 'Sedang Berlangsung', 'class' => 'status-berlangsung'],
            'perlu_penjadwalan_ulang', 'perlu penjadwalan ulang' => ['label' => 'Perlu Penjadwalan Ulang', 'class' => 'status-reschedule'],
            'perlu_sesi_lanjutan', 'perlu sesi lanjutan', 'perlu lanjut' => ['label' => 'Perlu Sesi Lanjutan', 'class' => 'status-follow-up'],
            default => ['label' => ucfirst($normalized ?: '-'), 'class' => 'status-default'],
        };
    }

    private function getIdentitasMahasiswaTampil(JadwalKonseling $jadwal): array
    {
        $mahasiswa = $jadwal->mahasiswa;
        $userMahasiswa = $mahasiswa?->user;

        $isAnonim = filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

        $namaAnonim = 'Anonim';

        if ($userMahasiswa && method_exists($userMahasiswa, 'getAnonimDisplayName')) {
            $namaAnonim = trim($userMahasiswa->getAnonimDisplayName()) ?: 'Anonim';
        }

        return [
            'is_anonim' => $isAnonim,
            'nama' => $isAnonim
                ? $namaAnonim
                : ($userMahasiswa->nama ?? '-'),
            'nim' => $isAnonim
                ? '-'
                : ($mahasiswa->nim ?? '-'),
        ];
    }

    public function riwayat()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Data mahasiswa tidak ditemukan.');
        }

        $query = JadwalKonseling::with([
            'mahasiswa.user.profil',
            'konselor.user',
            'sesiKonseling.feedback',
        ])
        ->where('mahasiswa_id', $mahasiswa->id);

        $totalSesi = (clone $query)->count();

        $sesiSelesai = (clone $query)
            ->where('status', 'selesai')
            ->count();

        $riwayat = $query
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(5);

        $riwayat->getCollection()->transform(function (JadwalKonseling $jadwal) {
            $identitas = $this->getIdentitasMahasiswaTampil($jadwal);

            $jadwal->nama_tampil = $identitas['nama'];
            $jadwal->nim_tampil = $identitas['nim'];
            $jadwal->is_anonim_tampil = $identitas['is_anonim'];

            return $jadwal;
        });

        return view('Pages.riwayat', compact(
            'riwayat',
            'totalSesi',
            'sesiSelesai'
        ));
    }

    public function detailRiwayat($id)
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (! $mahasiswa) {
            abort(403, 'Data mahasiswa tidak ditemukan.');
        }

        $jadwal = JadwalKonseling::with(['mahasiswa.user.profil', 'konselor.user', 'sesiKonseling.feedback'])
            ->where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $identitasMahasiswa = $this->getIdentitasMahasiswaTampil($jadwal);

        $laporan = null;
        if ($jadwal->sesiKonseling) {
            $laporan = Laporan::where('sesi_id', $jadwal->sesiKonseling->id)->first();
        }

        $statusInfo = $this->formatRiwayatStatus($jadwal->status);
        $topik = $this->extractTopik($jadwal->catatan, $jadwal->topik ?? null);
        $metode = strtolower(trim((string) $jadwal->jenis)) === 'offline'
            ? 'Tatap Muka'
            : 'Video Call';

        $feedback = $jadwal->sesiKonseling?->feedback;
        $bisaFeedback = $jadwal->status === 'selesai' && $jadwal->sesiKonseling && !$feedback;

        $catatanRingkasan = trim((string) ($jadwal->ringkasan_masalah ?? ''));
        if ($catatanRingkasan === '') {
            $catatanRingkasan = trim((string) ($laporan->isi_laporan ?? $jadwal->catatan ?? ''));
        }

        $observasi = trim((string) ($jadwal->observasi_konselor ?? ''));
        $progress = trim((string) ($jadwal->progress ?? ''));
        // Normalisasi state tindak lanjut untuk detail laporan mahasiswa.
        $tindakLanjutRaw = trim((string) ($jadwal->tindak_lanjut ?? ''));
        $tindakLanjutTipeRaw = trim((string) ($jadwal->tindak_lanjut_tipe ?? ''));
        $statusNormalized = strtolower(str_replace(' ', '_', trim((string) ($jadwal->status ?? ''))));
        $perluSesiLanjutan = $statusNormalized === 'perlu_sesi_lanjutan'
            || in_array(strtolower(str_replace('_', ' ', $tindakLanjutTipeRaw ?: $tindakLanjutRaw)), ['perlu lanjut', 'perlu sesi lanjutan', 'on', '1', 'ya'], true);
        $legacyTindakLanjutLabels = ['perlu sesi lanjutan', 'tidak perlu sesi lanjutan', 'perlu lanjut'];
        $tindakLanjut = $perluSesiLanjutan ? 'Perlu sesi lanjutan' : 'Tidak perlu sesi lanjutan';
        $tindakLanjutDeskripsi = $perluSesiLanjutan && !in_array(strtolower($tindakLanjutRaw), $legacyTindakLanjutLabels, true)
            ? $tindakLanjutRaw
            : '';

        return view('Pages.detail-riwayat', compact(
            'jadwal',
            'laporan',
            'identitasMahasiswa',
            'statusInfo',
            'topik',
            'metode',
            'feedback',
            'bisaFeedback',
            'catatanRingkasan',
            'observasi',
            'progress',
            'tindakLanjut',
            'tindakLanjutDeskripsi',
            'perluSesiLanjutan'
        ));
    }

    public function laporanAdmin(\Illuminate\Http\Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Mahasiswa::query()
            ->with('user')
            ->withCount(['jadwalKonseling as total_laporan' => function ($query) {
                $this->scopeJadwalWithReport($query);
            }])
            ->whereHas('jadwalKonseling', function ($query) {
                $this->scopeJadwalWithReport($query);
            });

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->whereHas('user', function ($qb) use ($q) {
                    $qb->where('nama', 'like', "%{$q}%");
                })->orWhere('nim', 'like', "%{$q}%")
                    ->orWhere('jurusan', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%");
            });
        }

        $mahasiswa = $query
            ->orderBy(
                \App\Models\User::select('nama')
                    ->whereColumn('users.id', 'mahasiswa.user_id')
                    ->limit(1)
            )
            ->paginate(10)
            ->withQueryString();

        return view('admin.laporan.index', compact('mahasiswa', 'q'));
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Mahasiswa::query()
            ->with('user')
            ->withCount(['jadwalKonseling as total_laporan' => function ($query) {
                $this->scopeJadwalWithReport($query);
            }])
            ->whereHas('jadwalKonseling', function ($query) {
                $this->scopeJadwalWithReport($query);
            });

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->whereHas('user', function ($qb) use ($q) {
                    $qb->where('nama', 'like', "%{$q}%");
                })->orWhere('nim', 'like', "%{$q}%")
                    ->orWhere('jurusan', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%");
            });
        }

        $results = $query->limit(10)->get()->map(function ($mahasiswa) {
            return [
                'id' => $mahasiswa->id,
                'nama' => optional($mahasiswa->user)->nama ?? 'Anonim',
                'nim' => $mahasiswa->nim ?? '-',
                'jurusan' => $mahasiswa->jurusan ?? '-',
                'angkatan' => $mahasiswa->angkatan ?? '-',
                'total_laporan' => $mahasiswa->total_laporan ?? 0,
                'url' => route('admin.laporan.mahasiswa', $mahasiswa->id),
            ];
        });

        return response()->json($results->values());
    }

    public function showMahasiswaLaporan(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load('user');

        $riwayat = $this->laporanRiwayatQuery()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where(function ($query) {
                $this->scopeJadwalWithReport($query);
            })
            ->paginate(10)
            ->withQueryString();

        $aiSummary = AiLaporanSummary::where('mahasiswa_id', $mahasiswa->id)
            ->latest('generated_at')
            ->latest()
            ->first();

        $sourceHash = $this->buildAiSummarySourceHash($mahasiswa);
        $summaryOutdated = $aiSummary && $aiSummary->source_hash !== $sourceHash;
        $summarySessionsCount = count($this->buildAiSummaryPayload($mahasiswa));

        return view('admin.laporan.show', compact(
            'mahasiswa',
            'riwayat',
            'aiSummary',
            'summaryOutdated',
            'summarySessionsCount'
        ));
    }

    public function generateAiSummary(Mahasiswa $mahasiswa, GroqSummaryService $groqSummaryService)
    {
        $sessions = $this->buildAiSummaryPayload($mahasiswa);

        if (empty($sessions)) {
            return back()->withErrors([
                'ai_summary' => 'Belum ada laporan sesi konseling yang dapat diringkas.',
            ]);
        }

        try {
            $summary = $groqSummaryService->summarize($sessions);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'ai_summary' => $e->getMessage(),
            ]);
        }

        AiLaporanSummary::create([
            'mahasiswa_id' => $mahasiswa->id,
            'konselor_id' => optional(auth()->user()->konselor)->id,
            'provider' => 'groq',
            'model' => config('services.groq.model', 'llama-3.1-8b-instant'),
            'summary' => $summary,
            'source_hash' => hash('sha256', json_encode($sessions)),
            'generated_at' => now(),
        ]);

        // <!-- MODIFIED: Tambah flash data modal untuk notifikasi sukses interaktif -->
        return redirect()
            ->route('admin.laporan.mahasiswa', $mahasiswa)
            ->with([
                'ai_summary_success' => true,
                'ai_summary_message' => 'Ringkasan AI berhasil dibuat.'
            ]);
    }

    public function createLaporan($id)
    {
        $jadwal = $this->laporanRiwayatQuery()->whereKey($id)->firstOrFail();
        $riwayat = $this->laporanRiwayatQuery()->get();
        $sesi = $this->resolveSesiKonseling($jadwal);
        $laporan = $sesi->laporan;
        // Sesi selesai tetap bisa dibuatkan laporan jika laporan belum tersimpan.
        $sudahAdaLaporan = $this->jadwalHasStoredReport($jadwal, $laporan);

        return view('admin.laporan', compact('jadwal', 'riwayat', 'sesi', 'laporan', 'sudahAdaLaporan'));
    }

    public function storeLaporan(Request $request, $id)
    {
        $ringkasanMasalah = trim((string) (
            $request->input('ringkasan_masalah')
            ?: $request->input('catatan')
        ));

        $observasiKonselor = trim((string) $request->input('observasi_konselor'));

        $perluLanjut = $request->has('tindak_lanjut') || $request->has('perlu_lanjut');
        $tindakLanjutDeskripsi = trim((string) $request->input('tindak_lanjut_deskripsi', ''));
        $progress = strtolower(trim((string) $request->input('progress')));

        $request->merge([
            'ringkasan_masalah' => $ringkasanMasalah,
            'observasi_konselor' => $observasiKonselor,
            'progress' => $progress,
            // Keterangan sesi lanjutan opsional dan hanya dipakai saat checkbox aktif.
            'tindak_lanjut_deskripsi' => $perluLanjut ? $tindakLanjutDeskripsi : null,
        ]);

        $rules = [
            'ringkasan_masalah' => ['required', 'string', 'min:3'],
            'observasi_konselor' => ['nullable', 'string'],
            'progress' => ['required', 'in:membaik,memburuk'],
            'tindak_lanjut_deskripsi' => ['nullable', 'string', 'max:1000'],
        ];

        $messages = [
            'ringkasan_masalah.required' => 'Ringkasan masalah wajib diisi sebelum laporan dapat disimpan.',
            'progress.required' => 'Silakan pilih progress mahasiswa sebelum menyimpan laporan.',
            'progress.in' => 'Progress mahasiswa harus dipilih antara Membaik atau Memburuk.',
            'tindak_lanjut_deskripsi.max' => 'Keterangan sesi lanjutan maksimal 1000 karakter.',
        ];

        $validated = $request->validate($rules, $messages);

        $jadwal = JadwalKonseling::findOrFail($id);
        $sesi = $this->resolveSesiKonseling($jadwal);
        $konselorId = optional(auth()->user()->konselor)->id ?? $jadwal->konselor_id;

        $isi = trim(
            $validated['ringkasan_masalah'] .
            "\n\nObservasi: " . $validated['observasi_konselor']
        );

        Laporan::updateOrCreate([
            'sesi_id' => $sesi->id,
        ], [
            'konselor_id' => $konselorId,
            'isi_laporan' => $isi,
        ]);

        $sesi->update([
            'status' => 'selesai',
        ]);

        $jadwal->update([
            'status' => 'selesai',
            'ringkasan_masalah' => $validated['ringkasan_masalah'],
            'observasi_konselor' => $validated['observasi_konselor'],
            'progress' => $validated['progress'],
            'tindak_lanjut' => $perluLanjut ? ($validated['tindak_lanjut_deskripsi'] ?? '') : null,
            'tindak_lanjut_tipe' => $perluLanjut ? 'perlu lanjut' : null,
            'tanggal_lanjut' => null,
            'laporan' => $isi,
        ]);

        if ($perluLanjut && $jadwal->mahasiswa?->user) {
            // Notifikasi sesi lanjutan diarahkan ke daftar riwayat agar mahasiswa membuka status terkait.
            $notifikasiLanjutanTarget = route('riwayat', ['jadwal' => $jadwal->id]);
            $sudahAdaNotifikasiLanjutan = Notifikasi::where('user_id', $jadwal->mahasiswa->user->id)
                ->where('cta_target', $notifikasiLanjutanTarget)
                ->where('pesan', 'like', '%perlu sesi lanjutan%')
                ->exists();

            if (! $sudahAdaNotifikasiLanjutan) {
                Notifikasi::create([
                    'user_id' => $jadwal->mahasiswa->user->id,
                    'pesan' => 'Konselor menandai sesi konseling Anda perlu sesi lanjutan. Silakan buka riwayat konseling untuk membuat sesi lanjutan.',
                    'status' => 'belum',
                    'cta_target' => $notifikasiLanjutanTarget,
                    'cta_label' => 'Buka Riwayat',
                ]);
            }
        }

        return redirect()
            ->route('admin.laporan.mahasiswa', $jadwal->mahasiswa_id)
            ->with('laporan_success', 'Laporan berhasil dibuat.');
    }

    private function laporanRiwayatQuery()
    {
        return JadwalKonseling::with([
            'mahasiswa.user',
            'konselor.user',
            'sesiKonseling.laporan',
        ])->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc');
    }

    private function scopeJadwalWithReport($query): void
    {
        $query->where(function ($builder) {
            $builder->whereNotNull('laporan')
                ->where('laporan', '<>', '')
                ->orWhere('status', 'selesai')
                ->orWhereNotNull('ringkasan_masalah')
                ->orWhereNotNull('observasi_konselor')
                ->orWhereHas('sesiKonseling.laporan');
        });
    }

    private function buildAiSummaryPayload(Mahasiswa $mahasiswa): array
    {
        return $this->laporanRiwayatQuery()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->get()
            ->filter(function (JadwalKonseling $jadwal) {
                return trim((string) ($jadwal->laporan ?? '')) !== ''
                    || trim((string) ($jadwal->ringkasan_masalah ?? '')) !== ''
                    || trim((string) ($jadwal->observasi_konselor ?? '')) !== ''
                    || optional($jadwal->sesiKonseling?->laporan)->isi_laporan;
            })
            ->values()
            ->map(function (JadwalKonseling $jadwal) {
                $laporan = trim((string) ($jadwal->laporan ?? ''));
                if ($laporan === '') {
                    $laporan = trim((string) optional($jadwal->sesiKonseling?->laporan)->isi_laporan);
                }

                return [
                    'topik' => $this->extractTopik($jadwal->catatan, $jadwal->topik ?? null),
                    'ringkasan_masalah' => trim((string) ($jadwal->ringkasan_masalah ?: $laporan)),
                    'observasi_konselor' => trim((string) ($jadwal->observasi_konselor ?? '')),
                    'progress' => trim((string) ($jadwal->progress ?? '')),
                    // MODIFIED: Fix bug text perlu_lanjut menjadi perlu lanjut dengan merubah underscore menjadi spasi
                    'tindak_lanjut' => str_replace('_', ' ', trim((string) ($jadwal->tindak_lanjut_tipe ?? $jadwal->tindak_lanjut ?? ''))),
                ];
            })
            ->all();
    }

    private function buildAiSummarySourceHash(Mahasiswa $mahasiswa): string
    {
        return hash('sha256', json_encode($this->buildAiSummaryPayload($mahasiswa)));
    }

    private function jadwalHasStoredReport(JadwalKonseling $jadwal, ?Laporan $laporan = null): bool
    {
        // Cek isi laporan aktual, bukan status jadwal.
        return trim((string) ($jadwal->laporan ?? '')) !== ''
            || trim((string) ($jadwal->ringkasan_masalah ?? '')) !== ''
            || trim((string) ($jadwal->observasi_konselor ?? '')) !== ''
            || trim((string) optional($laporan ?? $jadwal->sesiKonseling?->laporan)->isi_laporan) !== '';
    }

    private function resolveSesiKonseling(JadwalKonseling $jadwal): SesiKonseling
    {
        $foreignKey = SesiKonseling::jadwalForeignKey();

        return SesiKonseling::firstOrCreate([
            $foreignKey => $jadwal->id,
        ], [
            'status' => strtolower((string) ($jadwal->status ?: 'berlangsung')),
        ]);
    }
}
