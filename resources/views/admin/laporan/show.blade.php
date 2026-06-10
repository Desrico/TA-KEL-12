@extends('layouts.admin')

@section('page-title', 'Detail Laporan Mahasiswa')

@push('styles')
<style>
    .laporan-shell {
        max-width: 1160px;
        margin: 0 auto;
        width: calc(100% - 48px);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 16px;
        color: #475569;
        text-decoration: none;
        font-weight: 700;
        font-size: .86rem;
    }

    .btn-back:hover {
        color: #065f46;
    }

    .laporan-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(6, 78, 59, 0.08);
        overflow: hidden;
        margin-bottom: 22px;
    }

    .laporan-head {
        padding: 24px 28px;
        border-bottom: 1px solid #edf2ef;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
    }

    .laporan-head h5 {
        margin: 0 0 7px;
        color: #064e3b;
        font-weight: 800;
        font-size: 1.2rem;
    }

    .student-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: .84rem;
    }

    .student-meta span {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 5px 10px;
    }

    .laporan-table-wrap {
        padding: 20px;
        overflow-x: auto;
    }

    .laporan-table {
        width: 100%;
        min-width: 860px;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #d7e7de;
        border-radius: 14px;
        overflow: hidden;
        font-size: .86rem;
    }

    .laporan-table th {
        background: #f5f8f7;
        color: #111827;
        font-weight: 800;
        padding: 13px 14px;
        border-bottom: 1px solid #d6e9de;
        text-align: left;
    }

    .laporan-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #edf2ef;
        color: #1f2937;
        vertical-align: middle;
    }

    .detail-sesi {
        color: #334155;
        line-height: 1.4;
        white-space: nowrap;
    }

    .topic-text {
        max-width: 360px;
        line-height: 1.4;
    }

    .btn-laporan {
        border: 0;
        border-radius: 10px;
        background: #065f46;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 38px;
        padding: 0 14px;
        font-size: .78rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-laporan:hover {
        background: #064e3b;
        color: #fff;
    }

    .ai-card {
        padding: 24px 28px;
    }

    .ai-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .ai-head h6 {
        margin: 0 0 6px;
        color: #064e3b;
        font-size: 1rem;
        font-weight: 800;
    }

    .ai-head p {
        margin: 0;
        color: #64748b;
        font-size: .85rem;
        line-height: 1.45;
    }

    .ai-summary-box {
        border: 1px solid #dceee4;
        border-radius: 14px;
        background: #fbfefc;
        padding: 18px;
        color: #1f2937;
        font-size: .9rem;
        line-height: 1.65;
        white-space: pre-line;
    }

    .ai-empty {
        color: #64748b;
    }

    .alert {
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 16px;
        font-size: .86rem;
        font-weight: 700;
    }

    .alert-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .alert-warning {
        background: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .sesi-pagination {
        padding: 0 20px 22px;
        display: flex;
        justify-content: center;
    }

    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 34px 14px !important;
    }

    /* MODIFIED: CSS untuk animasi streaming text - efek teks smooth berjalan dari kanan ke kiri baris per baris */
    @keyframes slideInFromRight {
        0% {
            opacity: 0;
            transform: translateX(40px);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* MODIFIED: Animasi untuk setiap baris teks yang di-stream - smooth transition dari kanan ke kiri */
    .streaming-line {
        opacity: 0;
        animation: slideInFromRight 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        display: inline-block;
    }

    /* ADDED: Container untuk text yang sedang di-stream */
    #aiSummaryText {
        display: block;
    }

    /* ADDED: Styling untuk modal sukses AI summary */
    .ai-success-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease-out;
    }

    .ai-success-modal.show {
        display: flex;
    }

    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }

    .ai-modal-content {
        background: white;
        border-radius: 18px;
        padding: 28px;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ai-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .ai-modal-icon {
        width: 44px;
        height: 44px;
        background: #ecfdf5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .ai-modal-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #064e3b;
        margin: 0;
    }

    .ai-modal-message {
        color: #64748b;
        font-size: .9rem;
        line-height: 1.5;
        margin: 0 0 20px;
    }

    .ai-modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .ai-modal-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }

    .ai-modal-btn-primary {
        background: #065f46;
        color: white;
    }

    .ai-modal-btn-primary:hover {
        background: #064e3b;
    }

    @media (max-width: 768px) {
        .laporan-shell {
            width: calc(100% - 24px);
        }

        .laporan-head,
        .ai-head {
            flex-direction: column;
        }

        .ai-modal-content {
            width: 95%;
        }
    }
</style>
@endpush

@section('konten')
@php
    $namaMahasiswa = optional($mahasiswa->user)->nama ?? 'Anonim';
    $formatAiSummary = function (?string $summary): string {
        $summary = preg_replace('/\*\*(.*?)\*\*/s', '$1', (string) $summary);
        $summary = preg_replace('/^\s*\*\s*/m', '', $summary ?? '');
        $summary = trim((string) $summary);

        $escaped = e($summary);
        $importantWords = [
            'Ringkasan Masalah Utama',
            'Kondisi/Perkembangan Mahasiswa',
            'Catatan Penting dari Konselor',
            'Tindak Lanjut yang Disarankan Berdasarkan Laporan',
            'Tidak disebutkan dalam laporan',
            'masalah utama',
            'kondisi',
            'perkembangan',
            'catatan penting',
            'tindak lanjut',
            'disarankan',
            'prioritas',
            'hambatan',
            'risiko',
            'kecemasan',
            'cemas',
            'stres',
            'tekanan',
            'motivasi',
            'akademik',
            'dukungan',
            'konselor',
            'keluarga',
            'mahasiswa',
        ];

        foreach ($importantWords as $word) {
            $escaped = preg_replace(
                '/(?<![[:alnum:]_>])(' . preg_quote(e($word), '/') . ')(?![[:alnum:]_<])/iu',
                '<strong>$1</strong>',
                $escaped
            );
        }

        return $escaped;
    };
@endphp

<div class="laporan-shell">
    <a href="{{ route('admin.laporan') }}" class="btn-back">
        <i class="ti ti-arrow-left"></i>
        Kembali ke daftar laporan
    </a>

    @error('ai_summary')
        <div class="alert alert-error">{{ $message }}</div>
    @enderror

    <div class="laporan-card">

        <div class="laporan-table-wrap">
            <table class="laporan-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Detail Sesi</th>
                        <th>Deskripsi / Topik Konseling</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        @php
                            $topik = $item->topik ?? null;

                            if (!$topik && !empty($item->catatan) && preg_match('/Topik:\s*([^|]+)/i', $item->catatan, $match)) {
                                $topik = trim($match[1]);
                            }

                            if (!$topik) {
                                $topik = $item->ringkasan_masalah ?? $item->laporan ?? '-';
                            }

                            // Sesi selesai tanpa laporan tetap menjadi kandidat pembuatan laporan.
                            $sudahAdaLaporan = trim((string) ($item->laporan ?? '')) !== ''
                                || trim((string) ($item->ringkasan_masalah ?? '')) !== ''
                                || trim((string) ($item->observasi_konselor ?? '')) !== ''
                                || trim((string) optional($item->sesiKonseling?->laporan)->isi_laporan) !== '';
                        @endphp
                        <tr>
                            <td>{{ $namaMahasiswa }}</td>
                            <td>
                                <div class="detail-sesi">
                                    {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') : '-' }}<br>
                                    {{ $item->waktu ? substr($item->waktu, 0, 5) . ' WIB' : '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="topic-text">{{ $topik }}</div>
                            </td>
                            <td style="text-align:center;">
                                <a href="{{ route('admin.laporan.laporan', $item->id) }}" class="btn-laporan">
                                    <i class="ti {{ $sudahAdaLaporan ? 'ti-eye' : 'ti-file-plus' }}"></i>
                                    {{ $sudahAdaLaporan ? 'Lihat Detail' : 'Buat Laporan' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                Belum ada sesi yang memiliki laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($riwayat, 'links'))
            <div class="sesi-pagination">
                {{ $riwayat->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>

    <!-- MODIFIED: Hapus disclaimer text dari section Ringkasan AI -->
    <div class="laporan-card ai-card">
        <div class="ai-head">
            <div>
                <h6>Ringkasan AI</h6>
            </div>

            <form method="POST" action="{{ route('admin.laporan.ai-summary', $mahasiswa->id) }}" class="ai-summary-form">
                @csrf
                <button type="submit" class="btn-laporan" {{ $summarySessionsCount < 1 ? 'disabled' : '' }}>
                    <i class="ti ti-sparkles"></i>
                    {{ $aiSummary ? 'Perbarui Ringkasan AI' : 'Generate Ringkasan AI' }}
                </button>
            </form>
        </div>

        @if($summaryOutdated)
            <div class="alert alert-warning">
                Ada perubahan pada laporan sesi sejak ringkasan AI terakhir dibuat.
            </div>
        @endif

        <!-- MODIFIED: Hapus informasi Dibuat ... dan menambah container untuk animasi teks berjalan -->
        @if($aiSummary)
            <!-- ADDED: Container untuk animasi streaming text baris per baris dari kiri ke kanan -->
            <div class="ai-summary-box" id="aiSummaryContainer">
                <span id="aiSummaryText" data-summary="{{ $aiSummary->summary }}">{!! $formatAiSummary($aiSummary->summary) !!}</span>
            </div>
            <p class="ai-empty" style="margin:12px 0 0;">
                Ringkasan AI hanya bersifat bantuan awal dan tetap perlu ditinjau oleh konselor.
            </p>
        @else
            <div class="ai-summary-box ai-empty">
                Belum ada ringkasan AI untuk mahasiswa ini.
            </div>
            <p class="ai-empty" style="margin:12px 0 0;">
                Ringkasan AI hanya bersifat bantuan awal dan tetap perlu ditinjau oleh konselor.
            </p>
        @endif
    </div>
</div>

<!-- ADDED: Modal HTML untuk notifikasi sukses AI summary -->
<div id="aiSuccessModal" class="ai-success-modal">
    <div class="ai-modal-content">
        <div class="ai-modal-header">
            <div class="ai-modal-icon">✓</div>
            <h3 class="ai-modal-title">Ringkasan AI Berhasil</h3>
        </div>
        <p class="ai-modal-message">Ringkasan AI untuk laporan konseling telah berhasil dibuat. Silakan tinjau kembali untuk memastikan akurasi sebelum digunakan.</p>
        <div class="ai-modal-actions">
            <button class="ai-modal-btn ai-modal-btn-primary" onclick="closeAiSuccessModal()">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal fallback jika SweetAlert gagal dimuat -->
<div id="reportSuccessModal" class="ai-success-modal">
    <div class="ai-modal-content">
        <div class="ai-modal-header">
            <div class="ai-modal-icon">OK</div>
            <h3 class="ai-modal-title">Laporan Berhasil Dibuat</h3>
        </div>
        <p class="ai-modal-message" id="reportSuccessMessage">Laporan hasil konseling berhasil dibuat.</p>
        <div class="ai-modal-actions">
            <button class="ai-modal-btn ai-modal-btn-primary" onclick="closeReportSuccessModal()">Mengerti</button>
        </div>
    </div>
</div>

@push('scripts')
<!-- ADDED: JavaScript untuk menampilkan modal sukses dan animasi streaming text -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ADDED: Fungsi untuk menampilkan modal sukses AI summary
    function showAiSuccessModal() {
        const modal = document.getElementById('aiSuccessModal');
        if (modal) {
            modal.classList.add('show');
            // ADDED: Auto-close modal setelah 5 detik
            setTimeout(closeAiSuccessModal, 5000);
        }
    }

    // ADDED: Fungsi untuk menutup modal sukses
    function closeAiSuccessModal() {
        const modal = document.getElementById('aiSuccessModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    function showReportSuccessModal(message) {
        if (window.Swal && typeof window.Swal.fire === 'function') {
            Swal.fire({
                icon: 'success',
                title: 'Laporan Berhasil Dibuat',
                text: message,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#065f46'
            });
            return;
        }

        // Fallback modal lokal saat SweetAlert tidak tersedia.
        const modal = document.getElementById('reportSuccessModal');
        const messageEl = document.getElementById('reportSuccessMessage');

        if (messageEl) {
            messageEl.textContent = message;
        }

        if (modal) {
            modal.classList.add('show');
        }
    }

    function closeReportSuccessModal() {
        const modal = document.getElementById('reportSuccessModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    function escapeAiSummaryHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatAiSummaryLine(line) {
        const importantWords = [
            'Ringkasan Masalah Utama',
            'Kondisi/Perkembangan Mahasiswa',
            'Catatan Penting dari Konselor',
            'Tindak Lanjut yang Disarankan Berdasarkan Laporan',
            'Tidak disebutkan dalam laporan',
            'masalah utama',
            'kondisi',
            'perkembangan',
            'catatan penting',
            'tindak lanjut',
            'disarankan',
            'prioritas',
            'hambatan',
            'risiko',
            'kecemasan',
            'cemas',
            'stres',
            'tekanan',
            'motivasi',
            'akademik',
            'dukungan',
            'konselor',
            'keluarga',
            'mahasiswa',
        ];

        let formattedLine = escapeAiSummaryHtml(line)
            .replace(/\*\*(.*?)\*\*/g, '$1')
            .replace(/^\s*\*\s*/, '');

        importantWords.forEach((word) => {
            const escapedWord = escapeAiSummaryHtml(word).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            formattedLine = formattedLine.replace(
                new RegExp(`(^|[^\\p{L}\\p{N}_>])(${escapedWord})(?![\\p{L}\\p{N}_<])`, 'giu'),
                '$1<strong>$2</strong>'
            );
        });

        return formattedLine;
    }

    // MODIFIED: Fungsi untuk animasi streaming text - teks berjalan smooth dari kanan ke kiri baris per baris
    function animateAiSummaryText() {
        const textElement = document.getElementById('aiSummaryText');
        const container = document.getElementById('aiSummaryContainer');
        
        if (!textElement || !container) return;

        // ADDED: Ambil teks asli dan split per baris
        const originalText = textElement.dataset.summary || textElement.textContent;
        const lines = originalText.split('\n');
        
        // ADDED: Kosongkan container dan set initial state
        container.innerHTML = '';
        let delayOffset = 0;

        // MODIFIED: Proses setiap baris dengan delay yang dioptimalkan untuk animasi yang smooth (800ms per animasi)
        lines.forEach((line, lineIndex) => {
            if (line.trim() === '') {
                // ADDED: Buat line break untuk baris kosong
                const lineBreak = document.createElement('div');
                lineBreak.style.height = '1em';
                container.appendChild(lineBreak);
                delayOffset += 150;
                return;
            }

            // ADDED: Buat span untuk setiap baris dengan delay animation
            const lineSpan = document.createElement('span');
            lineSpan.className = 'streaming-line';
            lineSpan.style.animationDelay = `${delayOffset}ms`;
            lineSpan.innerHTML = formatAiSummaryLine(line);
            container.appendChild(lineSpan);

            // ADDED: Buat line break setelah setiap baris
            const br = document.createElement('br');
            container.appendChild(br);

            // MODIFIED: Tambah delay untuk baris berikutnya (800ms animasi + 150ms gap untuk smooth effect)
            delayOffset += 950;
        });
    }

    // ADDED: Cek session flash untuk menampilkan modal dan trigger animasi
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('ai_summary_success'))
            // ADDED: Tampilkan modal sukses jika ada session ai_summary_success
            showAiSuccessModal();
            // ADDED: Trigger animasi streaming text untuk ringkasan baru
            setTimeout(animateAiSummaryText, 500);
        @endif

        @if(session('laporan_success'))
            // Tampilkan sukses laporan sebagai SweetAlert atau fallback modal.
            showReportSuccessModal(@json(session('laporan_success')));
        @endif

        // ADDED: Close modal ketika user klik di luar modal
        const modal = document.getElementById('aiSuccessModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeAiSuccessModal();
                }
            });
        }

        const reportModal = document.getElementById('reportSuccessModal');
        if (reportModal) {
            reportModal.addEventListener('click', function(e) {
                if (e.target === reportModal) {
                    closeReportSuccessModal();
                }
            });
        }

        // ADDED: Trigger animasi text ketika halaman pertama kali dimuat (jika sudah ada summary)
        const textElement = document.getElementById('aiSummaryText');
        if (textElement && textElement.textContent.trim() && !@json(session('ai_summary_success'))) {
            // ADDED: Tampilkan teks langsung tanpa animasi jika bukan baru dibuat
            // (untuk menghindari delay ketika halaman di-refresh)
        }
    });

    // ADDED: Handle form submission untuk show loading state pada button
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList.contains('ai-summary-form')) {
            const button = form.querySelector('button');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="ti ti-loader-2" style="animation: spin 1s linear infinite;"></i> Memproses...';
            }
        }
    });
</script>

<!-- ADDED: CSS untuk animasi spinner pada button loading -->
<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush
@endsection
