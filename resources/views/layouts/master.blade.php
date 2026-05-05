<doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>BK Connect - IT Del Mental Health</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,700;1,9..144,400&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
  :root {
    --primary: #064E3B;
    --primary-700: #065F46;
    --primary-600: #047857;
    --primary-500: #10B981;
    --primary-soft: #D1FAE5;

    --navbar-bg: #EFFCF5;
    --surface: #FFFFFF;
    --surface-soft: #F7FCF9;
    --border: #DDEFE7;

    --text-dark: #0F172A;
    --text-mid: #475569;
    --text-light: #64748B;
    --white: #FFFFFF;
    --danger: #DC2626;

    --shadow-sm: 0 2px 12px rgba(6, 78, 59, 0.06);
    --shadow-md: 0 10px 30px rgba(6, 78, 59, 0.10);
    --radius: 16px;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }

  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--surface);
    color: var(--text-dark);
    overflow-x: hidden;
  }

  /* NAVBAR */
  .navbar-main {
    background: rgba(239, 252, 245, 0.96);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    padding: .7rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all .25s ease;
  }

  .navbar-main.scrolled {
    box-shadow: var(--shadow-sm);
  }

  .brand-top {
    font-family: 'Fraunces', serif;
    font-weight: 700;
    font-size: 1.05rem;
    color: var(--primary);
    line-height: 1.1;
  }

  .brand-sub {
    font-size: .68rem;
    color: var(--text-light);
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  .nav-link-custom {
    font-size: .92rem;
    font-weight: 600;
    color: var(--text-mid) !important;
    padding: .55rem .95rem !important;
    border-radius: 10px;
    transition: all .2s ease;
  }

  .nav-link-custom:hover,
  .nav-link-custom.active {
    color: var(--primary) !important;
    background: var(--primary-soft);
  }

  .navbar .dropdown-menu {
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: var(--shadow-md);
    padding: .45rem;
  }

  .navbar .dropdown-item {
    border-radius: 10px;
    font-size: .9rem;
    padding: .6rem .75rem;
    color: var(--text-mid);
  }

  .navbar .dropdown-item:hover {
    background: var(--primary-soft);
    color: var(--primary);
  }

  .notif-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 999px;
    color: var(--text-mid);
    text-decoration: none;
    transition: all .2s ease;
  }

  .notif-link:hover {
    background: var(--primary-soft);
    color: var(--primary);
  }

  .notif-badge {
    position: absolute;
    top: -2px;
    right: -3px;
    min-width: 17px;
    height: 17px;
    padding: 0 4px;
    border-radius: 999px;
    background: var(--danger);
    color: white;
    font-size: .62rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
  }

  .notif-dropdown {
    min-width: 320px;
    border: 1px solid var(--border) !important;
    border-radius: 14px;
    box-shadow: var(--shadow-md);
    padding: .4rem 0;
    max-height: min(70vh, 520px);
    overflow-x: hidden;
    overflow-y: auto;
    overscroll-behavior: contain;
    background: var(--white);
  }

  .notif-header {
    position: sticky;
    top: 0;
    z-index: 1;
    padding: .6rem 1rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: .05em;
    background: var(--white);
    border-bottom: 1px solid #F1F5F9;
  }

  .notif-item {
    display: block;
    padding: .7rem 1rem;
    text-decoration: none;
    border-top: 1px solid #F1F5F9;
  }

  .notif-item:hover {
    background: #F8FFFB;
  }

  .notif-item p {
    margin: 0;
    font-size: .84rem;
    color: var(--text-dark);
    line-height: 1.45;
  }

  .notif-time {
    display: block;
    font-size: .72rem;
    color: var(--text-light);
    margin-top: .25rem;
  }

  .notif-empty {
    padding: .9rem 1rem;
    font-size: .82rem;
    color: var(--text-light);
  }

  .notif-item-trigger {
    width: 100%;
    text-align: left;
    background: transparent;
    border: none;
    cursor: pointer;
  }

  .notif-item-trigger .notif-tag {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    margin-top: .45rem;
    padding: .28rem .55rem;
    border-radius: 999px;
    background: #e9fff1;
    color: #047857;
    font-size: .66rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
  }

  .letter-modal {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 1rem 1.25rem 1.5rem;
    overflow-y: auto;
    overscroll-behavior: contain;
    opacity: 0;
    pointer-events: none;
    transition: opacity .24s ease;
  }

  .letter-modal.show {
    opacity: 1;
    pointer-events: auto;
  }

  .letter-modal-backdrop {
    position: fixed;
    inset: 0;
    background:
      radial-gradient(circle at top, rgba(16, 185, 129, 0.18), transparent 36%),
      linear-gradient(180deg, rgba(15, 23, 42, 0.74), rgba(15, 23, 42, 0.7));
    backdrop-filter: blur(12px);
  }

  .letter-modal-dialog {
    position: relative;
    z-index: 1;
    width: min(100%, 640px);
    max-height: calc(100vh - 2.5rem);
    margin: .75rem auto;
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
    overflow: visible;
    transform: translateY(18px) scale(.96);
    transition: transform .3s ease;
  }

  .letter-modal-dialog::before,
  .letter-modal-dialog::after {
    display: none;
  }

  .letter-modal-dialog::before {
    width: 220px;
    height: 220px;
    top: -82px;
    right: -62px;
    background: radial-gradient(circle, rgba(110, 231, 183, 0.36), rgba(110, 231, 183, 0));
  }

  .letter-modal-dialog::after {
    width: 180px;
    height: 180px;
    left: -48px;
    bottom: -70px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.18), rgba(16, 185, 129, 0));
    animation-delay: -4s;
  }

  .letter-modal.show .letter-modal-dialog {
    transform: translateY(0) scale(1);
  }

  .letter-modal-head {
    position: absolute;
    top: -.15rem;
    right: .1rem;
    z-index: 4;
    display: flex;
    justify-content: flex-end;
    width: 100%;
    pointer-events: none;
  }

  .letter-modal-label {
    display: none;
  }

  .letter-modal-close {
    pointer-events: auto;
    width: 38px;
    height: 38px;
    border-radius: 999px;
    border: none;
    background: rgba(255, 255, 255, 0.84);
    color: #065f46;
    font-size: 1.1rem;
    box-shadow: 0 16px 36px rgba(6, 78, 59, 0.18);
    backdrop-filter: blur(14px);
  }

  .letter-modal-body {
    position: relative;
    padding: .4rem 0 .75rem;
    overflow: visible;
    max-height: calc(100vh - 3.25rem);
    display: block;
  }

  .letter-modal-body::before {
    display: none;
  }

  .envelope-stage {
    position: relative;
    padding: .95rem 0 1rem;
    display: grid;
    place-items: center;
    gap: 1.2rem;
    perspective: 1600px;
  }

  .envelope-stage::after {
    content: "";
    position: absolute;
    bottom: 28px;
    width: 280px;
    height: 30px;
    border-radius: 999px;
    background: radial-gradient(circle, rgba(6, 95, 70, 0.14), rgba(6, 95, 70, 0));
    filter: blur(12px);
    opacity: .6;
    pointer-events: none;
  }

  .envelope-speech {
    position: relative;
    width: min(100%, 410px);
    max-height: 0;
    overflow: hidden;
    padding: 0 1.15rem;
    border-radius: 26px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(248, 255, 251, 0.92));
    border: 1px solid rgba(209, 250, 229, 0.42);
    box-shadow: 0 24px 56px rgba(6, 78, 59, 0.16);
    backdrop-filter: blur(18px);
    text-align: left;
    animation: speechFloat 5.8s ease-in-out infinite;
    z-index: 1;
    cursor: pointer;
    opacity: 0;
    pointer-events: none;
    transform: translate3d(0, 18px, 0) scale(.92);
    transform-origin: bottom center;
    transition: max-height .52s cubic-bezier(.2,.85,.2,1), padding .42s ease, opacity .38s ease, transform .52s cubic-bezier(.18,.9,.22,1), box-shadow .34s ease, border-color .34s ease;
  }

  .envelope-stage.has-speech .envelope-speech {
    max-height: 340px;
    overflow: visible;
    padding: 1rem 1.15rem 1.25rem;
    border-color: rgba(110, 231, 183, 0.42);
    opacity: 1;
    pointer-events: auto;
    transform: translate3d(0, -8px, 0) scale(1);
  }

  .envelope-speech::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: -10px;
    width: 24px;
    height: 24px;
    border-radius: 999px;
    transform: translateX(-50%) scale(.78);
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(248, 255, 251, 0.92));
    border: 1px solid rgba(110, 231, 183, 0.34);
    box-shadow: 0 10px 24px rgba(6, 78, 59, 0.1);
    opacity: 0;
    transition: opacity .3s ease, transform .42s cubic-bezier(.18,.9,.22,1);
  }

  .envelope-stage.has-speech .envelope-speech::after {
    opacity: 1;
    transform: translateX(-50%) scale(1);
  }

  .envelope-speech-link,
  .envelope-speech-link::before,
  .envelope-speech-link::after {
    position: absolute;
    border-radius: 999px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(248, 255, 251, 0.92));
    border: 1px solid rgba(110, 231, 183, 0.28);
    box-shadow: 0 10px 24px rgba(6, 78, 59, 0.1);
  }

  .envelope-speech-link {
    left: 50%;
    bottom: -18px;
    width: 22px;
    height: 22px;
    opacity: 0;
    pointer-events: none;
    transform: translateX(-50%) scale(.72);
    transition: opacity .34s ease, transform .48s cubic-bezier(.18,.9,.22,1);
  }

  .envelope-speech-link::before,
  .envelope-speech-link::after {
    content: "";
  }

  .envelope-speech-link::before {
    width: 16px;
    height: 16px;
    left: -10px;
    top: 6px;
  }

  .envelope-speech-link::after {
    width: 30px;
    height: 30px;
    left: 8px;
    top: 11px;
  }

  .envelope-stage.has-speech .envelope-speech-link {
    opacity: 1;
    transform: translateX(-50%) scale(1);
  }

  .letter-modal[data-tone="waiting"] .envelope-speech {
    background: linear-gradient(180deg, rgba(240, 253, 244, 0.9), rgba(255, 255, 255, 0.92));
    box-shadow: 0 24px 56px rgba(16, 185, 129, 0.14);
  }

  .letter-modal[data-tone="waiting"] .envelope-stage.has-speech .envelope-speech {
    border-color: rgba(110, 231, 183, 0.45);
  }

  .letter-modal[data-tone="waiting"] .envelope-speech::after {
    background: linear-gradient(180deg, rgba(240, 253, 244, 0.98), rgba(255, 255, 255, 1));
    border-color: rgba(110, 231, 183, 0.35);
    box-shadow: 0 10px 24px rgba(16, 185, 129, 0.08);
  }

  .letter-modal[data-tone="waiting"] .envelope-speech-link,
  .letter-modal[data-tone="waiting"] .envelope-speech-link::before,
  .letter-modal[data-tone="waiting"] .envelope-speech-link::after {
    background: linear-gradient(180deg, rgba(240, 253, 244, 0.98), rgba(255, 255, 255, 1));
    border-color: rgba(110, 231, 183, 0.28);
    box-shadow: 0 10px 24px rgba(16, 185, 129, 0.08);
  }

  .envelope-speech:hover {
    transform: translate3d(0, -3px, 0) scale(1.01);
    box-shadow: 0 28px 60px rgba(16, 185, 129, 0.16);
  }

  .letter-modal[data-tone="waiting"] .envelope-speech:hover {
    box-shadow: 0 28px 60px rgba(16, 185, 129, 0.18);
  }

  .envelope-speech-kicker {
    display: inline-flex;
    align-items: center;
    width: 100%;
    gap: .45rem;
    color: #047857;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .05em;
    text-transform: uppercase;
  }

  .letter-modal[data-tone="waiting"] .envelope-speech-kicker {
    color: #047857;
  }

  .envelope-speech p {
    display: block;
    margin: .7rem 0 0;
    color: #475569;
    font-size: .94rem;
    line-height: 1.78;
    min-height: 5.3rem;
  }

  .envelope-speech-actions {
    margin-top: 1.15rem;
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transform: translateY(10px);
    transition: max-height .28s ease, opacity .24s ease, transform .24s ease;
  }

  .envelope-speech-actions[hidden] {
    display: none;
  }

  .envelope-stage.is-revealed .envelope-speech-actions {
    max-height: 120px;
    opacity: 1;
    transform: translateY(0);
  }

  .envelope-speech-actions form {
    margin: 0;
    padding-top: .15rem;
  }

  .envelope {
    --tilt-x: 0deg;
    --tilt-y: 0deg;
    --lift: 0px;
    --shell-scale: 1;
    --paper-1: #ecfdf5;
    --paper-2: #bbf7d0;
    --paper-3: #6ee7b7;
    --edge: #047857;
    --spark: rgba(16, 185, 129, 0.5);
    --blush: #f8b6a8;
    position: relative;
    width: min(100%, 340px);
    height: 300px;
    cursor: pointer;
    user-select: none;
    outline: none;
    touch-action: manipulation;
  }

  .letter-modal[data-tone="waiting"] .envelope {
    --paper-1: #f0fdf4;
    --paper-2: #d1fae5;
    --paper-3: #86efac;
    --edge: #059669;
    --spark: rgba(16, 185, 129, 0.58);
    --blush: #f6c2b9;
  }

  .envelope-aura {
    position: absolute;
    inset: 30px 42px 76px;
    border-radius: 999px;
    background: radial-gradient(circle, rgba(134, 239, 172, 0.42), rgba(134, 239, 172, 0));
    filter: blur(10px);
    animation: auraPulse 4.8s ease-in-out infinite;
  }

  .letter-modal[data-tone="waiting"] .envelope-aura {
    background: radial-gradient(circle, rgba(110, 231, 183, 0.42), rgba(110, 231, 183, 0));
  }

  .envelope-shadow {
    position: absolute;
    left: 50%;
    bottom: 26px;
    width: 72%;
    height: 22px;
    transform: translateX(-50%);
    background: radial-gradient(circle, rgba(6, 95, 70, 0.16), transparent 72%);
    filter: blur(12px);
    animation: shadowBreathe 4.8s ease-in-out infinite;
  }

  .envelope-spark {
    position: absolute;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.95), var(--spark));
    box-shadow: 0 0 0 7px rgba(255, 255, 255, 0.1);
    opacity: .9;
    animation: sparkleOrbit 5.6s ease-in-out infinite;
    pointer-events: none;
  }

  .envelope-spark--1 {
    top: 40px;
    left: 24px;
  }

  .envelope-spark--2 {
    top: 76px;
    right: 18px;
    width: 10px;
    height: 10px;
    animation-delay: -1.8s;
  }

  .envelope-spark--3 {
    right: 34px;
    bottom: 104px;
    width: 8px;
    height: 8px;
    animation-delay: -3.2s;
  }

  .envelope-character {
    position: absolute;
    inset: 16px 0 42px;
    display: grid;
    place-items: end center;
    transform-style: preserve-3d;
    animation: envelopeBob 5.8s ease-in-out infinite;
  }

  .envelope-shell {
    position: relative;
    width: 300px;
    height: 215px;
    transform-style: preserve-3d;
    transform: perspective(1200px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) translateY(var(--lift)) scale(var(--shell-scale));
    transition: transform .34s cubic-bezier(.18,.9,.22,1), filter .4s ease;
    filter: drop-shadow(0 20px 26px rgba(41, 24, 7, 0.14));
  }

  .letter-modal[data-tone="waiting"] .envelope-shell {
    filter: drop-shadow(0 20px 26px rgba(16, 185, 129, 0.12));
  }

  .envelope-back {
    position: absolute;
    inset: 28px 12px 18px;
    border-radius: 24px;
    background: linear-gradient(180deg, var(--paper-1), var(--paper-2));
    border: 2.5px solid var(--edge);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    overflow: hidden;
    z-index: 1;
  }

  .envelope-back::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
      linear-gradient(135deg, transparent 48.4%, rgba(93, 58, 20, 0.18) 48.6%, rgba(255, 255, 255, 0.3) 50%, transparent 50.4%) left bottom / 50% 100% no-repeat,
      linear-gradient(-135deg, transparent 48.4%, rgba(93, 58, 20, 0.18) 48.6%, rgba(255, 255, 255, 0.3) 50%, transparent 50.4%) right bottom / 50% 100% no-repeat;
    opacity: .92;
  }

  .envelope-letter {
    position: absolute;
    left: 50%;
    top: 50px;
    width: calc(100% - 78px);
    min-height: 140px;
    transform: translateX(-50%) translateY(34px);
    border-radius: 22px;
    background: linear-gradient(180deg, #fffefd, #fff6ec);
    box-shadow: 0 22px 38px rgba(15, 23, 42, 0.08);
    padding: 1rem 1rem 1.25rem;
    transition: transform .62s cubic-bezier(.2,.9,.2,1), box-shadow .3s ease;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: .45rem;
    overflow: hidden;
  }

  .letter-modal[data-tone="waiting"] .envelope-letter {
    background: linear-gradient(180deg, #ffffff, #f0fdf4);
  }

  .envelope-letter::before {
    content: "";
    position: absolute;
    left: 1rem;
    right: 1rem;
    bottom: .95rem;
    height: 32px;
    background: repeating-linear-gradient(
      180deg,
      rgba(148, 163, 184, 0.16) 0,
      rgba(148, 163, 184, 0.16) 2px,
      transparent 2px,
      transparent 10px
    );
    opacity: 0;
    transition: opacity .24s ease .26s;
  }

  .envelope.is-open .envelope-letter::before {
    opacity: .75;
  }

  .envelope.is-open .envelope-letter {
    transform: translateX(-50%) translateY(-86px);
    box-shadow: 0 26px 44px rgba(15, 23, 42, 0.12);
  }

  .envelope-letter h4 {
    position: relative;
    z-index: 1;
    font-size: .98rem;
    font-weight: 800;
    color: #065f46;
    margin: 0;
    opacity: 0;
    transform: translateY(10px);
    transition: opacity .24s ease .16s, transform .24s ease .16s;
  }

  .letter-modal[data-tone="waiting"] .envelope-letter h4 {
    color: #047857;
  }

  .envelope-letter p {
    margin: 0;
    position: relative;
    z-index: 1;
    color: #64748b;
    font-size: .82rem;
    line-height: 1.68;
    opacity: 0;
    transform: translateY(10px);
    transition: opacity .24s ease .22s, transform .24s ease .22s;
  }

  .envelope.is-open .envelope-letter h4,
  .envelope.is-open .envelope-letter p {
    opacity: 1;
    transform: translateY(0);
  }

  .envelope-flap {
    position: absolute;
    left: 12px;
    right: 12px;
    top: 28px;
    height: 102px;
    transform-origin: top center;
    transition: transform .72s cubic-bezier(.18,.9,.18,1), filter .24s ease;
    z-index: 5;
    transform-style: preserve-3d;
    backface-visibility: hidden;
  }

  .envelope-flap::before {
    content: "";
    position: absolute;
    inset: 0;
    clip-path: polygon(0 0, 50% 76%, 100% 0);
    background: linear-gradient(180deg, var(--paper-1), var(--paper-3));
    border-top-left-radius: 22px;
    border-top-right-radius: 22px;
    border: 2.5px solid var(--edge);
    box-shadow: inset 0 -10px 18px rgba(255, 255, 255, 0.22);
  }

  .envelope-flap::after {
    content: "";
    position: absolute;
    inset: 10px 32px 28px;
    border-radius: 0 0 999px 999px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.4), transparent);
    opacity: .75;
  }

  .envelope.is-open .envelope-flap {
    transform: rotateX(176deg) translateY(-3px);
  }

  .envelope-front {
    position: absolute;
    inset: 94px 12px 18px;
    border-radius: 0 0 24px 24px;
    background: linear-gradient(180deg, var(--paper-2), var(--paper-3));
    clip-path: polygon(0 0, 50% 58%, 100% 0, 100% 100%, 0 100%);
    z-index: 3;
    border: 2.5px solid var(--edge);
    box-shadow: inset 0 10px 16px rgba(255, 255, 255, 0.2);
  }

  .envelope-front::before,
  .envelope-front::after {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    width: 50%;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.18), rgba(157, 109, 59, 0.05));
  }

  .envelope-front::before {
    left: 0;
    clip-path: polygon(0 0, 100% 58%, 0 100%);
  }

  .envelope-front::after {
    right: 0;
    clip-path: polygon(100% 0, 100% 100%, 0 58%);
  }

  .envelope-face {
    position: absolute;
    left: 50%;
    top: 86px;
    width: 160px;
    height: 92px;
    transform: translateX(-50%);
    z-index: 6;
    pointer-events: none;
  }

  .envelope-brow {
    position: absolute;
    top: 8px;
    width: 28px;
    height: 12px;
    border-top: 4px solid rgba(77, 48, 25, 0.9);
    border-radius: 999px;
  }

  .envelope-brow--left {
    left: 30px;
    transform: rotate(-14deg);
  }

  .envelope-brow--right {
    right: 30px;
    transform: rotate(14deg);
  }

  .letter-modal[data-tone="waiting"] .envelope-brow {
    border-top-color: rgba(4, 120, 87, 0.9);
  }

  .envelope-eye {
    position: absolute;
    top: 22px;
    width: 34px;
    height: 40px;
    border-radius: 48% 48% 52% 52%;
    background: #2d1406;
    overflow: hidden;
    transform-origin: center 65%;
    animation: eyeBlink 5.6s ease-in-out infinite;
    box-shadow: inset 0 -5px 0 rgba(255, 255, 255, 0.05);
  }

  .letter-modal[data-tone="waiting"] .envelope-eye {
    background: #064e3b;
  }

  .envelope-eye--left {
    left: 30px;
  }

  .envelope-eye--right {
    right: 30px;
  }

  .envelope-eye-shine {
    position: absolute;
    top: 7px;
    left: 7px;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 10px 13px 0 -3px rgba(255, 255, 255, 0.88);
  }

  .envelope-mouth {
    position: absolute;
    left: 50%;
    top: 54px;
    width: 46px;
    height: 28px;
    transform: translateX(-50%);
    border-radius: 0 0 28px 28px;
    background: #5b1f14;
    overflow: hidden;
    box-shadow: inset 0 -2px 0 rgba(0, 0, 0, 0.08);
  }

  .letter-modal[data-tone="waiting"] .envelope-mouth {
    background: #14532d;
  }

  .envelope-mouth-inner {
    position: absolute;
    left: 50%;
    bottom: -3px;
    width: 24px;
    height: 16px;
    transform: translateX(-50%);
    border-radius: 999px 999px 0 0;
    background: #ff9092;
  }

  .envelope-cheek {
    position: absolute;
    top: 52px;
    width: 24px;
    height: 16px;
    border-radius: 999px;
    background: radial-gradient(circle, var(--blush), rgba(255, 182, 173, 0.18));
    filter: blur(.5px);
  }

  .envelope-cheek--left {
    left: 18px;
  }

  .envelope-cheek--right {
    right: 18px;
  }

  .envelope-arm,
  .envelope-leg {
    position: absolute;
    pointer-events: none;
  }

  .envelope-arm {
    top: 112px;
    width: 78px;
    height: 82px;
    z-index: 0;
  }

  .envelope-arm::before,
  .envelope-arm::after {
    content: "";
    position: absolute;
  }

  .envelope-arm--left {
    left: -42px;
    transform-origin: 88% 18%;
    animation: armWaveLeft 6.2s ease-in-out infinite;
  }

  .envelope-arm--left::before {
    left: 22px;
    top: 10px;
    width: 36px;
    height: 44px;
    border-left: 4px solid var(--edge);
    border-bottom: 4px solid var(--edge);
    border-radius: 0 0 0 30px;
    transform: rotate(16deg);
  }

  .envelope-arm--left::after {
    left: 0;
    top: 2px;
    width: 26px;
    height: 26px;
    border-radius: 46% 54% 44% 56%;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.75), var(--paper-1));
    border: 3px solid var(--edge);
  }

  .envelope-arm--right {
    right: -42px;
    transform-origin: 12% 18%;
    animation: armWaveRight 6.2s ease-in-out infinite;
    animation-delay: -3.1s;
  }

  .envelope-arm--right::before {
    right: 22px;
    top: 10px;
    width: 36px;
    height: 44px;
    border-right: 4px solid var(--edge);
    border-bottom: 4px solid var(--edge);
    border-radius: 0 0 30px 0;
    transform: rotate(-16deg);
  }

  .envelope-arm--right::after {
    right: 0;
    top: 20px;
    width: 26px;
    height: 26px;
    border-radius: 54% 46% 56% 44%;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.75), var(--paper-1));
    border: 3px solid var(--edge);
  }

  .envelope-leg {
    bottom: -48px;
    width: 34px;
    height: 60px;
    z-index: 0;
    animation: legHop 5.8s ease-in-out infinite;
  }

  .envelope-leg::before,
  .envelope-leg::after {
    content: "";
    position: absolute;
  }

  .envelope-leg::before {
    left: 14px;
    top: 0;
    width: 6px;
    height: 36px;
    border-radius: 999px;
    background: var(--edge);
  }

  .envelope-leg::after {
    left: 0;
    bottom: 0;
    width: 34px;
    height: 22px;
    border-radius: 18px 18px 14px 14px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), var(--paper-1));
    border: 3px solid var(--edge);
  }

  .envelope-leg--left {
    left: 96px;
  }

  .envelope-leg--right {
    right: 96px;
    animation-delay: -2.4s;
  }

  .envelope-badge {
    position: absolute;
    right: 16px;
    top: 10px;
    width: 58px;
    height: 58px;
    border-radius: 18px 20px 18px 20px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #d1fae5, #34d399);
    color: #065f46;
    font-size: 1.15rem;
    box-shadow: 0 12px 24px rgba(16, 185, 129, 0.22);
    border: 2px solid rgba(6, 95, 70, 0.18);
    z-index: 7;
    transform: rotate(8deg);
    transition: transform .3s ease, box-shadow .3s ease;
  }

  .letter-modal[data-tone="waiting"] .envelope-badge {
    background: linear-gradient(135deg, #d1fae5, #6ee7b7);
    color: #065f46;
    border-color: rgba(6, 95, 70, 0.18);
    box-shadow: 0 12px 24px rgba(16, 185, 129, 0.2);
  }

  .envelope:hover .envelope-badge {
    transform: rotate(12deg) scale(1.05);
  }

  .envelope.is-surprised .envelope-character {
    animation: envelopeCelebrate .92s cubic-bezier(.2,.9,.2,1);
  }

  .envelope.is-surprised .envelope-arm--left {
    animation: armCelebrateLeft .92s cubic-bezier(.2,.9,.2,1);
  }

  .envelope.is-surprised .envelope-arm--right {
    animation: armCelebrateRight .92s cubic-bezier(.2,.9,.2,1);
  }

  .envelope.is-surprised .envelope-eye {
    animation: eyeExcited .72s ease-in-out 1;
  }

  .envelope.is-surprised .envelope-brow--left {
    animation: browLiftLeft .72s ease-in-out 1;
  }

  .envelope.is-surprised .envelope-brow--right {
    animation: browLiftRight .72s ease-in-out 1;
  }

  .envelope.is-surprised .envelope-badge {
    animation: badgePop .72s cubic-bezier(.2,.9,.2,1) 1;
  }

  .envelope.is-surprised .envelope-spark {
    animation: sparkleBurst .82s ease-out 1;
  }

  .envelope.is-greeting .envelope-character {
    animation: envelopeGreeting 1.18s cubic-bezier(.18,.9,.22,1) 1;
  }

  .envelope.is-greeting .envelope-arm--left {
    animation: armLiftLeft 1.18s cubic-bezier(.18,.9,.22,1) 1;
  }

  .envelope.is-greeting .envelope-arm--right {
    animation: armLiftRight 1.18s cubic-bezier(.18,.9,.22,1) 1;
  }

  .envelope.is-greeting .envelope-eye {
    animation: eyeGreeting 1s ease-in-out 1;
  }

  .envelope.is-greeting .envelope-badge {
    animation: badgeGreeting 1.08s cubic-bezier(.18,.9,.22,1) 1;
  }

  .envelope-helper {
    margin-top: .35rem;
    color: rgba(248, 250, 252, 0.96);
    font-size: .82rem;
    font-weight: 600;
    text-align: center;
    max-width: 360px;
    max-height: 48px;
    overflow: hidden;
    line-height: 1.55;
    text-shadow: 0 2px 14px rgba(15, 23, 42, 0.42);
    transition: opacity .2s ease, transform .2s ease, max-height .2s ease, margin .2s ease;
  }

  .envelope-stage.is-revealed .envelope-helper {
    opacity: 0;
    transform: translateY(-6px);
    max-height: 0;
    margin-top: -.2rem;
  }

  @media (max-width: 767.98px) {
    .letter-modal {
      padding: .7rem .85rem 1rem;
    }

    .letter-modal-dialog {
      width: min(100%, 460px);
      margin: .35rem auto;
    }

    .letter-modal-body {
      max-height: calc(100vh - 1.5rem);
    }

    .envelope-stage {
      padding-top: .6rem;
      gap: 1rem;
    }

    .envelope {
      width: min(100%, 312px);
      height: 278px;
    }

    .envelope-shell {
      width: 276px;
      height: 202px;
    }

    .envelope-speech {
      width: min(100%, 360px);
    }

    .envelope-stage.has-speech .envelope-speech {
      max-height: 380px;
      transform: translate3d(0, -10px, 0) scale(1);
    }

    .envelope-helper {
      max-width: 320px;
    }
  }

  .letter-start-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    border: none;
    border-radius: 16px;
    padding: .9rem 1.2rem;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 34px rgba(6, 95, 70, 0.22);
    font-weight: 800;
    width: 100%;
    margin-top: .15rem;
    transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
  }

  .letter-start-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 20px 38px rgba(6, 95, 70, 0.24);
  }

  .letter-modal[data-tone="waiting"] .letter-start-btn {
    background: linear-gradient(135deg, #047857, #10b981);
    box-shadow: 0 16px 34px rgba(16, 185, 129, 0.22);
  }

  .letter-start-btn:disabled {
    opacity: .88;
    cursor: default;
  }

  .schedule-guard-modal {
    position: fixed;
    inset: 0;
    z-index: 2050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity .24s ease;
  }

  .schedule-guard-modal.show {
    opacity: 1;
    pointer-events: auto;
  }

  .schedule-guard-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.48);
    backdrop-filter: blur(8px);
  }

  .schedule-guard-dialog {
    position: relative;
    z-index: 1;
    width: min(100%, 460px);
    background: linear-gradient(180deg, #ffffff, #f8fffb);
    border: 1px solid rgba(209, 250, 229, 0.95);
    border-radius: 28px;
    box-shadow: 0 26px 80px rgba(6, 78, 59, 0.22);
    padding: 1.35rem 1.35rem 1.25rem;
    transform: translateY(18px) scale(.96);
    transition: transform .28s ease;
  }

  .schedule-guard-modal.show .schedule-guard-dialog {
    transform: translateY(0) scale(1);
  }

  .schedule-guard-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 38px;
    height: 38px;
    border-radius: 999px;
    border: none;
    background: rgba(209, 250, 229, 0.9);
    color: #065f46;
    font-size: 1.05rem;
  }

  .schedule-guard-icon {
    width: 72px;
    height: 72px;
    border-radius: 24px;
    display: grid;
    place-items: center;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #047857;
    font-size: 1.8rem;
    box-shadow: 0 14px 28px rgba(16, 185, 129, 0.18);
  }

  .schedule-guard-dialog h3 {
    font-size: 1.2rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .55rem;
  }

  .schedule-guard-dialog p {
    color: #475569;
    line-height: 1.75;
    margin-bottom: 1.2rem;
  }

  .schedule-guard-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 16px;
    padding: .85rem 1.15rem;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    font-weight: 800;
    box-shadow: 0 14px 28px rgba(6, 95, 70, 0.2);
  }

  @keyframes letterFloat {
    0%, 100% { transform: translate3d(0, 0, 0); }
    50% { transform: translate3d(0, 12px, 0); }
  }

  @keyframes letterPulse {
    0%, 100% { transform: scale(1); opacity: .8; }
    50% { transform: scale(1.08); opacity: 1; }
  }

  @keyframes envelopeBob {
    0%, 100% { transform: translate3d(0, 0, 0); }
    50% { transform: translate3d(0, -8px, 0); }
  }

  @keyframes shadowBreathe {
    0%, 100% { transform: translateX(-50%) scaleX(1); opacity: .55; }
    50% { transform: translateX(-50%) scaleX(0.92); opacity: .38; }
  }

  @keyframes speechFloat {
    0%, 100% { transform: translate3d(0, 0, 0); }
    50% { transform: translate3d(0, -6px, 0); }
  }

  @keyframes speechTextSweep {
    0% { opacity: 0; transform: translate3d(-24px, 0, 0); }
    55% { opacity: 1; transform: translate3d(10px, 0, 0); }
    100% { opacity: 1; transform: translate3d(0, 0, 0); }
  }

  @keyframes sparkleOrbit {
    0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: .8; }
    50% { transform: translate3d(0, -10px, 0) scale(1.12); opacity: 1; }
  }

  @keyframes eyeBlink {
    0%, 44%, 48%, 100% { transform: scaleY(1); }
    46% { transform: scaleY(0.12); }
  }

  @keyframes armWaveLeft {
    0%, 100% { transform: rotate(3deg); }
    25% { transform: rotate(13deg); }
    50% { transform: rotate(6deg); }
    75% { transform: rotate(15deg); }
  }

  @keyframes armWaveRight {
    0%, 100% { transform: rotate(-4deg); }
    25% { transform: rotate(-12deg); }
    50% { transform: rotate(-6deg); }
    75% { transform: rotate(-14deg); }
  }

  @keyframes legHop {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(3px); }
  }

  @keyframes auraPulse {
    0%, 100% { transform: scale(1); opacity: .85; }
    50% { transform: scale(1.08); opacity: 1; }
  }

  @keyframes envelopeCelebrate {
    0% { transform: translate3d(0, 0, 0) scale(1); }
    30% { transform: translate3d(0, -16px, 0) scale(1.04); }
    55% { transform: translate3d(0, -7px, 0) scale(1.02); }
    100% { transform: translate3d(0, 0, 0) scale(1); }
  }

  @keyframes envelopeGreeting {
    0% { transform: translate3d(0, 0, 0) scale(1); }
    32% { transform: translate3d(0, -12px, 0) scale(1.03); }
    68% { transform: translate3d(0, -5px, 0) scale(1.01); }
    100% { transform: translate3d(0, 0, 0) scale(1); }
  }

  @keyframes armCelebrateLeft {
    0% { transform: rotate(2deg); }
    25% { transform: rotate(32deg); }
    55% { transform: rotate(18deg); }
    100% { transform: rotate(8deg); }
  }

  @keyframes armCelebrateRight {
    0% { transform: rotate(-2deg); }
    25% { transform: rotate(-32deg); }
    55% { transform: rotate(-18deg); }
    100% { transform: rotate(-8deg); }
  }

  @keyframes armLiftLeft {
    0% { transform: rotate(4deg); }
    38% { transform: rotate(26deg) translateY(-4px); }
    70% { transform: rotate(18deg) translateY(-2px); }
    100% { transform: rotate(8deg); }
  }

  @keyframes armLiftRight {
    0% { transform: rotate(-4deg); }
    38% { transform: rotate(-26deg) translateY(-4px); }
    70% { transform: rotate(-18deg) translateY(-2px); }
    100% { transform: rotate(-8deg); }
  }

  @keyframes eyeExcited {
    0% { transform: scaleY(1) scaleX(1); }
    18% { transform: scaleY(0.08) scaleX(1.02); }
    36% { transform: scaleY(1.1) scaleX(1.04); }
    60% { transform: scaleY(0.12) scaleX(.98); }
    100% { transform: scaleY(1) scaleX(1); }
  }

  @keyframes eyeGreeting {
    0% { transform: scaleY(1) scaleX(1); }
    24% { transform: scaleY(0.14) scaleX(1.02); }
    48% { transform: scaleY(1.06) scaleX(1.03); }
    100% { transform: scaleY(1) scaleX(1); }
  }

  @keyframes browLiftLeft {
    0% { transform: rotate(-14deg) translateY(0); }
    40% { transform: rotate(-6deg) translateY(-7px); }
    100% { transform: rotate(-14deg) translateY(0); }
  }

  @keyframes browLiftRight {
    0% { transform: rotate(14deg) translateY(0); }
    40% { transform: rotate(6deg) translateY(-7px); }
    100% { transform: rotate(14deg) translateY(0); }
  }

  @keyframes badgePop {
    0% { transform: rotate(8deg) scale(1); }
    30% { transform: rotate(-8deg) scale(1.18); }
    55% { transform: rotate(12deg) scale(1.07); }
    100% { transform: rotate(8deg) scale(1); }
  }

  @keyframes badgeGreeting {
    0% { transform: rotate(8deg) scale(1); }
    38% { transform: rotate(-5deg) scale(1.14); }
    100% { transform: rotate(8deg) scale(1); }
  }

  @keyframes sparkleBurst {
    0% { transform: translate3d(0, 0, 0) scale(1); opacity: .9; }
    50% { transform: translate3d(0, -16px, 0) scale(1.24); opacity: 1; }
    100% { transform: translate3d(0, -6px, 0) scale(1); opacity: .9; }
  }

  @media (prefers-reduced-motion: reduce) {
    .letter-modal-dialog::before,
    .letter-modal-dialog::after,
    .letter-modal-body::before,
    .envelope-speech,
    .envelope-aura,
    .envelope-shadow,
    .envelope-spark,
    .envelope-character,
    .envelope-arm,
    .envelope-leg,
    .envelope-eye {
      animation: none !important;
    }

    .envelope-shell,
    .envelope-flap,
    .envelope-letter,
    .envelope-letter h4,
    .envelope-letter p,
    .letter-start-btn,
    .envelope-badge,
    .letter-modal-close {
      transition-duration: .01ms !important;
    }

    .envelope-stage.animate-copy #letterSpeechKickerText,
    .envelope-stage.animate-copy #letterSpeechMessage {
      animation: none !important;
      opacity: 1 !important;
      transform: none !important;
    }
  }

  .envelope-stage.animate-copy #letterSpeechKickerText,
  .envelope-stage.animate-copy #letterSpeechMessage {
    display: inline-block;
    width: 100%;
    opacity: 0;
    will-change: transform, opacity;
  }

  .envelope-stage.animate-copy #letterSpeechKickerText {
    animation: speechTextSweep .9s cubic-bezier(.2,.9,.2,1) forwards;
  }

  .envelope-stage.animate-copy #letterSpeechMessage {
    animation: speechTextSweep 1.2s cubic-bezier(.18,.9,.22,1) .12s forwards;
  }

  .profile-wrap { position: relative; }

 .profile-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  background: #f1f5f9;
  border: 2px solid rgba(255,255,255,.95);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
  transition: transform .2s ease, box-shadow .2s ease;
}

.profile-btn:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.profile-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.online-dot {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--primary-500);
  border: 2px solid white;
}

.profile-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 270px;
  background: white;
  border-radius: 14px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border);
  opacity: 0;
  pointer-events: none;
  transform: translateY(-8px);
  transition: all .2s ease;
  z-index: 999;
  overflow: hidden;
}

.profile-dropdown.show {
  opacity: 1;
  pointer-events: all;
  transform: translateY(0);
}

.pd-header {
  padding: .9rem 1rem;
  border-bottom: 1px solid #F1F5F9;
  display: flex;
  align-items: center;
  gap: .75rem;
}

.pd-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  overflow: hidden;
  background: #f1f5f9;
  flex-shrink: 0;
}

.pd-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.pd-name {
  font-weight: 700;
  font-size: .92rem;
  color: var(--text-dark);
  line-height: 1.2;
  margin-bottom: .15rem;
}

.pd-nim {
  font-size: .76rem;
  color: var(--text-light);
  line-height: 1.45;
  word-break: break-word;
}

.pd-item {
  display: flex;
  align-items: center;
  gap: .7rem;
  width: 100%;
  padding: .78rem 1rem;
  font-size: .88rem;
  color: var(--text-mid);
  text-decoration: none;
  background: transparent;
  border: 0;
  transition: all .15s ease;
}

.pd-item:hover {
  background: #F8FFFB;
  color: var(--primary);
}

.pd-item i {
  font-size: .98rem;
  width: 18px;
  flex-shrink: 0;
}

.pd-item.danger {
  color: var(--danger);
}

.pd-item.danger:hover {
  background: #FEF2F2;
  color: #dc2626;
}

.pd-divider {
  height: 1px;
  background: #F1F5F9;
  margin: 0;
}

.btn-login-custom {
  border: 1px solid var(--primary);
  color: var(--primary);
  background: transparent;
  border-radius: 12px;
  padding: .58rem 1rem;
  font-weight: 600;
  font-size: .9rem;
  transition: all .2s ease;
}

.btn-login-custom:hover {
  background: var(--primary-soft);
  color: var(--primary);
}

.btn-register-custom {
  border: 1px solid var(--primary);
  background: var(--primary);
  color: white;
  border-radius: 12px;
  padding: .58rem 1rem;
  font-weight: 600;
  font-size: .9rem;
  transition: all .2s ease;
}

.btn-register-custom:hover {
  background: var(--primary-700);
  border-color: var(--primary-700);
  color: white;
}

.page-in {
  animation: pageIn .45s ease both;
}

@keyframes pageIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

/* FOOTER */
footer {
  background: var(--primary);
  color: rgba(255,255,255,.78);
  padding: 4rem 0 2rem;
  margin-top: 5rem;
}

.footer-brand-txt {
  font-family: 'Fraunces', serif;
  font-size: 1.45rem;
  font-weight: 700;
  color: white;
}

footer h6 {
  color: rgba(255,255,255,.55);
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  font-size: .72rem;
  margin-bottom: 1rem;
}

footer a {
  color: rgba(255,255,255,.78);
  text-decoration: none;
  font-size: .88rem;
  display: block;
  margin-bottom: .5rem;
  transition: color .2s ease;
}

footer a:hover {
  color: #D1FAE5;
}

.footer-copy {
  border-top: 1px solid rgba(255,255,255,.14);
  margin-top: 2.5rem;
  padding-top: 1.5rem;
  font-size: .78rem;
  color: rgba(255,255,255,.55);
  text-align: center;
}

.footer-social a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255,255,255,.10);
  color: rgba(255,255,255,.85) !important;
  transition: all .2s ease;
  margin-right: .4rem;
}

.footer-social a:hover {
  background: rgba(255,255,255,.18);
  color: white !important;
}

@media (max-width: 991.98px) {
  .navbar-nav {
    padding-top: 1rem;
    align-items: flex-start !important;
  }

  .navbar-collapse {
    background: var(--navbar-bg);
    border-radius: 16px;
    padding: .5rem .25rem 1rem;
    margin-top: .75rem;
  }

  .profile-dropdown {
    width: 250px;
  }
}
</style>
  @vite(['resources/js/app.js'])
  @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-main" id="mainNav">
    <div class="container">
        @php
          $unreadNotif = 0;
          $notifItems = collect();
          $chatGuardBlocked = false;
          $chatGuardTitle = 'Sesi Belum Dimulai';
          $chatGuardMessage = null;
          $nextOnlineChatSchedule = null;
          if (Auth::check()) {
            $mahasiswaId = optional(Auth::user()->mahasiswa)->id;
            if ($mahasiswaId) {
              $approvedBookings = \App\Models\JadwalKonseling::where('mahasiswa_id', $mahasiswaId)
                ->where('status', 'disetujui')
                ->get(['id', 'tanggal', 'waktu', 'jenis']);

              foreach ($approvedBookings as $jadwal) {
                $jenisKonseling = strtolower(trim((string) $jadwal->jenis)) === 'offline' ? 'offline' : 'online';
                $dateTime = $jadwal->scheduledAt();

                if (! $dateTime) {
                  continue;
                }

                $pesanBaru = 'Konseling ' . $jenisKonseling . ' pada ' . $dateTime->translatedFormat('j F Y') . ' pukul ' . $dateTime->format('H:i') . ' telah disetujui oleh konselor.';
                $pesanLama = [
                  'Booking #' . $jadwal->id . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . ' telah disetujui oleh konselor.',
                  'Jadwal #' . $jadwal->id . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . ' telah disetujui oleh konselor.',
                ];

                $existingNotif = \App\Models\Notifikasi::where('user_id', Auth::id())
                  ->where(function ($query) use ($pesanBaru, $pesanLama) {
                    $query->where('pesan', $pesanBaru)
                      ->orWhereIn('pesan', $pesanLama);
                  })
                  ->latest()
                  ->first();

                if ($existingNotif) {
                  if ($existingNotif->pesan !== $pesanBaru) {
                    $existingNotif->pesan = $pesanBaru;
                    $existingNotif->save();
                  }
                } else {
                  \App\Models\Notifikasi::create([
                    'user_id' => Auth::id(),
                    'pesan' => $pesanBaru,
                    'status' => 'belum',
                  ]);
                }
              }
            }

              $chatGuardSchedule = \App\Models\JadwalKonseling::where('mahasiswa_id', $mahasiswaId)
                ->where('jenis', 'online')
                ->whereIn('status', ['disetujui', 'berlangsung'])
                ->get(['id', 'tanggal', 'waktu', 'status', 'jenis'])
                ->sort(fn ($left, $right) => $left->compareSessionPriority($right))
                ->first();

              if ($chatGuardSchedule) {
                $chatGuardAt = $chatGuardSchedule->scheduledAt();
                $nextOnlineChatSchedule = $chatGuardSchedule->scheduledStartLabel();

                if ($chatGuardAt && ! $chatGuardSchedule->hasScheduledTimeStarted()) {
                  $chatGuardBlocked = true;
                  $chatGuardMessage = 'Sesi konseling online Anda akan dimulai pada ' . $nextOnlineChatSchedule . '. Sebelum itu, ruang chat belum bisa diakses.';
                }
              }

              $unreadNotif = Auth::user()->notifikasi()->where('status', 'belum')->count();
              $notifItems = Auth::user()->notifikasi()->latest()->get();

              $jadwalItems = \App\Models\JadwalKonseling::where('mahasiswa_id', $mahasiswaId)
                ->get(['id', 'tanggal', 'waktu', 'jenis']);

              $jadwalById = $jadwalItems->keyBy('id');
              $jadwalByApprovedMessage = $jadwalItems->mapWithKeys(function ($jadwalItem) {
                $jenisKonseling = strtolower(trim((string) $jadwalItem->jenis)) === 'offline' ? 'offline' : 'online';
                $dateTime = $jadwalItem->scheduledAt();

                if (! $dateTime) {
                  return [];
                }

                $approvedMessage = 'Konseling ' . $jenisKonseling . ' pada ' . $dateTime->translatedFormat('j F Y') . ' pukul ' . $dateTime->format('H:i') . ' telah disetujui oleh konselor.';

                return [$approvedMessage => $jadwalItem];
              });

              $notifItems = $notifItems->map(function ($notif) use ($jadwalById, $jadwalByApprovedMessage, $chatGuardBlocked, $nextOnlineChatSchedule) {
                $pesan = $notif->pesan;
                $notif->cta_target = route('riwayat');
                $notif->is_letter_prompt = false;
                $notif->prompt_title = null;
                $notif->prompt_message = null;
                $notif->prompt_cta = null;
                $notif->prompt_note = null;
                $notif->prompt_locked = false;
                $notifText = strtolower((string) $pesan);
                $matchedApprovedJadwal = $jadwalByApprovedMessage->get($pesan);

                if (preg_match('/^(Booking|Jadwal|Penjadwalan)\s+#(\d+)\s+/i', $pesan, $match)) {
                  $jadwal = $jadwalById->get((int) $match[2]);

                  if ($jadwal) {
                    $jenisKonseling = strtolower(trim((string) $jadwal->jenis)) === 'offline' ? 'offline' : 'online';
                    $dateTime = $jadwal->scheduledAt();
                    $tanggalWaktu = $jadwal->scheduledStartLabel();

                    if (str_contains(strtolower($pesan), 'telah disetujui oleh konselor')) {
                      $notif->pesan = 'Konseling ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' telah disetujui oleh konselor.';

                      if ($jenisKonseling === 'online') {
                        $isLockedBySchedule = $dateTime ? ! $jadwal->hasScheduledTimeStarted() : true;
                        $notif->cta_target = route('mahasiswa.chat.start', ['jadwal_id' => $jadwal->id]);
                        $notif->is_letter_prompt = true;
                        $notif->prompt_locked = $isLockedBySchedule;
                        $notif->prompt_title = $isLockedBySchedule ? 'Sesi Belum Dimulai' : 'Undangan Sesi Konseling';
                        $notif->prompt_message = $isLockedBySchedule
                          ? 'Sesi konseling online Anda sudah disetujui. Ruang chat baru bisa diakses pada ' . $tanggalWaktu . '.'
                          : 'Halo, terima kasih sudah sampai di tahap ini. Sesi ini bukan ruang untuk menilai, tapi tempat aman untuk bercerita. Kalau sudah siap, mari mulai sesi konseling bersama konselor.';
                        $notif->prompt_cta = $isLockedBySchedule ? 'Menunggu Jadwal Sesi' : 'Mulai Sesi Konseling';
                        $notif->prompt_note = $isLockedBySchedule
                          ? 'Sesi ini akan dimulai pada ' . $tanggalWaktu . '.'
                          : 'Saat siap, Anda bisa langsung masuk ke ruang chat konseling.';
                      }
                    } elseif (str_contains(strtolower($pesan), 'menunggu persetujuan konselor')) {
                      $notif->pesan = 'Pengajuan konseling ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' berhasil dibuat dan menunggu persetujuan konselor.';
                    }
                  }
                }

                if (
                  ! $notif->is_letter_prompt &&
                  str_contains($notifText, 'konseling online') &&
                  str_contains($notifText, 'telah disetujui oleh konselor')
                ) {
                  $matchedDateTime = null;
                  $matchedTanggalWaktu = null;

                  if ($matchedApprovedJadwal) {
                    $matchedDateTime = $matchedApprovedJadwal->scheduledAt();
                    $matchedTanggalWaktu = $matchedApprovedJadwal->scheduledStartLabel();
                    $notif->pesan = 'Konseling online pada ' . $matchedTanggalWaktu . ' telah disetujui oleh konselor.';
                  } elseif (preg_match('/Konseling\s+online\s+pada\s+(.+?)\s+telah\s+disetujui\s+oleh\s+konselor\./iu', $pesan, $matches)) {
                    $matchedTanggalWaktu = trim($matches[1]);
                  }

                  $isLockedBySpecificSchedule = $matchedApprovedJadwal ? ! $matchedApprovedJadwal->hasScheduledTimeStarted() : $chatGuardBlocked;
                  $notif->cta_target = route('mahasiswa.chat.start', ['jadwal_id' => $matchedApprovedJadwal?->id]);
                  $notif->is_letter_prompt = true;
                  $notif->prompt_locked = $isLockedBySpecificSchedule;
                  $notif->prompt_title = $isLockedBySpecificSchedule ? 'Sesi Belum Dimulai' : 'Undangan Sesi Konseling';
                  $notif->prompt_message = $isLockedBySpecificSchedule && $matchedTanggalWaktu
                    ? 'Sesi konseling online Anda sudah disetujui. Ruang chat baru bisa diakses pada ' . $matchedTanggalWaktu . '.'
                    : ($chatGuardBlocked && $nextOnlineChatSchedule
                      ? 'Sesi konseling online Anda sudah disetujui. Ruang chat baru bisa diakses pada ' . $nextOnlineChatSchedule . '.'
                      : 'Halo, terima kasih sudah sampai di tahap ini. Sesi ini bukan ruang untuk menilai, tapi tempat aman untuk bercerita. Kalau sudah siap, mari mulai sesi konseling bersama konselor.');
                  $notif->prompt_cta = $isLockedBySpecificSchedule ? 'Menunggu Jadwal Sesi' : 'Mulai Sesi Konseling';
                  $notif->prompt_note = $isLockedBySpecificSchedule && $matchedTanggalWaktu
                    ? 'Sesi ini akan dimulai pada ' . $matchedTanggalWaktu . '.'
                    : ($chatGuardBlocked && $nextOnlineChatSchedule
                      ? 'Sesi ini akan dimulai pada ' . $nextOnlineChatSchedule . '.'
                      : 'Saat siap, Anda bisa langsung masuk ke ruang chat konseling.');
                }

                return $notif;
              });
          }
        @endphp

        <a class="d-flex align-items-center gap-2 text-decoration-none" href="/">

            <!-- LOGO GAMBAR -->
            <div class="">
                <img src="{{ asset('img/logo.png') }}" 
                  alt="Logo Campus Care"
                  style="width: 45px; height: 45px; object-fit: contain;">
            </div>

            <!-- TEXT -->
            <div>
                <div class="brand-top">Campus Care</div>
                <div class="brand-sub">IT Del - Mental Health</div>
            </div>
        </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link nav-link-custom {{ request()->is('/') ? 'active' : '' }}" href="/">Beranda</a></li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom {{ request()->is('tentang') ? 'active' : '' }}" href="/tentang">Tentang</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom {{ request()->is('konseling*') ? 'active' : '' }}" href="/konseling">
            Konseling
          </a>
        </li>
        @auth
        @if(Auth::user()->role === 'mahasiswa')
        <li class="nav-item">
          <a
            class="nav-link nav-link-custom {{ request()->routeIs('mahasiswa.chat*') ? 'active' : '' }}"
            href="{{ route('mahasiswa.chat') }}"
            @if($chatGuardBlocked)
              data-chat-guard="true"
              data-chat-guard-title="{{ $chatGuardTitle }}"
              data-chat-guard-message="{{ $chatGuardMessage }}"
            @endif
          >
            Chat
          </a>
        </li>
        @endif
        @endauth

        @auth
        <li class="nav-item dropdown ms-1">
          <a class="notif-link" href="#" id="notifDropdownBtn" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
            <i class="bi bi-bell" style="font-size:1rem;"></i>
            <span id="notifBadge" class="notif-badge {{ $unreadNotif > 0 ? '' : 'd-none' }}">{{ $unreadNotif > 9 ? '9+' : $unreadNotif }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-end notif-dropdown" aria-labelledby="notifDropdownBtn">
            <div class="notif-header">Notifikasi</div>
            @forelse($notifItems as $notif)
              @if(!empty($notif->is_letter_prompt))
              <button
                type="button"
                class="notif-item notif-item-trigger"
                data-letter-title="{{ $notif->prompt_title }}"
                data-letter-message="{{ $notif->prompt_message }}"
                data-letter-cta="{{ $notif->prompt_cta }}"
                data-letter-action="{{ $notif->cta_target }}"
                data-letter-note="{{ $notif->prompt_note }}"
                data-letter-locked="{{ !empty($notif->prompt_locked) ? '1' : '0' }}"
              >
                <p>{{ $notif->pesan }}</p>
                <span class="notif-time">{{ $notif->created_at?->diffForHumans() ?? 'Baru saja' }}</span>
                <span class="notif-tag"><i class="bi bi-envelope-paper-heart"></i> Buka Undangan</span>
              </button>
              @else
              <a href="{{ $notif->cta_target ?? route('riwayat') }}" class="notif-item">
                <p>{{ $notif->pesan }}</p>
                <span class="notif-time">{{ $notif->created_at?->diffForHumans() ?? 'Baru saja' }}</span>
              </a>
              @endif
            @empty
              <div class="notif-empty">Belum ada notifikasi.</div>
            @endforelse
          </div>
        </li>
        @endauth
      </ul>
      <div class="d-flex align-items-center ms-lg-3 mt-3 mt-lg-0">
        {{-- Jika SUDAH LOGIN --}}
       @auth
      <div class="profile-wrap">
          <div class="profile-btn" id="profileBtn" onclick="toggleProfile()">
              <img 
                  src="{{ optional(Auth::user()->profil)->foto 
                          ? Storage::url(Auth::user()->profil->foto) 
                          : asset('img/default-avatar.png') }}"
                  alt="Profile"
                  class="profile-img"
              >
              <div class="online-dot"></div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
              <div class="pd-header">
                  <div class="pd-avatar">
                      <img 
                          src="{{ optional(Auth::user()->profil)->foto 
                                  ? Storage::url(Auth::user()->profil->foto) 
                                  : asset('img/default-avatar.png') }}"
                          alt="Profile"
                          class="pd-avatar-img"
                      >
                  </div>

                  <div>
                      @if(Auth::user()->isAnonim())
                          <div class="pd-name">Mahasiswa Anonim</div>
                          <div class="pd-nim">
                              {{ optional(Auth::user()->mahasiswa)->jurusan ?? '' }}
                              {{ optional(Auth::user()->mahasiswa)->angkatan ?? '' }}
                          </div>
                      @else
                          <div class="pd-name">{{ Auth::user()->nama }}</div>
                          <div class="pd-nim">
                              {{ optional(Auth::user()->mahasiswa)->nim ?? '' }}
                              · {{ optional(Auth::user()->mahasiswa)->jurusan ?? '' }}
                              {{ optional(Auth::user()->mahasiswa)->angkatan ?? '' }}
                          </div>
                      @endif
                  </div>
              </div>

              <a href="{{ route('profil') }}" class="pd-item">
                  <i class="bi bi-person-circle"></i>
                  <span>Profil Saya</span>
              </a>

              <a href="{{ route('riwayat') }}" class="pd-item">
                  <i class="bi bi-calendar2-check"></i>
                  <span>Riwayat Konseling</span>
              </a>

              <div class="pd-divider"></div>

              <form action="{{ route('logout') }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="pd-item danger w-100 text-start bg-transparent border-0">
                      <i class="bi bi-box-arrow-right"></i>
                      <span>Keluar</span>
                  </button>
              </form>
          </div>
      </div>
        @endauth


        {{-- Jika BELUM LOGIN --}}
        @guest
        <a href="{{ route('login') }}" class="btn btn-login-custom me-2">Login</a>
        @endguest

      <!-- </div>
            </div>
            <a href="#" class="pd-item"><i class="bi bi-person-circle"></i> Profil Saya</a>
            <a href="#" class="pd-item"><i class="bi bi-calendar2-check"></i> Riwayat Konseling</a>
            <a href="#" class="pd-item"><i class="bi bi-bell"></i> Notifikasi <span class="badge bg-danger ms-auto" style="font-size:.62rem">3</span></a>
            <div class="pd-divider"></div>
            <a href="#" class="pd-item danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</nav>

<div class="letter-modal" id="letterModal" aria-hidden="true">
  <div class="letter-modal-backdrop" data-letter-close></div>
  <div class="letter-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="letterModalTitle">
    <div class="letter-modal-head">
      <div class="letter-modal-label">
        <i class="bi bi-envelope-heart"></i>
        <span>Undangan Sesi</span>
      </div>
      <button type="button" class="letter-modal-close" data-letter-close aria-label="Tutup">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="letter-modal-body">
      <div class="envelope-stage">
        <div class="envelope-speech" id="letterSpeech" aria-live="polite">
          <span class="envelope-speech-kicker">
            <i class="bi bi-stars" id="letterSpeechIcon"></i>
            <span id="letterSpeechKickerText">Salam dari undanganmu</span>
          </span>
          <p id="letterSpeechMessage">Aku sudah siap membawakan kabar baik. Klik aku untuk membuka pesanmu.</p>
          <div class="envelope-speech-actions" id="letterSpeechActions" hidden>
            <form id="letterActionForm" method="POST">
              @csrf
              <button type="submit" class="letter-start-btn" id="letterActionButton">
                <i class="bi bi-chat-dots-fill"></i>
                <span>Mulai Sesi Konseling</span>
              </button>
            </form>
          </div>
          <span class="envelope-speech-link" aria-hidden="true"></span>
        </div>
        <div class="envelope" id="letterEnvelope" tabindex="0" role="button" aria-label="Buka amplop undangan">
          <div class="envelope-aura"></div>
          <div class="envelope-shadow"></div>
          <span class="envelope-spark envelope-spark--1"></span>
          <span class="envelope-spark envelope-spark--2"></span>
          <span class="envelope-spark envelope-spark--3"></span>
          <div class="envelope-character">
            <span class="envelope-arm envelope-arm--left"></span>
            <span class="envelope-arm envelope-arm--right"></span>
            <span class="envelope-leg envelope-leg--left"></span>
            <span class="envelope-leg envelope-leg--right"></span>
            <div class="envelope-shell">
              <div class="envelope-back"></div>
              <div class="envelope-letter">
                <h4 id="letterModalTitle">Undangan Sesi Konseling</h4>
                <p id="envelopeLetterPreview">Ada pesan hangat yang siap dibuka khusus untukmu.</p>
              </div>
              <div class="envelope-front"></div>
              <div class="envelope-flap"></div>
              <div class="envelope-face" aria-hidden="true">
                <span class="envelope-brow envelope-brow--left"></span>
                <span class="envelope-brow envelope-brow--right"></span>
                <span class="envelope-eye envelope-eye--left"><span class="envelope-eye-shine"></span></span>
                <span class="envelope-eye envelope-eye--right"><span class="envelope-eye-shine"></span></span>
                <span class="envelope-mouth"><span class="envelope-mouth-inner"></span></span>
                <span class="envelope-cheek envelope-cheek--left"></span>
                <span class="envelope-cheek envelope-cheek--right"></span>
              </div>
              <div class="envelope-badge"><i class="bi bi-envelope-heart-fill"></i></div>
            </div>
          </div>
        </div>
        <div class="envelope-helper">Gerakkan kursor atau ketuk amplop untuk membuka pesanmu.</div>
      </div>
    </div>
  </div>
</div>

<div class="schedule-guard-modal" id="scheduleGuardModal" aria-hidden="true">
  <div class="schedule-guard-backdrop" data-schedule-guard-close></div>
  <div class="schedule-guard-dialog" role="dialog" aria-modal="true" aria-labelledby="scheduleGuardTitle">
    <button type="button" class="schedule-guard-close" data-schedule-guard-close aria-label="Tutup">
      <i class="bi bi-x-lg"></i>
    </button>
    <div class="schedule-guard-icon">
      <i class="bi bi-clock-history"></i>
    </div>
    <h3 id="scheduleGuardTitle">Sesi Belum Dimulai</h3>
    <p id="scheduleGuardMessage">Sesi konseling online Anda akan dimulai sesuai jadwal yang ditentukan.</p>
    <button type="button" class="schedule-guard-btn" data-schedule-guard-close>Mengerti</button>
  </div>
</div>

<div class="page-in">@yield('konten')</div>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand-txt mb-2"><i class="bi bi-heart-pulse-fill me-2" style="color:var(--accent)"></i>BK Connect</div>
        <p style="font-size:.86rem;line-height:1.75;margin-bottom:1.5rem">Platform Bimbingan dan Konseling digital IT Del — mendukung kesehatan mental mahasiswa dengan layanan profesional, aman, dan mudah diakses.</p>
        <div class="footer-social">
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-whatsapp"></i></a>
          <a href="#"><i class="bi bi-youtube"></i></a>
          <a href="#"><i class="bi bi-envelope-fill"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6>Navigasi</h6>
        <a href="/">Beranda</a>
        <a href="/tentang">Tentang</a>
        <a href="/konseling">Konseling</a>
      </div>
      <div class="col-6 col-lg-3">
        <h6>Layanan</h6>
        <a href="/konseling">Konseling</a>
      </div>
        <div class="col-lg-3">
        <h6>Kontak</h6>
        <a href="mailto:bk@del.ac.id"><i class="bi bi-envelope me-2"></i>bk@del.ac.id</a>
        <a href="#"><i class="bi bi-telephone me-2"></i>(0623) 95102</a>
        <a href="#"><i class="bi bi-geo-alt me-2"></i>Sitoluama, Laguboti, Toba</a>
        <div class="mt-3 p-3" style="background:rgba(255,255,255,.07);border-radius:10px;">
          <div style="color:rgba(255,255,255,.4);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem">Jam Operasional</div>
          <div style="color:rgba(255,255,255,.8);font-size:.82rem;">Senin – Jumat: 08.00 – 17.00</div>
        </div>
      </div>
    </div>
    <div class="footer-copy">© 2024 BK Connect · Institut Teknologi Del — Pengembangan Digital Mental Health Intervention</div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('scroll',()=>{
  document.getElementById('mainNav').classList.toggle('scrolled',window.scrollY>20);
});
function toggleProfile(){
  document.getElementById('profileDropdown').classList.toggle('show');
}
document.addEventListener('click',(e)=>{
  if(!document.getElementById('profileBtn')?.contains(e.target)&&!document.getElementById('profileDropdown')?.contains(e.target)){
    document.getElementById('profileDropdown')?.classList.remove('show');
  }
});

const notifDropdownBtn = document.getElementById('notifDropdownBtn');
const notifBadge = document.getElementById('notifBadge');
let notifMarkedRead = false;
const letterModal = document.getElementById('letterModal');
const letterEnvelope = document.getElementById('letterEnvelope');
const envelopeStage = letterModal?.querySelector('.envelope-stage');
const letterSpeechActions = document.getElementById('letterSpeechActions');
const letterActionForm = document.getElementById('letterActionForm');
const letterActionButton = document.getElementById('letterActionButton');
const letterActionIcon = letterActionButton?.querySelector('i');
const letterModalTitle = document.getElementById('letterModalTitle');
const envelopeHelper = document.querySelector('.envelope-helper');
const envelopeShell = letterModal?.querySelector('.envelope-shell');
const envelopeCharacter = letterModal?.querySelector('.envelope-character');
const envelopeBadge = letterModal?.querySelector('.envelope-badge');
const envelopeBadgeIcon = envelopeBadge?.querySelector('i');
const envelopeLetterPreview = document.getElementById('envelopeLetterPreview');
const letterSpeech = document.getElementById('letterSpeech');
const letterSpeechIcon = document.getElementById('letterSpeechIcon');
const letterSpeechKickerText = document.getElementById('letterSpeechKickerText');
const letterSpeechMessage = document.getElementById('letterSpeechMessage');
const reduceMotionPreference = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
const scheduleGuardModal = document.getElementById('scheduleGuardModal');
const scheduleGuardTitle = document.getElementById('scheduleGuardTitle');
const scheduleGuardMessage = document.getElementById('scheduleGuardMessage');
let envelopeSurpriseTimer = null;
let envelopeGreetingTimer = null;

const syncBodyScrollLock = () => {
  const hasOpenModal = letterModal?.classList.contains('show') || scheduleGuardModal?.classList.contains('show');
  document.body.style.overflow = hasOpenModal ? 'hidden' : '';
};

const isReducedMotion = () => Boolean(reduceMotionPreference?.matches);

const cancelElementAnimations = (...elements) => {
  elements.forEach((element) => {
    element?.getAnimations?.().forEach((animation) => animation.cancel());
  });
};

const setEnvelopeInteractionState = ({
  tiltX = '0deg',
  tiltY = '0deg',
  lift = '0px',
  scale = '1',
} = {}) => {
  if (!letterEnvelope) {
    return;
  }

  letterEnvelope.style.setProperty('--tilt-x', tiltX);
  letterEnvelope.style.setProperty('--tilt-y', tiltY);
  letterEnvelope.style.setProperty('--lift', lift);
  letterEnvelope.style.setProperty('--shell-scale', scale);
};

const resetEnvelopeInteractionState = () => {
  setEnvelopeInteractionState();
};

const formatLetterFinalMessage = (message, isLocked) => {
  const normalizedMessage = (message || '').trim();

  if (!normalizedMessage) {
    return isLocked
      ? 'Kabar baik, sesi konseling online-mu sudah disetujui dan akan dibuka tepat saat waktunya tiba.'
      : 'Undanganmu sudah siap. Saat kamu nyaman, kamu bisa langsung masuk ke sesi konseling.';
  }

  if (!isLocked) {
    return normalizedMessage;
  }

  const simplifiedMessage = normalizedMessage
    .replace(/^Sesi konseling online Anda sudah disetujui\.\s*/i, 'Sesi online-mu sudah disetujui. ')
    .replace(/^Ruang chat baru bisa diakses pada\s*/i, 'Chat bisa dibuka pada ')
    .replace(/Ruang chat baru bisa diakses pada\s*/i, 'Chat bisa dibuka pada ');

  return `Kabar baik, ${simplifiedMessage.charAt(0).toLowerCase()}${simplifiedMessage.slice(1)}`;
};

const formatEnvelopePaperMessage = (message, isLocked) => {
  const normalizedMessage = (message || '').trim();

  if (!normalizedMessage) {
    return isLocked
      ? 'Ruang chat akan terbuka tepat sesuai jam sesi.'
      : 'Saat kamu siap, ruang chat konseling sudah menunggumu.';
  }

  if (isLocked) {
    const shortMessage = normalizedMessage
      .replace(/^Kabar baik,\s*/i, '')
      .replace(/^Sesi konseling online Anda sudah disetujui\.\s*/i, '')
      .replace(/^Ruang chat baru bisa diakses pada\s*/i, 'Chat dibuka ')
      .replace(/^ruang chat baru bisa diakses pada\s*/i, 'Chat dibuka ');

    return shortMessage;
  }

  return 'Saat kamu siap, ruang chat konseling sudah menunggumu.';
};

const resetLetterSpeechState = () => {
  envelopeStage?.classList.remove('has-speech');
  envelopeStage?.classList.remove('is-revealed');
  envelopeStage?.classList.remove('animate-copy');
  if (letterSpeechActions) {
    letterSpeechActions.hidden = true;
  }
};

const playLetterCopyAnimation = () => {
  if (!envelopeStage) {
    return;
  }

  envelopeStage.classList.remove('animate-copy');
  void envelopeStage.offsetWidth;
  envelopeStage.classList.add('animate-copy');
};

const configureLetterEnvelope = (isLocked) => {
  if (!letterModal) {
    return;
  }

  letterModal.dataset.tone = isLocked ? 'waiting' : 'ready';

  if (letterSpeechIcon) {
    letterSpeechIcon.className = `bi ${isLocked ? 'bi-hourglass-split' : 'bi-stars'}`;
  }

  if (letterSpeechKickerText) {
    letterSpeechKickerText.textContent = isLocked ? 'Aku menjaga jadwalmu' : 'Salam dari undanganmu';
  }

  if (letterSpeechMessage) {
    letterSpeechMessage.textContent = isLocked
      ? 'Aku bawa kabar baik. Klik aku untuk lihat kapan chat-mu bisa dibuka.'
      : 'Undangan sesi-mu sudah siap. Klik aku, nanti aku antarkan ke ruang chat.';
  }

  if (envelopeLetterPreview) {
    envelopeLetterPreview.textContent = isLocked
      ? 'Ruang chat akan kubuka tepat saat waktunya tiba.'
      : 'Saat kamu siap, sesi konselingmu bisa segera dimulai.';
  }

  if (envelopeBadgeIcon) {
    envelopeBadgeIcon.className = `bi ${isLocked ? 'bi-clock-history' : 'bi-envelope-heart-fill'}`;
  }

  if (letterActionIcon) {
    letterActionIcon.className = `bi ${isLocked ? 'bi-hourglass-split' : 'bi-chat-dots-fill'}`;
  }

  if (envelopeHelper) {
    envelopeHelper.textContent = isLocked
      ? 'Klik amplop untuk membuka pesan.'
      : 'Klik amplop untuk membuka undanganmu.';
  }
};

const triggerEnvelopeSurprise = () => {
  if (!letterEnvelope) {
    return;
  }

  window.clearTimeout(envelopeGreetingTimer);
  letterEnvelope.classList.remove('is-greeting');
  window.clearTimeout(envelopeSurpriseTimer);
  letterEnvelope.classList.remove('is-surprised');

  // Force restart of the one-shot animation.
  void letterEnvelope.offsetWidth;

  letterEnvelope.classList.add('is-surprised');
  envelopeSurpriseTimer = window.setTimeout(() => {
    letterEnvelope.classList.remove('is-surprised');
  }, 960);
};

const triggerEnvelopeGreeting = () => {
  if (!letterEnvelope) {
    return;
  }

  window.clearTimeout(envelopeGreetingTimer);
  letterEnvelope.classList.remove('is-greeting');

  void letterEnvelope.offsetWidth;

  letterEnvelope.classList.add('is-greeting');
  envelopeGreetingTimer = window.setTimeout(() => {
    letterEnvelope.classList.remove('is-greeting');
  }, 1240);
};

const playLetterModalEntrance = () => {
  cancelElementAnimations(envelopeCharacter, letterSpeech, envelopeBadge);

  if (isReducedMotion()) {
    return;
  }

  envelopeCharacter?.animate(
    [
      { transform: 'translate3d(0, 26px, 0) scale(.92)', opacity: .85 },
      { transform: 'translate3d(0, -8px, 0) scale(1.03)', opacity: 1, offset: .72 },
      { transform: 'translate3d(0, 0, 0) scale(1)', opacity: 1 },
    ],
    {
      duration: 680,
      easing: 'cubic-bezier(.18,.9,.22,1)',
      fill: 'both',
    }
  );

  envelopeBadge?.animate(
    [
      { transform: 'rotate(-10deg) scale(.84)' },
      { transform: 'rotate(14deg) scale(1.08)', offset: .7 },
      { transform: 'rotate(8deg) scale(1)' },
    ],
    {
      duration: 640,
      easing: 'cubic-bezier(.16,.84,.2,1)',
      fill: 'both',
    }
  );
};

const playLetterReveal = () => {
  cancelElementAnimations(envelopeCharacter, letterSpeech, envelopeBadge);

  if (isReducedMotion()) {
    return;
  }

  envelopeCharacter?.animate(
    [
      { transform: 'translate3d(0, 0, 0) scale(1)' },
      { transform: 'translate3d(0, -10px, 0) scale(1.03)', offset: .5 },
      { transform: 'translate3d(0, 0, 0) scale(1)' },
    ],
    {
      duration: 560,
      easing: 'cubic-bezier(.18,.9,.22,1)',
      fill: 'both',
    }
  );

  letterSpeech?.animate(
    [
      { transform: 'translate3d(0, 0, 0) scale(1)' },
      { transform: 'translate3d(0, -6px, 0) scale(1.02)', offset: .5 },
      { transform: 'translate3d(0, 0, 0) scale(1)' },
    ],
    {
      duration: 480,
      easing: 'ease-out',
      fill: 'both',
    }
  );

  envelopeBadge?.animate(
    [
      { transform: 'rotate(8deg) scale(1)' },
      { transform: 'rotate(-8deg) scale(1.12)', offset: .52 },
      { transform: 'rotate(10deg) scale(1)' },
    ],
    {
      duration: 520,
      easing: 'cubic-bezier(.2,.9,.2,1)',
      fill: 'both',
    }
  );
};

const openLetterModal = ({ title, message, cta, action, note, locked }) => {
  if (!letterModal || !letterEnvelope || !letterActionForm) {
    return;
  }

  const isLocked = Boolean(Number(locked));
  cancelElementAnimations(envelopeCharacter, letterSpeech, envelopeBadge);
  resetEnvelopeInteractionState();
  letterEnvelope.classList.remove('is-open');
  letterEnvelope.classList.remove('is-surprised');
  letterEnvelope.classList.remove('is-greeting');
  letterModal.dataset.locked = isLocked ? '1' : '0';
  letterModal.dataset.finalMessage = formatLetterFinalMessage(message, isLocked);
  configureLetterEnvelope(isLocked);
  resetLetterSpeechState();
  letterModalTitle.textContent = isLocked ? 'Jadwal Sesi Online' : title;
  envelopeLetterPreview.textContent = formatEnvelopePaperMessage(message, isLocked);
  letterActionForm.setAttribute('action', action);
  letterActionButton.querySelector('span').textContent = cta;
  letterActionButton.disabled = isLocked;
  letterModal.classList.add('show');
  letterModal.setAttribute('aria-hidden', 'false');
  closeScheduleGuardModal();
  syncBodyScrollLock();
  window.bootstrap?.Dropdown.getInstance(notifDropdownBtn)?.hide();
  playLetterModalEntrance();
  window.setTimeout(() => {
    letterEnvelope.focus();
  }, 60);
};

const closeLetterModal = () => {
  if (!letterModal) {
    return;
  }

  window.clearTimeout(envelopeSurpriseTimer);
  window.clearTimeout(envelopeGreetingTimer);
  letterModal.classList.remove('show');
  letterModal.setAttribute('aria-hidden', 'true');
  letterModal.removeAttribute('data-tone');
  letterModal.removeAttribute('data-final-message');
  letterEnvelope?.classList.remove('is-open');
  letterEnvelope?.classList.remove('is-surprised');
  letterEnvelope?.classList.remove('is-greeting');
  resetLetterSpeechState();
  resetEnvelopeInteractionState();
  syncBodyScrollLock();
};

const openScheduleGuardModal = ({ title, message }) => {
  if (!scheduleGuardModal || !scheduleGuardTitle || !scheduleGuardMessage) {
    return;
  }

  scheduleGuardTitle.textContent = title;
  scheduleGuardMessage.textContent = message;
  scheduleGuardModal.classList.add('show');
  scheduleGuardModal.setAttribute('aria-hidden', 'false');
  closeLetterModal();
  syncBodyScrollLock();
};

const closeScheduleGuardModal = () => {
  if (!scheduleGuardModal) {
    return;
  }

  scheduleGuardModal.classList.remove('show');
  scheduleGuardModal.setAttribute('aria-hidden', 'true');
  syncBodyScrollLock();
};

if (letterEnvelope) {
  const revealLetter = () => {
    if (!envelopeStage || !letterModal) {
      return;
    }

    const alreadyRevealed = envelopeStage.classList.contains('is-revealed');
    const isLocked = letterModal.dataset.locked === '1';

    if (alreadyRevealed) {
      triggerEnvelopeSurprise();
      playLetterReveal();
      playLetterCopyAnimation();
      return;
    }

    envelopeStage.classList.add('has-speech');
    envelopeStage.classList.add('is-revealed');
    letterEnvelope.classList.add('is-open');
    triggerEnvelopeGreeting();
    triggerEnvelopeSurprise();
    playLetterReveal();
    if (letterSpeechActions) {
      letterSpeechActions.hidden = false;
    }
    if (letterSpeechMessage) {
      letterSpeechMessage.textContent = letterModal.dataset.finalMessage || '';
    }
    if (envelopeHelper) {
      envelopeHelper.textContent = isLocked
        ? 'Jadwal sesi sudah terbuka. Tombol akan aktif saat waktunya tiba.'
        : 'Undangan sudah terbuka. Kamu bisa langsung lanjut ke ruang chat.';
    }
    playLetterCopyAnimation();
  };

  const handleSpeechClick = (event) => {
    if (event.target.closest('#letterSpeechActions')) {
      return;
    }

    revealLetter();
    letterEnvelope.focus();
  };

  const handleEnvelopePointerMove = (event) => {
    if (isReducedMotion()) {
      return;
    }

    const rect = letterEnvelope.getBoundingClientRect();
    const pointerX = ((event.clientX - rect.left) / rect.width) - .5;
    const pointerY = ((event.clientY - rect.top) / rect.height) - .5;

    setEnvelopeInteractionState({
      tiltX: `${(-pointerY * 12).toFixed(2)}deg`,
      tiltY: `${(pointerX * 18).toFixed(2)}deg`,
      lift: '-6px',
      scale: '1.01',
    });
  };

  const handleEnvelopePointerLeave = () => {
    resetEnvelopeInteractionState();
  };

  const handleEnvelopePointerDown = () => {
    if (isReducedMotion()) {
      return;
    }

    letterEnvelope.style.setProperty('--shell-scale', '.985');
    letterEnvelope.style.setProperty('--lift', '-2px');
  };

  const handleEnvelopePointerUp = () => {
    if (isReducedMotion()) {
      return;
    }

    letterEnvelope.style.setProperty('--shell-scale', '1');
    letterEnvelope.style.setProperty('--lift', '0px');
  };

  letterEnvelope.addEventListener('click', revealLetter);
  letterEnvelope.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      revealLetter();
    }
  });
  letterEnvelope.addEventListener('pointermove', handleEnvelopePointerMove);
  letterEnvelope.addEventListener('pointerleave', handleEnvelopePointerLeave);
  letterEnvelope.addEventListener('pointercancel', handleEnvelopePointerLeave);
  letterEnvelope.addEventListener('pointerdown', handleEnvelopePointerDown);
  letterEnvelope.addEventListener('pointerup', handleEnvelopePointerUp);
  letterSpeech?.addEventListener('click', handleSpeechClick);
}

if (letterActionForm) {
  letterActionForm.addEventListener('submit', (event) => {
    if (letterActionButton?.disabled) {
      event.preventDefault();
    }
  });
}

document.querySelectorAll('[data-letter-close]').forEach((element) => {
  element.addEventListener('click', closeLetterModal);
});

document.querySelectorAll('.notif-item-trigger').forEach((button) => {
  button.addEventListener('click', () => {
    openLetterModal({
      title: button.dataset.letterTitle || 'Undangan Sesi Konseling',
      message: button.dataset.letterMessage || 'Saat Anda siap, mari mulai sesi konseling.',
      cta: button.dataset.letterCta || 'Mulai Sesi Konseling',
      action: button.dataset.letterAction || "{{ route('mahasiswa.chat.start') }}",
      note: button.dataset.letterNote || 'Saat siap, Anda bisa langsung masuk ke ruang chat konseling.',
      locked: button.dataset.letterLocked || '0',
    });
  });
});

document.querySelectorAll('[data-chat-guard="true"]').forEach((link) => {
  link.addEventListener('click', (event) => {
    event.preventDefault();
    openScheduleGuardModal({
      title: link.dataset.chatGuardTitle || 'Sesi Belum Dimulai',
      message: link.dataset.chatGuardMessage || 'Sesi konseling online Anda belum dapat diakses saat ini.',
    });
  });
});

document.querySelectorAll('[data-schedule-guard-close]').forEach((element) => {
  element.addEventListener('click', closeScheduleGuardModal);
});

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    closeLetterModal();
    closeScheduleGuardModal();
  }
});

if (notifDropdownBtn) {
  notifDropdownBtn.addEventListener('shown.bs.dropdown', async () => {
    if (notifMarkedRead || !notifBadge || notifBadge.classList.contains('d-none')) {
      return;
    }

    notifMarkedRead = true;

    try {
      const response = await fetch("{{ route('notifikasi.baca') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
      });

      if (response.ok) {
        notifBadge.classList.add('d-none');
      } else {
        notifMarkedRead = false;
      }
    } catch (error) {
      notifMarkedRead = false;
    }
  });
}
</script>
@stack('scripts')
</body>
</html>