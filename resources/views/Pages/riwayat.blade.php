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

    <div class="riwayat-content">

        <!-- LEFT: LIST -->
        <div class="riwayat-list">

           @foreach($riwayat as $item)
    @php
        $status = strtolower($item->status ?? '');

        $statusLabel = match($status) {
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            'disetujui', 'diterima' => 'Diterima',
            'menunggu', 'menunggu konfirmasi' => 'Menunggu Konfirmasi',
            'berlangsung', 'sedang berlangsung' => 'Sedang Berlangsung',
            default => ucfirst($item->status ?? '-')
        };

        $statusClass = match($status) {
            'selesai' => 'status-selesai',
            'ditolak' => 'status-ditolak',
            'dibatalkan' => 'status-dibatalkan',
            'disetujui', 'diterima' => 'status-diterima',
            'menunggu', 'menunggu konfirmasi' => 'status-menunggu',
            'berlangsung', 'sedang berlangsung' => 'status-berlangsung',
            default => 'status-default'
        };

        $topikText = '-';

        if (!empty($item->topik)) {
            $topikText = $item->topik;
        } elseif (!empty($item->catatan) && str_contains($item->catatan, 'Topik:')) {
            $parts = explode('|', $item->catatan);
            $topikText = trim(str_replace('Topik:', '', $parts[0]));
        }

        $jenis = strtolower($item->jenis ?? '');
        $metodeText = $jenis == 'offline' ? 'Tatap Muka' : 'Video Call';
    @endphp

    <div class="riwayat-card">
        <div class="card-left">
            <div class="avatar">
                {{ strtoupper(substr($item->mahasiswa->nama ?? 'M', 0, 1)) }}
            </div>
            <div class="info">
                <h3>{{ $item->mahasiswa->nama ?? 'Mahasiswa' }}</h3>
                <p>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }} • {{ substr($item->waktu ?? '00:00', 0, 5) }} WIB</p>
            </div>
        </div>

        <div class="card-middle">
            <div class="meta-col">
                <small>DURASI</small>
                <strong>{{ $item->durasi ?? '60 Menit' }}</strong>
            </div>
            <div class="meta-col">
                <small>METODE</small>
                <strong>{{ $metodeText }}</strong>
            </div>
            <div class="meta-col meta-topic">
                <small>TOPIK</small>
                <strong>{{ Str::limit($topikText, 45, '...') }}</strong>
            </div>
        </div>

        <div class="card-right">
            <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
            <a href="{{ route('detail.riwayat', $item->id) }}" class="btn-riwayat">Lihat Riwayat</a>
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

        <!-- RIGHT: SIDEBAR -->
        <div class="riwayat-sidebar">

            <div class="summary-box">
                <h3>Ringkasan Perjalananmu</h3>

                <div class="summary-item">
                    <span>Total Sesi</span>
                    <strong>{{ count($riwayat) ?? 0 }}</strong>
                </div>

                <div class="summary-item">
                    <span>Sesi Selesai</span>
                    <strong>{{ isset($riwayat) ? collect($riwayat)->where('status','Selesai')->count() : 0 }}</strong>
                </div>
            </div>

            <div class="cta-box">
                <h4>Butuh dukungan lagi?</h4>
                <p>
                    Jadwalkan sesi baru dengan konselor untuk melanjutkan perjalananmu.
                </p>

                <a href="{{ route('konseling') }}" class="btn-jadwal">
                    Jadwalkan Konseling Baru
                </a>
            </div>

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

/* MAIN LAYOUT */
.riwayat-content {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 280px;
    gap: 28px;
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
    grid-template-columns: minmax(250px, 280px) minmax(300px, 1fr) minmax(280px, 340px);
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

/* MIDDLE */
.card-middle {
    display: grid;
    grid-template-columns: 90px 110px minmax(120px, 1fr);
    gap: 20px;
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
    min-width: 120px;
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
    gap: 12px;
    flex-shrink: 0;
    min-width: auto;
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

.status-default {
    background: #E5E7EB;
    color: #4B5563;
}

/* BUTTON */
.btn-riwayat {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 130px;
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
        grid-template-columns: 80px 100px minmax(100px, 1fr);
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
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .card-right {
        justify-content: space-between;
        gap: 12px;
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
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }

    .status-pill,
    .btn-riwayat {
        width: 100%;
        min-width: auto;
    }
}
</style>
@endsection