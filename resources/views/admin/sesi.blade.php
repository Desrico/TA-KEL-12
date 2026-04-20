@extends('layouts.mantis')

@section('page-title', 'Sesi Konseling')

@push('styles')
<style>
    .sesi-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .sesi-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem 0;
        flex-wrap: wrap;
    }

    .sesi-search-wrap {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .sesi-search {
        position: relative;
    }

    .sesi-search input {
        width: 240px;
        border: 1px solid #d9e8df;
        border-radius: 12px;
        padding: .7rem 1rem .7rem 2.5rem;
        font-size: .86rem;
        outline: none;
    }

    .sesi-search i {
        position: absolute;
        left: .85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9aa8b5;
        font-size: 1rem;
    }

    .sesi-filter-btn {
        border: 1px solid #d9e8df;
        background: #fff;
        color: #64748b;
        border-radius: 12px;
        padding: .7rem 1rem;
        font-size: .84rem;
        font-weight: 600;
    }

    .sesi-table-wrap {
        padding: 1.2rem 1.5rem 1.5rem;
    }

    .sesi-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .86rem;
    }

    .sesi-table thead th {
        background: #dff1e5;
        color: #0f172a;
        font-weight: 700;
        padding: 1rem .95rem;
        text-align: left;
        border-bottom: 1px solid #d6e9de;
    }

    .sesi-table thead th:first-child {
        border-top-left-radius: 18px;
    }

    .sesi-table thead th:last-child {
        border-top-right-radius: 18px;
        text-align: center;
    }

    .sesi-table tbody td {
        padding: .95rem .95rem;
        border-bottom: 1px solid #edf2ef;
        vertical-align: middle;
        color: #334155;
    }

    .sesi-table tbody tr:hover {
        background: #fbfefc;
    }

    .mahasiswa-cell {
        display: flex;
        align-items: center;
        gap: .85rem;
        min-width: 210px;
    }

    .mahasiswa-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        overflow: hidden;
        background: #d9e6df;
        flex-shrink: 0;
    }

    .mahasiswa-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .mahasiswa-avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #cfe9da;
        color: #064E3B;
        font-weight: 700;
        font-size: .9rem;
    }

    .mahasiswa-name {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .mahasiswa-sub {
        font-size: .74rem;
        color: #7b8a97;
        margin-top: 2px;
    }

    .waktu-text {
        line-height: 1.45;
        min-width: 120px;
    }

    .layanan-text,
    .durasi-text {
        white-space: nowrap;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .42rem .95rem;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 600;
        min-width: 118px;
        line-height: 1.1;
        text-align: center;
    }

    .status-menunggu {
        background: #f3efb0;
        color: #8a7b1f;
    }

    .status-diterima {
        background: #bfeec9;
        color: #228b52;
    }

    .status-berlangsung {
        background: #cfc3fa;
        color: #6d4ee6;
    }

    .status-selesai {
        background: #a9d6ff;
        color: #1f78d1;
    }

    .status-ditolak,
    .status-dibatalkan {
        background: #ffb1b1;
        color: #d93030;
    }

    .btn-lihat {
        background: #065F46;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: .5rem 1rem;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 66px;
    }

    .btn-lihat:hover {
        background: #064E3B;
        color: #fff;
    }

    .empty-row {
        text-align: center;
        color: #94a3b8;
        padding: 2.5rem 1rem !important;
    }

    .sesi-pagination {
        display: flex;
        justify-content: center;
        gap: .55rem;
        padding: 0 1.5rem 1.5rem;
    }

    .sesi-page-item {
        width: 28px;
        height: 28px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: .82rem;
        font-weight: 600;
        color: #065F46;
        background: transparent;
    }

    .sesi-page-item.active {
        background: #065F46;
        color: #fff;
    }

    @media (max-width: 991.98px) {
        .sesi-search input {
            width: 100%;
            min-width: 220px;
        }

        .sesi-table-wrap {
            overflow-x: auto;
        }

        .sesi-table {
            min-width: 880px;
        }
    }
</style>
@endpush

@section('konten')
<div class="sesi-card">
    <div class="sesi-toolbar">
        <div></div>

        <div class="sesi-search-wrap">
            <div class="sesi-search">
                <i class="ti ti-search"></i>
                <input type="text" placeholder="Cari mahasiswa...">
            </div>

            <button type="button" class="sesi-filter-btn">
                <i class="ti ti-filter me-1"></i> Filter
            </button>
        </div>
    </div>

    <div class="sesi-table-wrap">
        <table class="sesi-table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Waktu</th>
                    <th>Layanan</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sesi as $item)
                    @php
                        $jadwal = $item->jadwal;
                        $mahasiswa = optional($jadwal)->mahasiswa;
                        $user = optional($mahasiswa)->user;

                        $nama = optional($user)->nama ?? 'Mahasiswa';
                        $nim = optional($mahasiswa)->nim ?? '-';
                        $foto = optional(optional($user)->profil)->foto ?? null;

                        $tanggal = optional($jadwal)->tanggal
                            ? \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y')
                            : '-';

                        $waktu = optional($jadwal)->waktu
                            ? substr($jadwal->waktu, 0, 5) . ' WIB'
                            : '-';

                        $layanan = 'Sesi ' . ucfirst(optional($jadwal)->jenis ?? 'Online');
                        $durasi = '60 Menit';

                        $statusRaw = strtolower($item->status ?? optional($jadwal)->status ?? 'menunggu');

                        $statusLabel = match ($statusRaw) {
                            'menunggu' => 'Menunggu Konfirmasi',
                            'disetujui', 'diterima' => 'Diterima',
                            'berlangsung' => 'Sedang Berlangsung',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Dibatalkan',
                            'dibatalkan' => 'Dibatalkan',
                            default => ucfirst($statusRaw),
                        };

                        $statusClass = match ($statusRaw) {
                            'menunggu' => 'status-menunggu',
                            'disetujui', 'diterima' => 'status-diterima',
                            'berlangsung' => 'status-berlangsung',
                            'selesai' => 'status-selesai',
                            'ditolak' => 'status-dibatalkan',
                            'dibatalkan' => 'status-dibatalkan',
                            default => 'status-menunggu',
                        };
                    @endphp

                    <tr>
                        <td>
                            <div class="mahasiswa-cell">
                                <div class="mahasiswa-avatar">
                                    @if($foto)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($foto) }}" alt="{{ $nama }}">
                                    @else
                                        <div class="mahasiswa-avatar-fallback">
                                            {{ strtoupper(substr($nama, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <div class="mahasiswa-name">{{ $nama }}</div>
                                    <div class="mahasiswa-sub">{{ $nim }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="waktu-text">
                                <div>{{ $tanggal }}</div>
                                <div>{{ $waktu }}</div>
                            </div>
                        </td>

                        <td>
                            <div class="layanan-text">{{ $layanan }}</div>
                        </td>

                        <td>
                            <div class="durasi-text">{{ $durasi }}</div>
                        </td>

                        <td>
                            <span class="status-pill {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td style="text-align:center;">
                            <a href="#" class="btn-lihat">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">
                            Belum ada data sesi konseling
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sesi-pagination">
        <span class="sesi-page-item active">1</span>
        <span class="sesi-page-item">2</span>
        <span class="sesi-page-item">3</span>
        <span class="sesi-page-item">
            <i class="ti ti-chevron-right"></i>
        </span>
    </div>
</div>
@endsection