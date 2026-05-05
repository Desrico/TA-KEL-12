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

    <div class="kons-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.85rem">
                <thead style="background:#f8fafb">
                    <tr style="color:#8898aa;font-size:.73rem;text-transform:uppercase">
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="py-3">Jenis</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Waktu</th>
                        <th class="py-3">Catatan</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $l)
                    @php
                        $status = strtolower($l->status ?? 'menunggu');
                        $jenis = strtolower($l->jenis ?? 'offline');
                        $scheduledAt = $jenis === 'online'
                            ? \Carbon\Carbon::parse(trim($l->tanggal . ' ' . ($l->waktu ?? '00:00:00')))
                            : null;
                        $isBeforeSchedule = $scheduledAt && now()->lt($scheduledAt);
                        $canStartChat = $jenis === 'online'
                            && in_array($status, ['disetujui', 'berlangsung'], true)
                            && ! $isBeforeSchedule;
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <div style="font-weight:600">{{ optional(optional($l->mahasiswa)->user)->nama ?? 'Anonim' }}</div>
                            <div style="font-size:.75rem;color:#8898aa">{{ optional($l->mahasiswa)->nim ?? '-' }}</div>
                        </td>
                        <td class="py-3">{{ ucfirst($l->jenis ?? '-') }}</td>
                        <td class="py-3">{{ ucfirst($l->status ?? '-') }}</td>
                        <td class="py-3">{{ \Carbon\Carbon::parse($l->tanggal)->format('d M Y') }}</td>
                        <td class="py-3">{{ $l->waktu }} WIB</td>
                        <td class="py-3" style="font-size:.78rem">{{ $l->catatan ?? '-' }}</td>
                        <td class="py-3 text-center">
                            @if($canStartChat)
                                <a href="{{ route('mahasiswa.chat') }}" class="btn btn-sm" style="background:#065F46;color:#fff;border-radius:10px;padding:.45rem .85rem;font-weight:600;">
                                    {{ $status === 'berlangsung' ? 'Lanjutkan Chat' : 'Mulai Sesi' }}
                                </a>
                            @elseif($jenis === 'online' && in_array($status, ['disetujui', 'berlangsung'], true) && $isBeforeSchedule)
                                <button
                                    type="button"
                                    class="btn btn-sm"
                                    style="background:#cbd5e1;color:#475569;border-radius:10px;padding:.45rem .85rem;font-weight:600;border:none;"
                                    title="Sesi dimulai {{ $scheduledAt->translatedFormat('j F Y') }} pukul {{ $scheduledAt->format('H:i') }} WIB"
                                    disabled
                                >
                                    Mulai {{ $scheduledAt->format('H:i') }} WIB
                                </button>
                            @else
                                <span style="font-size:.75rem;color:#94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5" style="color:#8898aa">Belum ada laporan</td></tr>
                    @endforelse
                </tbody>
            </table>

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
                <p>
                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}
                    • {{ $item->waktu ?? '00:00' }} WIB
                </p>
            </div>
        </div>

        <div class="card-middle">
            <div>
                <small>DURASI</small>
                <strong>{{ $item->durasi ?? '60 Menit' }}</strong>
            </div>

            <div>
                <small>METODE</small>
                <strong>{{ $metodeText }}</strong>
            </div>

            <div>
                <small>TOPIK</small>
                <strong>{{ $topikText }}</strong>
            </div>
        </div>

        <div class="card-right">
            <span class="status-pill {{ $statusClass }}">
                {{ $statusLabel }}
            </span>

            <a href="{{ route('riwayat.detail', $item->id) }}" class="btn-riwayat">
                Lihat Riwayat
            </a>
        </div>

    </div>
@endforeach

      @if($riwayat->hasPages())
    <div class="simple-pagination">

        @if(!$riwayat->onFirstPage())
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

        @if($riwayat->hasMorePages())
            <a href="{{ $riwayat->nextPageUrl() }}" class="simple-next">
                ›
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
                    <strong>{{ $totalSesi }}</strong>
                </div>

                <div class="summary-item">
                    <span>Sesi Selesai</span>
                    <strong>{{ $sesiSelesai }}</strong>
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

@endsection

<style>
.riwayat-page {
    padding: 70px 80px;
    background: #F6FBF8;
    min-height: 100vh;
    overflow-x: hidden;
    box-sizing: border-box;
}

/* HEADER */
.riwayat-header h1 {
    font-size: 52px;
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
    font-size: 17px;
    line-height: 1.7;
    margin-bottom: 55px;
}

/* MAIN LAYOUT */
.riwayat-content {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 300px;
    gap: 32px;
    align-items: flex-start;
}

.riwayat-list {
    min-width: 0;
}

.riwayat-sidebar {
    width: 300px;
    min-width: 300px;
}

/* CARD */
.riwayat-card {
    display: grid;
    grid-template-columns: 300px minmax(280px, 1fr) 320px;
    align-items: center;
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
    padding: 22px 26px;
    border-radius: 22px;
    border: 2px solid #CFF2E3;
    margin-bottom: 20px;
    background: #ffffff;
    overflow: hidden;
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
    font-size: 17px;
    font-weight: 800;
    color: #111827;
}

.info p {
    margin: 0;
    font-size: 14px;
    color: #6B7280;
    line-height: 1.4;
    white-space: nowrap;
}

/* MIDDLE */
.card-middle {
    display: grid;
    grid-template-columns: 90px 110px 90px;
    gap: 20px;
    align-items: center;
    min-width: 0;
}

.card-middle small {
    display: block;
    font-size: 11px;
    color: #9CA3AF;
    font-weight: 700;
    margin-bottom: 4px;
}

.card-middle strong {
    display: block;
    font-size: 15px;
    color: #111827;
    font-weight: 800;
    line-height: 1.3;
}

/* RIGHT */
.card-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 16px;
    min-width: 0;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 170px;
    height: 42px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
    text-align: center;
    line-height: 1.1;
    white-space: nowrap;
    margin-left: 10px;
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
    width: 130px;
    height: 42px;
    background: #064E3B;
    color: white;
    border-radius: 999px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    margin-left: 6px;
}

.btn-riwayat:hover {
    opacity: 0.9;
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
    padding: 28px;
    border-radius: 22px;
    margin-bottom: 22px;
    box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
}

.summary-box h3 {
    margin: 0 0 22px;
    font-size: 28px;
    line-height: 1.1;
    font-weight: 800;
    color: #111827;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    color: #111827;
    font-size: 15px;
}

.summary-item strong {
    font-size: 18px;
}

/* CTA */
.cta-box {
    background: #D1FAE5;
    padding: 28px;
    border-radius: 22px;
}

.cta-box h4 {
    margin: 0 0 10px;
    font-size: 22px;
    font-weight: 800;
    color: #064E3B;
}

.cta-box p {
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 22px;
    color: #374151;
}

.btn-jadwal {
    display: block;
    background: #064E3B;
    color: white;
    padding: 13px 18px;
    text-align: center;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 800;
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
@media (max-width: 1250px) {
    .riwayat-page {
        padding: 55px 45px;
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
        gap: 18px;
    }

    .card-middle {
        grid-template-columns: repeat(3, 1fr);
    }

    .card-right {
        justify-content: flex-start;
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
        padding: 20px;
    }

    .card-middle {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .card-right {
        flex-direction: column;
        align-items: stretch;
    }

    .status-pill,
    .btn-riwayat {
        width: 100%;
    }
}
</style>
@endsection
