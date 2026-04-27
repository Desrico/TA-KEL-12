@extends('layouts.admin')

@section('page-title', 'Alasan Penolakan')

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

    .alasan-textarea {
        width: 100%;
        min-height: 145px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1rem;
        resize: vertical;
        outline: none;
        color: #334155;
        font-size: .88rem;
    }

    .alasan-textarea:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, .08);
    }

    .detail-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn-kirim,
    .btn-batal {
        border: none;
        border-radius: 10px;
        padding: .72rem 1.5rem;
        font-weight: 700;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 105px;
    }

    .btn-kirim {
        background: #0d8df2;
    }

    .btn-batal {
        background: #ff2b2b;
    }
</style>
@endpush

@section('konten')
@php
    $mahasiswa = optional($jadwal)->mahasiswa;
    $user = optional($mahasiswa)->user;

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

    <div class="detail-section-title">
        <i class="ti ti-user"></i> Informasi Pribadi
    </div>
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

    <div class="detail-section-title">
        <i class="ti ti-clock"></i> Detail Jadwal
    </div>
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

    <div class="detail-section-title">
        <i class="ti ti-headphones"></i> Layanan
    </div>
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
            <td>Menunggu Konfirmasi</td>
        </tr>
    </table>

    <form action="{{ route('admin.sesi.tolak.kirim', $jadwal->id) }}" method="POST">
        @csrf

        <div class="detail-section-title">
            <i class="ti ti-clipboard-x"></i> Alasan Penolakan
        </div>

        <textarea name="alasan_penolakan"
                  class="alasan-textarea"
                  required
                  placeholder="Tuliskan alasan penolakan...">{{ old('alasan_penolakan') }}</textarea>

        <div class="detail-actions">
            <button type="submit" class="btn-kirim">Kirim</button>
            <a href="{{ route('admin.sesi.detail', $jadwal->id) }}" class="btn-batal">Batalkan</a>
        </div>
    </form>
</div>
@endsection