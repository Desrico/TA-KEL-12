@extends('layouts.master')

@push('styles')
<style>
.detail-riwayat-page{
    background:#f3faf5;
    min-height:100vh;
    padding:32px 0 90px;
}

.detail-wrapper{
    width:min(1200px,92%);
    margin:auto;
}

.back-icon{
    width:36px;
    height:36px;
    border-radius:10px;
    background:#ffffff;
    color:#1f2937;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
    margin-bottom:24px;
    transition:.2s ease;
}

.back-icon:hover{
    transform:translateY(-2px);
    color:#1f2937;
}

.detail-hero{
    margin-bottom:32px;
}

.detail-hero h1{
     font-size: clamp(42px, 4.5vw, 52px);
    line-height:1.08;
    font-weight:900;
    color:#202020;
    max-width:760px;
    margin-bottom:20px;
    letter-spacing:-2px;
}

.detail-hero h1 span{
    color:#55765f;
}

.detail-hero p{
    max-width:540px;
    font-size:16px;
    line-height:1.8;
    color:#6b7280;
    margin:0;
}

.detail-grid{
    display:grid;
    grid-template-columns:320px 1fr;
    gap:42px;
    align-items:start;
}

.side-card,
.main-card{
    background:#ffffff;
    border-radius:28px;
    border:1px solid #e5e7eb;
    box-shadow:0 12px 24px rgba(15,23,42,.08);
}

.side-card{
    padding:28px;
}

.counselor-profile{
    display:flex;
    align-items:center;
    gap:16px;
    margin-bottom:26px;
}

.counselor-profile img{
    width:72px;
    height:72px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #dff5e5;
}

.counselor-profile h5{
    margin:0;
    font-size:22px;
    font-weight:800;
    color:#202020;
}

.counselor-profile p{
    margin:4px 0 0;
    color:#65806c;
    font-size:15px;
}

.side-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    padding:16px 0;
    border-bottom:1px solid #edf1f3;
}

.side-row:last-child{
    border-bottom:none;
}

.side-row span{
    color:#607165;
    font-size:15px;
    display:flex;
    align-items:center;
    gap:8px;
}

.side-row i{
    color:#4e6f59;
    font-size:15px;
}

.side-row strong{
    color:#202020;
    font-size:15px;
    text-align:right;
}

.note-card{
    margin-top:24px;
    background:#c9f0d3;
    border-radius:26px;
    padding:28px;
    color:#066344;
}

.note-card h5{
    font-size:26px;
    font-weight:800;
    margin-bottom:12px;
}

.note-card p{
    margin:0;
    line-height:1.9;
    font-size:16px;
}

.main-card{
    padding:34px;
}

.main-card h3{
    font-size 32px;
    font-weight:900;
    color:#202020;
    margin-bottom:28px;
    letter-spacing:-1px;
}

.section-label{
    display:flex;
    align-items:center;
    gap:12px;
    margin-top:26px;
    margin-bottom:10px;
    font-size:22px;
    font-weight:800;
    color:#111827;
}

.section-label:first-of-type{
    margin-top:0;
}

.section-label i{
    font-size:22px;
    color:#111827;
}

.detail-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    padding:18px 0;
    border-bottom:1px solid #edf1f3;
}

.detail-row:last-child{
    border-bottom:none;
}

.detail-row span{
    color:#505050;
    font-size:16px;
}

.detail-row strong{
    color:#202020;
    font-size:16px;
    font-weight:700;
    text-align:right;
}

.detail-action-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 0;
    padding-top: 0;
    border-top: none;
}

.btn-detail-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 160px;
    height: 42px;
    padding: 0 18px;
    border-radius: 999px;
    background: #047857;
    color: #ffffff !important;
    font-size: 13px;
    font-weight: 800;
    text-decoration: none !important;
    box-shadow: 0 18px 35px rgba(4, 120, 87, 0.22);
    transition: 0.2s ease;
}

.btn-detail-action:hover {
    background: #065f46;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.detail-cancel-form {
    margin: 0;
}

.btn-detail-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 160px;
    height: 42px;
    padding: 0 18px;
    border: 1px solid #FECACA;
    border-radius: 999px;
    background: #FEF2F2;
    color: #DC2626;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    cursor: pointer;
    transition: all .2s ease;
}

.btn-detail-cancel:hover {
    background: #FEE2E2;
    border-color: #FCA5A5;
    transform: translateY(-1px);
}

.counselor-avatar {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: #b7eaff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #064e3b;
    font-size: 34px;
    flex-shrink: 0;
}

.summary-box{
    margin-top:18px;
    background:#f9fbfa;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:22px;
}

.summary-box p{
    margin:0;
    color:#374151;
    line-height:1.8;
    font-size:16px;
}

.status-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:10px 18px;
    border-radius:999px;
    font-size:14px;
    font-weight:700;
}

.status-menunggu{
    background:#fff7d6;
    color:#a16207;
}

.status-disetujui{
    background:#dcfce7;
    color:#166534;
}

.status-ditolak{
    background:#fee2e2;
    color:#991b1b;
}

.status-selesai{
    background:#dbeafe;
    color:#1d4ed8;
}

.detail-action{
    margin-top:32px;
    display:flex;
    justify-content:center;
}

.detail-action .btn{
    min-width:260px;
    height:52px;
    border-radius:999px;
    font-weight:700;
    border:1px solid #d1d5db;
    background:#fff;
    color:#202020;
    transition:.2s ease;
}

.detail-action .btn:hover{
    background:#0f5f43;
    border-color:#0f5f43;
    color:#fff;
}

.btn-back-riwayat{
    min-width:260px;
    height:54px;
    border-radius:999px;
    background:#0f5f43;
    color:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    text-decoration:none;
    font-size:16px;
    font-weight:700;
    transition:.2s ease;
    box-shadow:0 10px 20px rgba(15,95,67,.18);
}

.btn-back-riwayat:hover{
    background:#0c4f38;
    color:#fff;
    transform:translateY(-2px);
}

.btn-back-riwayat i{
    font-size:15px;
}

.detail-action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 32px;
    padding-top: 0;
    border-top: none;
}

.btn-detail {
    min-width: 260px;
    height: 56px;
    border-radius: 999px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-back {
    background: #047857;
    color: #ffffff;
    border: 1px solid #047857;
    box-shadow: 0 10px 24px rgba(4, 120, 87, 0.18);
}

.btn-back:hover {
    background: #065f46;
    color: #ffffff;
    transform: translateY(-1px);
}

.btn-reschedule {
    background: #ffffff;
    color: #064e3b;
    border: 2px solid #064e3b;
}

.btn-reschedule:hover {
    background: #ecfdf5;
    border-color: #10b981;
    color: #065f46;
    transform: translateY(-1px);
}

.schedule-cancel-modal {
    position: fixed;
    inset: 0;
    z-index: 10060;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .24s ease, visibility .24s ease;
}

.schedule-cancel-modal.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.schedule-cancel-backdrop {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, .62);
    backdrop-filter: blur(4px);
}

.schedule-cancel-dialog {
    position: relative;
    width: min(420px, 100%);
    border-radius: 12px;
    background: #066145;
    box-shadow: 0 24px 80px rgba(6, 78, 59, .28);
    padding: 30px 28px;
    text-align: center;
    transform: translateY(14px) scale(.96);
    transition: transform .24s ease;
}

.schedule-cancel-modal.show .schedule-cancel-dialog {
    transform: translateY(0) scale(1);
}

.schedule-cancel-icon {
    width: 56px;
    height: 56px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    border: 4px solid #FDE68A;
    background: transparent;
    color: #FDE68A;
    font-size: 32px;
    font-weight: 900;
    line-height: 1;
}

.schedule-cancel-dialog h3 {
    margin: 0 0 18px;
    color: #FFFFFF;
    font-size: 23px;
    font-weight: 900;
}

.schedule-cancel-dialog p {
    margin: 0;
    color: rgba(255,255,255,.92);
    font-size: 13px;
    line-height: 1.45;
    font-weight: 700;
}

.schedule-cancel-actions {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 26px;
}

.schedule-cancel-btn {
    min-width: 110px;
    height: 38px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 900;
    cursor: pointer;
    transition: all .2s ease;
}

.schedule-cancel-btn.secondary {
    border: 2px solid rgba(255,255,255,.70);
    background: transparent;
    color: #FFFFFF;
}

.schedule-cancel-btn.danger {
    border: 2px solid #FDE68A;
    background: #FDE68A;
    color: #064E3B;
}

.schedule-cancel-btn:hover {
    transform: translateY(-1px);
}

/* ── Feedback Modal ── */
#feedbackModal {
    z-index: 10050 !important;
}

.modal-backdrop {
    z-index: 10049 !important;
}

#feedbackModal .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    overflow: hidden;
}

.fm-header {
    background: #1a4731;
    padding: 22px 24px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: none;
}

.fm-header-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 1.5px solid rgba(255,255,255,.28);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.fm-header-icon i {
    color: #fff;
    font-size: 16px;
}

.fm-header-title {
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 2px;
}

.fm-header-sub {
    font-size: 12px;
    color: rgba(255,255,255,.65);
    margin: 0;
}

#feedbackModal .modal-body {
    padding: 20px 24px 0;
}

.fm-desc {
    font-size: 13px;
    color: #607b6a;
    line-height: 1.65;
    margin: 0 0 16px;
}

/* Star rating */
.fm-stars {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 18px;
}

.fm-stars .star-btn {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    font-size: 26px;
    color: #d1d5db;
    line-height: 1;
    transition: color .12s, transform .1s;
}

.fm-stars .star-btn:hover,
.fm-stars .star-btn.active {
    color: #f59e0b;
}

.fm-stars .star-btn:active {
    transform: scale(.88);
}

.fm-star-label {
    font-size: 12.5px;
    color: #607b6a;
    margin-left: 6px;
    min-width: 80px;
}

/* Textarea */
.fm-label {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: #2d6a4f;
    margin-bottom: 8px;
}

.fm-textarea {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #c3dccb;
    border-radius: 12px;
    padding: 11px 13px;
    font-size: 13.5px;
    color: #1a2e22;
    background: #f8fdf9;
    resize: none;
    outline: none;
    line-height: 1.65;
    transition: border-color .15s, background .15s;
}

.fm-textarea:focus {
    border-color: #2d6a4f;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(45,106,79,.1);
}

.fm-textarea::placeholder {
    color: #9ab5a3;
}

.fm-char-count {
    display: flex;
    justify-content: flex-end;
    margin-top: 5px;
    font-size: 11px;
    color: #9ab5a3;
}

.fm-char-count.near-limit {
    color: #e24b4a;
}

/* Footer */
#feedbackModal .modal-footer {
    border-top: 1px solid #eef3f0;
    padding: 14px 24px 20px;
    gap: 10px;
}

.fm-btn-cancel {
    height: 38px;
    padding: 0 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    background: transparent;
    border: 1px solid #c3dccb;
    color: #607b6a;
    transition: background .12s;
    cursor: pointer;
}

.fm-btn-cancel:hover {
    background: #f0f7f3;
}

.fm-btn-submit {
    height: 38px;
    padding: 0 20px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    background: #1a4731;
    border: none;
    color: #fff;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background .12s, opacity .15s;
    cursor: pointer;
}

.fm-btn-submit:hover:not(:disabled) {
    background: #143a27;
}

.fm-btn-submit:disabled {
    opacity: .5;
    cursor: default;
}

@media (max-width: 768px) {
    .detail-action-buttons {
        flex-direction: column;
        gap: 12px;
    }

    .detail-action-wrapper {
        width: 100%;
        flex-direction: column;
    }

    .btn-detail {
        width: 100%;
        min-width: unset;
    }

    .btn-detail-action,
    .detail-cancel-form,
    .btn-detail-cancel {
        width: 100%;
        min-width: unset;
    }

    .schedule-cancel-dialog {
        padding: 24px 18px;
    }

    .schedule-cancel-actions {
        flex-direction: column-reverse;
    }

    .schedule-cancel-btn {
        width: 100%;
    }
}

@media (max-width:1200px){
    .detail-hero h1{
        font-size:54px;
    }
}

@media (max-width:992px){
    .detail-grid{
        grid-template-columns:1fr;
    }

    .detail-hero h1{
        font-size:44px;
    }

    .main-card h3{
        font-size:34px;
    }
}

@media (max-width:768px){
    .detail-riwayat-page{
        padding:24px 0 70px;
    }

    .detail-wrapper{
        width:94%;
    }

    .detail-hero h1{
        font-size:36px;
        line-height:1.1;
    }

    .detail-hero p{
        font-size:16px;
    }

    .main-card,
    .side-card,
    .note-card{
        border-radius:22px;
    }

    .main-card{
        padding:24px;
    }

    .section-label{
        font-size:22px;
    }

    .detail-row{
        flex-direction:column;
        align-items:flex-start;
        gap:8px;
    }

    .detail-row strong{
        text-align:left;
    }
}
</style>
@endpush

@section('konten')
@php
    use Carbon\Carbon;

    $mahasiswa = $jadwal->mahasiswa;
    $userMahasiswa = optional($mahasiswa)->user;
    $konselorUser = optional(optional($jadwal->konselor)->user);

    $isAnonim = filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

    $namaTampil = $isAnonim
        ? (
            $userMahasiswa && method_exists($userMahasiswa, 'getAnonimDisplayName')
                ? trim($userMahasiswa->getAnonimDisplayName())
                : 'Anonim'
          )
        : ($userMahasiswa->nama ?? 'Mahasiswa');

    $namaTampil = $namaTampil ?: 'Anonim';

    $nimTampil = $isAnonim
        ? '-'
        : ($mahasiswa->nim ?? '-');

    $tanggal = $jadwal->tanggal
        ? Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y')
        : '-';

    $waktu = $jadwal->waktu
        ? Carbon::parse($jadwal->waktu)->format('H.i')
        : '-';

    $metode = $jadwal->metode ?? $jadwal->jenis ?? 'Online';

    $namaKonselor = env('CIS_KONSELOR_NAME', $konselorUser->nama ?? 'Konselor');

    $jenisKonseling = strtolower($jadwal->jenis ?? $jadwal->metode ?? 'online');

    $isOnline = $jenisKonseling === 'online';

    $mediaTampil = $isOnline ? 'Chat' : 'Tatap Muka';

    $lokasiTampil = $isOnline
        ? 'Jarak Jauh<br>Akses dari ruang personalmu'
        : 'Gedung 5, Lt. 2<br>Antara GD 525 & 526';

    $feedback = $feedback ?? optional($jadwal->sesiKonseling)->feedback;
    $bisaFeedback = $bisaFeedback ?? ($jadwal->status === 'selesai' && $jadwal->sesiKonseling && !$feedback);
    $statusNormalized = strtolower(str_replace(' ', '_', trim((string) ($jadwal->status ?? ''))));
    $bisaBatalkan = in_array($statusNormalized, ['menunggu', 'menunggu_konfirmasi'], true);

    $tanggalLanjutLabel = $tanggalLanjut
        ? Carbon::parse($tanggalLanjut)->translatedFormat('d F Y')
        : '-';
@endphp

<section class="detail-riwayat-page">
    <div class="detail-wrapper">

        <div class="detail-hero">
            <h1>
                Detail <span>Riwayatmu</span><br>
                Bersama Konselor.
            </h1>
            <p>
                Lihat kembali detail jadwal konseling yang pernah Anda ajukan.
                Informasi ini tersimpan dengan aman di sistem Campus Care.
            </p>
        </div>

        <div class="detail-grid">

            <aside>
                <div class="side-card">
                    <div class="counselor-profile">
                        <div class="counselor-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div>
                            <h5>{{ $namaKonselor }}</h5>
                            <p>Konselor</p>
                        </div>
                    </div>

                    <div class="side-row">
                        <span><i class="bi bi-stopwatch"></i> Durasi</span>
                        <strong>{{ $jadwal->durasi ?? '60 Menit' }}</strong>
                    </div>

                    <div class="side-row">
                        <span><i class="bi bi-camera-video"></i> Media</span>
                        <strong>{{ $mediaTampil }}</strong>
                    </div>

                    <div class="side-row">
                        <span><i class="bi bi-geo-alt-fill"></i> Lokasi</span>
                        <strong>{!! $lokasiTampil !!}</strong>
                    </div>
                </div>

                <div class="note-card">
                    <h5>Catatan Sesi</h5>
                    <p>
                        Informasi ini merupakan riwayat konseling Anda.
                        Jika membutuhkan dukungan lanjutan, Anda dapat menjadwalkan sesi baru.
                    </p>
                </div>
            </aside>

            <main class="main-card">
                <h3>Detail Riwayat Konseling</h3>

                <div class="section-label">
                    <i class="bi bi-person-fill"></i>
                    <span>Informasi Pribadi</span>
                </div>

                <div class="detail-row">
                    <span>NIM</span>
                    <strong>{{ $nimTampil }}</strong>
                </div>

                <div class="detail-row">
                    <span>Nama</span>
                    <strong>{{ $namaTampil }}</strong>
                </div>

                <div class="detail-row">
                    <span>Program Studi</span>
                    <strong>{{ $mahasiswa->jurusan ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Angkatan</span>
                    <strong>{{ $mahasiswa->angkatan ?? '-' }}</strong>
                </div>

                <div class="section-label mt-4">
                    <i class="bi bi-clock"></i>
                    <span>Detail Jadwal</span>
                </div>

                <div class="detail-row">
                    <span>Tanggal</span>
                    <strong>{{ $tanggal }}</strong>
                </div>

                <div class="detail-row">
                    <span>Waktu</span>
                    <strong>{{ $waktu }}</strong>
                </div>

                <div class="section-label mt-4">
                    <i class="bi bi-headset"></i>
                    <span>Layanan</span>
                </div>

                <div class="detail-row">
                    <span>Layanan Konseling</span>
                    <strong>{{ ucfirst($metode) }}</strong>
                </div>

                <div class="detail-row">
                    <span>Topik</span>
                    <strong>{{ $jadwal->topik ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Status</span>
                    <strong>{{ ucwords(str_replace('_', ' ', $jadwal->status ?? '-')) }}</strong>
                </div>

                <div class="section-label mt-4">
                    <span>Tindak Lanjut</span>
                </div>

                <div class="detail-row">
                    <span>Status Tindak Lanjut</span>
                    <strong>{{ $tindakLanjut }}</strong>
                </div>

                @if($perluSesiLanjutan)
                    <div class="detail-row">
                        <span>Tanggal Sesi Lanjutan</span>
                        <strong>{{ $tanggalLanjutLabel }}</strong>
                    </div>
                @endif

                <div class="detail-action-buttons">
                    @if($bisaFeedback)
                        <button
                            type="button"
                            class="btn-detail btn-back"
                            data-bs-toggle="modal"
                            data-bs-target="#feedbackModal"
                        >
                            Berikan Ulasan
                        </button>
                    @else
                        @php
                            $statusJadwal = strtolower($jadwal->status ?? '');
                            $statusSesi = strtolower($sesi->status ?? '');
                            $jenisKonseling = strtolower($jadwal->jenis ?? $jadwal->jenis_konseling ?? '');

                            $isSelesai = $statusJadwal === 'selesai' || $statusSesi === 'selesai';
                            $isOnline = $jenisKonseling === 'online';

                            $sudahMemberiUlasan = isset($feedback) && $feedback;
                        @endphp

                        <div class="detail-action-wrapper">
                            @if($isSelesai && $isOnline && $sudahMemberiUlasan)
                                <a href="{{ url('/chat?jadwal_id=' . $jadwal->id) }}" class="btn-detail-action">
                                    Lihat Riwayat Chat
                                </a>
                            @else
                                <a href="{{ route('riwayat') }}" class="btn-detail-action">
                                    Kembali ke Riwayat
                                </a>
                            @endif

                            @if($bisaBatalkan)
                                <form action="{{ route('riwayat.batalkan', $jadwal->id) }}"
                                      method="POST"
                                      class="detail-cancel-form"
                                      data-cancel-schedule-form>
                                    @csrf
                                    <button type="submit" class="btn-detail-cancel">
                                        Batalkan Penjadwalan
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    @if ($jadwal->status === 'perlu_penjadwalan_ulang')
                        <a href="{{ route('konseling.jadwal_ulang.edit', $jadwal->id) }}" class="btn-detail btn-reschedule">
                            Jadwalkan Ulang
                        </a>
                    @endif
                </div>
            </main>

        </div>
    </div>
</section>

<div class="schedule-cancel-modal" id="scheduleCancelModal" aria-hidden="true">
    <div class="schedule-cancel-backdrop" data-cancel-modal-close></div>
    <div class="schedule-cancel-dialog" role="dialog" aria-modal="true" aria-labelledby="scheduleCancelTitle">
        <div class="schedule-cancel-icon">?</div>
        <h3 id="scheduleCancelTitle">Konfirmasi Pembatalan</h3>
        <p>
            Apakah kamu yakin ingin membatalkan penjadwalan konseling ini?
            Pastikan keputusanmu sudah sesuai.
        </p>

        <div class="schedule-cancel-actions">
            <button type="button" class="schedule-cancel-btn secondary" data-cancel-modal-close>
                Batalkan
            </button>
            <button type="button" class="schedule-cancel-btn danger" id="confirmCancelSchedule">
                Ya, Batalkan
            </button>
        </div>
    </div>
</div>
@endsection

@if($bisaFeedback)
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true" aria-labelledby="feedbackModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('mahasiswa.feedback.store') }}" method="POST" id="feedbackForm">
                @csrf
                <input type="hidden" name="sesi_id" value="{{ $jadwal->sesiKonseling->id }}">
                <input type="hidden" name="rating" id="rating-input" value="0">

                {{-- Header --}}
                <div class="modal-header border-0 fm-header">
                    <div>
                        <p class="fm-header-title" id="feedbackModalLabel">Berikan Ulasan Konseling</p>
                        <p class="fm-header-sub">Sesi selesai · {{ $tanggal }}</p>
                    </div>
                </div>

                {{-- Body --}}
                <div class="modal-body">
                    <p class="fm-desc">
                        Ceritakan pengalamanmu setelah mengikuti sesi konseling ini.
                        Ulasanmu membantu kami meningkatkan layanan.
                    </p>

                    {{-- Textarea --}}
                    <textarea
                        id="isi_feedback"
                        name="isi_feedback"
                        class="fm-textarea"
                        rows="4"
                        maxlength="500"
                        placeholder="Tuliskan ulasan kamu di sini..."
                        required
                    ></textarea>
                    <div class="fm-char-count" id="fm-char-wrap">
                       <span id="fm-char">0 / 500</span> 
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="fm-btn-cancel" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="fm-btn-submit" id="fm-submit" disabled>
                        <i class="bi bi-send"></i> Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
let isEditMode = false;

document.addEventListener('DOMContentLoaded', function () {
    const cancelModal = document.getElementById('scheduleCancelModal');
    const confirmCancelButton = document.getElementById('confirmCancelSchedule');
    let pendingCancelForm = null;

    if (cancelModal && cancelModal.parentElement !== document.body) {
        document.body.appendChild(cancelModal);
    }

    function openCancelModal(form) {
        pendingCancelForm = form;
        cancelModal?.classList.add('show');
        cancelModal?.setAttribute('aria-hidden', 'false');
    }

    function closeCancelModal() {
        cancelModal?.classList.remove('show');
        cancelModal?.setAttribute('aria-hidden', 'true');
        pendingCancelForm = null;
    }

    document.querySelectorAll('[data-cancel-schedule-form]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            openCancelModal(form);
        });
    });

    document.querySelectorAll('[data-cancel-modal-close]').forEach(function (button) {
        button.addEventListener('click', closeCancelModal);
    });

    if (confirmCancelButton) {
        confirmCancelButton.addEventListener('click', function () {
            if (!pendingCancelForm) {
                return;
            }

            const form = pendingCancelForm;
            pendingCancelForm = null;
            confirmCancelButton.disabled = true;
            confirmCancelButton.textContent = 'Membatalkan...';
            form.submit();
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && cancelModal?.classList.contains('show')) {
            closeCancelModal();
        }
    });

    const hideIds = ['edit-nama', 'edit-nim', 'edit-jurusan', 'edit-angkatan'];
    hideIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    const saveBtn = document.getElementById('save-btn-wrap');
    if (saveBtn) saveBtn.style.display = 'none';

    ['view-nama', 'view-nim', 'view-jurusan', 'view-angkatan'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'flex';
    });

    setAvatarEditState(false);
    isEditMode = false;

    // Feedback modal logic
    const textarea  = document.getElementById('isi_feedback');
    const charEl    = document.getElementById('fm-char');
    const charWrap  = document.getElementById('fm-char-wrap');
    const submitBtn = document.getElementById('fm-submit');
    const modal     = document.getElementById('feedbackModal');

    function checkReady() {
        if (!textarea || !submitBtn) return;

        const isiFeedback = textarea.value.trim();

        // Tombol aktif kalau ulasan tidak kosong
        submitBtn.disabled = isiFeedback.length === 0;
    }

    if (textarea) {
        textarea.addEventListener('input', function () {
            const len = Math.min(textarea.value.length, 500);

            if (charEl) {
                charEl.textContent = len + ' / 500';
            }

            if (charWrap) {
                charWrap.classList.toggle('near-limit', len >= 480);
            }

            checkReady();
        });
    }

    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            if (textarea) textarea.value = '';
            if (charEl) charEl.textContent = '0 / 500';
            if (charWrap) charWrap.classList.remove('near-limit');
            if (submitBtn) submitBtn.disabled = true;
        });
    }
});

function toggleEdit() {
    isEditMode = !isEditMode;

    ['nama', 'nim', 'jurusan', 'angkatan'].forEach(field => {
        const viewEl = document.getElementById(`view-${field}`);
        const editEl = document.getElementById(`edit-${field}`);

        if (viewEl) viewEl.style.display = isEditMode ? 'none' : 'flex';
        if (editEl) editEl.style.display = isEditMode ? 'block' : 'none';
    });

    const saveBtn = document.getElementById('save-btn-wrap');
    if (saveBtn) saveBtn.style.display = isEditMode ? 'flex' : 'none';

    const editIcon = document.getElementById('edit-icon');
    const editText = document.getElementById('edit-btn-text');

    if (editIcon) {
        editIcon.className = isEditMode ? 'bi bi-x-lg me-1' : 'bi bi-pencil-fill me-1';
    }

    if (editText) {
        editText.textContent = isEditMode ? 'Batal Edit' : 'Edit Profil';
    }

    setAvatarEditState(isEditMode);
}

function setAvatarEditState(editable) {
    const avatar = document.getElementById('avatar-preview-wrap');
    const fotoInput = document.getElementById('foto-input');

    if (!avatar || !fotoInput) return;

    if (editable) {
        avatar.classList.add('editable');
        avatar.onclick = function () {
            fotoInput.click();
        };
    } else {
        avatar.classList.remove('editable');
        avatar.onclick = null;
    }
}

function previewFoto(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const wrap = document.getElementById('avatar-preview-wrap');

        if (!wrap) return;

        wrap.innerHTML = `
            <img src="${e.target.result}" alt="Preview Foto Profil">
            <div class="profil-avatar-overlay" id="avatar-overlay">
              <i class="bi bi-camera"></i>
              <span>Ganti Foto</span>
            </div>
        `;

        if (isEditMode) {
            wrap.classList.add('editable');
            wrap.onclick = function () {
                document.getElementById('foto-input').click();
            };
        }
    };

    reader.readAsDataURL(file);
}
</script>
@endpush
