@extends('layouts.admin')

@section('page-title', 'Laporan Konseling')

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

    $tanggalTindakLanjut = old('tanggal_tindak_lanjut');

    if (!$tanggalTindakLanjut && !empty($jadwal->tanggal_lanjut)) {
        $tanggalTindakLanjut = $jadwal->tanggal_lanjut;
    }

    $ringkasanMasalah = old('catatan');

    if ($ringkasanMasalah === null) {
        $ringkasanMasalah = $modeLihat
            ? ($jadwal->ringkasan_masalah ?? $jadwal->catatan ?? '')
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
                <form id="formLaporan" action="{{ route('admin.laporan.laporan.store', $jadwal->id) }}" method="POST">
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
                        <label class="form-label">
                            <i class="ti ti-progress"></i>
                            Progress Mahasiswa
                        </label>

                        <div class="option-list">
                            <label class="option-item">
                                <input
                                    type="checkbox"
                                    name="progress"
                                    value="Membaik"
                                    @disabled($modeLihat)
                                    @checked(old('progress') === 'Membaik' || ($modeLihat && $jadwal->progress === 'Membaik'))
                                >
                                Membaik
                            </label>

                            <label class="option-item">
                                <input
                                    type="checkbox"
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
                        <label class="form-label" for="tanggal_tindak_lanjut">
                            <i class="ti ti-calendar"></i>
                            Pilih Tanggal
                        </label>

                        <input
                            id="tanggal_tindak_lanjut"
                            type="date"
                            class="form-control"
                            name="tanggal_tindak_lanjut"
                            value="{{ $tanggalTindakLanjut }}"
                            @disabled($modeLihat)
                        >
                    </div>
                    @if(!$modeLihat)
                        <div class="form-footer">
                            <button type="button" class="submit-btn" onclick="openConfirmLaporanModal()">
                                Simpan Laporan
                            </button>
                        </div>
                    @else
                        <!-- MODIFIED: Fix button kembali untuk direct ke detail laporan mahasiswa, bukan ke daftar laporan -->
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

@if(session('success'))
<div class="success-overlay show" id="successLaporanModal">
    <div class="success-box">
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>

        <h3>Laporan Berhasil Dibuat</h3>
        <p>{{ session('success') }}</p>

        <button type="button" class="btn-success-ok" onclick="closeSuccessLaporanModal()">
            Mengerti
        </button>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openConfirmLaporanModal() {
    const modal = document.getElementById('confirmLaporanModal');
    document.body.appendChild(modal);
    modal.classList.add('show');
}

function closeConfirmLaporanModal() {
    document.getElementById('confirmLaporanModal').classList.remove('show');
}

function submitLaporan() {
    closeConfirmLaporanModal();
    document.getElementById('formLaporan').submit();
}

function closeSuccessLaporanModal() {
    document.getElementById('successLaporanModal').classList.remove('show');
    window.location.href = "{{ route('admin.laporan') }}";
}

document.addEventListener('DOMContentLoaded', function () {
    const isModeLihat = @json($modeLihat);
    const progressCheckboxes = document.querySelectorAll('input[name="progress"]');
    const tindakLanjutCheckbox = document.getElementById('tindak_lanjut');
    const tanggalInput = document.getElementById('tanggal_tindak_lanjut');

    progressCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (!this.checked) {
                return;
            }

            progressCheckboxes.forEach(function (otherCheckbox) {
                if (otherCheckbox !== checkbox) {
                    otherCheckbox.checked = false;
                }
            });
        });
    });

   const tanggalWrap = document.getElementById('tanggal_tindak_lanjut_wrap');

    if (tindakLanjutCheckbox && tanggalInput && tanggalWrap) {
        const syncTanggalState = function () {
            if (tindakLanjutCheckbox.checked) {
                tanggalWrap.style.display = 'block';
                tanggalInput.disabled = isModeLihat;
            } else {
                tanggalWrap.style.display = 'none';
                tanggalInput.disabled = true;

                if (!isModeLihat) {
                    tanggalInput.value = '';
                }
            }
        };

        syncTanggalState();

        if (!isModeLihat) {
            tindakLanjutCheckbox.addEventListener('change', syncTanggalState);
        }
    }
});
</script>
@endpush