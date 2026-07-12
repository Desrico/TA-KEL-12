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

        <div class="riwayat-list">

        @foreach($riwayat as $item)
            @php
                $status = strtolower(trim($item->status ?? ''));
                $status = str_replace(' ', '_', $status);
                $isPerluSesiLanjutan = $status === 'selesai'
                    && in_array(strtolower(str_replace('_', ' ', (string) ($item->tindak_lanjut_tipe ?? $item->tindak_lanjut ?? ''))), ['perlu lanjut', 'perlu sesi lanjutan', 'on', '1', 'ya'], true);

                $sesi = $item->sesiKonseling;
                $feedback = $sesi?->feedback;
                $bisaFeedback = $status === 'selesai' && $sesi;

                $statusLabel = $isPerluSesiLanjutan ? 'Perlu Sesi Lanjutan' : match($status) {
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak',
                    'dibatalkan' => 'Dibatalkan',
                    'disetujui', 'diterima' => 'Diterima',
                    'menunggu', 'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                    'berlangsung', 'sedang_berlangsung' => 'Sedang Berlangsung',
                    'perlu_penjadwalan_ulang' => 'Perlu Penjadwalan Ulang',
                    default => ucfirst(str_replace('_', ' ', $item->status ?? '-'))
                };

                $statusClass = $isPerluSesiLanjutan ? 'status-follow-up' : match($status) {
                    'selesai' => 'status-selesai',
                    'ditolak' => 'status-ditolak',
                    'dibatalkan' => 'status-dibatalkan',
                    'disetujui', 'diterima' => 'status-diterima',
                    'menunggu', 'menunggu_konfirmasi' => 'status-menunggu',
                    'berlangsung', 'sedang_berlangsung' => 'status-berlangsung',
                    'perlu_penjadwalan_ulang' => 'status-reschedule',
                    default => 'status-default'
                };

                $isActionableStatus = $isPerluSesiLanjutan;

            @endphp

    <div class="riwayat-card" id="jadwal-{{ $item->id }}" data-highlight-target="{{ request('jadwal') == $item->id ? 'true' : 'false' }}">
        <div class="card-left">
            @php
    $userMahasiswa = $item->mahasiswa?->user;
    $isAnonim = filter_var($item->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

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

    $inisialMahasiswa = strtoupper(substr($namaMahasiswa, 0, 1));
    $jenisSesi = ucfirst(strtolower($item->jenis ?? $item->metode ?? 'Online'));
@endphp
    

            <div class="avatar">
                {{ $inisialMahasiswa }}
            </div>

            <div class="info">
                <h3>
                    <span>{{ $namaMahasiswa }}</span>
                </h3>
                @if($isAnonim)
                    <div class="anonim-mode-line">
                        <span class="anonim-mode-badge">Anonim</span>
                    </div>
                @endif
                <p>
                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                    • {{ substr($item->waktu ?? '00:00', 0, 5) }} WIB
                    • Sesi {{ $jenisSesi }}
                </p>
            </div>
        </div>

        <div class="card-right">
            @if($isActionableStatus)
                {{-- Status khusus bisa dibuka untuk menampilkan aksi sesi lanjutan/penjadwalan ulang. --}}
                <a href="{{ route('detail.riwayat', $item->id) }}" class="status-pill status-link {{ $statusClass }}">
                    {{ $statusLabel }}
                </a>
            @else
                <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
            @endif

            @if($isPerluSesiLanjutan)
                <a href="{{ route('konseling', ['follow_up_from' => $item->id]) }}" class="btn-riwayat btn-follow-up">
                    Ajukan Sesi Lanjutan
                </a>
            @endif

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
                        </a>
                    @endif

                    @for($i = 1; $i <= $riwayat->lastPage(); $i++)
                        <a href="{{ $riwayat->url($i) }}"
                        class="simple-page {{ $riwayat->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

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
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 18px;
    width: 100%;
    box-sizing: border-box;
    padding: 18px 26px;
    border-radius: 18px;
    border: 2px solid #E8F0EB;
    margin-bottom: 12px;
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
    width: 54px;
    height: 54px;
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
    font-size: 15.5px;
    font-weight: 800;
    color: #111827;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    min-width: 0;
    flex-wrap: wrap;
}

.info h3 > span:first-child {
    min-width: 0;
}

.anonim-mode-line {
    display: flex;
    align-items: center;
    margin: 2px 0 4px;
}

.anonim-mode-badge {
    display: inline-flex;
    align-items: center;
    height: 22px;
    padding: 0 9px;
    border-radius: 999px;
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    color: #047857;
    font-size: 11px;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
}

.info p {
    margin: 0;
    font-size: 13.5px;
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

.status-link {
    text-decoration: none;
    cursor: pointer;
    transition: transform .16s ease, box-shadow .16s ease;
}

.status-link:hover {
    color: inherit;
    transform: translateY(-1px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
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

.status-follow-up {
    background: #EDE9FE;
    color: #5B21B6;
}

.status-pill.status-follow-up {
    min-width: 180px;
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
    color: #ffffff;
    transform: translateY(-1px);
}

.btn-riwayat:focus,
.btn-riwayat:active {
    color: #ffffff;
    text-decoration: none;
}

.btn-follow-up {
    background: #FFFBB8;
    color: #B8A84A;
    min-width: 152px;
}

.btn-follow-up:hover {
    background: #FFF6A3;
    color: #9F8F31;
}

.riwayat-card.is-highlighted {
    border-color: #7c3aed;
    box-shadow: 0 14px 32px rgba(91, 33, 182, .14);
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
    gap: 16px;
    margin-top: 24px;
}

.simple-page,
.simple-next {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    color: #064E3B;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
}

.simple-page.active {
    background: #064E3B;
    color: white;
}

.simple-next {
    font-size: 24px;
    line-height: 1;
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
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
        padding: 18px 24px;
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
    const highlightedCard = document.querySelector('[data-highlight-target="true"]');

    if (!highlightedCard) {
        return;
    }

    // Notifikasi membuka riwayat dan langsung mengarahkan perhatian ke status yang perlu aksi.
    highlightedCard.classList.add('is-highlighted');
    highlightedCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
});
</script>

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
