@extends('layouts.admin')

@section('page-title', 'Detail Sesi Konseling')

@push('styles')
<style>
    .detail-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
        padding: 1.5rem 1.6rem;
        max-width: 900px;
    }

    .detail-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1rem;
    }

    .detail-section-title {
        font-size: .92rem;
        font-weight: 700;
        color: #065F46;
        margin: 1.2rem 0 .6rem;
    }

    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-table td {
        padding: .75rem 0;
        border-bottom: 1px solid #edf2ef;
        font-size: .88rem;
    }

    .detail-table td:first-child {
        width: 220px;
        color: #64748b;
    }

    .detail-table td:last-child {
        color: #0f172a;
        font-weight: 500;
        text-align: right;
    }

    .detail-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-terima,
    .btn-tolak {
        border: none;
        border-radius: 10px;
        padding: .75rem 1.5rem;
        font-weight: 600;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-terima {
        background: #065F46;
    }

    .btn-tolak {
        background: #ff2b2b;
    }

    .alasan-box {
        margin-top: .5rem;
        padding: 1rem 1.2rem;
        border-radius: 16px;
        background: #f8fafb;
        border: 1px solid #edf2ef;
        color: #334155;
        font-size: .88rem;
        line-height: 1.5;
    }
</style>
@endpush

@section('konten')
@php
    $mahasiswa = optional($jadwal)->mahasiswa;
    $user = optional($mahasiswa)->user;
    $status = strtolower($jadwal->status ?? 'menunggu');

    $statusLabel = match ($status) {
        'menunggu' => 'Menunggu Konfirmasi',
        'disetujui', 'diterima' => 'Diterima',
        'berlangsung' => 'Sedang Berlangsung',
        'selesai' => 'Selesai',
        'ditolak', 'dibatalkan' => 'Ditolak',
        default => ucfirst($jadwal->status ?? 'Menunggu'),
    };

    $topik = $jadwal->topik ?? null;

    if (!$topik && !empty($jadwal->catatan)) {
        if (preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
            $topik = trim($match[1]);
        } else {
            $topik = $jadwal->catatan;
        }
    }
@endphp

<div class="detail-card">
    <div class="detail-title">Detail Penjadwalan</div>

    <div class="detail-section-title">Informasi Pribadi</div>
    <table class="detail-table">
        <tr>
            <td>NIM</td>
            <td>{{ $mahasiswa->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>{{ $user->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>{{ $mahasiswa->jurusan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Angkatan</td>
            <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
        </tr>
    </table>

    <div class="detail-section-title">Detail Jadwal</div>
    <table class="detail-table">
        <tr>
            <td>Tanggal</td>
            <td>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y') }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>{{ substr($jadwal->waktu, 0, 5) }}</td>
        </tr>
    </table>

    <div class="detail-section-title">Layanan</div>
    <table class="detail-table">
        <tr>
            <td>Layanan Konseling</td>
            <td>{{ ucfirst($jadwal->jenis ?? 'Online') }}</td>
        </tr>
        <tr>
            <td>Topik</td>
            <td>{{ $topik ?? '-' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>{{ $statusLabel }}</td>
        </tr>
    </table>

    @if($status === 'menunggu')
        <div class="detail-actions">
            <form action="{{ route('admin.sesi.terima', $jadwal->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-terima">Terima</button>
            </form>

            <a href="{{ route('admin.sesi.tolak', $jadwal->id) }}" class="btn-tolak">
                Tolak
            </a>
        </div>

    @elseif($status === 'ditolak')
        <div class="detail-section-title">Alasan Penolakan</div>
        <div class="alasan-box">
            {{ $jadwal->alasan_penolakan ?? '-' }}
        </div>

    @elseif($status === 'disetujui')
        <div class="detail-actions">
            <a href="#" class="btn-terima" style="min-width:220px;text-align:center;">
                Mulai Sesi
            </a>
        </div>
    @endif
</div>
@endsection