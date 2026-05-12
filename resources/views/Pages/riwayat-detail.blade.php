@extends('layouts.master')

@push('styles')
<style>
  .riwayat-detail-page {
    padding: 2.5rem 0 4rem;
    background: linear-gradient(180deg, #f3fbf6 0%, #ffffff 32%);
    min-height: 100vh;
  }

  .riwayat-detail-hero {
    margin-bottom: 1.5rem;
  }

  .riwayat-detail-breadcrumb {
    font-size: .82rem;
    color: #6b7280;
    margin-bottom: .7rem;
  }

  .riwayat-detail-breadcrumb a {
    color: #6b7280;
    text-decoration: none;
  }

  .riwayat-detail-breadcrumb a:hover {
    color: #0f7a4d;
  }

  .riwayat-detail-title {
    font-family: 'Fraunces', serif;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    color: #123024;
    margin: 0 0 .35rem;
  }

  .riwayat-detail-desc {
    max-width: 760px;
    color: #4b5563;
    line-height: 1.8;
    margin: 0;
  }

  .riwayat-detail-card {
    background: #fff;
    border: 1px solid #dfeee5;
    border-radius: 24px;
    box-shadow: 0 18px 40px rgba(14, 36, 23, .06);
    padding: 1.5rem;
  }

  .riwayat-detail-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: flex-start;
    flex-wrap: wrap;
    padding-bottom: 1rem;
    border-bottom: 1px solid #edf4ef;
    margin-bottom: 1rem;
  }

  .riwayat-detail-person {
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .riwayat-detail-avatar {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, #0f7a4d, #15a06d);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    flex: 0 0 auto;
  }

  .riwayat-detail-person h2 {
    font-size: 1.1rem;
    margin: 0 0 .2rem;
    color: #123024;
  }

  .riwayat-detail-person p {
    margin: 0;
    color: #6b7280;
    font-size: .88rem;
    line-height: 1.6;
  }

  .riwayat-detail-status {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: .38rem .8rem;
    font-size: .78rem;
    font-weight: 700;
    background: #eef9f2;
    color: #0f7a4d;
  }

  .riwayat-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  .riwayat-detail-box {
    border: 1px solid #e5efe8;
    border-radius: 18px;
    padding: 1rem;
    background: #fcfffd;
  }

  .riwayat-detail-box small {
    display: block;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .06em;
    font-size: .72rem;
    margin-bottom: .45rem;
  }

  .riwayat-detail-box strong,
  .riwayat-detail-box span,
  .riwayat-detail-box p {
    color: #123024;
  }

  .riwayat-detail-box p {
    margin: 0;
    line-height: 1.75;
    color: #334155;
  }

  .riwayat-detail-section {
    margin-top: 1rem;
    border-top: 1px solid #edf4ef;
    padding-top: 1rem;
  }

  .riwayat-detail-section h3 {
    margin: 0 0 .8rem;
    font-size: 1rem;
    color: #123024;
  }

  .riwayat-detail-empty {
    color: #94a3b8;
    background: #f8fafc;
    border: 1px dashed #d7e2db;
    border-radius: 14px;
    padding: .9rem 1rem;
    margin: 0;
  }

  .riwayat-detail-actions {
    margin-top: 1.25rem;
    display: flex;
    gap: .75rem;
    flex-wrap: wrap;
  }

  .riwayat-detail-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    padding: .75rem 1rem;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid transparent;
  }

  .riwayat-detail-btn.primary {
    background: #0f7a4d;
    color: #fff;
    border-color: #0f7a4d;
  }

  .riwayat-detail-btn.primary:hover {
    background: #0c633f;
    color: #fff;
  }

  .riwayat-detail-btn.secondary {
    background: #fff;
    color: #0f7a4d;
    border-color: #cde4d6;
  }

  .riwayat-detail-btn.secondary:hover {
    background: #f4fbf6;
    color: #0f7a4d;
  }

  @media (max-width: 767.98px) {
    .riwayat-detail-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('konten')
@php
  $tanggal = $jadwal->tanggal ? \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') : '-';
  $waktu = !empty($jadwal->waktu) ? substr((string) $jadwal->waktu, 0, 5) . ' WIB' : '-';
  $konselorNama = optional(optional($jadwal->konselor)->user)->nama ?? 'Konselor';
  $mahasiswaNama = optional(optional($jadwal->mahasiswa)->user)->nama ?? 'Mahasiswa';
  $nomorHuruf = strtoupper(substr($mahasiswaNama, 0, 1));
@endphp

<section class="riwayat-detail-page">
  <div class="container">
    <div class="riwayat-detail-hero">
      <div class="riwayat-detail-breadcrumb">
        <a href="{{ route('beranda') }}">Beranda</a> / <a href="{{ route('riwayat') }}">Riwayat</a> / Detail
      </div>
      <h1 class="riwayat-detail-title">Detail Riwayat Konseling</h1>
      <p class="riwayat-detail-desc">Ringkasan sesi yang pernah Anda jalani, termasuk topik, metode, status, dan catatan tindak lanjut bila tersedia.</p>
    </div>

    <div class="riwayat-detail-card">
      <div class="riwayat-detail-head">
        <div class="riwayat-detail-person">
          <div class="riwayat-detail-avatar">{{ $nomorHuruf ?: 'M' }}</div>
          <div>
            <h2>{{ $mahasiswaNama }}</h2>
            <p>{{ $tanggal }} • {{ $waktu }}</p>
            <p>Konselor: {{ $konselorNama }}</p>
          </div>
        </div>

        <span class="riwayat-detail-status {{ $statusInfo['class'] ?? 'status-default' }}">
          {{ $statusInfo['label'] ?? ucfirst((string) $jadwal->status) }}
        </span>
      </div>

      <div class="riwayat-detail-grid">
        <div class="riwayat-detail-box">
          <small>Topik</small>
          <strong>{{ $topik }}</strong>
        </div>
        <div class="riwayat-detail-box">
          <small>Metode</small>
          <strong>{{ $metode }}</strong>
        </div>
        <div class="riwayat-detail-box">
          <small>Durasi</small>
          <strong>{{ $jadwal->durasi ?? '60 Menit' }}</strong>
        </div>
        <div class="riwayat-detail-box">
          <small>Laporan</small>
          <strong>{{ $laporan ? 'Tersedia' : 'Belum tersedia' }}</strong>
        </div>
      </div>

      <div class="riwayat-detail-section">
        <h3>Ringkasan Masalah</h3>
        @if($catatanRingkasan !== '')
          <div class="riwayat-detail-box">
            <p>{{ $catatanRingkasan }}</p>
          </div>
        @else
          <p class="riwayat-detail-empty">Belum ada ringkasan masalah untuk sesi ini.</p>
        @endif
      </div>

      <div class="riwayat-detail-section">
        <h3>Observasi Konselor</h3>
        @if($observasi !== '')
          <div class="riwayat-detail-box">
            <p>{{ $observasi }}</p>
          </div>
        @else
          <p class="riwayat-detail-empty">Belum ada observasi konselor yang tercatat.</p>
        @endif
      </div>

      <div class="riwayat-detail-section">
        <h3>Progress</h3>
        @if($progress !== '')
          <div class="riwayat-detail-box">
            <p>{{ $progress }}</p>
          </div>
        @else
          <p class="riwayat-detail-empty">Belum ada progres yang dicatat.</p>
        @endif
      </div>

      <div class="riwayat-detail-section">
        <h3>Tindak Lanjut</h3>
        @if($tindakLanjut !== '' || $tanggalLanjut !== '')
          <div class="riwayat-detail-grid">
            <div class="riwayat-detail-box">
              <small>Jenis Tindak Lanjut</small>
              <strong>{{ $tindakLanjut !== '' ? $tindakLanjut : '-' }}</strong>
            </div>
            <div class="riwayat-detail-box">
              <small>Tanggal Lanjut</small>
              <strong>{{ $tanggalLanjut !== '' ? \Carbon\Carbon::parse($tanggalLanjut)->translatedFormat('d F Y') : '-' }}</strong>
            </div>
          </div>
        @else
          <p class="riwayat-detail-empty">Belum ada tindak lanjut yang dijadwalkan.</p>
        @endif
      </div>

      <div class="riwayat-detail-actions">
        <a href="{{ route('riwayat') }}" class="riwayat-detail-btn secondary">
          Kembali ke Riwayat
        </a>
        <a href="{{ route('profil') }}" class="riwayat-detail-btn primary">
          Buka Profil
        </a>
      </div>
    </div>
  </div>
</section>
@endsection
