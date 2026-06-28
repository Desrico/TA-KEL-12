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

  .anonim-toggle-card {
    margin-top: 1.2rem;
    border: 1px solid #D5D5D5;
    border-radius: 20px;
    background:  #ffffff;
    padding: 1rem 1.05rem;
  }

  .anonim-toggle-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
  }

  .anonim-toggle-copy {
    flex: 1;
    min-width: 0;
  }

  .anonim-toggle-title {
    margin: 0;
    color: #173428;
    font-size: .95rem;
    font-weight: 800;
  }

  .anonim-toggle-status {
    margin-top: .2rem;
    color: var(--care-green);
    font-size: .77rem;
    font-weight: 700;
  }

  .anonim-toggle-status.is-error {
    color: #B42318;
  }

  .anonim-toggle-switch {
    position: relative;
    width: 52px;
    height: 30px;
    flex-shrink: 0;
  }

  .anonim-toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .anonim-toggle-slider {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #D4DAD7;
    cursor: pointer;
    transition: background .2s ease;
  }

  .anonim-toggle-slider::before {
    content: '';
    position: absolute;
    left: 3px;
    top: 3px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 4px 14px rgba(15, 23, 42, .14);
    transition: transform .2s ease;
  }

  .anonim-toggle-switch input:checked + .anonim-toggle-slider {
    background: var(--care-green);
  }

  .anonim-toggle-switch input:checked + .anonim-toggle-slider::before {
    transform: translateX(22px);
  }

  .anonim-toggle-switch input:disabled + .anonim-toggle-slider {
    cursor: wait;
    opacity: .72;
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

  /* =========================
   MODAL / POPUP ALERT
========================= */

.service-modal-overlay {
  position: fixed;
  inset: 0;
  display: none;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: rgba(15, 23, 42, .34);
  backdrop-filter: blur(4px);
  z-index: 999999;
}

.service-modal-overlay.show {
  display: flex;
}

.service-modal {
  position: relative;
  width: min(100%, 450px);
  border-radius: 16px;
  background: linear-gradient(180deg, #0C7A52 0%, #0A6D4A 100%);
  color: #fff;
  padding: 2rem 1.8rem;
  text-align: center;
  box-shadow: 0 28px 68px rgba(15, 23, 42, .3);
  animation: modalEntrance .28s ease both;
}

.service-modal::before {
  content: "";
  position: absolute;
  inset: 1px;
  border-radius: 15px;
  border: 1px solid rgba(255, 255, 255, .08);
  pointer-events: none;
}

/* ICON DEFAULT */
.service-modal-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 1.1rem;
  border: 5px solid #FFE06B;
  border-radius: 50%;
  display: grid;
  place-items: center;
  color: #FFE06B;
  font-size: 2.4rem;
  font-weight: 800;
  line-height: 1;
  animation: iconBounce .55s ease both;
}

/* WARNING BIASA - TETAP TANDA TANYA KUNING */
.service-modal.is-warning .service-modal-icon {
  border-color: #FFE06B;
  color: #FFE06B;
}

.service-modal.is-warning .service-modal-icon > * {
  display: inline;
}

/* KHUSUS POPUP KONSELOR TIDAK TERSEDIA */
.service-modal.is-unavailable .service-modal-icon {
  position: relative;
  border-color: #EF4E4E;
  color: transparent;
}

.service-modal.is-unavailable .service-modal-icon > * {
  display: none;
}

.service-modal.is-unavailable .service-modal-icon::before {
  content: "!";
  color: #EF4E4E;
  font-size: 2.8rem;
  font-weight: 900;
  line-height: 1;
  transform: translateY(2px);
}
/* ICON SUCCESS */
.service-modal.is-success .service-modal-icon {
  border-color: #D8F5E5;
  color: #D8F5E5;
}

/* ICON ERROR */
.service-modal.is-error .service-modal-icon {
  border-color: #FFD18A;
  color: #FFD18A;
}

.service-modal h3 {
  margin: 0 0 .9rem;
  color: #fff;
  font-size: 1.35rem;
  font-weight: 800;
  line-height: 1.25;
}

/* TEKS UTAMA MODAL */
.service-modal p {
  max-width: 370px;
  margin: 0 auto 1.5rem;
  color: rgba(255, 255, 255, .96);
  font-size: 1rem;
  line-height: 1.5;
  white-space: normal;
}

/* ISI POPUP KETIDAKTERSEDIAAN */
.unavailable-popup-content {
  width: 100%;
  max-width: 390px;
  margin: 0 auto;
  color: #ffffff;
}

.unavailable-popup-desc {
  width: 100%;
  margin: 0 0 1.6rem 0;
  text-align: left;
  font-size: 1rem;
  line-height: 1.5;
}

.unavailable-detail {
  width: 100%;
  margin: 0;
  text-align: left;
  font-size: 1rem;
  line-height: 1.45;
}


.detail-row {
  display: grid;
  grid-template-columns: 95px 14px 1fr;
  align-items: start;
  margin-bottom: 4px;
}

.detail-label,
.detail-separator,
.detail-value {
  color: #ffffff;
  font-weight: 600;
}

.detail-label {
  text-align: left;
}

.detail-separator {
  text-align: center;
}

.detail-value {
  text-align: left;
}

.detail-label,
.detail-separator,
.detail-value {
  color: #ffffff;
  font-weight: 500;
}

.detail-label {
  text-align: left;
}

.detail-separator {
  text-align: center;
}

.detail-value {
  text-align: left;
}

/* BUTTON AREA */
.service-modal-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: 1.8rem;
}

.service-modal-btn {
  min-width: 130px;
  min-height: 54px;
  border-radius: 7px;
  padding: .6rem 1.1rem;
  font-size: .9rem;
  font-weight: 800;
  transition: transform .2s ease, background .2s ease, color .2s ease, border-color .2s ease;
}

.service-modal-btn:hover {
  transform: translateY(-1px);
}

.service-modal-btn-primary {
  border: 0;
  background: #FFE06B;
  color: #07533A;
}

.service-modal-btn-primary:hover {
  background: #FFD84F;
  color: #064A34;
}

.service-modal-btn-secondary {
  border: 1px solid rgba(255, 255, 255, .8);
  background: transparent;
  color: #fff;
}

.service-modal-btn-secondary:hover {
  border-color: #fff;
  background: rgba(255, 255, 255, .08);
  color: #fff;
}

.service-modal-btn[hidden] {
  display: none !important;
}

/* ANIMATION */
@keyframes modalEntrance {
  from {
    opacity: 0;
    transform: translateY(18px) scale(.95);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes iconBounce {
  0% {
    opacity: 0;
    transform: scale(.35);
  }

  60% {
    opacity: 1;
    transform: scale(1.08);
  }

  100% {
    transform: scale(1);
  }
}

/* RESPONSIVE */
@media (max-width: 767.98px) {
  .service-modal {
    width: min(100%, 340px);
    padding: 1.65rem 1.25rem;
    border-radius: 14px;
  }

  .service-modal h3 {
    font-size: 1.15rem;
  }

  .service-modal p {
    font-size: .9rem;
  }

  .unavailable-detail {
    min-width: 220px;
    font-size: .9rem;
  }

  .detail-row {
    grid-template-columns: 68px 12px 1fr;
  }

  .service-modal-btn {
    flex: 1 1 100%;
  }
}

@media (prefers-reduced-motion: reduce) {
  .service-modal,
  .service-modal-icon,
  .service-modal-btn {
    animation: none;
    transition: none;
  }
}

  @keyframes modalEntrance {
    from {
      opacity: 0;
      transform: translateY(18px) scale(.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  @keyframes iconBounce {
    0% {
      opacity: 0;
      transform: scale(.35);
    }
    60% {
      opacity: 1;
      transform: scale(1.08);
    }
    100% {
      transform: scale(1);
    }
  }

  @media (max-width: 991.98px) {
    .service-hero {
      padding-bottom: 2.7rem;
    }
  }

  @media (max-width: 767.98px) {
    .anonim-toggle-row {
      flex-direction: column;
    }

    .anonim-toggle-switch {
      align-self: flex-end;
    }

    .service-modal {
      width: min(100%, 340px);
      padding: 1.65rem 1.25rem;
      border-radius: 14px;
    }

    .service-modal-btn {
      flex: 1 1 100%;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .service-modal,
    .service-modal-icon,
    .service-modal-btn {
      animation: none;
      transition: none;
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
            <h2>Jadwalkan Online</h2>
            <p>Ajukan jadwal konseling online untuk berdiskusi dengan konselor melalui ruang chat. Setelah jadwal disetujui, Anda dapat mengikuti sesi konseling sesuai waktu yang telah dipilih.</p>
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
                <i class="bi bi-geo-alt-fill"></i>
                <strong>Jarak Jauh</strong>
                <span>Akses dari ruang personalmu</span>
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
            <h2>Jadwalkan Offline</h2>
            <p>Ajukan jadwal konseling offline untuk bertemu langsung dengan konselor di kampus. Setelah jadwal disetujui, Anda dapat datang sesuai waktu dan lokasi yang telah ditentukan.</p>
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
                <span>Area GD 525 & GD 526</span>
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
              @php
                  $namaKonselor = env('CIS_KONSELOR_NAME', 'Konselor');
                  $inisialKonselor = strtoupper(mb_substr($namaKonselor, 0, 1));
              @endphp

              <div class="counselor-name">{{ $namaKonselor }}</div>
              <div class="counselor-role">Konselor</div>
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

           <div class="anonim-toggle-card" id="anonim-toggle-card">
            <div class="anonim-toggle-row">
              <div class="anonim-toggle-copy">
                <p class="anonim-toggle-title">Mode Anonim</p>
                <div class="anonim-toggle-status" id="anonim-toggle-status">
                  {{ $isAnonim ? 'Mode anonim aktif.' : 'Mode anonim nonaktif.' }}
                </div>
              </div>
              <label class="anonim-toggle-switch" aria-label="Toggle mode anonim">
                <input
                  type="checkbox"
                  id="anonim-toggle"
                  {{ $isAnonim ? 'checked' : '' }}
                  {{ Auth::check() ? '' : 'disabled' }}
                >
                <span class="anonim-toggle-slider"></span>
              </label>
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

<div class="service-modal-overlay" id="confirmModal" aria-hidden="true">
  <div class="service-modal is-warning" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
    <div class="service-modal-icon" aria-hidden="true">?</div>
    <h3 id="confirmModalTitle">Konfirmasi Penjadwalan</h3>
    <p>Apakah kamu yakin ingin menjadwalkan sesi konseling ini?
Pastikan tanggal, waktu, dan metode yang dipilih sudah sesuai.</p>
    <div class="service-modal-actions">
      <button type="button" class="service-modal-btn service-modal-btn-primary" onclick="confirmSubmitJadwal()">
        Jadwalkan
      </button>
      <button type="button" class="service-modal-btn service-modal-btn-secondary" onclick="closeConfirmModal()">
        Batalkan
      </button>
    </div>
  </div>
</div>
<div class="service-modal-overlay" id="successModal" aria-hidden="true">
  <div class="service-modal is-success" role="dialog" aria-modal="true" aria-labelledby="successModalTitle">
    <div class="service-modal-icon" aria-hidden="true">
      <i class="bi bi-check-lg"></i>
    </div>
    <h3 id="successModalTitle">Penjadwalan Berhasil</h3>
    <p>Pengajuan jadwal konseling berhasil dibuat dan sedang menunggu persetujuan konselor.</p>
    <div class="service-modal-actions">
      <button type="button" class="service-modal-btn service-modal-btn-primary" onclick="closeSuccessModal()">
        OK
      </button>
    </div>
  </div>
</div>
<div class="service-modal-overlay" id="alertModal" aria-hidden="true">
  <div class="service-modal is-warning" role="dialog" aria-modal="true" aria-labelledby="alertModalTitle">
    <div class="service-modal-icon" id="alertModalIcon" aria-hidden="true">!</div>
    <h3 id="alertModalTitle">Informasi</h3>
    <p id="alertModalText"></p>
    <div class="service-modal-actions">
      <button type="button" class="service-modal-btn service-modal-btn-primary" id="alertModalConfirm">
        OK
      </button>
      <button type="button" class="service-modal-btn service-modal-btn-secondary" id="alertModalCancel" hidden>
        Tutup
      </button>
    </div>
  </div>
</div>
@endsection

@php
    $isJadwalUlang = isset($jadwalUlang);
    $isFollowUpSchedule = isset($followUpJadwal) && $followUpJadwal;

    $jadwalUlangPayload = null;
    $followUpPayload = null;
    $submitUrl = route('jadwal.store');
    $submitMethod = 'POST';
    $successRedirectUrl = url('/riwayat');

    if ($isFollowUpSchedule) {
        // Sesi lanjutan hanya prefill topik; submit tetap membuat jadwal baru.
        $followUpPayload = [
            'id' => $followUpJadwal->id,
            'jenis' => strtolower($followUpJadwal->jenis ?? 'offline'),
            'topik' => $followUpJadwal->topik,
        ];
    }

    if ($isJadwalUlang) {
        $jadwalUlangPayload = [
            'id' => $jadwalUlang->id,
            'tanggal' => \Carbon\Carbon::parse($jadwalUlang->tanggal)->format('Y-m-d'),
            'waktu' => \Carbon\Carbon::parse($jadwalUlang->waktu)->format('H:i'),
            'jenis' => strtolower($jadwalUlang->jenis ?? 'offline'),
            'topik' => $jadwalUlang->topik,
        ];

        $submitUrl = route('konseling.jadwal_ulang.update', $jadwalUlang->id);
        $submitMethod = 'PUT';
        $successRedirectUrl = url('/riwayat/' . $jadwalUlang->id);
    }
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
  let isAnonim = {{ $isAnonim ? 'true' : 'false' }};

  const isJadwalUlang = @js($isJadwalUlang);
  const jadwalUlangData = @js($jadwalUlangPayload);
  const followUpData = @js($followUpPayload);

  const submitUrl = @js($submitUrl);
  const submitMethod = @js($submitMethod);
  const successRedirectUrl = @js($successRedirectUrl);

  const bookedSlots = new Map();
  const serviceTimes = ['08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00'];

  const profileDisplay = {
    nim: @js($nimMahasiswa),
    nama: @js($namaMahasiswa),
    anonimName: @js($anonimDisplayName),
  };

  let selectedService = jadwalUlangData?.jenis || 'offline';

  // Elements
  const bookingEl = document.getElementById('booking');
  const tanggalEl = document.getElementById('tanggal');
  const waktuEl = document.getElementById('waktu');
  const topikEl = document.getElementById('topik');
  const topikLainnyaEl = document.getElementById('topik-lainnya');
  const submitBtn = document.getElementById('submit-booking');
  const tanggalNote = document.getElementById('tanggal-note');
  const waktuNote = document.getElementById('waktu-note');
  const anonimToggleCardEl = document.getElementById('anonim-toggle-card');
  const anonimToggleEl = document.getElementById('anonim-toggle');
  const anonimStatusEl = document.getElementById('anonim-toggle-status');
  const profileNimEl = document.getElementById('profile-nim');
  const profileNamaEl = document.getElementById('profile-nama');
  const confirmModalEl = document.getElementById('confirmModal');
  const successModalEl = document.getElementById('successModal');
  const alertModalEl = document.getElementById('alertModal');
  const alertModalBoxEl = alertModalEl?.querySelector('.service-modal');
  const alertModalIconEl = document.getElementById('alertModalIcon');
  const alertModalTitleEl = document.getElementById('alertModalTitle');
  const alertModalTextEl = document.getElementById('alertModalText');
  const alertModalConfirmEl = document.getElementById('alertModalConfirm');
  const alertModalCancelEl = document.getElementById('alertModalCancel');

  let activeAlertResolver = null;

  const serviceConfig = {
    online: {
      label: 'Online',
      icon: 'bi-chat-dots',
      subtitle: 'Lengkapi tanggal, waktu, dan topik untuk mengajukan konseling online.',
      sideMedia: 'Chat',
      sideLocation: 'Jarak Jauh<br>Akses dari ruang personalmu',
      noteClass: 'online',
      note: 'Pastikan kamu berada di tempat yang tenang dan memiliki koneksi internet stabil sebelum sesi dimulai.',
      submit: 'Jadwalkan Online'
    },
    offline: {
      label: 'Offline',
      icon: 'bi-geo-alt',
      subtitle: 'Lengkapi tanggal, waktu, dan topik untuk mengajukan konseling offline.',
      sideMedia: 'Tatap Muka',
      sideLocation: 'Gedung 5, Lt. 2<br>Antara GD 525 & 526',
      noteClass: '',
      note: 'Harap tiba 10 menit lebih awal sebagai persiapan awal.',
      submit: 'Jadwalkan Offline'
    }
  };

  function todayYmd() {
    const now = new Date();
    return now.toISOString().split('T')[0];
  }

  function parseYmd(ymd) {
    const [y, m, d] = ymd.split('-').map(Number);
    return new Date(y, m - 1, d);
  }

  function isWeekday(ymd) {
    const date = parseYmd(ymd);
    const day = date.getDay();
    return day >= 1 && day <= 5;
  }

  function getTopikValue() {
    const topikVal = topikEl.value;

    if (topikVal === 'lainnya') {
      return topikLainnyaEl.value || null;
    }

    return topikVal || null;
  }

  function isApprovedSlotStatus(status) {
    return ['disetujui', 'diterima', 'berlangsung'].includes(String(status || '').toLowerCase());
  }

  function isBookedSlotStatus(status) {
    return ['menunggu', 'menunggu_konfirmasi', 'disetujui', 'diterima', 'berlangsung', 'selesai'].includes(String(status || '').toLowerCase());
  }

  function getModalIconMarkup(icon) {
    const iconMap = {
      warning: '!',
      error: '!',
      success: '<i class="bi bi-check-lg"></i>',
      info: '<i class="bi bi-info-lg"></i>',
      unavailable: '!'
    };

    return iconMap[icon] || iconMap.info;
  }

  function getModalVariant(icon) {
    if (icon === 'unavailable') return 'is-unavailable';
    if (icon === 'warning') return 'is-warning';
    if (icon === 'error') return 'is-error';
    if (icon === 'success') return 'is-success';

    return 'is-info';
  }

  function openModal(modal) {
    if (!modal) return;

    document.body.appendChild(modal);
    modal.classList.add('show');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeModal(modal) {
    if (!modal) return;

    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');

    const hasVisibleModal = document.querySelector('.service-modal-overlay.show');

    if (!hasVisibleModal) {
      document.body.style.overflow = '';
    }
  }

  function showInteractiveAlert({
    title = 'Informasi',
    text = '',
    html = '',
    icon = 'info',
    confirmButtonText = 'OK'
  } = {}) {
    if (!alertModalEl || !alertModalBoxEl) {
      alert(text || title);
      return Promise.resolve();
    }

    alertModalBoxEl.classList.remove(
      'is-warning',
      'is-error',
      'is-success',
      'is-info',
      'is-unavailable'
    );

    alertModalBoxEl.classList.add(getModalVariant(icon));

    alertModalIconEl.innerHTML = getModalIconMarkup(icon);
    alertModalTitleEl.textContent = title;

    if (html) {
      alertModalTextEl.innerHTML = html;
    } else {
      alertModalTextEl.textContent = text;
    }

    alertModalConfirmEl.textContent = confirmButtonText;
    alertModalCancelEl.hidden = true;

    openModal(alertModalEl);

    return new Promise(resolve => {
      activeAlertResolver = resolve;
    });
  }

  function closeInteractiveAlert() {
    closeModal(alertModalEl);

    if (typeof activeAlertResolver === 'function') {
      const resolve = activeAlertResolver;
      activeAlertResolver = null;
      resolve();
    }
  }

  function syncAnonimSection() {
    const isOnlineMode = selectedService === 'online';
    const useAnonimIdentity = isOnlineMode && isAnonim;

    if (anonimToggleCardEl) {
      anonimToggleCardEl.hidden = !isOnlineMode;
    }

    if (anonimToggleEl) {
      anonimToggleEl.checked = isAnonim;
      anonimToggleEl.disabled = !isOnlineMode || !isLoggedIn;
    }

    profileNimEl.value = useAnonimIdentity ? '********' : profileDisplay.nim;
    profileNamaEl.value = useAnonimIdentity ? profileDisplay.anonimName : profileDisplay.nama;
  }

  function updateAnonimUI(active) {
    isAnonim = Boolean(active);

    if (profileNimEl) {
      profileNimEl.value = isAnonim ? '********' : profileDisplay.nim;
    }

    if (profileNamaEl) {
      profileNamaEl.value = isAnonim ? profileDisplay.anonimName : profileDisplay.nama;
    }

    if (anonimStatusEl) {
      anonimStatusEl.textContent = isAnonim ? 'Mode anonim aktif.' : 'Mode anonim nonaktif.';
      anonimStatusEl.classList.remove('is-error');
    }

    syncAnonimSection();
  }

  async function toggleAnonimMode(checkbox) {
    if (!isLoggedIn) {
      checkbox.checked = false;
      window.location.href = '/login';
      return;
    }

    if (selectedService !== 'online') {
      checkbox.checked = isAnonim;
      return;
    }

    const previousState = isAnonim;
    const requestedState = checkbox.checked;

    checkbox.disabled = true;

    if (anonimStatusEl) {
      anonimStatusEl.textContent = 'Menyimpan perubahan mode anonim...';
      anonimStatusEl.classList.remove('is-error');
    }

    try {
      const response = await fetch('{{ route('profil.anonim') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ anonim: requestedState }),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        checkbox.checked = previousState;
        updateAnonimUI(previousState);

        if (anonimStatusEl) {
          anonimStatusEl.textContent = data.message || 'Gagal memperbarui mode anonim.';
          anonimStatusEl.classList.add('is-error');
        }

        return;
      }

      const newAnonimState = typeof data.anonim !== 'undefined'
        ? Boolean(data.anonim)
        : requestedState;

      checkbox.checked = newAnonimState;
      updateAnonimUI(newAnonimState);

    } catch (error) {
      checkbox.checked = previousState;
      updateAnonimUI(previousState);

      if (anonimStatusEl) {
        anonimStatusEl.textContent = 'Gagal memperbarui mode anonim.';
        anonimStatusEl.classList.add('is-error');
      }
    } finally {
      checkbox.disabled = false;
    }
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

    tanggalNote.textContent = isJadwalUlang
      ? 'Tanggal baru tersedia untuk pengajuan jadwal ulang.'
      : 'Tanggal tersedia untuk pengajuan jadwal.';

    return true;
  }

  function showUnavailablePopup(detail = {}) {
    return showInteractiveAlert({
      title: 'Konselor Tidak Tersedia',
      icon: 'unavailable',
      confirmButtonText: 'Pilih Waktu Lain',
      html: `
        <div class="unavailable-popup-content">
          <div class="unavailable-popup-desc">
            Jadwal tidak dapat dipilih karena konselor tidak tersedia pada tanggal dan waktu tersebut.
          </div>

          <div class="unavailable-detail">
            <div class="detail-row">
              <span class="detail-label">Tanggal</span>
              <span class="detail-separator">:</span>
              <span class="detail-value">${detail.tanggal || '-'}</span>
            </div>

            <div class="detail-row">
              <span class="detail-label">Waktu</span>
              <span class="detail-separator">:</span>
              <span class="detail-value">${detail.waktu || '-'}</span>
            </div>

            <div class="detail-row">
              <span class="detail-label">Alasan</span>
              <span class="detail-separator">:</span>
              <span class="detail-value">${detail.alasan || 'Tidak ada alasan tambahan.'}</span>
            </div>
          </div>
        </div>
      `
    });
  }

  function renderTimeOptions() {
    const ymd = tanggalEl.value;
    const selectedBeforeRender = waktuEl.value || jadwalUlangData?.waktu || '';

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

      const isPastTime = slotDate <= now;
      const slotInfo = bookedSlots.get(`${ymd}-${time}`);
      const slotStatus = String(slotInfo?.status || '').toLowerCase();
      const isApproved = isApprovedSlotStatus(slotStatus);
      const isBooked = slotInfo && isBookedSlotStatus(slotStatus);
      const isUnavailable = slotStatus === 'tidak_tersedia';

      if (isPastTime) {
        option.disabled = true;
        option.textContent += ' - lewat';
      } else if (isUnavailable) {
        option.textContent += ` - ${slotInfo?.label || 'Tidak tersedia'}`;
        option.dataset.status = 'tidak_tersedia';
        option.dataset.detail = JSON.stringify(slotInfo?.detail || {});
      } else if (isBooked) {
        // Semua slot yang sudah dipakai dikunci di dropdown agar tidak perlu menunggu submit.
        option.disabled = true;
        option.dataset.status = slotStatus;
        option.textContent += ` - ${slotInfo?.label || (isApproved ? 'Telah Terjadwal' : 'Sudah Terisi')}`;
      }

      waktuEl.appendChild(option);
    });

    if (selectedBeforeRender) {
      const selectedOption = Array.from(waktuEl.options)
        .find(option => option.value === selectedBeforeRender);

      if (selectedOption && !selectedOption.disabled) {
        waktuEl.value = selectedBeforeRender;
      }
    }

    waktuNote.textContent = isJadwalUlang
      ? 'Pilih waktu baru untuk menjadwalkan ulang sesi konseling.'
      : 'Slot dengan status "Telah Terjadwal" sudah disetujui dan tidak dapat dipilih.';
  }

  async function fetchBookedSlots() {
    try {
      const url = new URL('{{ route("jadwal.terisi") }}', window.location.origin);

      if (isJadwalUlang && jadwalUlangData?.id) {
        url.searchParams.set('exclude_jadwal_id', jadwalUlangData.id);
      }

      const res = await fetch(url.toString(), {
        headers: { 'Accept': 'application/json' },
        cache: 'no-store'
      });

      const data = await res.json();

      bookedSlots.clear();

      data.forEach(slot => {
        if (typeof slot === 'string') {
          bookedSlots.set(slot, {
            status: 'menunggu',
            label: 'Sudah Terisi'
          });
          return;
        }

        if (slot && slot.slot) {
          bookedSlots.set(slot.slot, {
            status: slot.status || 'menunggu',
            label: slot.label || 'Sudah Terisi',
            detail: slot.detail || null,
          });
        }
      });
    } catch (error) {
      bookedSlots.clear();
    }
  }

  function setSubmitButtonLabel(mode) {
    if (!submitBtn) return;

    if (isJadwalUlang) {
      submitBtn.textContent = 'Simpan Jadwal Ulang';
      return;
    }

    submitBtn.textContent = serviceConfig[mode]?.submit || 'Jadwalkan Konseling';
  }

  function setServiceMode(mode, shouldScroll = false) {
    if (!serviceConfig[mode]) return;

    selectedService = mode;

    const config = serviceConfig[mode];

    bookingEl?.classList.add('is-visible');
    document.body.classList.toggle('is-jadwal-ulang-mode', isJadwalUlang);

    document.querySelectorAll('[data-panel]').forEach(panel => {
      panel.classList.toggle('active', panel.dataset.panel === mode);
    });

    const scheduleSubtitleEl = document.getElementById('schedule-subtitle');
    const selectedModePillEl = document.getElementById('selected-mode-pill');
    const sideMediaEl = document.getElementById('side-media');
    const sideLocationEl = document.getElementById('side-location');
    const sessionNoteEl = document.getElementById('session-note');
    const sessionNoteTextEl = document.getElementById('session-note-text');

    if (scheduleSubtitleEl) {
      scheduleSubtitleEl.textContent = isJadwalUlang
        ? 'Ubah tanggal, waktu, atau topik untuk menjadwalkan ulang sesi konseling Anda.'
        : config.subtitle;
    }

    submitBtn.textContent = config.submit;
    submitBtn.classList.toggle('online', mode === 'online');
    syncAnonimSection();

    if (selectedModePillEl) {
      selectedModePillEl.className = `selected-mode-pill ${mode === 'online' ? 'online' : ''}`;
      selectedModePillEl.innerHTML = `<i class="bi ${config.icon}"></i> ${config.label}`;
    }

    if (sideMediaEl) {
      sideMediaEl.innerHTML = config.sideMedia;
    }

    if (sideLocationEl) {
      sideLocationEl.innerHTML = config.sideLocation;
    }

    if (sessionNoteEl) {
      sessionNoteEl.className = `session-note ${config.noteClass}`;
    }

    if (sessionNoteTextEl) {
      sessionNoteTextEl.textContent = isJadwalUlang
        ? 'Silakan pilih jadwal baru yang tersedia. Data lama akan diperbarui, bukan membuat riwayat baru.'
        : config.note;
    }

    setSubmitButtonLabel(mode);

    submitBtn?.classList.toggle('online', mode === 'online');

    if (shouldScroll && bookingEl) {
      bookingEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function setTopikValue(value) {
    if (!topikEl) return;

    const normalizedValue = value || '';

    const hasOption = Array.from(topikEl.options)
      .some(option => option.value === normalizedValue);

    if (hasOption) {
      topikEl.value = normalizedValue;
      handleTopikChange();
      return;
    }

    if (normalizedValue) {
      topikEl.value = 'lainnya';

      if (topikLainnyaEl) {
        topikLainnyaEl.value = normalizedValue;
      }

      handleTopikChange();
    }
  }

  function initializeJadwalUlangMode() {
    if (!isJadwalUlang || !jadwalUlangData) return;

    selectedService = jadwalUlangData.jenis || 'offline';

    setServiceMode(selectedService, false);

    if (tanggalEl) {
      tanggalEl.value = jadwalUlangData.tanggal || '';
      validateDate();
    }

    renderTimeOptions();

    if (waktuEl && jadwalUlangData.waktu) {
      waktuEl.value = jadwalUlangData.waktu;
    }

    setTopikValue(jadwalUlangData.topik);

    const pageTitle = document.querySelector('.konseling-title, .page-title, h1');
    if (pageTitle) {
      pageTitle.textContent = 'Jadwalkan Ulang Konseling';
    }

    setSubmitButtonLabel(selectedService);

    if (bookingEl) {
      bookingEl.classList.add('is-visible');

      setTimeout(() => {
        bookingEl.scrollIntoView({
          behavior: 'auto',
          block: 'start'
        });
      }, 100);
    }
  }

  function initializeFollowUpMode() {
    if (isJadwalUlang || !followUpData) return;

    selectedService = followUpData.jenis || 'offline';
    setServiceMode(selectedService, false);
    setTopikValue(followUpData.topik);

    const pageTitle = document.querySelector('.konseling-title, .page-title, h1');
    if (pageTitle) {
      pageTitle.textContent = 'Ajukan Sesi Lanjutan';
    }

    setSubmitButtonLabel(selectedService);

    if (bookingEl) {
      bookingEl.classList.add('is-visible');
      bookingEl.scrollIntoView({ behavior: 'auto', block: 'start' });
    }
  }

  function openConfirmModal() {
    if (!isLoggedIn) {
      window.location.href = '/login';
      return;
    }

    const checkbox = document.getElementById('confirmation-checkbox');

    if (!checkbox?.checked) {
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

    openModal(confirmModalEl);
  }

  function closeConfirmModal() {
    closeModal(confirmModalEl);
  }

  function openSuccessModal() {
    openModal(successModalEl);
  }

  function closeSuccessModal() {
    closeModal(successModalEl);
    window.location.href = successRedirectUrl;
  }

  async function submitJadwal() {
    if (!isLoggedIn) {
      window.location.href = '/login';
      return;
    }

    const checkbox = document.getElementById('confirmation-checkbox');

    if (!checkbox?.checked) {
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

    if (!isJadwalUlang && followUpData?.id) {
      payload.follow_up_from = followUpData.id;
    }

    const originalText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Memproses...';

    try {
      const res = await fetch(submitUrl, {
        method: submitMethod,
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
        console.error('ERROR submit jadwal:', res.status, data);

        closeConfirmModal();

        await fetchBookedSlots();
        renderTimeOptions();

        if (res.status === 409) {
          waktuEl.value = '';

          let title = 'Jadwal Sudah Terisi';
          let text = data.message || 'Jadwal ini sudah terisi. Silakan pilih waktu lain.';
          let icon = 'warning';

          if (data.type === 'konselor_tidak_tersedia') {
            const detail = data.detail || {};

            title = data.title || 'Konselor Tidak Tersedia';

            text = `
              <div class="unavailable-popup-content">
                <div class="unavailable-popup-desc">
                  ${data.message || 'Konselor tidak tersedia pada jadwal tersebut.'}
                </div>

                <div class="unavailable-detail">
                  <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-separator">:</span>
                    <span class="detail-value">${detail.tanggal || '-'}</span>
                  </div>

                  <div class="detail-row">
                    <span class="detail-label">Waktu</span>
                    <span class="detail-separator">:</span>
                    <span class="detail-value">${detail.waktu || '-'}</span>
                  </div>

                  <div class="detail-row">
                    <span class="detail-label">Alasan</span>
                    <span class="detail-separator">:</span>
                    <span class="detail-value">${detail.alasan || 'Tidak ada alasan tambahan.'}</span>
                  </div>
                </div>
              </div>
            `;

            icon = 'unavailable';
          } else if (isApprovedSlotStatus(data.slot_status)) {
            title = 'Slot Sudah Terjadwal';
            icon = 'info';
          }

          await showInteractiveAlert({
            title: title,
            html: text,
            icon: icon,
            confirmButtonText: 'Pilih Waktu Lain'
          });
        } else {
          await showInteractiveAlert({
            title: 'Penjadwalan Gagal',
            text: data.message || 'Jadwal gagal disimpan.',
            icon: 'error'
          });
        }

        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
      }

      if (data.success) {
    closeConfirmModal();

    await showInteractiveAlert({
        title: isJadwalUlang ? 'Jadwal Ulang Berhasil' : 'Penjadwalan Berhasil',
        text: data.message || 'Jadwal berhasil disimpan.',
        icon: 'success',
        confirmButtonText: 'OK'
    });

    window.location.href = data.redirect_url || successRedirectUrl;
    return;
}

      closeConfirmModal();

      await showInteractiveAlert({
        title: 'Penjadwalan Gagal',
        text: data.message || 'Jadwal gagal disimpan.',
        icon: 'error'
      });

      submitBtn.disabled = false;
      submitBtn.textContent = originalText;

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

  function handleTopikChange() {
    const val = topikEl.value;

    if (val === 'lainnya') {
      topikLainnyaEl.style.display = 'block';
      topikLainnyaEl.required = true;
    } else {
      topikLainnyaEl.style.display = 'none';
      topikLainnyaEl.required = false;
      topikLainnyaEl.value = '';
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

  // Setup event listeners
  tanggalEl.min = todayYmd();

  tanggalEl.addEventListener('change', function () {
    validateDate();
    renderTimeOptions();
  });

  waktuEl.addEventListener('change', async function () {
  const selectedOption = this.options[this.selectedIndex];

  if (selectedOption?.dataset.status !== 'tidak_tersedia') {
    return;
  }

  let detail = {};

  try {
    detail = JSON.parse(selectedOption.dataset.detail || '{}');
  } catch (error) {
    detail = {};
  }

  await showUnavailablePopup(detail);

  this.value = '';
});

  topikEl.addEventListener('change', handleTopikChange);

  anonimToggleEl?.addEventListener('change', function () {
    toggleAnonimMode(this);
  });

  alertModalConfirmEl?.addEventListener('click', closeInteractiveAlert);
  alertModalCancelEl?.addEventListener('click', closeInteractiveAlert);

  [confirmModalEl, successModalEl, alertModalEl].forEach(modal => {
    modal?.addEventListener('click', function (event) {
      if (event.target !== modal) return;

      if (modal === confirmModalEl) {
        closeConfirmModal();
        return;
      }

      if (modal === successModalEl) {
        closeSuccessModal();
        return;
      }

      closeInteractiveAlert();
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;

    if (alertModalEl?.classList.contains('show')) {
      closeInteractiveAlert();
      return;
    }

    if (confirmModalEl?.classList.contains('show')) {
      closeConfirmModal();
      return;
    }

    if (successModalEl?.classList.contains('show')) {
      closeSuccessModal();
    }
  });

  document.querySelectorAll('[data-mode-action]').forEach(el => {
    el.addEventListener('click', function (event) {
      event.preventDefault();
      setServiceMode(this.dataset.modeAction, true);
    });
  });

  // Initialize
  updateAnonimUI(isAnonim);
  handleTopikChange();

  fetchBookedSlots().then(() => {
    if (isJadwalUlang && jadwalUlangData) {
      initializeJadwalUlangMode();
    } else if (followUpData) {
      initializeFollowUpMode();
    } else {
      bookingEl?.classList.remove('is-visible');

      document.querySelectorAll('[data-panel]').forEach(panel => {
        panel.classList.remove('active');
      });

      renderTimeOptions();
    }
  });
});
</script>
@endpush
