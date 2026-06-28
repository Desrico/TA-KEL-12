@extends('layouts.admin')

@section('page-title', 'Laporan Hasil Konseling')
@section('page-hero')
{{-- Header H1 layout disembunyikan agar daftar/form laporan tidak memiliki judul dobel. --}}
<div hidden></div>
@endsection

@push('styles')
<style>
    /* ── LIST VIEW STYLES ── */
    .laporan-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 8px 22px rgba(6, 78, 59, 0.08);
        overflow: hidden;
        max-width: 1100px;
        margin: .75rem auto 0;
        width: calc(100% - 48px);
    }

    .laporan-table-wrap {
        padding: 1.2rem 1.2rem 1.35rem;
    }

    .laporan-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .82rem;
        border: 1px solid #d7e7de;
        border-radius: 16px;
        overflow: hidden;
    }

    .laporan-table thead th {
        background: #f5f8f7;
        color: #111827;
        font-weight: 700;
        padding: .72rem .9rem;
        border-bottom: 1px solid #d6e9de;
        font-size: .86rem;
        text-align: left;
    }

    .laporan-table tbody td {
        padding: .62rem .9rem;
        border-bottom: 1px solid #edf2ef;
        vertical-align: middle;
        color: #1f2937;
    }

    .laporan-table tbody tr:hover {
        background: #fbfefc;
    }

    .student-name {
        font-weight: 700;
        color: #111827;
    }

    .jadwal-text {
        line-height: 1.35;
        color: #334155;
        min-width: 120px;
    }

    .ringkasan-text {
        max-width: 340px;
        color: #374151;
        line-height: 1.35;
    }

    .btn-laporan {
        border: none;
        border-radius: 8px;
        padding: .3rem .9rem;
        font-size: .72rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        min-width: 74px;
    }

    .btn-buat {
        background: #065F46;
        color: #fff;
    }

    .btn-buat:hover {
        background: #064E3B;
        color: #fff;
    }

    .btn-lihat {
        background: #065F46;
        color: #fff;
    }

    .btn-lihat:hover {
        background: #064E3B;
        color: #fff;
    }

    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 2.5rem 1rem !important;
    }

    /* ── FORM VIEW STYLES ── */
    .laporan-form-container {
        display: grid;
        grid-template-columns: 1fr 1.3fr;
        gap: 24px;
        max-width: 1200px;
        margin: .75rem auto 0;
        width: calc(100% - 48px);
    }

    .detail-laporan-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 4px 16px rgba(0,0,0,.04);
        height: fit-content;
    }

    .detail-laporan-card h6 {
        font-size: .85rem;
        font-weight: 800;
        color: #064E3B;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e6f5ec;
    }

    .info-section {
        margin-bottom: 24px;
    }

    .info-group {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .info-label {
        font-size: .78rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        min-width: 100px;
    }

    .info-value {
        font-size: .88rem;
        font-weight: 600;
        color: #0f172a;
        text-align: right;
        max-width: 160px;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        background: #f0fdf4;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .student-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #059669, #10b981);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .student-details h5 {
        margin: 0 0 4px;
        font-size: .95rem;
        font-weight: 700;
        color: #0f172a;
    }

    .student-details p {
        margin: 0;
        font-size: .82rem;
        color: #64748b;
    }

    /* Laporan Form Styles */
    .laporan-form-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 4px 16px rgba(0,0,0,.04);
    }

    .laporan-form-card h6 {
        font-size: .85rem;
        font-weight: 800;
        color: #064E3B;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin: 0 0 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e6f5ec;
    }

    .form-section {
        margin-bottom: 28px;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .form-section-title {
        font-size: .9rem;
        font-weight: 700;
        color: #064E3B;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section-title svg {
        width: 18px;
        height: 18px;
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #059669;
    }

    .checkbox-item input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #059669;
    }

    .checkbox-item label {
        margin: 0;
        font-size: .86rem;
        color: #334155;
        cursor: pointer;
        font-weight: 500;
    }

    textarea.form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #dceee4;
        border-radius: 10px;
        font-size: .86rem;
        font-family: inherit;
        color: #0f172a;
        resize: vertical;
        min-height: 100px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    textarea.form-control:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .radio-item input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #059669;
    }

    .radio-item label {
        margin: 0;
        font-size: .86rem;
        color: #334155;
        cursor: pointer;
        font-weight: 500;
    }

    .date-picker-group {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 12px;
        padding-left: 28px;
    }

    .date-picker-group input[type="date"],
    .date-picker-group textarea {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #dceee4;
        border-radius: 8px;
        font-size: .86rem;
        color: #0f172a;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .date-picker-group textarea {
        min-height: 92px;
        resize: vertical;
    }

    .follow-up-description-group {
        flex-direction: column;
        align-items: stretch;
        padding-left: 0;
    }

    .follow-up-description-group .date-picker-label {
        white-space: normal;
    }

    .date-picker-group input[type="date"]:focus,
    .date-picker-group textarea:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .date-picker-group input[type="date"]:disabled,
    .date-picker-group textarea:disabled {
        background: #f5f5f5;
        cursor: not-allowed;
        opacity: 0.5;
    }

    .date-picker-label {
        font-size: .86rem;
        color: #334155;
        font-weight: 500;
        white-space: nowrap;
    }

    .follow-up-date-value {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #dceee4;
        border-radius: 8px;
        background: #f8fafc;
        color: #0f172a;
        font-size: .86rem;
        font-weight: 600;
        white-space: pre-wrap;
    }

    .btn-simpan {
        width: 100%;
        padding: 14px 20px;
        border: none;
        border-radius: 12px;
        background: #065F46;
        color: white;
        font-weight: 700;
        font-size: .9rem;
        cursor: pointer;
        transition: background .2s;
        margin-top: 20px;
    }

    .btn-simpan:hover {
        background: #064E3B;
    }

    .btn-kembali {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #475569;
        text-decoration: none;
        font-size: .85rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding: 8px 12px;
        border-radius: 8px;
        transition: background .2s, color .2s;
    }

    .btn-kembali:hover {
        background: #f0fdf4;
        color: #059669;
    }

    .confirm-overlay {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background: rgba(255, 248, 240, 0.72);
        backdrop-filter: none;
        z-index: 2000;
        padding: 24px;
    }

    .confirm-overlay.show {
        display: flex;
    }

    .confirm-box {
        width: min(310px, 100%);
        background: #0f6b53;
        border-radius: 16px;
        padding: 26px 22px 24px;
        text-align: center;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22);
        color: #fff;
    }

    .confirm-icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        border: 4px solid #fde68a;
        color: #fde68a;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.95rem;
        line-height: 1;
        margin-bottom: 14px;
    }

    .confirm-box h3 {
        margin: 0 0 10px;
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
    }

    .confirm-box p {
        margin: 0;
        font-size: .78rem;
        line-height: 1.35;
        color: rgba(255, 255, 255, 0.92);
    }

    .confirm-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 18px;
        flex-wrap: wrap;
    }

    .btn-confirm,
    .btn-cancel {
        min-width: 86px;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-weight: 700;
        font-size: .8rem;
        cursor: pointer;
        transition: transform .15s ease, background .2s ease, color .2s ease, border-color .2s ease;
    }

    .btn-confirm {
        background: #fde68a;
        color: #14532d;
    }

    .btn-confirm:hover {
        transform: translateY(-1px);
        background: #fcd34d;
    }

    .btn-cancel {
        background: transparent;
        border-color: rgba(255, 255, 255, 0.65);
        color: #fff;
    }

    .btn-cancel:hover {
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.08);
    }

    .laporan-search-area {
    padding: 1rem 1.7rem 0;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

    .laporan-toolbar {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .laporan-search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .laporan-search-icon {
        position: absolute;
        left: 16px;
        color: #8aa29a;
        font-size: 18px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .laporan-search-input {
        min-width: 300px;
        max-width: 420px;
        height: 46px;
        padding: 12px 16px 12px 46px;
        border-radius: 999px;
        border: 1px solid #e6f0ea;
        background: #fff;
        font-size: 14px;
        color: #0f172a;
        outline: none;
    }

    .laporan-search-input:focus {
        border-color: #9bd8bd;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.08);
    }

    .btn-search-laporan {
        height: 46px;
        padding: 0 24px;
        border-radius: 14px;
        border: 1px solid #e6f0ea;
        background: #fff;
        color: #065f46;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        line-height: 1;
    }

    .btn-search-laporan:hover {
        background: #f0fdf4;
        border-color: #bde8ce;
    }


    @media (max-width: 768px) {
        .laporan-toolbar {
            width: 100%;
            margin-left: 0;
        }

        .laporan-search-box {
            flex: 1;
        }

        .laporan-search-input {
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 991.98px) {
        .laporan-table-wrap {
            overflow-x: auto;
        }

        .laporan-table {
            min-width: 900px;
        }

        .laporan-form-container {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('konten')
@php
    // Check if this is showing detail form or list
    $isDetailForm = isset($jadwal);
    // Read-only hanya untuk jadwal yang sudah punya isi laporan.
    $isReadOnly = !empty($jadwal) && ($sudahAdaLaporan ?? false);
    $riwayat = $riwayat ?? collect();
    // Normalisasi state tindak lanjut dari data lama dan baru.
    $tindakLanjutRaw = trim((string) ($jadwal->tindak_lanjut ?? ''));
    $tindakLanjutTipeRaw = trim((string) ($jadwal->tindak_lanjut_tipe ?? ''));
    $isPerluLanjut = isset($jadwal)
        && in_array(strtolower(str_replace('_', ' ', $tindakLanjutTipeRaw ?: $tindakLanjutRaw)), ['perlu lanjut', 'perlu sesi lanjutan', 'on', '1', 'ya'], true);
    $legacyTindakLanjutLabels = ['perlu sesi lanjutan', 'tidak perlu sesi lanjutan', 'perlu lanjut'];
    $tindakLanjutDeskripsiValue = old('tindak_lanjut_deskripsi');

    if ($tindakLanjutDeskripsiValue === null && $isPerluLanjut) {
        // Kolom tindak_lanjut sekarang menyimpan keterangan sesi lanjutan, bukan sekadar label ya/tidak.
        $tindakLanjutDeskripsiValue = in_array(strtolower($tindakLanjutRaw), $legacyTindakLanjutLabels, true)
            ? ''
            : $tindakLanjutRaw;
    }
@endphp

@if($isDetailForm)
    <div class="laporan-form-container">
        {{-- LEFT: DETAIL LAPORAN --}}
        <div class="detail-laporan-card"> 

            {{-- Informasi Pribadi --}}
            <div class="info-section">
                <h6 style="font-size: .75rem;">Informasi Pribadi</h6>
                <div class="info-group">
                    <span class="info-label">NIM</span>
                    <span class="info-value">{{ optional($jadwal->mahasiswa)->nim ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ optional(optional($jadwal->mahasiswa)->user)->nama ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Program Studi</span>
                    <span class="info-value">{{ optional($jadwal->mahasiswa)->jurusan ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Angkatan</span>
                    <span class="info-value">{{ optional($jadwal->mahasiswa)->angkatan ?? '-' }}</span>
                </div>
            </div>

            {{-- Detail Jadwal --}}
            <div class="info-section">
                <h6 style="font-size: .75rem;">Detail Jadwal</h6>
                @php
                    $topikDetail = $jadwal->topik ?? null;
                @endphp
                <div class="info-group">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Waktu</span>
                    <span class="info-value">{{ substr($jadwal->waktu, 0, 5) }} WIB</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Layanan</span>
                    <span class="info-value">{{ ucfirst($jadwal->jenis ?? 'Online') }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Topik</span>
                    <span class="info-value">{{ $topikDetail ?? '-' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Status</span>
                    <span class="info-value" style="color: #059669; font-weight: 700;">Selesai</span>
                </div>
            </div>
        </div>

        {{-- RIGHT: LAPORAN FORM --}}
        <form id="laporanForm" action="{{ route('admin.laporan.laporan.store', $jadwal->id) }}" method="POST" class="laporan-form-card">
            @csrf
            <h6>📝 Laporan</h6>

            @if($isReadOnly)
                <div class="info-section" style="margin-bottom: 18px;">
                    <div class="info-value" style="text-align:left; max-width:none; color:#64748b; font-weight:600;">
                        Laporan sudah disimpan dan tidak dapat diubah lagi.
                    </div>
                </div>
            @endif

            {{-- Ringkasan Masalah --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="ti ti-circle-check"></i> Ringkasan Masalah
                </div>
                <textarea name="ringkasan_masalah" class="form-control" placeholder="Tuliskan ringkasan masalah yang dihadapi mahasiswa..." {{ $isReadOnly ? 'readonly' : '' }}>{{ old('ringkasan_masalah', $jadwal->ringkasan_masalah ?? '') }}</textarea>
            </div>

            {{-- Observasi Konselor --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="ti ti-eye"></i> Observasi Konselor
                </div>
                <textarea name="observasi_konselor" class="form-control" placeholder="Catatan observasi dari hasil konseling..." {{ $isReadOnly ? 'readonly' : '' }}>{{ old('observasi_konselor', $jadwal->observasi_konselor ?? '') }}</textarea>
            </div>

            {{-- Progress Mahasiswa --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="ti ti-trending-up"></i> Progress Mahasiswa
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="radio" id="membaik" name="progress" value="membaik" 
                            {{ old('progress', $jadwal->progress ?? '') === 'membaik' ? 'checked' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                        <label for="membaik">Membaik</label>
                    </div>
                    <div class="checkbox-item">
                        <input type="radio" id="memburuk" name="progress" value="memburuk" 
                            {{ old('progress', $jadwal->progress ?? '') === 'memburuk' ? 'checked' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                        <label for="memburuk">Memburuk</label>
                    </div>
                </div>
            </div>

            {{-- Tindak Lanjut --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="ti ti-arrow-right"></i> Tindak Lanjut
                </div>
                <div class="checkbox-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="perlu_lanjut" name="perlu_lanjut" value="1" 
                            {{ old('perlu_lanjut') === '1' || (!old() && $isPerluLanjut) ? 'checked' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                        <label for="perlu_lanjut">Perlu sesi lanjutan</label>
                    </div>
                </div>
                @if($isReadOnly)
                    @if($isPerluLanjut && trim((string) $tindakLanjutDeskripsiValue) !== '')
                        <div class="date-picker-group follow-up-description-group" id="followUpDescriptionGroup">
                            <span class="date-picker-label">Keterangan Sesi Lanjutan</span>
                            <span class="follow-up-date-value">{{ $tindakLanjutDeskripsiValue }}</span>
                        </div>
                    @endif
                @else
                    <div class="date-picker-group follow-up-description-group" id="followUpDescriptionGroup" style="display: none;">
                        <span class="date-picker-label">Keterangan Sesi Lanjutan</span>
                        <textarea
                            id="tindak_lanjut_deskripsi"
                            name="tindak_lanjut_deskripsi"
                            class="form-control"
                            rows="3"
                            maxlength="1000"
                            placeholder="Tuliskan rencana atau catatan sesi lanjutan bila diperlukan..."
                        >{{ $tindakLanjutDeskripsiValue }}</textarea>
                    </div>
                @endif
            </div>

            @if($isReadOnly)
                <!-- MODIFIED: Update rute tombol kembali ke halaman Detail Laporan Mahasiswa -->
                <a href="{{ route('admin.laporan.mahasiswa', $jadwal->mahasiswa_id) }}" class="btn-simpan" style="text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center;">
                    <i class="ti ti-arrow-left"></i>&nbsp;Kembali
                </a>
            @else
                <button type="button" class="btn-simpan" onclick="openReportConfirmModal()">
                    <i class="ti ti-check"></i> Simpan Laporan
                </button>
            @endif
        </form>
    </div>

    @unless($isReadOnly)
        <div class="confirm-overlay" id="reportConfirmModal" aria-hidden="true">
            <div class="confirm-box" role="dialog" aria-modal="true" aria-labelledby="reportConfirmTitle">
                <div class="confirm-icon">?</div>
                <h3 id="reportConfirmTitle">Konfirmasi Laporan</h3>
                <p>
                    Apakah kamu yakin data laporan yang diisi sudah sesuai?
                    Pastikan ringkasan masalah, observasi, progress, dan tindak lanjut sudah benar sebelum disimpan.
                </p>
                <div class="confirm-actions">
                    <button type="button" class="btn-confirm" onclick="confirmSaveLaporan()">Ya, Simpan</button>
                    <button type="button" class="btn-cancel" onclick="closeReportConfirmModal()">Batalkan</button>
                </div>
            </div>
        </div>

        <div class="confirm-overlay" id="reportValidationModal" aria-hidden="true">
    <div class="confirm-box" role="dialog" aria-modal="true">
        <div class="confirm-icon">!</div>

        <h3>Data Belum Lengkap</h3>

        <p id="reportValidationMessage">
            Mohon lengkapi data laporan terlebih dahulu.
        </p>

        <div class="confirm-actions">
            <button type="button" class="btn-confirm" onclick="closeReportValidationModal()">
                OK
            </button>
        </div>
    </div>
</div>
    @endunless

    @unless($isReadOnly)
<script>
    (function() {
        const perluLanjutCheckbox = document.getElementById('perlu_lanjut');
        const followUpDescriptionGroup = document.getElementById('followUpDescriptionGroup');
        const followUpDescriptionInput = document.getElementById('tindak_lanjut_deskripsi');
        const laporanForm = document.getElementById('laporanForm');
        const reportConfirmModal = document.getElementById('reportConfirmModal');
        const reportValidationModal = document.getElementById('reportValidationModal');
        const reportValidationMessage = document.getElementById('reportValidationMessage');

        function showReportValidationModal(message) {
            if (!reportValidationModal || !reportValidationMessage) {
                alert(message);
                return;
            }

            reportValidationMessage.textContent = message;
            reportValidationModal.classList.add('show');
            reportValidationModal.setAttribute('aria-hidden', 'false');
        }

        window.closeReportValidationModal = function() {
            if (reportValidationModal) {
                reportValidationModal.classList.remove('show');
                reportValidationModal.setAttribute('aria-hidden', 'true');
            }
        };

       function validateLaporanForm() {
    const ringkasanInput = document.querySelector('textarea[name="ringkasan_masalah"]');
    const progressInput = document.querySelector('input[name="progress"]:checked');

    const ringkasan = ringkasanInput ? ringkasanInput.value.trim() : '';

    if (!ringkasan) {
        showReportValidationModal('Ringkasan masalah wajib diisi sebelum laporan dapat disimpan.');
        setTimeout(() => ringkasanInput?.focus(), 200);
        return false;
    }

    if (!progressInput) {
        showReportValidationModal('Silakan pilih progress mahasiswa sebelum menyimpan laporan.');
        return false;
    }

    return true;
}

        function updateFollowUpDescriptionVisibility() {
            if (!followUpDescriptionGroup || !followUpDescriptionInput) {
                return;
            }

            if (perluLanjutCheckbox && perluLanjutCheckbox.checked) {
                followUpDescriptionGroup.style.display = 'flex';
                followUpDescriptionInput.disabled = false;
            } else {
                followUpDescriptionGroup.style.display = 'none';
                followUpDescriptionInput.disabled = true;
                followUpDescriptionInput.value = '';
            }
        }

        if (perluLanjutCheckbox) {
            perluLanjutCheckbox.addEventListener('change', updateFollowUpDescriptionVisibility);
        }

        window.openReportConfirmModal = function() {
            if (!validateLaporanForm()) {
                return;
            }

            if (reportConfirmModal) {
                reportConfirmModal.classList.add('show');
                reportConfirmModal.setAttribute('aria-hidden', 'false');
            }
        };

        window.closeReportConfirmModal = function() {
            if (reportConfirmModal) {
                reportConfirmModal.classList.remove('show');
                reportConfirmModal.setAttribute('aria-hidden', 'true');
            }
        };

        window.confirmSaveLaporan = function() {
            if (!validateLaporanForm()) {
                window.closeReportConfirmModal();
                return;
            }

            window.closeReportConfirmModal();

            if (laporanForm) {
                laporanForm.submit();
            }
        };

        if (reportConfirmModal) {
            reportConfirmModal.addEventListener('click', function(event) {
                if (event.target === reportConfirmModal) {
                    window.closeReportConfirmModal();
                }
            });
        }

        if (reportValidationModal) {
            reportValidationModal.addEventListener('click', function(event) {
                if (event.target === reportValidationModal) {
                    window.closeReportValidationModal();
                }
            });
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.closeReportConfirmModal();
                window.closeReportValidationModal();
            }
        });

        updateFollowUpDescriptionVisibility();
    })();
</script>
@endunless

@else
    {{-- LIST VIEW --}}
    <div class="laporan-card">
        <div class="laporan-search-area">
            <div class="laporan-toolbar">
                <div class="laporan-search-box">
                    <span class="laporan-search-icon">🔍</span>

                    <input 
                        id="laporanSearch" 
                        type="search" 
                        name="q" 
                        placeholder="Cari mahasiswa..." 
                        value="{{ old('q', request('q', '')) }}" 
                        class="laporan-search-input"
                    >
                </div>

                <button 
                    type="button" 
                    id="laporanSearchBtn" 
                    class="btn-search-laporan"
                >
                    Cari
                </button>
            </div>
        </div>

        <div class="laporan-table-wrap">
            <table class="laporan-table">
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Layanan</th>
                        <th>Topik</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $l)
                        @php
                            $nama = optional(optional($l->mahasiswa)->user)->nama ?? 'Anonim';
                            $nim = optional($l->mahasiswa)->nim ?? '-';

                            $ringkasan = trim((string) ($l->ringkasan_masalah ?? ''));
                            $topik = $l->topik ?? null;

                            if (!$topik && !empty($l->catatan) && preg_match('/Topik:\s*([^|]+)/i', $l->catatan, $match)) {
                                $topik = trim($match[1]);
                            }

                            if (!$topik && $ringkasan !== '' && preg_match('/Topik:\s*([^|]+)/i', $ringkasan, $match)) {
                                $topik = trim($match[1]);
                            }

                            if (!$topik && $ringkasan !== '') {
                                // Fallback untuk data lama yang topiknya tidak lagi tersimpan terpisah.
                                $topik = $ringkasan;
                            }

                            // Status selesai belum tentu laporan sudah diisi.
                            $sudahAdaLaporan = trim((string) ($l->laporan ?? '')) !== ''
                                || trim((string) ($l->ringkasan_masalah ?? '')) !== ''
                                || trim((string) ($l->observasi_konselor ?? '')) !== ''
                                || trim((string) optional($l->sesiKonseling?->laporan)->isi_laporan) !== '';
                        @endphp

                        <tr id="laporan-row-{{ $l->id }}">
                            <td>
                                <div class="student-name">{{ $nama }}</div>
                                <div class="student-sub">{{ $nim }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>{{ substr($l->waktu, 0, 5) }} WIB</td>
                            <td>{{ ucfirst($l->jenis ?? 'Online') }}</td>
                            <td>
                                <div class="topic-text">{{ $topik ?? '-' }}</div>
                            </td>
                            <td>
                                @if($sudahAdaLaporan)
                                    <span class="status-pill status-selesai">Laporan Tersedia</span>
                                @else
                                    <span class="status-pill status-belum">Belum Dilaporkan</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($sudahAdaLaporan)
                                    <a href="{{ route('admin.laporan.laporan', $l->id) }}"
                                       class="btn-laporan btn-lihat">
                                        <i class="ti ti-eye"></i>
                                        Lihat Laporan
                                    </a>
                                @else
                                    <a href="{{ route('admin.laporan.laporan', $l->id) }}"
                                       class="btn-laporan btn-buat">
                                        <i class="ti ti-file-plus"></i>
                                        Buat Laporan
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                Belum ada data konseling yang dapat dibuatkan laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($riwayat, 'links'))
            <div class="sesi-pagination" style="margin-top:18px; display:flex; justify-content:center;">
                {{ $riwayat->links('pagination::bootstrap-4') }}
            </div>
        @endif

        <script>
            (function () {
                const input = document.getElementById('laporanSearch');
                const clearBtn = document.getElementById('laporanClear');
                const tbody = document.querySelector('.laporan-table tbody');
                const baseAdmin = "{{ url('/admin') }}";

                function emptyRow() {
                    return `<tr><td colspan="7" class="empty-state">Tidak ada hasil pencarian.</td></tr>`;
                }

                function renderRows(items) {
                    if (!items || items.length === 0) return emptyRow();

                    return items.map(i => {
                        const actionLabel = i.laporan_available ? 'Lihat Laporan' : 'Buat Laporan';
                        const actionClass = i.laporan_available ? 'btn-lihat' : 'btn-buat';
                        return `
                            <tr>
                                <td>
                                    <div class="student-name">${escapeHtml(i.nama)}</div>
                                    <div class="student-sub">${escapeHtml(i.nim)}</div>
                                </td>
                                <td>${escapeHtml(i.tanggal)}</td>
                                <td>${escapeHtml(i.waktu)}</td>
                                <td>${escapeHtml(i.jenis)}</td>
                                <td>
                                    <div class="topic-text">${escapeHtml(i.topik)}</div>
                                </td>
                                <td>
                                    <span class="status-pill ${escapeHtml(i.status_class)}">${escapeHtml(i.status_label)}</span>
                                </td>
                                <td style="text-align:center;">
                                    <a href="${baseAdmin}/laporan/${i.id}/laporan" class="btn-laporan ${actionClass}">
                                        <i class="ti ti-eye"></i>
                                        ${actionLabel}
                                    </a>
                                </td>
                            </tr>`;
                    }).join('');
                }

                function escapeHtml(text) {
                    if (!text) return '';
                    return String(text)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                let timer = null;

                function doSearch(q) {
                    fetch(`${baseAdmin}/laporan/search?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(data => {
                            tbody.innerHTML = renderRows(data);
                        })
                        .catch(() => {
                            tbody.innerHTML = emptyRow();
                        });
                }

                input.addEventListener('input', function (e) {
                    const val = this.value.trim();
                    clearTimeout(timer);
                    timer = setTimeout(() => doSearch(val), 300);
                });
                // bind search button
                const searchBtn = document.getElementById('laporanSearchBtn');
                if (searchBtn) {
                    searchBtn.addEventListener('click', function () {
                        const v = input.value.trim();
                        doSearch(v);
                    });
                }

                // clear behavior when pressing ESC inside input
                input.addEventListener('keydown', function (ev) {
                    if (ev.key === 'Escape') {
                        input.value = '';
                        doSearch('');
                    }
                });

                // Initialize with current value (in case q prefilled)
                if (input.value && input.value.trim() !== '') {
                    doSearch(input.value.trim());
                }
            })();
        </script>

        @if(request()->filled('scroll_to'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const targetRow = document.getElementById('laporan-row-{{ request('scroll_to') }}');

                    if (targetRow) {
                        targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        targetRow.style.backgroundColor = '#f0fdf4';
                        targetRow.style.transition = 'background-color .25s ease';
                    }
                });
            </script>
        @endif
    </div>
@endif
@endsection
