@extends('layouts.admin')

@section('page-title', 'Laporan Hasil Konseling')

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
        margin: 0 auto;
        width: calc(100% - 48px);
    }

    .laporan-head {
        padding: 1.5rem 1.7rem 1rem;
        border-bottom: 1px solid #edf2ef;
    }

    .laporan-head h5 {
        margin: 0 0 .3rem 0;
        font-weight: 700;
        color: var(--admin-primary);
        font-size: 1.25rem;
        line-height: 1.2;
        letter-spacing: -0.3px;
    }

    .laporan-head h5 .accent {
        color: var(--admin-primary);
    }

    .laporan-head p {
        margin: 0;
        color: var(--admin-text-light);
        font-size: .85rem;
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
        align-items: center;
        gap: 12px;
        margin-top: 12px;
        padding-left: 28px;
    }

    .date-picker-group input[type="date"] {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #dceee4;
        border-radius: 8px;
        font-size: .86rem;
        color: #0f172a;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .date-picker-group input[type="date"]:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .date-picker-group input[type="date"]:disabled {
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
    $isReadOnly = !empty($jadwal) && (!empty($jadwal->laporan) || strtolower($jadwal->status ?? '') === 'selesai');
    $riwayat = $riwayat ?? collect();
@endphp

@if($isDetailForm)
    <div class="laporan-form-container">
        {{-- LEFT: DETAIL LAPORAN --}}
        <div class="detail-laporan-card">
            <h6>📋 Detail Laporan</h6>  

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
                    if (!$topikDetail && !empty($jadwal->catatan)) {
                        if (preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
                            $topikDetail = trim($match[1]);
                        }
                    }
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
                            {{ old('perlu_lanjut', $jadwal->tindak_lanjut_tipe ?? '') === '1' || $jadwal->tindak_lanjut_tipe === 'perlu_lanjut' ? 'checked' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                        <label for="perlu_lanjut">Perlu sesi lanjutan</label>
                    </div>
                </div>
                <div class="date-picker-group" id="datePickerGroup" style="display: none;">
                    <span class="date-picker-label">Pilih Tanggal</span>
                    <input type="date" id="tanggal_lanjut" name="tanggal_lanjut" 
                        value="{{ old('tanggal_lanjut', $jadwal->tanggal_lanjut ?? '') }}" {{ $isReadOnly ? 'disabled' : '' }}>
                </div>
            </div>

            @if($isReadOnly)
                <a href="{{ route('admin.laporan') }}" class="btn-simpan" style="text-align:center; text-decoration:none; display:flex; align-items:center; justify-content:center;">
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
    @endunless

    @unless($isReadOnly)
    <script>
        (function() {
            const perluLanjutCheckbox = document.getElementById('perlu_lanjut');
            const datePickerGroup = document.getElementById('datePickerGroup');
            const tanggalLanjutInput = document.getElementById('tanggal_lanjut');
            const progressRadios = document.querySelectorAll('input[name="progress"]');
            const laporanForm = document.getElementById('laporanForm');
            const reportConfirmModal = document.getElementById('reportConfirmModal');

            function updateDatePickerVisibility() {
                if (perluLanjutCheckbox && perluLanjutCheckbox.checked) {
                    datePickerGroup.style.display = 'flex';
                    if (tanggalLanjutInput) tanggalLanjutInput.required = true;
                } else {
                    datePickerGroup.style.display = 'none';
                    if (tanggalLanjutInput) tanggalLanjutInput.required = false;
                }
            }

            if (perluLanjutCheckbox) {
                perluLanjutCheckbox.addEventListener('change', updateDatePickerVisibility);
            }

            progressRadios.forEach(radio => {
                radio.addEventListener('pointerdown', function() {
                    this.dataset.wasChecked = this.checked ? '1' : '0';
                });

                radio.addEventListener('click', function() {
                    if (this.dataset.wasChecked === '1') {
                        this.checked = false;
                    }
                    updateDatePickerVisibility();
                });
            });

            window.openReportConfirmModal = function() {
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

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    window.closeReportConfirmModal();
                }
            });

            updateDatePickerVisibility();
        })();
    </script>
    @endunless

@else
    {{-- LIST VIEW --}}
    <div class="laporan-card">
        <div class="laporan-head">
            <div>
                <h5>Laporan Hasil Konseling</h5>
                <p>Dokumentasikan hasil sesi konseling dan perkembangan kondisi mahasiswa.</p>
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
                        <th>Ringkasan</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $l)
                        @php
                            $nama = optional(optional($l->mahasiswa)->user)->nama ?? 'Anonim';
                            $nim = optional($l->mahasiswa)->nim ?? '-';

                            $ringkasan = trim($l->ringkasan_masalah ?? '');
                            $topik = $l->topik ?? null;
                            if (!$topik && !empty($l->catatan)) {
                                if (preg_match('/Topik:\s*([^|]+)/i', $l->catatan, $match)) {
                                    $topik = trim($match[1]);
                                }
                            }

                            $sudahAdaLaporan = !empty($l->laporan) || strtolower($l->status ?? '') === 'selesai';
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
                                <div class="topic-text">{{ $ringkasan !== '' ? $ringkasan : ($topik ?? '-') }}</div>
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