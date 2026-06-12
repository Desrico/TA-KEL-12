@extends('layouts.master')

@section('konten')
<div class="riwayat-page">

    <!-- HEADER -->
    <div class="riwayat-header">
        <h1>Riwayat <span>Konseling</span> Anda.</h1>
        <p>
            Pantau dan tinjau kembali seluruh sesi yang telah Anda lalui.
            Privasi dan data Anda tersimpan dengan aman dalam sistem kami.
        </p>
    </div>
    @if(session('success'))
        <div class="riwayat-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="riwayat-alert error">
            {{ session('error') }}
        </div>
    @endif

    <div class="riwayat-content">

        <!-- LEFT: LIST -->
        <div class="riwayat-list">

           @foreach($riwayat as $item)
@php
    $status = strtolower(trim($item->status ?? ''));
    $status = str_replace(' ', '_', $status);

    $statusLabel = match($status) {
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
        'disetujui', 'diterima' => 'Diterima',
        'menunggu', 'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'berlangsung', 'sedang_berlangsung' => 'Sedang Berlangsung',
        'perlu_penjadwalan_ulang' => 'Perlu Penjadwalan Ulang',
        default => ucfirst(str_replace('_', ' ', $item->status ?? '-'))
    };

    $statusClass = match($status) {
        'selesai' => 'status-selesai',
        'ditolak' => 'status-ditolak',
        'dibatalkan' => 'status-dibatalkan',
        'disetujui', 'diterima' => 'status-diterima',
        'menunggu', 'menunggu_konfirmasi' => 'status-menunggu',
        'berlangsung', 'sedang_berlangsung' => 'status-berlangsung',
        'perlu_penjadwalan_ulang' => 'status-reschedule',
        default => 'status-default'
    };

    $topikText = '-';

    if (!empty($item->topik)) {
        $topikText = $item->topik;
    } elseif (!empty($item->catatan) && str_contains($item->catatan, 'Topik:')) {
        $parts = explode('|', $item->catatan);
        $topikText = trim(str_replace('Topik:', '', $parts[0]));
    }

    $sesi = $item->sesiKonseling;
    $feedback = $sesi?->feedback;
    $bisaFeedback = $status === 'selesai' && $sesi;
@endphp

    <div class="riwayat-card">
        <div class="card-left">
            @php
    $userMahasiswa = $item->mahasiswa?->user;
    $isAnonim = filter_var($item->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

    if ($isAnonim) {
        $namaMahasiswa = method_exists($userMahasiswa, 'getAnonimDisplayName')
            ? trim($userMahasiswa->getAnonimDisplayName())
            : 'Anonim';

        $namaMahasiswa = $namaMahasiswa ?: 'Anonim';
    } else {
        $namaMahasiswa =
            $userMahasiswa?->name
            ?? $userMahasiswa?->nama
            ?? $userMahasiswa?->nama_lengkap
            ?? $item->mahasiswa?->nama
            ?? $item->mahasiswa?->nama_lengkap
            ?? auth()->user()->name
            ?? auth()->user()->nama
            ?? auth()->user()->nama_lengkap
            ?? 'Mahasiswa';
    }

    $inisialMahasiswa = strtoupper(substr($namaMahasiswa, 0, 1));
@endphp
    

            <div class="avatar">
                {{ $inisialMahasiswa }}
            </div>

            <div class="info">
                <h3>{{ $namaMahasiswa }}</h3>
                <p>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }} • {{ substr($item->waktu ?? '00:00', 0, 5) }} WIB</p>
            </div>
        </div>

        <div class="card-middle">
            <div class="meta-col">
                <small>DURASI</small>
                <strong>{{ $item->durasi ?? '60 Menit' }}</strong>
            </div>

            <div class="meta-col meta-topic">
                <small>TOPIK</small>
                <strong>{{ \Illuminate\Support\Str::limit($topikText, 45, '...') }}</strong>
            </div>
        </div>

        <div class="card-right">
            <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>

            <a href="{{ route('detail.riwayat', $item->id) }}" class="btn-riwayat">
                Lihat Riwayat
            </a>
        </div>
    </div>
            @endforeach

            @if(is_object($riwayat) && method_exists($riwayat, 'hasPages') && $riwayat->hasPages())
                <div class="simple-pagination">

                    @if(method_exists($riwayat,'onFirstPage') && !$riwayat->onFirstPage())
                        <a href="{{ $riwayat->previousPageUrl() }}" class="simple-next">
                            ‹
                        </a>
                    @endif

                    @for($i = 1; $i <= $riwayat->lastPage(); $i++)
                        <a href="{{ $riwayat->url($i) }}"
                        class="simple-page {{ $riwayat->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if(method_exists($riwayat,'hasMorePages') && $riwayat->hasMorePages())
                        <a href="{{ $riwayat->nextPageUrl() }}" class="simple-next">
                            >
                        </a>
                    @endif

                </div>
            @endif

        </div>
    </div>
</div>

<style>
.riwayat-page {
    padding: 60px 60px;
    background: #F6FBF8;
    min-height: 100vh;
    overflow-x: hidden;
    box-sizing: border-box;
}

/* HEADER */
.riwayat-header h1 {
    font-size: 42px;
    line-height: 1.1;
    font-weight: 800;
    margin-bottom: 16px;
    color: #111827;
}

.riwayat-header h1 span {
    color: #064E3B;
}

.riwayat-header p {
    color: #6B7280;
    max-width: 680px;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 45px;
}

.riwayat-alert {
    max-width: 100%;
    margin: 0 0 20px;
    padding: 14px 18px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
}

.riwayat-alert.success {
    background: #ECFDF5;
    color: #047857;
    border: 1px solid #A7F3D0;
}

.riwayat-alert.error {
    background: #FEF2F2;
    color: #B91C1C;
    border: 1px solid #FECACA;
}

/* MAIN LAYOUT */
.riwayat-content {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 0;
    align-items: flex-start;
}

.riwayat-list {
    min-width: 0;
}

.riwayat-sidebar {
    width: 280px;
    min-width: 280px;
}

/* CARD */
.riwayat-card {
    display: grid;
    grid-template-columns: minmax(250px, 280px) minmax(0, 1fr) minmax(340px, 400px);
    align-items: center;
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
    padding: 20px 24px;
    border-radius: 20px;
    border: 2px solid #E8F0EB;
    margin-bottom: 14px;
    background: #ffffff;
    overflow: visible;
}

/* LEFT */
.card-left {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
}

.avatar {
    width: 58px;
    height: 58px;
    flex-shrink: 0;
    background: #A7F3D0;
    color: #064E3B;
    font-size: 18px;
    font-weight: 800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.info {
    min-width: 0;
}

.info h3 {
    margin: 0 0 4px;
    font-size: 15px;
    font-weight: 800;
    color: #111827;
}

.info p {
    margin: 0;
    font-size: 13px;
    color: #6B7280;
    line-height: 1.3;
    white-space: nowrap;
}

.btn-feedback {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 130px;
    height: 38px;
    border: none;
    border-radius: 999px;
    background: #ECFDF5;
    color: #065F46;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    white-space: nowrap;
}

.btn-feedback:hover {
    background: #D1FAE5;
}

.feedback-done-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 132px;
    height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    background: #E0F2FE;
    color: #0369A1;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
    margin-left: 0;
}

.feedback-modal .modal-body,
.feedback-modal .modal-header,
.feedback-modal .modal-footer {
    background: #ffffff;
}

.feedback-subtitle {
    margin: 4px 0 0;
    color: #64748B;
    font-size: 13px;
}

.feedback-label {
    display: block;
    margin-bottom: 8px;
    color: #111827;
    font-weight: 800;
    font-size: 14px;
}


.feedback-textarea:focus {
    border-color: #10B981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .12);
}

.btn-feedback-cancel,
.btn-feedback-submit {
    border: none;
    border-radius: 14px;
    padding: 10px 16px;
    font-weight: 800;
    font-size: 13px;
}

.btn-feedback-cancel {
    background: #F1F5F9;
    color: #334155;
}

.btn-feedback-submit {
    background: #064E3B;
    color: #fff;
}

/* MIDDLE */
.card-middle {
    display: grid;
    grid-template-columns: 100px minmax(140px, 1fr);
    gap: 24px;
    align-items: center;
    min-width: 0;
    flex: 1;
}

.meta-col {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.meta-topic {
    min-width: 0;
}

.card-middle small {
    display: block;
    font-size: 10px;
    color: #9CA3AF;
    font-weight: 700;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.card-middle strong {
    display: block;
    font-size: 14px;
    color: #111827;
    font-weight: 700;
    line-height: 1.3;
    word-break: break-word;
}

/* RIGHT */
.card-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex-shrink: 0;
    min-width: auto;
    flex-wrap: nowrap;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 130px;
    height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    line-height: 1.2;
    white-space: nowrap;
}

/* STATUS COLOR */
.status-selesai {
    background: #BDE3FF;
    color: #1684E8;
}

.status-ditolak,
.status-dibatalkan {
    background: #FFB6B6;
    color: #E11D1D;
}

.status-diterima {
    background: #C6F8D2;
    color: #299C72;
}

.status-menunggu {
    background: #FFFBB8;
    color: #B8A84A;
}

.status-berlangsung {
    background: #C8BEF4;
    color: #7B6DFF;
}

.status-reschedule {
    background: #FFF0D6;
    color: #B45309;
}

.status-pill.status-reschedule {
    min-width: 190px;
}

.status-default {
    background: #E5E7EB;
    color: #4B5563;
}

/* BUTTON */
.btn-riwayat {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 118px;
    height: 38px;
    background: #064E3B;
    color: white;
    border-radius: 999px;
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-riwayat:hover {
    background: #053B2E;
    transform: translateY(-1px);
}

/* LOAD MORE */
.load-more {
    text-align: center;
    padding: 22px;
    border: 2px dashed #D1D5DB;
    border-radius: 18px;
    color: #064E3B;
    font-weight: 700;
    cursor: pointer;
}

.load-more-btn {
    width: 100%;
    padding: 16px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    background: transparent;
    color: #064E3B;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.load-more-btn:hover {
    background: #EFFCF5;
    border-color: #064E3B;
}

/* SUMMARY */
.summary-box {
    background: white;
    padding: 24px;
    border-radius: 20px;
    margin-bottom: 18px;
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
}

.summary-box h3 {
    margin: 0 0 18px;
    font-size: 22px;
    line-height: 1.1;
    font-weight: 800;
    color: #111827;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    color: #111827;
    font-size: 14px;
}

.summary-item strong {
    font-size: 16px;
}

/* CTA */
.cta-box {
    background: #D1FAE5;
    padding: 24px;
    border-radius: 20px;
}

.cta-box h4 {
    margin: 0 0 8px;
    font-size: 18px;
    font-weight: 800;
    color: #064E3B;
}

.cta-box p {
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 18px;
    color: #374151;
}

.btn-jadwal {
    display: block;
    background: #064E3B;
    color: white;
    padding: 12px 16px;
    text-align: center;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    font-size: 13px;
}

.simple-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 28px;
    margin-top: 32px;
}

.simple-page,
.simple-next {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    color: #064E3B;
    font-size: 18px;
    font-weight: 800;
    text-decoration: none;
}

.simple-page.active {
    background: #064E3B;
    color: white;
}

.simple-next {
    font-size: 36px;
    line-height: 1;
}

.simple-page:hover,
.simple-next:hover {
    background: #E5F7EF;
    color: #064E3B;
}

/* RESPONSIVE */
@media (max-width: 1400px) {
    .riwayat-page {
        padding: 55px 40px;
    }

    .riwayat-card {
        grid-template-columns: minmax(240px, 260px) minmax(280px, 1fr) minmax(260px, 320px);
        gap: 20px;
        padding: 22px 24px;
    }

    .card-middle {
        grid-template-columns: 90px minmax(120px, 1fr);
        gap: 18px;
    }
}

@media (max-width: 1050px) {
    .riwayat-page {
        padding: 45px 30px;
    }

    .riwayat-content {
        grid-template-columns: 1fr;
    }

    .riwayat-sidebar {
        width: 100%;
        min-width: 0;
    }

    .riwayat-card {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .card-left {
        gap: 12px;
    }

    .card-middle {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .card-right {
        justify-content: space-between;
        gap: 12px;
        flex-wrap: nowrap;
    }

    .status-pill {
        min-width: 120px;
    }

    .btn-riwayat {
        min-width: 120px;
    }
}

@media (max-width: 640px) {
    .riwayat-page {
        padding: 35px 22px;
    }

    .riwayat-header h1 {
        font-size: 38px;
    }

    .riwayat-card {
        padding: 18px;
        gap: 12px;
    }

    .card-middle {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .card-right {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }

   .status-pill,
    .btn-riwayat,
    .btn-feedback,
    .feedback-done-badge {
        width: 100%;
        min-width: auto;
    }
}

/* Ensure Bootstrap modal is above other custom backdrops */
body > .modal-backdrop {
    z-index: 9998 !important;
}

body > .modal.feedback-modal-wrapper {
    z-index: 9999 !important;
    pointer-events: auto !important;
}

body > .modal.feedback-modal-wrapper.show {
    display: block !important;
    pointer-events: auto !important;
}

body > .modal.feedback-modal-wrapper .modal-dialog {
    z-index: 10000 !important;
    pointer-events: auto !important;
}

body > .modal.feedback-modal-wrapper .modal-content {
    pointer-events: auto !important;
}

.feedback-modal {
    background: #ffffff;
    border: none;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 24px 80px rgba(15, 23, 42, 0.18);
    pointer-events: auto !important;
}

.feedback-modal * {
    pointer-events: auto !important;
}

.feedback-textarea {
    width: 100%;
    min-height: 140px;
    border: 1px solid #D8E7E0;
    border-radius: 16px;
    padding: 14px 16px;
    resize: vertical;
    outline: none;
    font-size: 14px;
    background: #ffffff;
    color: #111827;
    pointer-events: auto !important;
}

.feedback-modal {
    background: #ffffff;
    border: none;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 24px 80px rgba(15, 23, 42, 0.18);
    pointer-events: auto !important;
}

.feedback-modal * {
    pointer-events: auto !important;
}

.feedback-textarea {
    width: 100%;
    min-height: 140px;
    border: 1px solid #D8E7E0;
    border-radius: 16px;
    padding: 14px 16px;
    resize: vertical;
    outline: none;
    font-size: 14px;
    background: #ffffff;
    color: #111827;
    pointer-events: auto !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.feedback-modal-wrapper').forEach(function (modal) {
        document.body.appendChild(modal);

        modal.addEventListener('shown.bs.modal', function () {
            const textarea = modal.querySelector('textarea[name="isi_feedback"]');

            if (textarea) {
                setTimeout(function () {
                    textarea.focus();
                }, 150);
            }
        });
    });
});
</script>
@endsection