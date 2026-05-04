@extends('layouts.master')

@section('konten')
@php
    $status = strtolower(trim($jadwal->jenis ?? 'menunggu konfirmasi'));

    $statusClass = match($status) {
        'menunggu konfirmasi' => 'bg-yellow',
        'disetujui', 'diterima' => 'bg-green',
        'ditolak' => 'bg-red',
        'sedang berlangsung' => 'bg-blue',
        'selesai' => 'bg-gray',
        default => 'bg-gray',
    };

    // ===== FIX JENIS =====
    $jenis = strtolower(trim($jadwal->jenis ?? $jadwal->metode ?? ''));

    // fallback dari catatan jika kosong
    if (!$jenis && !empty($jadwal->catatan) && str_contains($jadwal->catatan, 'Jenis:')) {
        $parts = explode('Jenis:', $jadwal->catatan);
        $jenis = strtolower(trim($parts[1]));
    }

    // NORMALISASI
    if ($jenis === 'online') {
        $mediaText = 'Chat, Video Call';
        $lokasiText = '-';
        $layananText = 'Online (Chat)';
    } elseif ($jenis === 'offline') {
        $mediaText = 'Tatap Muka';
        $lokasiText = 'Gedung 5 Lantai 2<br>(Antara GD 525 - GD 526)';
        $layananText = 'Offline (Tatap Muka)';
    } else {
        $mediaText = '-';
        $lokasiText = '-';
        $layananText = '-';
    }

    // ===== TOPIK =====
    $topikText = $jadwal->catatan ?? '-';

    if (!empty($jadwal->catatan) && str_contains($jadwal->catatan, 'Topik:')) {
        $parts = explode('|', $jadwal->catatan);
        $topikText = trim(str_replace('Topik:', '', $parts[0]));
    }

    $alasanText = $jadwal->alasan_penolakan ?? null;
@endphp

<div class="detail-page">

    <div class="detail-header">
        <a href="/riwayat" class="back-icon">←</a>
        <h1>Detail <span>Sesi Konseling</span></h1>
        <p>Lihat informasi lengkap dari sesi konseling yang telah Anda jadwalkan.</p>
    </div>

    <div class="detail-wrapper">

        <div class="side-section">
            <div class="counselor-card">
                <div class="counselor-top">
                    <div class="avatar">
                        <img src="{{ asset('assets/images/avatar-konselor.png') }}" alt="Konselor">
                    </div>
                    <div>
                        <h3>{{ $jadwal->konselor->nama ?? 'Laura' }}</h3>
                        <p>Konselor Utama</p>
                    </div>
                </div>

                <div class="info-row">
                    <span>⏱️ Durasi</span>
                    <strong>{{ $jadwal->durasi ?? '60 Menit' }}</strong>
                </div>

                <div class="info-row">
                    <span>🎥 Media</span>
                    <strong>{{ $mediaText }}</strong>
                </div>

                <div class="info-row">
                    <span>📍 Lokasi</span>
                    <strong>{!! $lokasiText !!}</strong>
                </div>
            </div>

            <div class="prepare-card">
                <h3>Persiapan Sesi</h3>

                @if($jenis == 'offline')
                    <p>Harap tiba 10 menit lebih awal di lokasi konseling yang telah ditentukan.</p>
                @else
                    <p>
                        Pastikan Anda berada di tempat yang tenang dan memiliki koneksi internet
                        yang stabil sebelum sesi dimulai. Kami merekomendasikan penggunaan
                        <em>earphone</em>.
                    </p>
                @endif
            </div>
        </div>

        <div class="detail-card">

            <h2>Detail Penjadwalan</h2>

            <div class="section-title">👤 Informasi Pribadi</div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>NIM</span>
                    <strong>{{ $jadwal->mahasiswa->nim ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Nama</span>
                    <strong>
                        {{ $jadwal->mahasiswa->user->nama ?? '-' }}
                    </strong>
                </div>

                <div class="detail-row">
                    <span>Program Studi</span>
                    <strong>{{ $jadwal->mahasiswa->program_studi ?? $jadwal->mahasiswa->jurusan ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Angkatan</span>
                    <strong>{{ $jadwal->mahasiswa->angkatan ?? '-' }}</strong>
                </div>
            </div>

            <div class="section-title">🕒 Detail Jadwal</div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Tanggal</span>
                    <strong>{{ $jadwal->tanggal }}</strong>
                </div>

                <div class="detail-row">
                    <span>Waktu</span>
                    <strong>{{ $jadwal->waktu }}</strong>
                </div>
            </div>

            <div class="section-title">🎧 Layanan</div>

            <div class="detail-list">
                <div class="detail-row">
                    <span>Layanan Konseling</span>
                    <strong>{{ $layananText }}</strong>
                </div>

                <div class="detail-row">
                    <span>Topik</span>
                    <strong>{{ $topikText }}</strong>
                </div>

                <div class="detail-row">
                    <span>Status</span>
                    <span class="status-pill {{ $statusClass }}">
                        {{ ucwords($jadwal->status) }}
                    </span>
                </div>

               @php
                    $statusLower = strtolower($jadwal->status ?? '');
                @endphp

                @if(in_array($statusLower, ['menunggu', 'menunggu konfirmasi']))
                    <div class="action-center">
                        <a href="{{ route('riwayat') }}" class="btn-ubah">
                            Kembali ke Riwayat
                        </a>
                    </div>
                @endif

@if(in_array($status, ['disetujui', 'diterima', 'sedang berlangsung']) && !empty($jadwal->link_zoom))
    <div class="detail-row zoom-row">
        <span>Link Zoom</span>

        <div class="zoom-link-box">
            <a href="{{ $jadwal->link_zoom }}" target="_blank">
                {{ $jadwal->link_zoom }}
            </a>

            <button type="button" class="btn-copy" onclick="copyZoomLink('{{ $jadwal->link_zoom }}')">
                Copy
            </button>
        </div>
    </div>
@endif

            </div>

            @php
    $statusLower = strtolower($jadwal->status ?? '');
@endphp

<div class="action-section" style="margin-top: 20px;">

    {{-- DITERIMA --}}
    @if(in_array($statusLower, ['disetujui', 'diterima']))
    <div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('sesi.mulai', $jadwal->id) }}" class="btn-mulai">
        Mulai Sesi
    </a>
</div>

    {{-- MENUNGGU --}}
    @elseif($statusLower == 'menunggu konfirmasi')
        <button class="btn-disabled">Menunggu Konfirmasi</button>

    {{-- DITOLAK --}}
    @elseif($statusLower == 'ditolak')
        <a href="{{ route('riwayat') }}" class="btn-secondary">
            Kembali ke Riwayat
        </a>

    {{-- BERLANGSUNG --}}
    @elseif($statusLower == 'sedang berlangsung')
        <a href="{{ route('sesi.lanjut', $jadwal->id) }}" class="btn-mulai">
            Lanjutkan Sesi
        </a>

    {{--     SELESAI --}}
    @elseif($statusLower == 'selesai')
        <a href="{{ route('sesi.detail', $jadwal->id) }}" class="btn-secondary">
            Lihat Sesi
        </a>
    @endif

</div>

            @if($status == 'ditolak')
                <div class="catatan">
                    <strong>Alasan Penolakan:</strong>
                    <p>{{ $alasanText ?: 'Tidak ada keterangan.' }}</p>
                </div>
            @endif

            @if($status == 'selesai')
                <div class="catatan">
                    <strong>Catatan Konselor:</strong>
                    <p>{{ $jadwal->catatan ?? 'Tidak ada catatan.' }}</p>
                </div>
            @endif

            <div class="bottom-action">

                @if($status == 'menunggu konfirmasi')
                    <a href="#" class="btn-outline">Ubah Jadwal</a>
                @endif

                @if(in_array($status, ['disetujui', 'diterima']))
                    @if($jenis == 'online' && !empty($jadwal->link_zoom))
                        <a href="{{ $jadwal->link_zoom }}" target="_blank" class="btn-primary">Mulai Sesi</a>
                    @elseif($jenis == 'online')
                        <button class="btn-disabled" disabled>Link Zoom Belum Ada</button>
                    @else
                        <a href="#" class="btn-primary">Mulai Sesi</a>
                    @endif
                @endif

                @if($status == 'sedang berlangsung')
                    @if($jenis == 'online' && !empty($jadwal->link_zoom))
                        <a href="{{ $jadwal->link_zoom }}" target="_blank" class="btn-primary">Lanjutkan Sesi</a>
                    @elseif($jenis == 'online')
                        <button class="btn-disabled" disabled>Link Zoom Belum Ada</button>
                    @else
                        <a href="#" class="btn-primary">Lanjutkan Sesi</a>
                    @endif
                @endif

                @if($status == 'selesai')
                    <a href="#" class="btn-outline">Lihat Sesi</a>
                @endif

                @if(in_array($status, ['ditolak', 'selesai']))
                    <a href="/riwayat" class="btn-back">Kembali ke Riwayat</a>
                @endif

            </div>

        </div>
    </div>
</div>

<style>
.detail-page {
    padding: 70px 80px;
    background: #F6FBF8;
    min-height: 100vh;
    color: #242424;
}

.back-icon {
    width: 34px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: white;
    color: #1F2937;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    text-decoration: none;
    margin-bottom: 18px;
}

.detail-header h1 {
    font-size: 54px;
    line-height: 1.08;
    font-weight: 900;
    margin-bottom: 14px;
    max-width: 720px;
}

.detail-header span {
    color: #416B4C;
}

.detail-header p {
    color: #6B7280;
    margin-bottom: 34px;
    font-size: 17px;
    max-width: 560px;
    line-height: 1.6;
}

.detail-wrapper {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 42px;
    align-items: flex-start;
}

.side-section {
    display: flex;
    flex-direction: column;
    gap: 26px;
}

.counselor-card,
.detail-card {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 24px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
}

.counselor-card {
    padding: 24px;
}

.counselor-top {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 28px;
}

.avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #DDF6F2;
    overflow: hidden;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.counselor-top h3 {
    margin: 0;
    font-size: 17px;
}

.counselor-top p {
    margin: 5px 0 0;
    color: #55715F;
    font-size: 14px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    gap: 18px;
    padding: 15px 0;
    border-top: 1px solid #E5E7EB;
    font-size: 14px;
}

.info-row span {
    color: #55715F;
}

.info-row strong {
    color: #416B4C;
    text-align: right;
}

.prepare-card {
    background: #D1F0D5;
    border-radius: 24px;
    padding: 24px;
    color: #047857;
}

.prepare-card h3 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 18px;
}

.prepare-card p {
    margin: 0;
    line-height: 1.7;
    font-size: 14px;
}

.detail-card {
    padding: 28px 32px 34px;
}

.detail-card h2 {
    font-size: 25px;
    margin: 0 0 25px;
}

.section-title {
    font-size: 19px;
    font-weight: 800;
    margin: 24px 0 12px;
}

.detail-list {
    width: 100%;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    padding: 12px 0;
    border-bottom: 1px solid #E5E7EB;
    font-size: 16px;
}

.detail-row span {
    color: #444;
}

.detail-row strong {
    text-align: right;
    font-weight: 800;
}

.status-pill {
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
    display: inline-block;
}

.bg-blue { background: #DBEAFE; color: #1D4ED8; }
.bg-red { background: #FEE2E2; color: #B91C1C; }
.bg-green { background: #D1FAE5; color: #047857; }
.bg-yellow { background: #FEF3C7; color: #B45309; }
.bg-gray { background: #F3F4F6; color: #4B5563; }

.catatan {
    margin-top: 22px;
}

.catatan strong {
    display: block;
    margin-bottom: 8px;
}

.catatan p {
    margin: 0;
}

.bottom-action {
    display: flex;
    justify-content: center;
    gap: 14px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn-mulai {
    display: inline-block;
    padding: 12px 26px;
    background: #064E3B;
    color: white;
    border-radius: 999px;
    font-weight: 700;
    text-decoration: none;
}

.btn-mulai:hover {
    background: #043d2f;
}

.action-center {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.btn-ubah {
    background: #FDBA5A;
    color: #fff;
    padding: 10px 20px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}

.btn-ubah:hover {
    background: #f59e0b;
}

.btn-secondary {
    display: inline-block;
    padding: 12px 26px;
    background: #E5E7EB;
    color: #374151;
    border-radius: 999px;
    font-weight: 600;
    text-decoration: none;
}

.action-center {
    display: flex;
    justify-content: center;
    margin-top: 24px;
}

.btn-ubah {
    background: #064E3B;
    color: white;
    padding: 12px 28px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 700;
}

.btn-disabled {
    padding: 12px 26px;
    background: #F3F4F6;
    color: #9CA3AF;
    border-radius: 999px;
    border: none;
}

.btn-outline,
.btn-primary,
.btn-back,
.btn-disabled {
    min-width: 220px;
    text-align: center;
    padding: 12px 22px;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 700;
    transition: 0.2s ease;
}

.btn-outline {
    border: 1px solid #D1D5DB;
    color: #374151;
    background: white;
}

.btn-primary {
    background: #064E3B;
    color: white;
    border: 1px solid #064E3B;
}

.btn-back {
    background: #F3F4F6;
    color: #374151;
    border: 1px solid #E5E7EB;
}

.btn-disabled {
    background: #E5E7EB;
    color: #6B7280;
    border: 1px solid #D1D5DB;
    cursor: not-allowed;
}

.zoom-row {
    align-items: center;
}

.zoom-link-box {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 70%;
}

.zoom-link-box a {
    color: #064E3B;
    font-weight: 700;
    word-break: break-all;
}

.btn-copy {
    border: none;
    background: #E5F7EF;
    color: #064E3B;
    padding: 8px 14px;
    border-radius: 999px;
    font-weight: 700;
    cursor: pointer;
}

.btn-copy:hover {
    background: #CFEFD8;
}

@media (max-width: 900px) {
    .detail-page {
        padding: 40px 24px;
    }

    .detail-header h1 {
        font-size: 40px;
    }

    .detail-wrapper {
        grid-template-columns: 1fr;
    }

    .bottom-action {
        flex-direction: column;
    }

    .btn-outline,
    .btn-primary,
    .btn-back,
    .btn-disabled {
        width: 100%;
    }
}
</style>

<script>
function copyZoomLink(link) {
    navigator.clipboard.writeText(link).then(function () {
        alert('Link Zoom berhasil disalin.');
    });
}
</script>

@endsection 