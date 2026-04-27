@extends('layouts.admin')

@section('page-title', 'Laporan Konseling')

@push('styles')
<style>
    .laporan-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .laporan-head {
        padding: 1.4rem 1.6rem;
        border-bottom: 1px solid #edf2ef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .laporan-head h5 {
        margin: 0;
        font-weight: 800;
        color: #064E3B;
    }

    .laporan-head p {
        margin: .25rem 0 0;
        color: #718096;
        font-size: .86rem;
    }

    .laporan-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .86rem;
    }

    .laporan-table thead th {
        background: #e6f5ec;
        color: #0f172a;
        font-weight: 700;
        padding: 1rem;
        border-bottom: 1px solid #d6e9de;
    }

    .laporan-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #edf2ef;
        vertical-align: middle;
        color: #334155;
    }

    .laporan-table tbody tr:hover {
        background: #fbfefc;
    }

    .student-name {
        font-weight: 700;
        color: #0f172a;
    }

    .student-sub {
        font-size: .76rem;
        color: #8191a3;
        margin-top: 2px;
    }

    .topic-text {
        max-width: 230px;
        color: #475569;
        font-size: .82rem;
    }

    .status-pill {
        display: inline-flex;
        border-radius: 999px;
        padding: .42rem .85rem;
        font-size: .74rem;
        font-weight: 700;
    }

    .status-selesai {
        background: #d1fae5;
        color: #047857;
    }

    .status-belum {
        background: #fef3c7;
        color: #b45309;
    }

    .btn-laporan {
        border: none;
        border-radius: 10px;
        padding: .55rem .9rem;
        font-size: .78rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        white-space: nowrap;
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
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #bbf7d0;
    }

    .btn-lihat:hover {
        background: #d1fae5;
        color: #065F46;
    }

    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 2.5rem 1rem !important;
    }

    @media (max-width: 991.98px) {
        .laporan-table-wrap {
            overflow-x: auto;
        }

        .laporan-table {
            min-width: 900px;
        }
    }
</style>
@endpush

@section('konten')
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

                        $topik = $l->topik ?? null;
                        if (!$topik && !empty($l->catatan)) {
                            if (preg_match('/Topik:\s*([^|]+)/i', $l->catatan, $match)) {
                                $topik = trim($match[1]);
                            }
                        }

                        $sudahAdaLaporan = !empty($l->laporan) || strtolower($l->status ?? '') === 'selesai';
                    @endphp

                    <tr>
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
                            <a href="{{ route('admin.laporan.laporan', $l->id) }}"
                               class="btn-laporan {{ $sudahAdaLaporan ? 'btn-lihat' : 'btn-buat' }}">
                                <i class="ti {{ $sudahAdaLaporan ? 'ti-eye' : 'ti-file-plus' }}"></i>
                                {{ $sudahAdaLaporan ? 'Lihat Laporan' : 'Buat Laporan' }}
                            </a>
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
</div>
@endsection