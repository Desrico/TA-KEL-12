@extends('layouts.admin')

@section('page-title', 'Laporan Hasil Konseling')
@section('breadcrumb-parent', 'Laporan Konseling')
@section('breadcrumb-parent-url', route('admin.laporan'))

@push('styles')
<style>
    .report-page {
        padding: 6px 2px 12px;
    }

    .report-title {
        font-size: clamp(1.8rem, 2.8vw, 2.6rem);
        font-weight: 800;
        line-height: 1;
        color: #111827;
        margin: 0 0 .5rem;
        letter-spacing: -.03em;
    }

    .report-title span {
        color: #5f7f67;
    }

    .report-subtitle {
        margin: 0;
        color: #6b7280;
        font-size: .95rem;
    }

    .report-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.02fr) minmax(0, .98fr);
        gap: 1rem;
        margin-top: 1.4rem;
    }

    .report-panel {
        background: #fff;
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .report-panel-head {
        padding: 1.1rem 1.2rem .7rem;
        border-bottom: 1px solid #edf2ef;
    }

    .report-panel-head h5 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #111827;
    }

    .report-section {
        padding: .9rem 1.2rem 1.2rem;
    }

    .report-section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        color: #0f766e;
        font-size: .92rem;
        font-weight: 700;
        margin: .2rem 0 .65rem;
    }

    .report-details {
        width: 100%;
        border-collapse: collapse;
    }

    .report-details td {
        padding: .72rem 0;
        border-bottom: 1px solid #edf2ef;
        font-size: .9rem;
        vertical-align: top;
    }

    .report-details td:first-child {
        color: #6b7280;
        width: 42%;
        padding-right: .8rem;
    }

    .report-details td:last-child {
        text-align: right;
        color: #111827;
        font-weight: 600;
    }

    .topic-value {
        display: inline-block;
        max-width: 100%;
        white-space: normal;
        word-break: break-word;
        overflow-wrap: anywhere;
        line-height: 1.5;
    }

    .report-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .45rem .9rem;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        font-size: .8rem;
        font-weight: 700;
    }

    .form-laporan {
        padding: 1rem 1.2rem 1.2rem;
    }

    .form-block {
        margin-bottom: 1rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: .45rem;
        color: #0f766e;
        font-size: .92rem;
        font-weight: 700;
        margin-bottom: .55rem;
    }

    .form-control,
    .form-select,
    .form-textarea {
        width: 100%;
        border: 1px solid #dfe6e1;
        border-radius: 16px;
        background: #fff;
        color: #111827;
        font-size: .92rem;
        box-shadow: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .form-control:disabled,
    .form-select:disabled,
    .form-textarea:disabled {
        background: #f3f4f3;
        border-color: #e5e7eb;
        color: #6b7280;
        cursor: not-allowed;
        opacity: 1;
    }

    .option-item input[type="checkbox"]:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .form-control:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 .2rem rgba(15, 118, 110, .08);
    }

    .form-textarea {
        min-height: 115px;
        resize: vertical;
        padding: .95rem 1rem;
    }

    .form-control,
    .form-select {
        min-height: 52px;
        padding: .8rem 1rem;
    }

    .option-list {
        display: grid;
        gap: .5rem;
        margin-top: .2rem;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: .55rem;
        font-size: .9rem;
        color: #374151;
        padding: .45rem 0;
    }

    .option-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #0f766e;
        flex-shrink: 0;
    }

    .form-footer {
        display: flex;
        justify-content: flex-end;
        padding-top: .35rem;
    }

    .submit-btn {
        min-width: 160px;
        border: none;
        border-radius: 10px;
        background: #0f766e;
        color: #fff;
        font-size: .95rem;
        font-weight: 700;
        padding: .82rem 1.4rem;
        transition: transform .15s ease, background .15s ease;
    }

    .submit-btn:hover {
        background: #065f46;
        transform: translateY(-1px);
        color: #fff;
    }

    .finish-wrap {
        display: flex;
        justify-content: flex-end;
        padding-top: .35rem;
    }

    .finish-btn {
        min-width: 140px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .confirm-overlay,
    .success-overlay {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, .32);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999999;
    }

    .confirm-overlay.show,
    .success-overlay.show {
        display: flex !important;
    }

    .confirm-box {
        width: 360px;
        max-width: 90%;
        background: #066847;
        color: #fff;
        border-radius: 14px;
        padding: 1.8rem 1.5rem;
        text-align: center;
        animation: popFade .28s ease both;
    }

    .confirm-icon {
        width: 58px;
        height: 58px;
        border: 4px solid #ffe66d;
        color: #ffe66d;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 2rem;
        font-weight: 800;
        margin: 0 auto 1rem;
    }

    .confirm-box h3,
    .success-box h3 {
        font-weight: 800;
    }

    .confirm-box p {
        font-size: .8rem;
        line-height: 1.5;
    }

    .confirm-actions {
        display: flex;
        justify-content: center;
        gap: .8rem;
        margin-top: 1.2rem;
    }

    .btn-confirm {
        border: 0;
        background: #ffe66d;
        color: #064e3b;
        font-weight: 800;
        border-radius: 6px;
        padding: .48rem .95rem;
    }

    .btn-cancel {
        border: 1px solid #fff;
        background: transparent;
        color: #fff;
        font-weight: 700;
        border-radius: 6px;
        padding: .48rem .95rem;
    }

    .success-box {
        width: 390px;
        max-width: 90%;
        background: #fff;
        border-radius: 22px;
        padding: 2rem 1.7rem;
        text-align: center;
        box-shadow: 0 28px 70px rgba(15, 23, 42, .22);
        animation: popFade .28s ease both;
    }

    .success-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #DFF8EA;
        color: #066847;
        display: grid;
        place-items: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
    }

    .success-box h3 {
        color: #064E3B;
        font-size: 1.25rem;
    }

    .success-box p {
        color: #64748b;
        font-size: .9rem;
    }

    .btn-success-ok {
        border: 0;
        background: #066847;
        color: #fff;
        border-radius: 999px;
        padding: .7rem 1.4rem;
        font-size: .85rem;
        font-weight: 800;
    }

    @keyframes popFade {
        from {
            opacity: 0;
            transform: translateY(18px) scale(.94);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (max-width: 991.98px) {
        .report-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ── Custom Date Picker ── */
    .custom-datepicker { position: relative; }

    .cdp-input {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        min-height: 52px;
        padding: .8rem 1rem;
        border: 1px solid #dfe6e1;
        border-radius: 16px;
        background: #fff;
        color: #111827;
        font-size: .92rem;
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .cdp-input:hover { border-color: #0f766e; }
    .cdp-input.open {
        border-color: #0f766e;
        box-shadow: 0 0 0 .2rem rgba(15,118,110,.08);
    }

    .cdp-icon { color: #6b7280; font-size: 1.1rem; }

    .cdp-calendar {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        z-index: 9999;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(15,23,42,.13);
        padding: .9rem 1rem 1rem;
        width: 290px;
        animation: popFade .18s ease both;
    }

    .cdp-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .7rem;
    }

    .cdp-month-label {
        font-weight: 700;
        font-size: .93rem;
        color: #111827;
    }

    .cdp-nav {
        background: none;
        border: none;
        cursor: pointer;
        color: #6b7280;
        font-size: 1rem;
        padding: .1rem .3rem;
        border-radius: 6px;
    }
    .cdp-nav:hover { background: #f3f4f3; color: #111827; }

    .cdp-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        text-align: center;
        font-size: .78rem;
        font-weight: 700;
        color: #6b7280;
        margin-bottom: .35rem;
    }

    .cdp-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }

    .cdp-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: .84rem;
        cursor: pointer;
        color: #111827;
        transition: background .12s ease;
    }

    .cdp-day:hover:not(.disabled):not(.other-month) { background: #ecfdf5; color: #047857; }
    .cdp-day.today { font-weight: 800; }
    .cdp-day.selected { background: #1b4332; color: #fff; font-weight: 700; }
    .cdp-day.selected:hover { background: #066847; }

    .cdp-day.disabled {
        color: #d1d5db;
        cursor: not-allowed;
        pointer-events: none;
    }

    .cdp-day.other-month { color: #d1d5db; }
    .cdp-day.other-month.disabled { color: #e5e7eb; }

    .cdp-footer {
        display: flex;
        justify-content: space-between;
        margin-top: .7rem;
        font-size: .82rem;
    }

    .cdp-clear { background: none; border: none; color: #6b7280; cursor: pointer; padding: 0; }
    .cdp-clear:hover { color: #111827; }
    .cdp-today { background: none; border: none; color: #0f766e; font-weight: 700; cursor: pointer; padding: 0; }
    .cdp-today:hover { color: #065f46; }
</style>
@endpush

@section('konten')
@php
    $mahasiswa = optional($jadwal->mahasiswa);
    $user = optional($mahasiswa->user);

    $namaMahasiswa = $user->nama ?? '-';
    $nimMahasiswa = $mahasiswa->nim ?? '-';
    $prodiMahasiswa = $mahasiswa->jurusan ?? '-';
    $angkatanMahasiswa = $mahasiswa->angkatan ?? '-';

    $tanggalJadwal = $jadwal->tanggal
        ? \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y')
        : '-';

    $waktuJadwal = !empty($jadwal->waktu)
        ? substr($jadwal->waktu, 0, 5)
        : '-';

    $layanan = ucfirst($jadwal->jenis ?? 'Online');

    $topik = $jadwal->topik
        ?? $jadwal->topik_konseling
        ?? $jadwal->ringkasan_masalah
        ?? $jadwal->catatan
        ?? null;

    $status = strtolower($jadwal->status ?? 'menunggu');

    $statusLabel = match ($status) {
        'menunggu' => 'Menunggu',
        'disetujui' => 'Disetujui',
        'berlangsung' => 'Berlangsung',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        default => ucfirst($jadwal->status ?? '-'),
    };

    $modeLihat = isset($sudahAdaLaporan) && $sudahAdaLaporan;

    $minTanggalTindakLanjut = now('Asia/Jakarta')->toDateString();

    $tanggalTindakLanjut = old('tanggal_tindak_lanjut', $jadwal->tanggal_lanjut ?? '');

    if (!empty($tanggalTindakLanjut)) {
        try {
            $tanggalTindakLanjut = \Carbon\Carbon::parse($tanggalTindakLanjut)->format('Y-m-d');

            if ($tanggalTindakLanjut < $minTanggalTindakLanjut) {
                $tanggalTindakLanjut = '';
            }
        } catch (\Exception $e) {
            $tanggalTindakLanjut = '';
        }
    }

    $ringkasanMasalah = old('catatan');

    if ($ringkasanMasalah === null) {
        $ringkasanMasalah = $modeLihat
            ? ($jadwal->ringkasan_masalah ?? $jadwal->catatan ?? '')
            : '';
    }

    $observasiKonselor = old('observasi_konselor');

    if ($observasiKonselor === null) {
        $observasiKonselor = $modeLihat
            ? ($jadwal->observasi_konselor ?? '')
            : '';
    }
@endphp

<div class="report-page">
    <h1 class="report-title">Laporan <span>Hasil</span> Konseling</h1>

    <p class="report-subtitle">
        Dokumentasikan hasil sesi konseling serta perkembangan kondisi mahasiswa
    </p>

    <div class="report-grid">
        <div class="report-panel">
            <div class="report-panel-head">
                <h5>Detail Penjadwalan</h5>
            </div>

            <div class="report-section">
                <div class="report-section-title">
                    <i class="ti ti-user"></i>
                    Informasi Pribadi
                </div>

                <table class="report-details">
                    <tr>
                        <td>NIM</td>
                        <td>{{ $nimMahasiswa }}</td>
                    </tr>
                    <tr>
                        <td>Nama</td>
                        <td>{{ $namaMahasiswa }}</td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>{{ $prodiMahasiswa }}</td>
                    </tr>
                    <tr>
                        <td>Angkatan</td>
                        <td>{{ $angkatanMahasiswa }}</td>
                    </tr>
                </table>

                <div class="report-section-title" style="margin-top:1rem;">
                    <i class="ti ti-calendar-clock"></i>
                    Detail Jadwal
                </div>

                <table class="report-details">
                    <tr>
                        <td>Tanggal</td>
                        <td>{{ $tanggalJadwal }}</td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>{{ $waktuJadwal }} WIB</td>
                    </tr>
                </table>

                <div class="report-section-title" style="margin-top:1rem;">
                    <i class="ti ti-stethoscope"></i>
                    Layanan
                </div>

                <table class="report-details">
                    <tr>
                        <td>Layanan Konseling</td>
                        <td>{{ $layanan }}</td>
                    </tr>
                    <tr>
                        <td>Topik</td>
                        <td><span class="topic-value">{{ $topik ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><span class="report-pill">{{ $statusLabel }}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="report-panel">
            <div class="report-panel-head">
                <h5>Laporan</h5>
            </div>

            <div class="form-laporan">
                <form id="formLaporan" action="{{ route('admin.laporan.laporan.store', $jadwal->id) }}" method="POST" novalidate>
                    @csrf

                    <div class="form-block">
                        <label class="form-label" for="catatan">
                            <i class="ti ti-notes"></i>
                            Ringkasan Masalah
                        </label>

                        <textarea
                            id="catatan"
                            name="catatan"
                            class="form-textarea"
                            rows="5"
                            placeholder="Mahasiswa mengalami..."
                            @disabled($modeLihat)
                            required
                        >{{ $ringkasanMasalah }}</textarea>
                    </div>

                    <div class="form-block">
                    <label class="form-label" for="observasi_konselor">
                        <i class="ti ti-eye"></i>
                        Observasi Konselor
                    </label>

                    <textarea
                        id="observasi_konselor"
                        name="observasi_konselor"
                        class="form-textarea"
                        rows="5"
                        placeholder="Catatan observasi dari hasil konseling..."
                        @disabled($modeLihat)
                        required
                    >{{ $observasiKonselor }}</textarea>
                </div>

                    <div class="form-block">
                        <label class="form-label">
                            <i class="ti ti-progress"></i>
                            Progress Mahasiswa
                        </label>

                        <div class="option-list">
                            <label class="option-item">
                                <input
                                    type="radio"
                                    name="progress"
                                    value="Membaik"
                                    @disabled($modeLihat)
                                    @checked(old('progress') === 'Membaik' || ($modeLihat && $jadwal->progress === 'Membaik'))
                                >
                                Membaik
                            </label>

                            <label class="option-item">
                                <input
                                    type="radio"
                                    name="progress"
                                    value="Memburuk"
                                    @disabled($modeLihat)
                                    @checked(old('progress') === 'Memburuk' || ($modeLihat && $jadwal->progress === 'Memburuk'))
                                >
                                Memburuk
                            </label>
                        </div>
                    </div>

                    <div class="form-block">
                        <label class="form-label">
                            <i class="ti ti-sitemap"></i>
                            Tindak Lanjut
                        </label>

                        <div class="option-list">
                            <label class="option-item">
                                <input
                                    id="tindak_lanjut"
                                    type="checkbox"
                                    name="tindak_lanjut"
                                    value="on"
                                    @disabled($modeLihat)
                                    @checked(old('tindak_lanjut') === 'on' || ($modeLihat && $jadwal->tindak_lanjut))
                                >
                                Perlu sesi lanjutan
                            </label>
                        </div>
                    </div>

                    <div class="form-block" id="tanggal_tindak_lanjut_wrap" style="display: none;">
                        <label class="form-label">
                            <i class="ti ti-calendar"></i>
                            Pilih Tanggal
                        </label>

                        <input
                            id="tanggal_tindak_lanjut"
                            type="date"
                            class="form-control"
                            name="tanggal_tindak_lanjut"
                            value="{{ $tanggalTindakLanjut }}"
                            min="{{ $minTanggalTindakLanjut }}"
                            onkeydown="return false"
                            onpaste="return false"
                            @disabled($modeLihat)
                        >
                        @if(!$modeLihat)
                        {{-- Custom date picker --}}
                        <div class="custom-datepicker" id="customDatepicker">
                            <div class="cdp-input" id="cdpInput" onclick="toggleCdpCalendar()">
                                <span id="cdpDisplay">mm/dd/yyyy</span>
                                <i class="ti ti-calendar-event cdp-icon"></i>
                            </div>
                            <div class="cdp-calendar" id="cdpCalendar" style="display:none;">
                                <div class="cdp-header">
                                    <button type="button" class="cdp-nav" onclick="cdpChangeMonth(-1)">&#8593;</button>
                                    <span class="cdp-month-label" id="cdpMonthLabel"></span>
                                    <button type="button" class="cdp-nav" onclick="cdpChangeMonth(1)">&#8595;</button>
                                </div>
                                <div class="cdp-weekdays">
                                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                                </div>
                                <div class="cdp-days" id="cdpDays"></div>
                                <div class="cdp-footer">
                                    <button type="button" class="cdp-clear" onclick="cdpClear()">Clear</button>
                                    <button type="button" class="cdp-today" onclick="cdpGoToday()">Today</button>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="form-control" style="background:#f3f4f3;color:#6b7280;cursor:not-allowed;">
                            {{ $tanggalTindakLanjut ? \Carbon\Carbon::parse($tanggalTindakLanjut)->format('m/d/Y') : 'Tidak ada' }}
                        </div>
                        @endif
                    </div>
                    @if(!$modeLihat)
                        <div class="form-footer">
                            <button type="button" class="submit-btn" onclick="validateAndOpenLaporanModal()">
                                Simpan Laporan
                            </button>
                        </div>
                    @else
                        <div class="finish-wrap">
                            <a href="{{ route('admin.laporan.mahasiswa', $jadwal->mahasiswa_id) }}" class="submit-btn finish-btn">
                                Kembali
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<div class="confirm-overlay" id="confirmLaporanModal">
    <div class="confirm-box">
        <div class="confirm-icon">?</div>

        <h3>Konfirmasi Laporan</h3>

        <p>
            Apakah data laporan konseling sudah benar?<br>
            Pastikan ringkasan, progress, dan tindak lanjut sudah sesuai.
        </p>

        <div class="confirm-actions">
            <button type="button" class="btn-confirm" onclick="submitLaporan()">Simpan</button>
            <button type="button" class="btn-cancel" onclick="closeConfirmLaporanModal()">Batalkan</button>
        </div>
    </div>
</div>

<div class="confirm-overlay" id="validationLaporanModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>

        <h3>Data Belum Lengkap</h3>

        <p id="validationLaporanMessage">
            Mohon lengkapi data laporan terlebih dahulu.
        </p>

        <div class="confirm-actions">
            <button type="button" class="btn-confirm" onclick="closeValidationLaporanModal()">
                OK
            </button>
        </div>
    </div>
</div>

@if(session('success'))
<div class="success-overlay show" id="successLaporanModal">
    <div class="success-box">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>

        <h3>Laporan Berhasil Dibuat</h3>
        <p>{{ session('success') }}</p>

        <button type="button" class="btn-success-ok" onclick="closeSuccessLaporanModal()">
            OK
        </button>
    </div>
</div>
@endif
<script>
function showValidationLaporanModal(message) {
    const modal = document.getElementById('validationLaporanModal');
    const messageElement = document.getElementById('validationLaporanMessage');

    if (!modal || !messageElement) {
        alert(message);
        return;
    }

    messageElement.textContent = message;
    document.body.appendChild(modal);
    modal.classList.add('show');
}

function closeValidationLaporanModal() {
    const modal = document.getElementById('validationLaporanModal');

    if (modal) {
        modal.classList.remove('show');
    }
}

function validateAndOpenLaporanModal() {
    const ringkasanInput = document.getElementById('catatan');
    const observasiInput = document.getElementById('observasi_konselor');
    const progressInput = document.querySelector('input[name="progress"]:checked');
    const tindakLanjutInput = document.getElementById('tindak_lanjut');
    const tanggalInput = document.getElementById('tanggal_tindak_lanjut');

    const minTanggal = @json($minTanggalTindakLanjut);

    const ringkasan = ringkasanInput ? ringkasanInput.value.trim() : '';
    const observasi = observasiInput ? observasiInput.value.trim() : '';

    if (!ringkasan) {
        showValidationLaporanModal('Ringkasan masalah wajib diisi sebelum laporan dapat disimpan.');
        return;
    }

    if (!observasi) {
        showValidationLaporanModal('Observasi konselor wajib diisi sebelum laporan dapat disimpan.');
        return;
    }

    if (!progressInput) {
        showValidationLaporanModal('Silakan pilih progress mahasiswa sebelum menyimpan laporan.');
        return;
    }

    if (tindakLanjutInput && tindakLanjutInput.checked) {
        if (!tanggalInput || !tanggalInput.value) {
            showValidationLaporanModal('Silakan pilih tanggal tindak lanjut sebelum menyimpan laporan.');
            return;
        }

        if (tanggalInput.value < minTanggal) {
            tanggalInput.value = '';
            showValidationLaporanModal('Tanggal tindak lanjut tidak boleh menggunakan tanggal yang sudah lewat.');
            return;
        }
    }

    openConfirmLaporanModal();
}

function openConfirmLaporanModal() {
    const modal = document.getElementById('confirmLaporanModal');

    if (!modal) {
        return;
    }

    document.body.appendChild(modal);
    modal.classList.add('show');
}

function closeConfirmLaporanModal() {
    const modal = document.getElementById('confirmLaporanModal');

    if (modal) {
        modal.classList.remove('show');
    }
}

function submitLaporan() {
    const form = document.getElementById('formLaporan');

    if (!form) {
        return;
    }

    closeConfirmLaporanModal();
    form.submit();
}

function closeSuccessLaporanModal() {
    const modal = document.getElementById('successLaporanModal');

    if (modal) {
        modal.classList.remove('show');
    }

    window.location.href = "{{ route('admin.laporan') }}";
}

document.addEventListener('DOMContentLoaded', function () {
    const minTanggal = @json($minTanggalTindakLanjut);
    const tanggalInput = document.getElementById('tanggal_tindak_lanjut');
    const tindakLanjutInput = document.getElementById('tindak_lanjut');
    const tanggalWrap = document.getElementById('tanggal_tindak_lanjut_wrap');

    if (tanggalInput) {
        tanggalInput.setAttribute('min', minTanggal);

        if (tanggalInput.value && tanggalInput.value < minTanggal) {
            tanggalInput.value = '';
        }

        tanggalInput.addEventListener('input', function () {
            if (this.value && this.value < minTanggal) {
                this.value = '';
                showValidationLaporanModal('Tanggal tindak lanjut tidak boleh menggunakan tanggal yang sudah lewat.');
            }
        });

        tanggalInput.addEventListener('change', function () {
            if (this.value && this.value < minTanggal) {
                this.value = '';
                showValidationLaporanModal('Tanggal tindak lanjut tidak boleh menggunakan tanggal yang sudah lewat.');
            }
        });
    }

    if (tindakLanjutInput && tanggalInput && tanggalWrap) {
        const syncTanggalState = function () {
            if (tindakLanjutInput.checked) {
                tanggalWrap.style.display = 'block';
                tanggalInput.disabled = false;
                tanggalInput.setAttribute('min', minTanggal);

                if (tanggalInput.value && tanggalInput.value < minTanggal) {
                    tanggalInput.value = '';
                }
            } else {
                tanggalWrap.style.display = 'none';
                tanggalInput.disabled = true;
                tanggalInput.value = '';
            }
        };

        syncTanggalState();
        tindakLanjutInput.addEventListener('change', syncTanggalState);
    }
});
</script>
@endsection