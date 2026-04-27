@extends('layouts.master')

@push('styles')
<style>
  .service-page {
    --care-green: #0A523A;
    --care-green-dark: #063B2A;
    --care-green-mid: #0D6A4B;
    --care-green-soft: #E5F7EF;
    --care-mint: #CFEFD8;
    --care-warm: #FDBA5A;
    --care-warm-soft: #FFF1DA;
    --care-bg: #F6FBF8;
    --care-card: #FFFFFF;
    --care-border: #DCE9E3;
    --care-text: #26312D;
    --care-muted: #66736E;
    background: var(--care-bg);
    color: var(--care-text);
    min-height: 100vh;
    padding-bottom: 4.5rem;
  }

  .service-page a {
    text-decoration: none;
  }

  .service-hero {
    position: relative;
    overflow: hidden;
    padding: 4.8rem 0 1.1rem;
    background:
      linear-gradient(90deg, rgba(246, 251, 248, .98) 0%, rgba(246, 251, 248, .92) 54%, rgba(230, 244, 235, .82) 100%),
      url("{{ asset('img/bg.png') }}") center bottom / cover no-repeat;
  }

  .hero-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    border-radius: 999px;
    background: #F8D59E;
    color: #7B4E05;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: .42rem .82rem;
  }

  .service-title {
    max-width: 860px;
    margin: 1.15rem 0 1rem;
    color: var(--care-text);
    font-size: clamp(2.55rem, 5.6vw, 5.25rem);
    font-weight: 800;
    letter-spacing: -.035em;
    line-height: .98;
  }

  .service-title span {
    color: #46644D;
  }

  .service-anchor {
    scroll-margin-top: 110px;
  }

  .service-mode-strip {
    padding: .8rem 0 1.5rem;
  }

  .mode-panel {
    height: 100%;
    border: 1px solid var(--care-border);
    border-radius: 24px;
    background: #fff;
    padding: 1.35rem;
    transition: border-color .2s ease, box-shadow .2s ease;
  }

  .mode-panel.active {
    border-color: rgba(10, 82, 58, .36);
    box-shadow: 0 18px 42px rgba(10, 82, 58, .1);
  }

  .mode-panel-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.1rem;
  }

  .mode-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    border-radius: 999px;
    background: var(--care-green-soft);
    color: var(--care-green);
    padding: .42rem .78rem;
    font-size: .73rem;
    font-weight: 800;
  }

  .mode-panel.online .mode-badge {
    background: #E7F2FF;
    color: #1F5F8B;
  }

  .mode-panel h2 {
    margin: 0 0 .55rem;
    color: var(--care-text);
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing: -.015em;
  }

  .mode-panel p {
    margin: 0;
    color: var(--care-muted);
    font-size: .9rem;
    line-height: 1.7;
  }

  .mode-facts {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .65rem;
    margin-top: 1.15rem;
  }

  .mode-fact {
    min-height: 82px;
    border-radius: 16px;
    background: #F7FAF8;
    padding: .85rem;
  }

  .mode-fact i {
    color: var(--care-green);
    font-size: 1.05rem;
  }

  .mode-panel.online .mode-fact i {
    color: #1F5F8B;
  }

  .mode-fact strong {
    display: block;
    margin-top: .4rem;
    color: var(--care-text);
    font-size: .8rem;
    font-weight: 800;
  }

  .mode-fact span {
    display: block;
    margin-top: .15rem;
    color: var(--care-muted);
    font-size: .72rem;
    line-height: 1.35;
  }

  .mode-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    min-height: 44px;
    margin-top: 1.1rem;
    border: 0;
    border-radius: 999px;
    background: var(--care-green);
    color: #fff;
    padding: .62rem 1rem;
    font-size: .82rem;
    font-weight: 800;
    transition: background .2s ease, transform .2s ease;
  }

  .mode-panel.online .mode-action {
    background: #1F5F8B;
  }

  .mode-action:hover {
    background: var(--care-green-dark);
    color: #fff;
    transform: translateY(-1px);
  }

  .mode-panel.online .mode-action:hover {
    background: #17496E;
  }

  .booking-layout {
    align-items: start;
    row-gap: 2rem;
  }

  .booking-shell {
    display: none;
  }

  .booking-shell.is-visible {
    display: block;
  }

  .counselor-card,
  .session-note,
  .schedule-card {
    border: 1px solid var(--care-border);
    background: var(--care-card);
  }

  .counselor-card {
    border-radius: 28px;
    padding: 1.7rem;
  }

  .counselor-head {
    display: flex;
    align-items: center;
    gap: 1.1rem;
    margin-bottom: 1.8rem;
  }

  .counselor-avatar {
    width: 78px;
    height: 78px;
    border-radius: 50%;
    background: #BDEBFF;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: var(--care-green-dark);
    font-size: 2.2rem;
  }

  .counselor-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .counselor-name {
    margin: 0 0 .35rem;
    color: #111;
    font-size: 1.18rem;
    font-weight: 800;
  }

  .counselor-role {
    color: #5C7564;
    font-size: .92rem;
  }

  .info-list {
    display: grid;
    gap: 0;
  }

  .info-row {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: .9rem;
    align-items: start;
    padding: 1rem 0;
    border-top: 1px solid var(--care-border);
  }

  .info-row:first-child {
    border-top: 0;
  }

  .info-row i {
    color: #526B59;
    font-size: 1.15rem;
    line-height: 1.5;
  }

  .info-label {
    color: #526B59;
    font-size: .95rem;
  }

  .info-value {
    max-width: 235px;
    color: #526B59;
    text-align: right;
    font-size: .95rem;
    font-weight: 800;
    line-height: 1.45;
  }

  .session-note {
    margin-top: 1.4rem;
    border-color: #C9E8CA;
    border-radius: 28px;
    background: #D8F1D7;
    padding: 1.45rem;
    color: var(--care-green);
  }

  .session-note.online {
    border-color: #CFE3F7;
    background: #E7F2FF;
    color: #1F5F8B;
  }

  .session-note h3 {
    margin: 0 0 .7rem;
    color: inherit;
    font-size: 1.05rem;
    font-weight: 800;
  }

  .session-note p {
    margin: 0;
    font-size: .94rem;
    line-height: 1.75;
  }

  .schedule-card {
    border-radius: 28px;
    box-shadow: 0 18px 28px rgba(38, 47, 43, .12);
    padding: 2.2rem;
  }

  .schedule-card-head {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.7rem;
  }

  .schedule-title {
    margin: 0 0 .45rem;
    color: #0B0D0C;
    font-size: clamp(1.55rem, 2.4vw, 2.05rem);
    font-weight: 800;
    letter-spacing: -.02em;
  }

  .schedule-subtitle {
    max-width: 560px;
    margin: 0;
    color: var(--care-muted);
    font-size: .9rem;
    line-height: 1.65;
  }

  .selected-mode-pill {
    align-self: flex-start;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    border-radius: 999px;
    background: var(--care-green-soft);
    color: var(--care-green);
    padding: .52rem .85rem;
    font-size: .76rem;
    font-weight: 800;
  }

  .selected-mode-pill.online {
    background: #E7F2FF;
    color: #1F5F8B;
  }

  .form-section-title {
    display: flex;
    align-items: center;
    gap: .72rem;
    margin: 1.65rem 0 1rem;
    color: #0B0D0C;
    font-size: 1.17rem;
    font-weight: 800;
  }

  .form-section-title:first-of-type {
    margin-top: 0;
  }

  .form-section-title i {
    font-size: 1.22rem;
  }

  .field-label {
    display: block;
    margin-bottom: .45rem;
    color: #3F4844;
    font-size: .92rem;
    font-weight: 700;
  }

  .schedule-input,
  .schedule-select {
    width: 100%;
    min-height: 54px;
    border: 1px solid #D5D5D5;
    border-radius: 12px;
    background: #fff;
    color: #404844;
    font-size: .98rem;
    padding: .82rem 1rem;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease;
  }

  .schedule-input:focus,
  .schedule-select:focus {
    border-color: #A4C8AE;
    box-shadow: 0 0 0 4px rgba(10, 82, 58, .08);
  }

  .schedule-input[disabled] {
    background: #FCFCFC;
    color: #46514B;
    cursor: not-allowed;
  }

  .input-icon-wrap {
    position: relative;
  }

  .input-icon-wrap i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #BDBDBD;
    font-size: 1.18rem;
    pointer-events: none;
  }

  .input-icon-wrap .schedule-input,
  .input-icon-wrap .schedule-select {
    padding-left: 2.85rem;
  }

  .media-options {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .8rem;
  }

  .media-pill {
    border: 1px solid #D7D7D7;
    border-radius: 18px;
    background: #fff;
    color: #0B0D0C;
    padding: .95rem 1rem;
    text-align: left;
    font-size: .94rem;
    line-height: 1.25;
    font-weight: 800;
  }

  .media-pill i {
    margin-right: .45rem;
    color: var(--care-green);
  }

  .media-pill span {
    display: block;
    margin-top: .35rem;
    color: var(--care-muted);
    font-size: .76rem;
    font-weight: 600;
    line-height: 1.45;
  }

  .media-pill.active {
    border-color: rgba(10, 82, 58, .38);
    background: #F4FBF7;
    box-shadow: inset 0 0 0 1px rgba(10, 82, 58, .08);
  }

  .disabled-note {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: 1rem;
    border-radius: 999px;
    background: var(--care-green-soft);
    color: var(--care-green-dark);
    padding: .45rem .8rem;
    font-size: .75rem;
    font-weight: 800;
  }

  .form-note {
    margin-top: .65rem;
    color: var(--care-muted);
    font-size: .8rem;
    line-height: 1.55;
  }

  .submit-wrap {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
  }

  .schedule-submit {
    width: min(100%, 430px);
    min-height: 60px;
    border: 0;
    border-radius: 999px;
    background: var(--care-green);
    color: #fff;
    font-size: 1.05rem;
    font-weight: 800;
    transition: all .2s ease;
  }

  .schedule-submit:hover {
    background: var(--care-green-dark);
    transform: translateY(-1px);
  }

  .schedule-submit.online {
    background: #1F5F8B;
  }

  .schedule-submit.online:hover {
    background: #17496E;
  }

  .schedule-submit:disabled {
    opacity: .72;
    cursor: wait;
    transform: none;
  }

  .success-screen {
    display: none;
    border: 1px solid #CFE8D7;
    border-radius: 20px;
    background: #F3FBF6;
    padding: 1.25rem;
    margin-top: 1.5rem;
  }

  .success-screen h4 {
    margin-bottom: .4rem;
    color: var(--care-green);
    font-weight: 800;
  }

  .success-screen p {
    margin: 0;
    color: var(--care-muted);
  }

  .success-detail {
    margin-top: 1rem;
    display: grid;
    gap: .55rem;
  }

  .success-detail-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    border-top: 1px dashed #CFE8D7;
    padding-top: .55rem;
    color: #46514B;
    font-size: .9rem;
  }

  @media (max-width: 991.98px) {
    .service-hero {
      padding-bottom: 2.7rem;
    }

    .schedule-card {
      padding: 1.6rem;
    }

    .schedule-card-head {
      flex-direction: column;
    }
  }

  @media (max-width: 767.98px) {
    .mode-facts,
    .media-options {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 575.98px) {
    .service-title {
      font-size: clamp(2.35rem, 14vw, 3.65rem);
    }

    .counselor-card,
    .session-note,
    .schedule-card {
      border-radius: 22px;
    }

    .info-row {
      grid-template-columns: auto 1fr;
    }

    .info-value {
      grid-column: 2;
      text-align: left;
      max-width: none;
    }

    .disabled-note {
      align-items: flex-start;
      border-radius: 14px;
      line-height: 1.45;
    }
  }
</style>
@endpush

@section('konten')
@php
  $user = Auth::user();
  $mahasiswa = optional($user)->mahasiswa;
  $profil = optional($user)->profil;
  $isAnonim = $user ? $user->isAnonim() : false;
  $namaMahasiswa = $user?->nama ?? 'Silakan login';
  $nimMahasiswa = optional($mahasiswa)->nim ?? '-';
  $jurusanMahasiswa = optional($mahasiswa)->jurusan ?? '-';
  $angkatanMahasiswa = optional($mahasiswa)->angkatan ?? '-';
  $fotoProfil = optional($profil)->foto ? Storage::url($profil->foto) : null;
@endphp

<section class="service-page">
  <div class="service-hero">
    <div class="container">
      <div class="hero-kicker">Penjadwalan Konseling</div>
      <h1 class="service-title">Temukan Waktu <span>Terbaikmu</span><br>Untuk Bercerita</h1>
    </div>
  </div>

  <div class="service-mode-strip">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-6 service-anchor" id="online">
          <article class="mode-panel online" data-panel="online">
            <div class="mode-panel-head">
              <span class="mode-badge"><i class="bi bi-wifi"></i> Online</span>
            </div>
            <h2>Konseling Online</h2>
            <p>Gunakan layanan ini bila kamu ingin memulai percakapan dari tempat yang tenang. Data yang disimpan adalah jenis online, tanggal, waktu, dan topik.</p>
            <div class="mode-facts">
              <div class="mode-fact">
                <i class="bi bi-clock"></i>
                <strong>60 menit</strong>
                <span>Durasi sesi</span>
              </div>
              <div class="mode-fact">
                <i class="bi bi-camera-video"></i>
                <strong>Video/Chat</strong>
                <span>Media online</span>
              </div>
              <div class="mode-fact">
                <i class="bi bi-house-heart"></i>
                <strong>Ruang privat</strong>
                <span>Dari lokasi nyaman</span>
              </div>
            </div>
            <a href="#booking" class="mode-action" data-mode-action="online">Pilih Online <i class="bi bi-arrow-right"></i></a>
          </article>
        </div>

        <div class="col-lg-6 service-anchor" id="offline">
          <article class="mode-panel" data-panel="offline">
            <div class="mode-panel-head">
              <span class="mode-badge"><i class="bi bi-person-walking"></i> Offline</span>
            </div>
            <h2>Konseling Offline</h2>
            <p>Gunakan layanan ini bila kamu ingin bertemu langsung dengan konselor di kampus. Data yang disimpan adalah jenis offline, tanggal, waktu, dan topik.</p>
            <div class="mode-facts">
              <div class="mode-fact">
                <i class="bi bi-stopwatch"></i>
                <strong>60 menit</strong>
                <span>Durasi sesi</span>
              </div>
              <div class="mode-fact">
                <i class="bi bi-people"></i>
                <strong>Tatap muka</strong>
                <span>Media sesi</span>
              </div>
              <div class="mode-fact">
                <i class="bi bi-geo-alt-fill"></i>
                <strong>Gedung 5</strong>
                <span>Lantai 2</span>
              </div>
            </div>
            <a href="#penjadwalan" class="mode-action" data-mode-action="offline">Pilih Offline <i class="bi bi-arrow-right"></i></a>
          </article>
        </div>
      </div>
    </div>
  </div>

  <div class="container service-anchor booking-shell" id="booking">
    <div class="row booking-layout g-5">
      <div class="col-lg-4">
        <aside class="counselor-card">
          <div class="counselor-head">
            <div class="counselor-avatar">
              <i class="bi bi-person-fill"></i>
            </div>
            <div>
              <h2 class="counselor-name">Laura</h2>
              <div class="counselor-role">Konselor Utama</div>
            </div>
          </div>

          <div class="info-list">
            <div class="info-row">
              <i class="bi bi-stopwatch"></i>
              <div class="info-label">Durasi</div>
              <div class="info-value">60 Menit</div>
            </div>
            <div class="info-row">
              <i class="bi bi-headset"></i>
              <div class="info-label">Media</div>
              <div class="info-value" id="side-media">Tatap Muka</div>
            </div>
            <div class="info-row">
              <i class="bi bi-geo-alt-fill"></i>
              <div class="info-label">Lokasi</div>
              <div class="info-value" id="side-location">Gedung 5 Lantai 2<br>(Antara GD 525 - GD 526)</div>
            </div>
          </div>
        </aside>

        <div class="session-note" id="session-note">
          <h3>Persiapan Sesi</h3>
          <p id="session-note-text">Harap tiba 10 menit lebih awal sebagai persiapan awal.</p>
        </div>
      </div>

      <div class="col-lg-8">
        <main class="schedule-card">
          <div class="schedule-card-head">
            <div>
              <h2 class="schedule-title">Detail Penjadwalan</h2>
              <p class="schedule-subtitle" id="schedule-subtitle">
                Lengkapi tanggal, waktu, dan topik untuk mengajukan konseling offline.
              </p>
            </div>
            <span class="selected-mode-pill" id="selected-mode-pill">
              <i class="bi bi-geo-alt"></i>
              Online
            </span>
          </div>

          <div class="form-section-title">
            <i class="bi bi-person-fill"></i>
            <span>Informasi Pribadi</span>
          </div>

          <div class="disabled-note">
            <i class="bi bi-lock"></i>
            Data terhubung langsung dengan profil mahasiswa dan tidak dapat diubah dari halaman ini.
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <label class="field-label" for="profile-nim">NIM</label>
              <input type="text" class="schedule-input" id="profile-nim" value="{{ $isAnonim ? '********' : $nimMahasiswa }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="field-label" for="profile-jurusan">Program Studi</label>
              <input type="text" class="schedule-input" id="profile-jurusan" value="{{ $jurusanMahasiswa }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="field-label" for="profile-nama">Nama</label>
              <input type="text" class="schedule-input" id="profile-nama" value="{{ $isAnonim ? 'Mahasiswa Anonim' : $namaMahasiswa }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="field-label" for="profile-angkatan">Angkatan</label>
              <input type="text" class="schedule-input" id="profile-angkatan" value="{{ $angkatanMahasiswa }}" disabled>
            </div>
          </div>

          <div class="form-section-title">
            <i class="bi bi-clock"></i>
            <span>Detail Jadwal</span>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <label class="field-label" for="tanggal">Tanggal</label>
              <div class="input-icon-wrap">
                <i class="bi bi-calendar-fill"></i>
                <input type="date" class="schedule-input" id="tanggal">
              </div>
              <div class="form-note" id="tanggal-note">Pilih hari layanan Senin sampai Jumat.</div>
            </div>
            <div class="col-md-6">
              <label class="field-label" for="waktu">Waktu</label>
              <div class="input-icon-wrap">
                <i class="bi bi-clock-fill"></i>
                <select class="schedule-select" id="waktu">
                  <option value="">Pilih waktu</option>
                </select>
              </div>
              <div class="form-note" id="waktu-note">Slot yang sudah terisi akan otomatis dinonaktifkan.</div>
            </div>
          </div>

          <div class="form-section-title">
            <i class="bi bi-headphones"></i>
            <span>Layanan</span>
          </div>

          <div class="row g-4">
            <div class="col-md-7">
              <label class="field-label" for="topik">Topik Konseling</label>
              <select class="schedule-select" id="topik" onchange="handleTopikChange()">
                <option value="">Pilih topik konseling</option>
                <option value="Akademik (TA, Kuliah, KP, MBKM, others)">Akademik (TA, Kuliah, KP, MBKM, others)</option>
                <option value="Kehidupan di Kampus">Kehidupan di Kampus</option>
                <option value="Intrapersonal (Kecemasan, Kejenuhan, Motivasi Belajar, dll)">Intrapersonal (Kecemasan, Kejenuhan, Motivasi Belajar, dll)</option>
                <option value="Keluarga">Keluarga</option>
                <option value="Masalah di asrama">Masalah di asrama</option>
                <option value="Relasi (pertemanan, pacaran, ketidaknyamanan di asrama, kesalahpahaman)">Relasi (pertemanan, pacaran, ketidaknyamanan di asrama, kesalahpahaman)</option>
                <option value="lainnya">Lainnya</option>
              </select>
              <input
                type="text"
                id="topik-lainnya"
                class="schedule-input"
                placeholder="Tuliskan topik konseling..."
                style="display:none;"
              >
            </div>
            <div class="col-md-5">
              <label class="field-label">Jenis Tersimpan</label>
              <input type="text" class="schedule-input" id="jenis-display" value="Online" disabled>
            </div>

            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="confirmation-checkbox">
              <label class="form-check-label" for="confirmation-checkbox">
                  <span style="font-size: 0.9rem; color: #555;">Saya sudah memeriksa dan memastikan data penjadwalan sudah benar.</span>
              </label>
            </div>
            <div class="submit-wrap">
              <button type="button" class="schedule-submit" id="submit-booking" onclick="submitJadwal()">
                Jadwalkan Konseling
              </button>
            </div>
          

          <div class="success-screen" id="success-booking">
            <h4>Pengajuan jadwal berhasil</h4>
            <p>Jadwalmu sudah dibuat dan menunggu persetujuan konselor.</p>
            <div class="success-detail" id="success-detail"></div>
          </div>
        </main>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
const bookedSlots = new Set();
const serviceTimes = ['09:00','10:00','11:00','13:00','14:00','15:00','16:00'];
let selectedService = 'offline';

const serviceConfig = {
  online: {
    label: 'Online',
    stored: 'online',
    icon: 'bi-camera-video',
    subtitle: 'Lengkapi tanggal, waktu, dan topik untuk mengajukan konseling online.',
    sideMedia: 'Video / Chat',
    sideLocation: 'Online<br>Link sesi menyusul setelah disetujui',
    noteClass: 'online',
    note: 'Pastikan kamu berada di tempat yang tenang dan memiliki koneksi internet stabil sebelum sesi dimulai.',
    submit: 'Jadwalkan Online',
    mediaPrimaryIcon: 'bi-camera-video',
    mediaPrimary: 'Video / Chat',
    mediaPrimaryText: 'Media online akan dikonfirmasi setelah jadwal disetujui.',
    mediaSecondaryIcon: 'bi-wifi',
    mediaSecondary: 'Koneksi Stabil',
    mediaSecondaryText: 'Siapkan perangkat dan jaringan internet sebelum sesi dimulai.',
  },
  offline: {
    label: 'Offline',
    stored: 'offline',
    icon: 'bi-geo-alt',
    subtitle: 'Lengkapi tanggal, waktu, dan topik untuk mengajukan konseling offline.',
    sideMedia: 'Tatap Muka',
    sideLocation: 'Gedung 5 Lantai 2<br>(Antara GD 525 - GD 526)',
    noteClass: '',
    note: 'Harap tiba 10 menit lebih awal sebagai persiapan awal.',
    submit: 'Jadwalkan Offline',
    mediaPrimaryIcon: 'bi-people',
    mediaPrimary: 'Tatap Muka',
    mediaPrimaryText: 'Bertemu langsung dengan konselor di kampus.',
    mediaSecondaryIcon: 'bi-geo-alt',
    mediaSecondary: 'Ruang Kampus',
    mediaSecondaryText: 'Gedung 5 Lantai 2, antara GD 525 - GD 526.',
  }
};

const tanggalEl = document.getElementById('tanggal');
const waktuEl = document.getElementById('waktu');
const topikEl = document.getElementById('topik');
const topikLainnyaEl = document.getElementById('topik-lainnya');
const submitBtn = document.getElementById('submit-booking');
const tanggalNote = document.getElementById('tanggal-note');
const waktuNote = document.getElementById('waktu-note');
const successEl = document.getElementById('success-booking');

function todayYmd() {
  const d = new Date();
  d.setHours(0, 0, 0, 0);
  return toYmd(d);
}

function toYmd(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

function parseYmd(ymd) {
  const [year, month, day] = ymd.split('-').map(Number);
  return new Date(year, month - 1, day);
}

function isWeekday(ymd) {
  const day = parseYmd(ymd).getDay();
  return day >= 1 && day <= 5;
}

function setMediaButton(button, icon, label, text) {
  button.innerHTML = `<i class="bi ${icon}"></i> ${label}<span>${text}</span>`;
}

function handleTopikChange() {
  const select = document.getElementById('topik');
  const input = document.getElementById('topik-lainnya');

  if (select.value === 'lainnya') {
    select.style.display = 'none';
    input.style.display = 'block';
    input.value = '';
    input.focus();
  } else {
    select.style.display = 'block';
    input.style.display = 'none';
    input.value = '';
  }
}

function resetTopikSelect() {
  const select = document.getElementById('topik');
  const input = document.getElementById('topik-lainnya');

  select.style.display = 'block';
  select.value = '';
  input.style.display = 'none';
  input.value = '';
}

function getTopikValue() {
  const select = document.getElementById('topik');
  const input = document.getElementById('topik-lainnya');

  if (input.style.display !== 'none') {
    const customTopik = input.value.trim();
    if (!customTopik) {
      alert('Isi topik konseling terlebih dahulu.');
      return null;
    }
    return customTopik;
  }

  if (!select.value) {
    alert('Pilih topik konseling terlebih dahulu.');
    return null;
  }

  return select.value;
}

function setServiceMode(mode, shouldScroll = false) {
  if (!serviceConfig[mode]) return;

  selectedService = mode;
  const config = serviceConfig[mode];
  document.getElementById('booking').classList.add('is-visible');

  document.querySelectorAll('[data-mode], [data-mode-action]').forEach(el => {
    el.classList.toggle('active', (el.dataset.mode || el.dataset.modeAction) === mode);
  });

  document.querySelectorAll('[data-panel]').forEach(panel => {
    panel.classList.toggle('active', panel.dataset.panel === mode);
  });

  document.getElementById('schedule-subtitle').textContent = config.subtitle;
  document.getElementById('selected-mode-pill').className = `selected-mode-pill ${mode === 'online' ? 'online' : ''}`;
  document.getElementById('selected-mode-pill').innerHTML = `<i class="bi ${config.icon}"></i> ${config.label}`;
  document.getElementById('jenis-display').value = config.label;
  document.getElementById('jenis-note').textContent = config.stored;
  document.getElementById('side-media').innerHTML = config.sideMedia;
  document.getElementById('side-location').innerHTML = config.sideLocation;
  document.getElementById('session-note').className = `session-note ${config.noteClass}`;
  document.getElementById('session-note-text').textContent = config.note;
  setMediaButton(document.getElementById('media-primary'), config.mediaPrimaryIcon, config.mediaPrimary, config.mediaPrimaryText);
  setMediaButton(document.getElementById('media-secondary'), config.mediaSecondaryIcon, config.mediaSecondary, config.mediaSecondaryText);
  submitBtn.textContent = config.submit;
  submitBtn.classList.toggle('online', mode === 'online');

  if (successEl) {
    successEl.style.display = 'none';
  }

  if (shouldScroll) {
    document.getElementById('booking').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

async function fetchBookedSlots() {
  try {
    const res = await fetch('{{ route("jadwal.terisi") }}', {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    bookedSlots.clear();
    data.forEach(slot => bookedSlots.add(slot));
  } catch (error) {
    bookedSlots.clear();
  }
}

function renderTimeOptions() {
  const ymd = tanggalEl.value;
  waktuEl.innerHTML = '<option value="">Pilih waktu</option>';

  if (!ymd) {
    waktuEl.disabled = true;
    waktuNote.textContent = 'Pilih tanggal terlebih dahulu.';
    return;
  }

  if (!isWeekday(ymd)) {
    waktuEl.disabled = true;
    waktuNote.textContent = 'Layanan tersedia Senin sampai Jumat.';
    return;
  }

  waktuEl.disabled = false;
  const now = new Date();

  serviceTimes.forEach(time => {
    const option = document.createElement('option');
    option.value = time;
    option.textContent = `${time} WIB`;

    const slotDate = parseYmd(ymd);
    const [hour, minute] = time.split(':').map(Number);
    slotDate.setHours(hour, minute, 0, 0);

    const isPastTime = slotDate < new Date(now.getTime() - 30 * 60 * 1000);
    const isBooked = bookedSlots.has(`${ymd}-${time}`);

    if (isPastTime || isBooked) {
      option.disabled = true;
      option.textContent += isBooked ? ' - penuh' : ' - lewat';
    }

    waktuEl.appendChild(option);
  });

  waktuNote.textContent = 'Pilih salah satu slot konseling yang tersedia.';
}

function validateDate() {
  const ymd = tanggalEl.value;

  if (!ymd) {
    tanggalNote.textContent = 'Pilih hari layanan Senin sampai Jumat.';
    return false;
  }

  if (ymd < todayYmd()) {
    tanggalNote.textContent = 'Tanggal tidak boleh sebelum hari ini.';
    return false;
  }

  if (!isWeekday(ymd)) {
    tanggalNote.textContent = 'Layanan hanya tersedia Senin sampai Jumat.';
    return false;
  }

  tanggalNote.textContent = 'Tanggal tersedia untuk pengajuan jadwal.';
  return true;
}

function buildSuccessDetail(data, topikValue) {
  const config = serviceConfig[selectedService];
  const rows = [
    ['Kode Jadwal', data.kode_jadwal || '-'],
    ['Tanggal', tanggalEl.value],
    ['Waktu', `${waktuEl.value} WIB`],
    ['Topik', topikValue],
    ['Layanan', config.label],
    ['Status', 'Menunggu Konfirmasi'],
  ];

  document.getElementById('success-detail').innerHTML = rows.map(([label, value]) => `
    <div class="success-detail-row">
      <span>${label}</span>
      <strong>${value}</strong>
    </div>
  `).join('');
}

async function submitJadwal() {
  if (!isLoggedIn) {
    if (confirm('Anda harus login terlebih dahulu untuk membuat jadwal. Login sekarang?')) {
      window.location.href = '/login';
    }
    return;
  }

  if (!validateDate()) {
    alert('Pilih tanggal layanan yang valid.');
    return;
  }

  if (!waktuEl.value) {
    alert('Pilih waktu konseling terlebih dahulu.');
    return;
  }

  const topikValue = getTopikValue();
  if (!topikValue) {
    return;
  }

  const payload = {
    tanggal: tanggalEl.value,
    waktu: waktuEl.value,
    jenis: selectedService,
    topik: topikValue,
  };

  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Memproses...';

  try {
    const checkRes = await fetch('{{ route("jadwal.check") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    });

    const checkData = await checkRes.json();

    if (!checkData.success || !checkData.is_available) {
      alert(checkData.message || 'Jadwal ini sudah tidak tersedia. Silakan pilih waktu lain.');
      await fetchBookedSlots();
      renderTimeOptions();
      return;
    }

    const res = await fetch('{{ route("jadwal.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.success) {
      buildSuccessDetail(data, topikValue);
      successEl.style.display = 'block';
      successEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      await fetchBookedSlots();
      renderTimeOptions();
    } else {
      alert(data.message || 'Jadwal gagal dibuat.');
      if (data.redirect) window.location.href = data.redirect;
    }
  } catch (error) {
    alert('Terjadi kesalahan. Coba lagi.');
    console.error(error);
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
}

document.querySelectorAll('[data-mode], [data-mode-action]').forEach(el => {
  el.addEventListener('click', event => {
    const mode = el.dataset.mode || el.dataset.modeAction;
    if (!serviceConfig[mode]) return;
    event.preventDefault();
    setServiceMode(mode, true);
  });
});

if (topikEl) {
  topikEl.addEventListener('change', handleTopikChange);
}

if (tanggalEl) {
  tanggalEl.min = todayYmd();
  tanggalEl.addEventListener('change', () => {
    validateDate();
    renderTimeOptions();
  });
}

handleTopikChange();
fetchBookedSlots().then(renderTimeOptions);
</script>
@endpush
