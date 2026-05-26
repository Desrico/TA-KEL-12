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

  .booking-shell.is-visible,
  .booking-shell:target {
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

  .schedule-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M4 6L8 10L12 6' stroke='%23728680' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 14px 14px;
    padding-right: 2.8rem;
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

  .anonim-toggle-row {
    margin-top: 1.15rem;
    border: 1px solid #D8E7E0;
    border-radius: 16px;
    background: #F8FCFA;
    padding: .9rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
  }

  .anonim-toggle-copy {
    min-width: 0;
  }

  .anonim-toggle-title {
    margin: 0;
    color: #173428;
    font-size: .88rem;
    font-weight: 800;
  }

  .anonim-toggle-status {
    margin: .18rem 0 0;
    color: var(--care-muted);
    font-size: .78rem;
    line-height: 1.45;
  }

  .toggle-switch {
    position: relative;
    display: inline-flex;
    width: 52px;
    height: 30px;
    flex-shrink: 0;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
  }

  .toggle-slider {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #D8E3DD;
    transition: background .2s ease;
    cursor: pointer;
  }

  .toggle-slider::before {
    content: "";
    position: absolute;
    width: 22px;
    height: 22px;
    left: 4px;
    top: 4px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .15);
    transition: transform .2s ease;
  }

  .toggle-switch input:checked + .toggle-slider {
    background: var(--care-green);
  }

  .toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(22px);
  }

  .form-note {
    margin-top: .65rem;
    color: var(--care-muted);
    font-size: .8rem;
    line-height: 1.55;
  }

  .confirmation-wrap {
    display: flex;
    align-items: flex-start;
    margin-top: .65rem;
  }

  .confirmation-wrap .form-check {
    margin: 0;
    padding-left: 2.1rem;
  }

  .confirmation-wrap .form-check-input {
    margin-left: -2.1rem;
    margin-top: .18rem;
  }

  .confirmation-wrap .form-check-label {
    color: #555;
    font-size: .9rem;
    line-height: 1.55;
  }

  .swal2-popup.swal-service-popup {
    border-radius: 22px;
    padding: 1.5rem 1.35rem 1.35rem;
  }

  .swal2-popup.swal-service-popup .swal2-confirm {
    border-radius: 12px;
    padding: .72rem 1.15rem;
    font-weight: 800;
    box-shadow: none !important;
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

  .confirm-overlay {
  position: fixed !important;
  inset: 0 !important;
  width: 100vw !important;
  height: 100vh !important;
  background: rgba(0, 0, 0, .28);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 999999 !important;
}

.confirm-overlay.show {
  display: flex !important;
}

.confirm-box {
  width: 360px;
  max-width: 90%;
  background: #066847;
  color: #fff;
  border-radius: 12px;
  padding: 1.8rem 1.5rem;
  text-align: center;
  box-shadow: 0 24px 60px rgba(0,0,0,.25);
  animation: popFade .25s ease both;
}

.confirm-icon {
  width: 58px;
  height: 58px;
  border: 4px solid #ffe66d;
  color: #ffe66d;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 2rem;
  font-weight: 800;
  margin: 0 auto 1rem;
}

.confirm-box h3 {
  color: #fff;
  font-size: 1.15rem;
  font-weight: 800;
  margin-bottom: .8rem;
}

.confirm-box p {
  color: #fff;
  font-size: .78rem;
  line-height: 1.45;
  margin-bottom: 1.3rem;
}

.confirm-actions {
  display: flex;
  justify-content: center;
  gap: .8rem;
}

.btn-confirm {
  border: 0;
  background: #ffe66d;
  color: #064e3b;
  font-weight: 800;
  font-size: .78rem;
  border-radius: 5px;
  padding: .45rem .9rem;
}

.btn-cancel {
  border: 1px solid #fff;
  background: transparent;
  color: #fff;
  font-weight: 700;
  font-size: .78rem;
  border-radius: 5px;
  padding: .45rem .9rem;
}

@keyframes popFade {
  from {
    opacity: 0;
    transform: scale(.92);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
  @media (max-width: 991.98px) {
    .service-hero {
      padding-bottom: 2.7rem;
    }
}

.confirm-overlay.show,
.success-overlay.show {
  display: flex !important;
}

.confirm-box {
  width: 360px;
  max-width: 90%;
  background: #066847;
  color: #fff;
  border-radius: 14px;
  padding: 1.8rem 1.5rem;
  text-align: center;
  animation: popFade .28s ease both;
}

.confirm-icon {
  width: 58px;
  height: 58px;
  border: 4px solid #ffe66d;
  color: #ffe66d;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 2rem;
  font-weight: 800;
  margin: 0 auto 1rem;
  animation: iconBounce .55s ease both;
}

.confirm-box h3 {
  font-size: 1.15rem;
  font-weight: 800;
  margin-bottom: .8rem;
}

.confirm-box p {
  font-size: .78rem;
  line-height: 1.45;
  margin-bottom: 1.3rem;
}

.confirm-actions {
  display: flex;
  justify-content: center;
  gap: .8rem;
}

.btn-confirm {
  border: 0;
  background: #ffe66d;
  color: #064e3b;
  font-weight: 800;
  font-size: .78rem;
  border-radius: 6px;
  padding: .48rem .95rem;
}

.btn-cancel {
  border: 1px solid #fff;
  background: transparent;
  color: #fff;
  font-weight: 700;
  font-size: .78rem;
  border-radius: 6px;
  padding: .48rem .95rem;
}

.success-box {
  width: 390px;
  max-width: 90%;
  background: #fff;
  border-radius: 22px;
  padding: 2rem 1.7rem;
  text-align: center;
  box-shadow: 0 28px 70px rgba(15, 23, 42, .22);
  animation: popFade .28s ease both;
}

.success-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: #DFF8EA;
  color: #066847;
  display: grid;
  place-items: center;
  margin: 0 auto 1rem;
  font-size: 2rem;
  animation: iconBounce .55s ease both;
}

.success-box h3 {
  color: #064E3B;
  font-size: 1.25rem;
  font-weight: 800;
  margin-bottom: .65rem;
}

.success-box p {
  margin: 0 auto 1.4rem;
  color: #64748b;
  font-size: .9rem;
  line-height: 1.6;
  max-width: 300px;
}

.btn-success-ok {
  border: 0;
  background: #066847;
  color: #fff;
  border-radius: 999px;
  padding: .7rem 1.4rem;
  font-size: .85rem;
  font-weight: 800;
}

@keyframes popFade {
  from {
    opacity: 0;
    transform: translateY(18px) scale(.94);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes iconBounce {
  0% {
    opacity: 0;
    transform: scale(.3);
  }
  60% {
    opacity: 1;
    transform: scale(1.12);
  }
  100% {
    transform: scale(1);
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
  $anonimDisplayName = $user ? $user->getAnonimDisplayName() : 'Mahasiswa Anonim';
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
                <i class="bi bi-chat-dots"></i>
                <strong>Chat</strong>
                <span>Media sesi</span>
              </div>
              <div class="mode-fact">
                <i class="bi bi-hourglass-split"></i>
                <strong>Fleksibel</strong>
                <span>Dari Tempat Pilihanmu</span>
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
                <i class="bi bi-clock"></i>
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
                <strong>Gedung 5, Lt. 2</strong>
                <span>Area GD 525-GD 526</span>
              </div>
            </div>
            <a href="#booking" class="mode-action" data-mode-action="offline">Pilih Offline <i class="bi bi-arrow-right"></i></a>
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
              <i class="bi bi-clock"></i>
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
              <div class="info-value" id="side-location">Gedung 5, Lt. 2<br>Area GD 525-GD 526</div>
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
              Offline
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
              <input type="text" class="schedule-input" id="profile-nama" value="{{ $isAnonim ? $anonimDisplayName : $namaMahasiswa }}" disabled>
            </div>
            <div class="col-md-6">
              <label class="field-label" for="profile-angkatan">Angkatan</label>
              <input type="text" class="schedule-input" id="profile-angkatan" value="{{ $angkatanMahasiswa }}" disabled>
            </div>
          </div>

          <div class="anonim-toggle-row">
            <div class="anonim-toggle-copy">
              <p class="anonim-toggle-title">Mode Anonim</p>
              <p class="anonim-toggle-status" id="anonim-status-text">{{ $isAnonim ? 'Aktif' : 'Nonaktif' }}</p>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" id="anonim-toggle" {{ $isAnonim ? 'checked' : '' }} onchange="toggleAnonim(this)">
              <span class="toggle-slider"></span>
            </label>
          </div>

          <div class="form-section-title">
            <i class="bi bi-clock"></i>
            <span>Detail Jadwal</span>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <label class="field-label" for="tanggal">Tanggal</label>
              <input type="date" class="schedule-input" id="tanggal">
              <div class="form-note" id="tanggal-note">Pilih hari layanan Senin sampai Jumat.</div>
            </div>
            <div class="col-md-6">
              <label class="field-label" for="waktu">Waktu</label>
              <select class="schedule-select" id="waktu">
                <option value="">Pilih waktu</option>
              </select>
              <div class="form-note" id="waktu-note">Slot yang sudah terisi akan otomatis dinonaktifkan.</div>
            </div>
          </div>

          <div class="form-section-title">
            <i class="bi bi-headphones"></i>
            <span>Layanan</span>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
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
                style="display:none; margin-top:.75rem;"
              >
            </div>
            <div class="col-12 confirmation-wrap">
              <div class="form-check ms-md-1">
                <input type="checkbox" class="form-check-input" id="confirmation-checkbox">
                <label class="form-check-label" for="confirmation-checkbox">
                  Saya sudah memeriksa dan memastikan data penjadwalan sudah benar.
                </label>
              </div>
            </div>
            <div class="col-12">
              <div class="submit-wrap">
                <button type="button" class="schedule-submit" id="submit-booking" onclick="openConfirmModal()">
                  Jadwalkan Konseling
                </button>
              </div>
            </div>
          </div>

        </main>
      </div>
    </div>
  </div>
</section>

<div class="confirm-overlay" id="confirmModal">
  <div class="confirm-box">
    <div class="confirm-icon">?</div>

    <h3>Konfirmasi Penjadwalan</h3>

    <p>
      Apakah kamu yakin ingin menjadwalkan sesi konseling ini?<br>
      Pastikan tanggal, waktu, dan metode yang dipilih sudah sesuai.
    </p>

    <div class="confirm-actions">
      <button type="button" class="btn-confirm" onclick="confirmSubmitJadwal()">
        Jadwalkan
      </button>
      <button type="button" class="btn-cancel" onclick="closeConfirmModal()">
        Batalkan
      </button>
    </div>
  </div>
</div>
<div class="confirm-overlay" id="successModal">
  <div class="confirm-box">
    <div class="confirm-icon">
      <i class="bi bi-check-lg"></i>
    </div>

    <h3>Penjadwalan Berhasil</h3>
    <p>
      Pengajuan jadwal konseling berhasil dibuat dan sedang menunggu persetujuan konselor.
    </p>

    <div class="confirm-actions">
      <button type="button" class="btn-confirm" onclick="closeSuccessModal()">
        OK
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
  const bookedSlots = new Map();
  const serviceTimes = ['09:00','10:00','11:00','13:00','14:00','15:00','16:00'];
  const serviceModeStorageKey = 'konseling:selected-mode';

  let selectedService = 'offline';

  // Elements
  const bookingEl = document.getElementById('booking');
  const tanggalEl = document.getElementById('tanggal');
  const waktuEl = document.getElementById('waktu');
  const topikEl = document.getElementById('topik');
  const topikLainnyaEl = document.getElementById('topik-lainnya');
  const submitBtn = document.getElementById('submit-booking');
  const tanggalNote = document.getElementById('tanggal-note');
  const waktuNote = document.getElementById('waktu-note');

  const serviceConfig = {
    online: {
      label: 'Online',
      icon: 'bi-chat-dots',
      subtitle: 'Lengkapi tanggal, waktu, dan topik untuk mengajukan konseling online.',
      sideMedia: 'Chat',
      sideLocation: 'Online<br>Link sesi menyusul setelah disetujui',
      noteClass: 'online',
      note: 'Pastikan kamu berada di tempat yang tenang dan memiliki koneksi internet stabil sebelum sesi dimulai.',
      submit: 'Jadwalkan Online'
    },
    offline: {
      label: 'Offline',
      icon: 'bi-geo-alt',
      subtitle: 'Lengkapi tanggal, waktu, dan topik untuk mengajukan konseling offline.',
      sideMedia: 'Tatap Muka',
      sideLocation: 'Gedung 5, Lt. 2<br>Area GD 525-GD 526',
      noteClass: '',
      note: 'Harap tiba 10 menit lebih awal sebagai persiapan awal.',
      submit: 'Jadwalkan Offline'
    }
  };

  // Helper: Tanggal hari ini dalam format YYYY-MM-DD
  function todayYmd() {
    const now = new Date();
    return now.toISOString().split('T')[0];
  }

  // Helper: Parse YYYY-MM-DD ke Date
  function parseYmd(ymd) {
    const [y, m, d] = ymd.split('-').map(Number);
    return new Date(y, m - 1, d);
  }

  // Helper: Cek apakah tanggal adalah hari kerja (Senin-Jumat)
  function isWeekday(ymd) {
    const date = parseYmd(ymd);
    const day = date.getDay();
    return day >= 1 && day <= 5;
  }

  // Helper: Dapatkan nilai topik
  function getTopikValue() {
    const topikVal = topikEl.value;
    if (topikVal === 'lainnya') {
      return topikLainnyaEl.value || null;
    }
    return topikVal || null;
  }

  function isApprovedSlotStatus(status) {
    return ['disetujui', 'berlangsung'].includes(String(status || '').toLowerCase());
  }

  // Simpan mode aktif agar tetap kembali ke panel yang sama setelah reload.
  function persistSelectedServiceMode(mode) {
    try {
      sessionStorage.setItem(serviceModeStorageKey, mode);
    } catch (error) {
      // Abaikan jika sessionStorage tidak tersedia.
    }
  }

  // Ambil mode terakhir yang valid untuk memulihkan state tampilan.
  function getPersistedServiceMode() {
    try {
      const storedMode = sessionStorage.getItem(serviceModeStorageKey);
      return serviceConfig[storedMode] ? storedMode : null;
    } catch (error) {
      return null;
    }
  }

  function showInteractiveAlert({
    title = 'Informasi',
    text = '',
    icon = 'info',
    confirmButtonText = 'OK'
  } = {}) {
    if (window.Swal && typeof window.Swal.fire === 'function') {
      return Swal.fire({
        title,
        text,
        icon,
        confirmButtonText,
        confirmButtonColor: '#0A523A',
        customClass: {
          popup: 'swal-service-popup'
        }
      });
    }

    alert(text || title);
    return Promise.resolve();
  }

  // Sinkronkan status label anonim dengan posisi toggle.
  function syncAnonimStatusLabel(isActive) {
    const anonimStatusEl = document.getElementById('anonim-status-text');
    if (anonimStatusEl) {
      anonimStatusEl.textContent = isActive ? 'Aktif' : 'Nonaktif';
    }
  }

  // Gunakan endpoint yang sama dengan halaman profil agar perilaku tetap konsisten.
  async function toggleAnonim(checkbox) {
    const previousState = !checkbox.checked;
    syncAnonimStatusLabel(checkbox.checked);
    persistSelectedServiceMode(selectedService);

    try {
      const response = await fetch('{{ route('profil.anonim') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ anonim: checkbox.checked }),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        checkbox.checked = previousState;
        syncAnonimStatusLabel(previousState);
        await showInteractiveAlert({
          title: 'Mode Anonim Gagal Diperbarui',
          text: data.message || 'Gagal memperbarui mode anonim.',
          icon: 'error'
        });
        return;
      }

      window.location.reload();
    } catch (error) {
      checkbox.checked = previousState;
      syncAnonimStatusLabel(previousState);
      await showInteractiveAlert({
        title: 'Mode Anonim Gagal Diperbarui',
        text: 'Gagal memperbarui mode anonim.',
        icon: 'error'
      });
    }
  }

  // Helper: Validasi tanggal
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

  // Helper: Render dropdown waktu
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
      const slotInfo = bookedSlots.get(`${ymd}-${time}`);
      const isApproved = isApprovedSlotStatus(slotInfo?.status);

      if (isPastTime || isApproved) {
        option.disabled = true;
        option.textContent += isApproved
          ? ` - ${slotInfo?.label || 'Telah Terjadwal'}`
          : ' - lewat';
      }

      waktuEl.appendChild(option);
    });

    waktuNote.textContent = 'Slot dengan status "Telah Terjadwal" sudah disetujui dan tidak dapat dipilih.';
  }

  // Fetch booked slots
  async function fetchBookedSlots() {
    try {
      const res = await fetch('{{ route("jadwal.terisi") }}', {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      bookedSlots.clear();
      data.forEach(slot => {
        if (typeof slot === 'string') {
          bookedSlots.set(slot, { status: 'menunggu', label: 'Sudah Terisi' });
          return;
        }

        if (slot && slot.slot) {
          bookedSlots.set(slot.slot, {
            status: slot.status || 'menunggu',
            label: slot.label || 'Sudah Terisi',
          });
        }
      });
    } catch (error) {
      bookedSlots.clear();
    }
  }

  // Set service mode
  function setServiceMode(mode, shouldScroll = false) {
    if (!serviceConfig[mode]) return;

    selectedService = mode;
    persistSelectedServiceMode(mode);
    const config = serviceConfig[mode];

    bookingEl.classList.add('is-visible');

    document.querySelectorAll('[data-panel]').forEach(panel => {
      panel.classList.toggle('active', panel.dataset.panel === mode);
    });

    document.getElementById('schedule-subtitle').textContent = config.subtitle;
    document.getElementById('selected-mode-pill').className = `selected-mode-pill ${mode === 'online' ? 'online' : ''}`;
    document.getElementById('selected-mode-pill').innerHTML = `<i class="bi ${config.icon}"></i> ${config.label}`;
    document.getElementById('side-media').innerHTML = config.sideMedia;
    document.getElementById('side-location').innerHTML = config.sideLocation;
    document.getElementById('session-note').className = `session-note ${config.noteClass}`;
    document.getElementById('session-note-text').textContent = config.note;

    submitBtn.textContent = config.submit;
    submitBtn.classList.toggle('online', mode === 'online');

    if (shouldScroll) {
      bookingEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  // Modal functions
  function openConfirmModal() {
    if (!isLoggedIn) {
      window.location.href = '/login';
      return;
    }

    const checkbox = document.getElementById('confirmation-checkbox');
    if (!checkbox.checked) {
      showInteractiveAlert({
        title: 'Konfirmasi Diperlukan',
        text: 'Centang konfirmasi bahwa data penjadwalan sudah benar.',
        icon: 'warning'
      });
      return;
    }

    if (!validateDate()) {
      showInteractiveAlert({
        title: 'Tanggal Belum Valid',
        text: 'Pilih tanggal layanan yang valid.',
        icon: 'warning'
      });
      return;
    }

    if (!waktuEl.value) {
      showInteractiveAlert({
        title: 'Waktu Belum Dipilih',
        text: 'Pilih waktu konseling terlebih dahulu.',
        icon: 'warning'
      });
      return;
    }

    const topikValue = getTopikValue();
    if (!topikValue) {
      showInteractiveAlert({
        title: 'Topik Belum Dipilih',
        text: 'Pilih topik konseling terlebih dahulu.',
        icon: 'warning'
      });
      return;
    }

    const modal = document.getElementById('confirmModal');
    document.body.appendChild(modal);
    modal.classList.add('show');
  }

  function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
  }

  function openSuccessModal() {
    const modal = document.getElementById('successModal');
    document.body.appendChild(modal);
    modal.classList.add('show');
  }

  function closeSuccessModal() {
    document.getElementById('successModal').classList.remove('show');
    window.location.href = '{{ route("konseling") }}';
  }

  // Submit jadwal
  async function submitJadwal() {
    if (!isLoggedIn) {
      window.location.href = '/login';
      return;
    }

    const checkbox = document.getElementById('confirmation-checkbox');

    if (!checkbox.checked) {
      showInteractiveAlert({
        title: 'Konfirmasi Diperlukan',
        text: 'Centang konfirmasi bahwa data penjadwalan sudah benar.',
        icon: 'warning'
      });
      return;
    }

    if (!validateDate()) {
      showInteractiveAlert({
        title: 'Tanggal Belum Valid',
        text: 'Pilih tanggal layanan yang valid.',
        icon: 'warning'
      });
      return;
    }

    if (!waktuEl.value) {
      showInteractiveAlert({
        title: 'Waktu Belum Dipilih',
        text: 'Pilih waktu konseling terlebih dahulu.',
        icon: 'warning'
      });
      return;
    }

    const topikValue = getTopikValue();

    if (!topikValue) {
      showInteractiveAlert({
        title: 'Topik Belum Dipilih',
        text: 'Pilih topik konseling terlebih dahulu.',
        icon: 'warning'
      });
      return;
    }

    const payload = {
      tanggal: tanggalEl.value,
      waktu: waktuEl.value,
      jenis: selectedService,
      topik: topikValue,
      konfirmasi: checkbox.checked,
    };

    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Memproses...';

    try {
      const res = await fetch('{{ route("jadwal.store") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload)
      });

      const rawText = await res.text();
      let data = {};

      try {
        data = JSON.parse(rawText);
      } catch (e) {
        console.error('Response bukan JSON:', rawText);
        throw new Error('Controller tidak mengembalikan JSON.');
      }

      if (!res.ok) {
        console.error('ERROR jadwal.store:', res.status, data);
        closeConfirmModal();
        await fetchBookedSlots();
        renderTimeOptions();

        if (res.status === 409) {
          waktuEl.value = '';
          await showInteractiveAlert({
            title: isApprovedSlotStatus(data.slot_status) ? 'Slot Sudah Terjadwal' : 'Jadwal Sudah Terisi',
            text: data.message || 'Jadwal ini sudah terisi. Silakan pilih waktu lain.',
            icon: isApprovedSlotStatus(data.slot_status) ? 'info' : 'warning',
            confirmButtonText: 'Pilih Waktu Lain'
          });
        } else {
          await showInteractiveAlert({
            title: 'Penjadwalan Gagal',
            text: data.message || 'Jadwal gagal dibuat.',
            icon: 'error'
          });
        }

        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
      }

      if (data.success) {
        closeConfirmModal();
        openSuccessModal();
      } else {
        closeConfirmModal();
        await showInteractiveAlert({
          title: 'Penjadwalan Gagal',
          text: data.message || 'Jadwal gagal dibuat.',
          icon: 'error'
        });
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      }

    } catch (error) {
      console.error('ERROR DETAIL:', error);

      closeConfirmModal();
      await showInteractiveAlert({
        title: 'Terjadi Kendala',
        text: 'Gagal menyimpan jadwal. Silakan coba lagi.',
        icon: 'error'
      });

      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  }

  // Handle topik change
  function handleTopikChange() {
    const val = topikEl.value;
    if (val === 'lainnya') {
      topikLainnyaEl.style.display = 'block';
      topikLainnyaEl.required = true;
    } else {
      topikLainnyaEl.style.display = 'none';
      topikLainnyaEl.required = false;
    }
  }

  // Expose global functions
  window.setServiceMode = setServiceMode;
  window.openConfirmModal = openConfirmModal;
  window.closeConfirmModal = closeConfirmModal;
  window.confirmSubmitJadwal = function() {
    submitJadwal();
  };
  window.openSuccessModal = openSuccessModal;
  window.closeSuccessModal = closeSuccessModal;
  window.submitJadwal = submitJadwal;
  window.handleTopikChange = handleTopikChange;
  window.toggleAnonim = toggleAnonim;

  // Setup event listeners
  tanggalEl.min = todayYmd();

  tanggalEl.addEventListener('change', function () {
    validateDate();
    renderTimeOptions();
  });

  topikEl.addEventListener('change', handleTopikChange);

  // Mode action buttons
  document.querySelectorAll('[data-mode-action]').forEach(el => {
    el.addEventListener('click', function (event) {
      event.preventDefault();
      setServiceMode(this.dataset.modeAction, true);
    });
  });

  // Initialize
  const persistedMode = getPersistedServiceMode();
  if (persistedMode) {
    setServiceMode(persistedMode);
  }

  handleTopikChange();
  syncAnonimStatusLabel(document.getElementById('anonim-toggle')?.checked);
  fetchBookedSlots().then(renderTimeOptions);
});
</script>
@endpush
