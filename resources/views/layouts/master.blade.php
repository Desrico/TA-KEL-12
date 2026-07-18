<doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Campus Care - IT Del Mental Health</title>
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
    padding: 0;
    border-radius: 999px;
    border: none;
    background: transparent;
    color: var(--text-mid);
    text-decoration: none;
    cursor: pointer;
    transition: all .2s ease;
    z-index: 1001;
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
    right: 0 !important;
    left: auto !important;
    transform: none !important;
    border: 1px solid var(--border) !important;
    border-radius: 14px;
    box-shadow: var(--shadow-md);
    padding: .4rem 0;
    max-height: min(70vh, 520px);
    overflow-x: hidden;
    overflow-y: auto;
    overscroll-behavior: contain;
    background: var(--white);
    z-index: 2051;
    scrollbar-width: thin;
    scrollbar-color: rgba(6, 95, 70, .35) transparent;
  }

  .notif-dropdown.show {
    display: block;
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
    padding: .8rem 1rem;
    text-decoration: none;
    border: none;
    border-bottom: 1px solid rgba(221, 239, 231, .7);
    text-align: left;
  }

  .notif-item:last-child {
    border-bottom: none;
  }

  .notif-item:hover {
    background: #F8FFFB;
  }

  .notif-item p {
    margin: 0;
    font-size: .84rem;
    color: var(--text-dark);
    line-height: 1.45;
    text-align: left;
  }

  .notif-unread {
    background: #fbfffd;
  }

  .notif-unread p {
    font-weight: 700;
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

  .group-consent-modal {
    position: fixed;
    inset: 0;
    z-index: 2100;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, 0.32);
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease;
  }

  .group-consent-modal.show {
    opacity: 1;
    pointer-events: auto;
  }

  .group-consent-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, .35);
  }

  .group-consent-dialog {
    position: relative;
    width: min(760px, calc(100vw - 2rem));
    max-height: calc(100vh - 2rem);
    overflow: hidden;
    border-radius: 18px;
    background: #ffffff;
    border: 1px solid rgba(209, 250, 229, 0.95);
    box-shadow: 0 32px 90px rgba(15, 23, 42, .16);
    z-index: 1;
  }

  .group-consent-body {
    padding: .78rem 1rem 1rem;
    overflow: visible;
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(260px, .9fr);
    grid-template-areas:
      "copy copy"
      "info rules"
      "form form";
    gap: .68rem;
  }

  .group-consent-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: .78rem 1rem .5rem;
    background: linear-gradient(135deg, #f8fffb 0%, #ecfdf5 100%);
  }

  .group-consent-label {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    color: #047857;
    font-size: .78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
    width: fit-content;
    padding: .42rem .72rem;
    border-radius: 999px;
    background: #ecfdf5;
  }

  .group-consent-close {
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 999px;
    background: #f8faf9;
    color: #065f46;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 14px 34px rgba(15, 23, 42, .08);
    cursor: pointer;
  }

  .group-consent-copy h1 {
    margin: 0;
    font-size: clamp(1.18rem, 1.8vw, 1.48rem);
    font-weight: 900;
    color: #064e3b;
    line-height: 1.15;
  }

  .group-consent-copy p {
    margin: .42rem 0 0;
    color: #475569;
    line-height: 1.42;
    max-width: 690px;
    font-size: .84rem;
  }

  .group-consent-copy {
    grid-area: copy;
  }

  .group-consent-info {
    grid-area: info;
    margin: 0;
    padding: .75rem;
    border-radius: 14px;
    background: #fbfffd;
    border: 1px solid #dbece3;
  }

  .group-consent-info h3 {
    margin: 0 0 .6rem;
    color: #064e3b;
    font-size: .92rem;
    font-weight: 900;
  }

  .group-consent-info-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .65rem;
  }

  .group-consent-info-grid > div {
    min-width: 0;
  }

  .group-consent-info strong {
    display: block;
    margin-bottom: .45rem;
    color: #047857;
    font-size: .72rem;
    line-height: 1.35;
    font-weight: 900;
    text-transform: uppercase;
  }

  .group-consent-info p {
    margin: 0;
    color: #334155;
    line-height: 1.45;
    font-size: .8rem;
    font-weight: 800;
  }

  .group-consent-rules-card {
    grid-area: rules;
    border-radius: 14px;
    padding: .75rem .88rem;
    background: #f8fffb;
    border: 1px solid #dbece3;
  }

  .group-consent-rules-card h3 {
    margin: 0 0 .55rem;
    font-size: .92rem;
    font-weight: 900;
    color: #064e3b;
  }

  .group-consent-rules-card ul {
    margin: 0;
    padding-left: 1.25rem;
    color: #475569;
    line-height: 1.38;
    font-size: .8rem;
  }

  .group-consent-rules-card li + li {
    margin-top: .22rem;
  }

  .group-consent-form {
    grid-area: form;
  }

  .group-consent-checkbox {
    margin-top: 0;
  }

  .group-consent-checkbox label {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: .9rem;
    align-items: flex-start;
    padding: .64rem .8rem;
    border-radius: 12px;
    border: 1px solid #d1fae5;
    background: #f8faf9;
    color: #0f172a;
    font-size: .82rem;
    line-height: 1.38;
  }

  .group-consent-checkbox input {
    width: 19px;
    height: 19px;
    margin-top: .15rem;
    accent-color: #059669;
  }

  .group-consent-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .8rem;
    margin-top: .65rem;
  }

  .group-consent-submit {
    border-radius: 13px;
    min-height: 40px;
    padding: .62rem 1rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
  }

  .group-consent-submit {
    border: none;
    color: #ffffff;
    background: linear-gradient(135deg, #065f46, #10b981);
  }

  .group-consent-submit:disabled {
    opacity: .5;
    cursor: not-allowed;
  }

  .notif-tag {
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
    text-align: left;
  }

  @media (max-width: 860px) {
    .group-consent-modal {
      align-items: flex-start;
      padding: .85rem;
    }

    .group-consent-dialog {
      width: 100%;
      max-height: calc(100vh - 1.7rem);
      overflow-y: auto;
      border-radius: 18px;
    }

    .group-consent-body {
      display: block;
      padding: .95rem;
    }

    .group-consent-info {
      grid-template-columns: 1fr;
      margin: .85rem 0;
    }

    .group-consent-info-grid {
      grid-template-columns: 1fr;
    }

    .group-consent-rules-card {
      margin-bottom: .85rem;
    }
  }

  @media (max-height: 720px) {
    .group-consent-modal {
      align-items: flex-start;
      padding: .7rem;
    }

    .group-consent-dialog {
      max-height: calc(100vh - 1.4rem);
      overflow-y: auto;
    }

    .group-consent-head {
      padding: .65rem .85rem .45rem;
    }

    .group-consent-body {
      padding: .62rem .85rem .85rem;
      gap: .55rem;
    }
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

  .notif-item.notif-read {
    opacity: 0.72;
    background: #ffffff;
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

  .footer-logo-img {
    width: 78px;
    height: 78px;
    object-fit: contain;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.08);
    padding: 8px;
    flex-shrink: 0;
}

.footer-brand {
    align-items: center;
    gap: 18px !important;
}

.footer-brand-txt {
    font-size: 1.55rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}

.footer-brand-subtitle {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.62);
    letter-spacing: 0.08em;
    margin-top: 6px;
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
  width: 250px;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 18px 45px rgba(6, 78, 59, .16);
  border: 1px solid #DCEBE4;
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
  padding: .85rem .9rem;
  border-bottom: 1px solid #EAF2EE;
  display: flex;
  align-items: center;
  gap: .75rem;
  background: linear-gradient(135deg, #F6FFFA 0%, #FFFFFF 72%);
}

.pd-avatar {
  width: 40px;
  height: 40px;
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

.profile-fallback {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #A7F3D0;
    color: #064E3B;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 800;
}

.pd-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #D1FAE5;
}

.pd-avatar-fallback {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #A7F3D0;
    color: #064E3B;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    font-weight: 800;
}

.pd-info {
    min-width: 0;
    flex: 1;
}

.pd-name {
    max-width: 170px;
    white-space: normal;
    word-break: break-word;
}

.pd-nim {
    max-width: 190px;
    white-space: normal;
    word-break: break-word;
}

.pd-name {
  font-weight: 700;
  font-size: .88rem;
  color: var(--text-dark);
  line-height: 1.25;
  margin-bottom: .3rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.pd-role {
  display: inline-flex;
  align-items: center;
  min-height: 22px;
  padding: .18rem .5rem;
  border-radius: 999px;
  background: #E5F8EF;
  color: var(--primary);
  font-size: .66rem;
  font-weight: 800;
  line-height: 1;
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
  min-height: 48px;
  padding: .7rem .9rem;
  font-size: .86rem;
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

.pd-item .pd-item-arrow {
  width: auto;
  margin-left: auto;
  color: #94A3B8;
  font-size: .78rem;
  transition: transform .15s ease, color .15s ease;
}

.pd-item:hover .pd-item-arrow {
  color: var(--primary);
  transform: translateX(2px);
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
  padding: 3.25rem 0 1.75rem;
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
  margin-top: 1.6rem;
  padding-top: 1.25rem;
  font-size: .78rem;
  color: rgba(255,255,255,.55);
  text-align: center;
}

.footer-summary {
  max-width: 760px;
  font-size: .88rem;
  line-height: 1.75;
  margin: 0 0 1.25rem;
}

.footer-main-grid {
  margin-top: 0;
}

.footer-contact-item {
  display: flex;
  align-items: flex-start;
  gap: .65rem;
  margin-bottom: .65rem;
  color: rgba(255,255,255,.78);
  font-size: .88rem;
  line-height: 1.55;
}

.footer-contact-item i {
  color: #A7F3D0;
  margin-top: .12rem;
  flex: 0 0 auto;
}

.footer-hours {
  margin-top: 1rem;
  color: rgba(255,255,255,.78);
  font-size: .84rem;
  line-height: 1.6;
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

.mobile-menu-toggle {
  width: 44px;
  height: 44px;
  border: 2px solid var(--primary) !important;
  border-radius: 12px;
  background: var(--white) !important;
  display: none;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 0 !important;
  box-shadow: 0 8px 20px rgba(6, 78, 59, 0.08);
}

.mobile-menu-toggle span {
  width: 22px;
  height: 3px;
  background: var(--primary);
  border-radius: 999px;
  display: block;
  transition: transform .22s ease, opacity .18s ease;
}

.mobile-menu-toggle[aria-expanded="true"] span:nth-child(1) {
  transform: translateY(8px) rotate(45deg);
}

.mobile-menu-toggle[aria-expanded="true"] span:nth-child(2) {
  opacity: 0;
}

.mobile-menu-toggle[aria-expanded="true"] span:nth-child(3) {
  transform: translateY(-8px) rotate(-45deg);
}

.mobile-menu-toggle:focus {
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
}

/* MOBILE */
@media (max-width: 991.98px) {
  .navbar-main {
    padding: .6rem 0;
  }

  .navbar-main .container {
    position: relative;
    padding-inline: 18px;
  }

  .mobile-menu-toggle {
    display: flex;
  }

  #navMain {
    position: absolute;
    top: calc(100% + 10px);
    left: 18px;
    right: 18px;
    background: rgba(255, 255, 255, .98);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: .65rem;
    box-shadow: 0 22px 55px rgba(6, 78, 59, 0.18);
    z-index: 1200;
    max-height: calc(100vh - 100px);
    max-height: calc(100dvh - 100px);
    overflow-x: hidden;
    overflow-y: auto;
    overscroll-behavior: contain;
  }

  #navMain:not(.show) {
    display: none !important;
  }

  #navMain.show {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr);
    grid-auto-flow: row dense;
    column-gap: .65rem;
    animation: mobileMenuIn .2s ease-out;
  }

  #navMain.show::after {
    content: "";
    grid-column: 1 / -1;
    order: 1;
    height: 1px;
    margin: .45rem .15rem .15rem;
    background: var(--border);
  }

  @keyframes mobileMenuIn {
    from { opacity: 0; transform: translateY(-8px) scale(.985); }
    to { opacity: 1; transform: translateY(0) scale(1); }
  }

  #navMain .navbar-nav {
    display: contents;
    padding-top: 0;
    align-items: flex-start !important;
    gap: .2rem !important;
  }

  #navMain .nav-item {
    width: 100%;
  }

  #navMain .navbar-nav > .nav-item:not(.dropdown) {
    grid-column: 1 / -1;
    order: 0;
  }

  #navMain .nav-link-custom {
    display: flex;
    align-items: center;
    gap: .8rem;
    width: 100%;
    min-height: 46px;
    padding: .7rem .85rem !important;
    border-radius: 13px;
    font-size: .9rem;
    line-height: 1.25;
    overflow: hidden;
  }

  #navMain .nav-link-custom span {
    min-width: 0;
    overflow-wrap: anywhere;
  }

  #navMain .nav-link-custom.active {
    box-shadow: inset 3px 0 0 var(--primary-500);
  }

  #navMain .navbar-nav > .dropdown {
    display: block;
    position: absolute;
    top: -70px;
    right: 60px;
    width: 44px;
    height: 44px;
    z-index: 1250;
  }

  #navMain .notif-link {
    width: 44px;
    height: 44px;
    margin: 0;
    background: var(--surface-soft);
    border: 1px solid var(--border);
  }

  .navbar-main .navbar-notif-mobile {
    position: relative;
    display: block;
    width: 44px;
    margin: 0 .55rem 0 auto !important;
    list-style: none;
  }

  .navbar-main .navbar-notif-mobile .notif-link {
    width: 44px;
    height: 44px;
    margin: 0;
    background: rgba(255, 255, 255, .82);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
  }

  .navbar-main .navbar-notif-mobile .notif-dropdown {
    position: fixed !important;
    top: 78px !important;
    left: 18px !important;
    right: 18px !important;
    width: auto !important;
    min-width: 0 !important;
    max-height: calc(100vh - 98px);
    margin: 0;
    overflow-x: hidden;
    overflow-y: auto;
  }

  #navMain .d-flex.align-items-center.ms-lg-3 {
    display: contents !important;
  }

  #navMain .btn-login-custom {
    grid-column: 1 / -1;
    order: 2;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 48px;
    margin: .2rem 0 0 !important;
    padding: .75rem 1rem;
    border: 1px solid var(--primary);
    border-radius: 12px;
    background: transparent;
    color: var(--primary);
    font-size: .92rem;
    font-weight: 600;
    box-shadow: none;
  }

  #navMain .btn-login-custom:hover,
  #navMain .btn-login-custom:focus {
    background: var(--primary-soft);
    color: var(--primary);
    border-color: var(--primary);
  }

  #navMain .profile-wrap {
    display: contents;
  }

  #navMain .profile-btn {
    grid-column: 1 / -1;
    grid-row: auto;
    order: 2;
    width: 100%;
    height: 52px;
    margin-top: .2rem;
    padding: 4px 12px 4px 4px;
    border-radius: 14px;
    justify-content: flex-start;
    gap: .7rem;
    border-color: var(--white);
    box-shadow: 0 0 0 1px var(--border), var(--shadow-sm);
    overflow: visible;
  }

  #navMain .profile-btn .profile-img,
  #navMain .profile-btn .profile-fallback {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    border-radius: 50%;
  }

  #navMain .profile-btn .online-dot {
    left: 34px;
    right: auto;
    bottom: 5px;
  }

  .mobile-profile-name {
    display: block;
    min-width: 0;
    color: var(--text-dark);
    font-size: .88rem;
    font-weight: 700;
    line-height: 1.25;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  #navMain .profile-dropdown {
    grid-column: 1 / -1;
    order: 3;
    position: static !important;
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
    margin-top: .65rem;
    border-radius: 16px;
    opacity: 0;
    display: none;
    pointer-events: none;
    transform: none !important;
    box-shadow: 0 12px 30px rgba(6, 78, 59, 0.12);
  }

  #navMain .notif-dropdown {
    grid-column: 1 / -1;
    order: 3;
    position: static !important;
    width: 100% !important;
    min-width: 0 !important;
    max-height: none;
    margin-top: .65rem;
    transform: none !important;
    overflow: visible;
  }

  #navMain .navbar-nav > .dropdown .notif-dropdown {
    position: fixed !important;
    top: 78px !important;
    left: 18px !important;
    right: 18px !important;
    width: auto !important;
    max-height: calc(100vh - 98px);
    overflow-x: hidden;
    overflow-y: auto;
  }

  #navMain .profile-dropdown.show {
    display: block;
    opacity: 1;
    pointer-events: auto;
  }

  #navMain .pd-header {
    display: none;
  }

  #navMain .pd-info {
    min-width: 0;
    flex: 1;
  }

  #navMain .pd-name {
    max-width: 100% !important;
    white-space: normal;
    word-break: normal !important;
    overflow-wrap: anywhere;
    line-height: 1.25;
  }

  #navMain .pd-nim {
    max-width: 100% !important;
    white-space: normal;
    word-break: normal !important;
    overflow-wrap: anywhere;
    line-height: 1.35;
  }
}

@media (max-width: 575.98px) {
  .navbar-main .container {
    padding-inline: 12px;
  }

  #navMain {
    left: 12px;
    right: 12px;
    top: calc(100% + 8px);
    max-height: calc(100vh - 88px);
    max-height: calc(100dvh - 88px);
    padding: .5rem;
    border-radius: 16px;
  }

  #navMain .nav-link-custom {
    min-height: 44px;
    padding: .65rem .75rem !important;
    border-radius: 11px;
    font-size: .86rem;
  }

  #navMain .profile-btn {
    min-width: 0;
    height: 50px;
  }

  .navbar-main .navbar-notif-mobile {
    margin-right: .45rem !important;
  }

  .navbar-main .navbar-notif-mobile .notif-dropdown {
    left: 12px !important;
    right: 12px !important;
  }
}

/* DESKTOP */
@media (min-width: 992px) {
  .mobile-profile-name {
    display: none !important;
  }

  .mobile-menu-toggle {
    display: none !important;
  }

  #navMain {
    position: static !important;
    display: flex !important;
    width: auto !important;
    max-height: none !important;
    overflow: visible !important;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    margin-top: 0 !important;
    box-shadow: none !important;
  }

  #navMain.collapse:not(.show) {
    display: flex !important;
  }

  #navMain .navbar-nav {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 1.2rem !important;
    padding-top: 0 !important;
  }

  #navMain .nav-item {
    width: auto !important;
  }

  #navMain .nav-link-custom {
    width: auto !important;
    white-space: nowrap !important;
    padding: .75rem 1rem !important;
  }

  #navMain .notif-link {
    margin: 0 !important;
  }

  #navMain .profile-wrap {
    position: relative !important;
    width: auto !important;
    padding-left: 0 !important;
  }

  #navMain .profile-dropdown {
    position: absolute !important;
    width: 250px !important;
    right: 0 !important;
    left: auto !important;
  }
}

@media (min-width: 992px) and (max-width: 1199.98px) {
  #navMain .navbar-nav {
    gap: .2rem !important;
  }

  #navMain .nav-link-custom {
    padding: .65rem .55rem !important;
    font-size: .84rem;
  }

  #navMain .d-flex.align-items-center.ms-lg-3 {
    margin-left: .5rem !important;
  }
}

/* RESPONSIVE FOUNDATION — shared by every student-facing page */
.page-in,
.page-in > *,
.page-in .row > * {
  min-width: 0;
}

.page-in {
  width: 100%;
  max-width: 100%;
  overflow-x: clip;
}

.page-in :where(img, video, canvas, svg) {
  max-width: 100%;
}

.page-in :where(img, video) {
  height: auto;
}

.page-in :where(iframe) {
  max-width: 100%;
}

.page-in :where(h1, h2, h3, h4, h5, h6, p, a, span, label, td, th) {
  overflow-wrap: break-word;
}

.page-in :where(input, select, textarea, button) {
  max-width: 100%;
}

.page-in :where(.card, .modal-content, form, fieldset) {
  min-width: 0;
}

@media (max-width: 991.98px) {
  .page-in .container,
  .page-in .container-sm,
  .page-in .container-md,
  .page-in .container-lg,
  .page-in .container-xl {
    padding-inline: clamp(1rem, 4vw, 1.5rem);
  }

  .page-in :where(.modal-dialog) {
    width: auto;
    max-width: calc(100vw - 2rem);
    margin: 1rem auto;
  }

  .page-in :where(.table-responsive) {
    max-width: 100%;
    overscroll-behavior-inline: contain;
    -webkit-overflow-scrolling: touch;
  }

  .page-in :where([class*="actions"], [class*="action-buttons"], .modal-footer) {
    flex-wrap: wrap;
  }
}

@media (max-width: 767.98px) {
  .page-in {
    --mobile-page-gutter: clamp(.9rem, 4vw, 1.25rem);
  }

  .page-in .container,
  .page-in .container-fluid,
  .page-in .container-sm,
  .page-in .container-md,
  .page-in .container-lg,
  .page-in .container-xl {
    padding-inline: var(--mobile-page-gutter);
  }

  .page-in :where(input:not([type="checkbox"]):not([type="radio"]), select, textarea) {
    width: 100%;
    min-height: 44px;
    font-size: 16px;
  }

  .page-in textarea {
    line-height: 1.55;
  }

  .page-in :where(.modal-body, .modal-header, .modal-footer) {
    padding-inline: clamp(1rem, 4vw, 1.25rem);
  }

  .page-in :where(.modal-footer) > :where(button, .btn, a.btn) {
    flex: 1 1 140px;
    min-height: 44px;
    margin: 0;
  }

  .page-in :where(.btn, button):not(.btn-close):not(.star-btn):not([class*="icon"]):not([class*="close"]) {
    min-height: 42px;
  }

  .page-in :where(h1) {
    font-size: clamp(2rem, 10vw, 3rem);
    line-height: 1.08;
  }

  .page-in :where(h2) {
    font-size: clamp(1.45rem, 7vw, 2.15rem);
    line-height: 1.15;
  }

  .page-in :where(.feedback-textarea, .fm-textarea) {
    min-height: 130px;
    resize: vertical;
    background: #fff;
    border-color: #b8d5c7;
  }
}

@media (max-width: 380px) {
  .page-in {
    --mobile-page-gutter: .8rem;
  }

  .page-in :where(.modal-footer) > :where(button, .btn, a.btn) {
    flex-basis: 100%;
    width: 100%;
  }
}

</style>
  @vite(['resources/js/app.js'])
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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

                $pesanBaru = 'Penjadwalan ' . $jenisKonseling . ' pada ' . $dateTime->translatedFormat('j F Y') . ' pukul ' . $dateTime->format('H:i') . ' telah disetujui oleh konselor.';
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

              $unreadNotif = Auth::user()
                ->notifikasi()
                ->where('status', 'belum')
                ->count();

              $notificationViewer = Auth::user();

              $notifItems = Auth::user()
                  ->notifikasi()
                  ->latest()
                  ->take(10)
                  ->get();

              $jadwalItems = \App\Models\JadwalKonseling::where('mahasiswa_id', $mahasiswaId)
                ->get(['id', 'tanggal', 'waktu', 'jenis']);

              $jadwalById = $jadwalItems->keyBy('id');
              $jadwalByApprovedMessage = $jadwalItems->flatMap(function ($jadwalItem) {
                  $jenisKonseling = strtolower(trim((string) $jadwalItem->jenis)) === 'offline' ? 'offline' : 'online';
                  $dateTime = $jadwalItem->scheduledAt();

                  if (! $dateTime) {
                      return [];
                  }

                  $tanggalWaktu = $dateTime->translatedFormat('j F Y') . ' pukul ' . $dateTime->format('H:i');

                  return [
                      'Konseling ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' telah disetujui oleh konselor.' => $jadwalItem,
                      'Penjadwalan ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' telah disetujui oleh konselor.' => $jadwalItem,
                      'konseling ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' telah disetujui oleh konselor.' => $jadwalItem,
                      'penjadwalan ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' telah disetujui oleh konselor.' => $jadwalItem,
                  ];
              });

              $notifItems = $notifItems->map(function ($notif) use ($jadwalById, $jadwalByApprovedMessage, $chatGuardBlocked, $nextOnlineChatSchedule, $notificationViewer) {
                $pesan = $notif->pesan;
                $notif->cta_target = $notif->cta_target ?: route('riwayat');
                $notif->cta_label = $notif->cta_label ?: null;
                $notif->is_letter_prompt = false;
                $notif->prompt_title = null;
                $notif->prompt_message = null;
                $notif->prompt_cta = null;
                $notif->prompt_note = null;
                $notif->prompt_locked = false;
                $notif->is_group_invite = filled($notif->cta_target) && str_contains((string) $notif->cta_target, '/group-chat/undangan/');
                if ($notif->is_group_invite) {
                  $invitePath = parse_url((string) $notif->cta_target, PHP_URL_PATH) ?: '';
                  $inviteToken = basename($invitePath);
                  $inviteRoom = $inviteToken
                    ? \App\Models\GroupChatRoom::query()
                        ->where('invite_token', $inviteToken)
                        ->where('visibility', \App\Models\GroupChatRoom::VISIBILITY_PRIVATE)
                        ->where('is_active', true)
                        ->first()
                    : null;
                  $inviteMembership = $inviteRoom
                    ? \App\Support\GroupChatSupport::resolveRoomMember($notificationViewer, $inviteRoom)
                    : null;
                  $alreadyJoinedInvite = $inviteRoom && $inviteMembership && (
                    ! \App\Support\GroupChatSupport::supportsMembershipStatus()
                    || $inviteMembership->membership_status === \App\Models\GroupChatMember::STATUS_ACTIVE
                  );

                  // Jika consent sudah pernah disetujui, notifikasi tidak lagi membuka modal consent.
                  if ($alreadyJoinedInvite) {
                    $notif->is_group_invite = false;
                    $notif->cta_target = route('mahasiswa.group-chat.room', ['group' => $inviteRoom->id]);
                    $notif->cta_label = null;
                    $notif->pesan = 'Anda telah diundang ke grup "' . $inviteRoom->title . '".';

                    return $notif;
                  }

                  $notif->cta_label = 'Buka Undangan Grup';
                  $notif->group_invite_group_name = $inviteRoom?->title ?: 'Grup Privat';
                  if (preg_match('/Anda telah diundang ke grup "(.+?)"/i', $pesan, $matches)) {
                      $notif->group_invite_group_name = $matches[1];
                  }
                  $notif->group_invite_title = 'Undangan ke grup privat "' . $notif->group_invite_group_name . '"';
                  $notif->group_invite_message = 'Pastikan Anda memahami alasan undangan, tujuan grup, dan aturan komunikasi sebelum bergabung.';
                  $notif->group_invite_inviter_name = 'Konselor';
                  $notif->group_invite_reason = filled($inviteRoom?->description)
                    ? $inviteRoom->description
                    : 'Grup privat ini relevan untuk pendampingan dan diskusi konseling.';
                  $notif->group_invite_token = $inviteToken;
                  $notif->group_invite_hidden_fields = json_encode(['invite_token' => $inviteToken]);
                }
                $notifText = strtolower((string) $pesan);
                $matchedApprovedJadwal = $jadwalByApprovedMessage->get($pesan);

                if (preg_match('/^(Booking|Jadwal|Penjadwalan)\s+#(\d+)\s+/i', $pesan, $match)) {
                  $jadwal = $jadwalById->get((int) $match[2]);

                  if ($jadwal) {
                    $jenisKonseling = strtolower(trim((string) $jadwal->jenis)) === 'offline' ? 'offline' : 'online';
                    $dateTime = $jadwal->scheduledAt();
                    $tanggalWaktu = $jadwal->scheduledStartLabel();

                    if (str_contains(strtolower($pesan), 'telah disetujui oleh konselor')) {
                      $notif->pesan = 'Penjadwalan ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' telah disetujui oleh konselor.';

                      if ($jenisKonseling === 'online') {
                        $nowJakarta = \Carbon\Carbon::now('Asia/Jakarta');

                        $isLockedBySchedule = $dateTime
                            ? $nowJakarta->lt($dateTime)
                            : true;

                        $isExpiredBySchedule = $dateTime
                            ? $nowJakarta->gte($dateTime->copy()->addHours(24))
                            : false;

                        if ($isExpiredBySchedule) {
                            // Jika sesi sudah lewat 24 jam, jangan tampilkan "Buka Undangan".
                            // Notifikasi hanya menjadi notifikasi biasa yang mengarah ke halaman chat.
                            $notif->cta_target = route('mahasiswa.chat', ['jadwal_id' => $jadwal->id]);
                            $notif->cta_label = null;
                            $notif->is_letter_prompt = false;
                            $notif->prompt_locked = false;
                        } else {
                            // Jika sesi belum lewat 24 jam, undangan masih boleh tampil.
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
                    }
                    } elseif (str_contains(strtolower($pesan), 'menunggu persetujuan konselor')) {
                      $notif->pesan = 'Pengajuan Penjadwalan ' . $jenisKonseling . ' pada ' . $tanggalWaktu . ' berhasil dibuat dan menunggu persetujuan konselor.';
                    }
                  }
                }

                // ID jadwal adalah data internal dan tidak perlu terlihat pada notifikasi mahasiswa.
                $notif->pesan = preg_replace(
                  '/^(Booking|Jadwal|Penjadwalan)\s+#\d+\s+/i',
                  '$1 ',
                  (string) $notif->pesan
                );

                if (
                  ! $notif->is_letter_prompt &&
                  (
                      str_contains($notifText, 'konseling online') ||
                      str_contains($notifText, 'penjadwalan online')
                  ) &&
                  str_contains($notifText, 'telah disetujui oleh konselor')
                ) {
                  $matchedDateTime = null;
                  $matchedTanggalWaktu = null;

                  if ($matchedApprovedJadwal) {
                    $matchedDateTime = $matchedApprovedJadwal->scheduledAt();
                    $matchedTanggalWaktu = $matchedApprovedJadwal->scheduledStartLabel();
                    $notif->pesan = 'Penjadwalan online pada ' . $matchedTanggalWaktu . ' telah disetujui oleh konselor.';
                  } elseif (preg_match('/(?:Konseling|Penjadwalan)\s+online\s+pada\s+(.+?)\s+telah\s+disetujui\s+oleh\s+konselor\./iu', $pesan, $matches)) {
                    $matchedTanggalWaktu = trim($matches[1]);
                  }

                  $nowJakarta = \Carbon\Carbon::now('Asia/Jakarta');

                  if (! $matchedApprovedJadwal || ! $matchedDateTime) {
                      $notif->cta_label = null;
                      $notif->is_letter_prompt = false;
                      $notif->prompt_locked = false;

                      $notif->pesan = preg_replace(
                          '/\bPengajuan\s+konseling\s+(online|offline)\b/i',
                          'Pengajuan penjadwalan $1',
                          $notif->pesan
                      );

                      $notif->pesan = preg_replace(
                          '/\bKonseling\s+(online|offline)\b/i',
                          'Penjadwalan $1',
                          $notif->pesan
                      );

                      return $notif;
                  }

                  $isLockedBySpecificSchedule = ! $matchedApprovedJadwal->hasScheduledTimeStarted();

                  $isExpiredBySpecificSchedule = $nowJakarta->gte($matchedDateTime->copy()->addHours(24));

                  if ($isExpiredBySpecificSchedule) {
                      $notif->cta_target = route('mahasiswa.chat', ['jadwal_id' => $matchedApprovedJadwal->id]);
                      $notif->cta_label = null;
                      $notif->is_letter_prompt = false;
                      $notif->prompt_locked = false;

                    $notif->pesan = preg_replace(
                        '/\bPengajuan\s+konseling\s+(online|offline)\b/i',
                        'Pengajuan penjadwalan $1',
                        $notif->pesan
                    );

                    $notif->pesan = preg_replace(
                        '/\bKonseling\s+(online|offline)\b/i',
                        'Penjadwalan $1',
                        $notif->pesan
                    );

                      return $notif;
                  }

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

    <button
      class="navbar-toggler mobile-menu-toggle"
      type="button"
      id="mobileMenuToggle"
      aria-label="Buka menu navigasi"
      aria-controls="navMain"
      aria-expanded="false"
      onclick="toggleMobileMenu(this)"
    >
      <span></span>
      <span></span>
      <span></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link nav-link-custom {{ request()->is('/') ? 'active' : '' }}" href="/"><span>Beranda</span></a></li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom {{ request()->is('edukasi-mental') ? 'active' : '' }}" href="/edukasi-mental">
            <span>Ruang Edukasi</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-link-custom {{ request()->is('konseling*') ? 'active' : '' }}" href="/konseling">
            <span>Penjadwalan</span>
          </a>
        </li>
        @auth
        @if(Auth::user()->role === 'mahasiswa')
        <li class="nav-item">
          <a
            class="nav-link nav-link-custom {{ request()->is('riwayat*') ? 'active' : '' }}"
            href="{{ route('riwayat') }}"
          >
            <span>Riwayat</span>
          </a>
        </li>
        <li class="nav-item">
          <a
            class="nav-link nav-link-custom {{ request()->routeIs('mahasiswa.chat*') ? 'active' : '' }}"
            href="{{ route('mahasiswa.chat') }}"
          >
            <span>Chat</span>
          </a>
        </li>
        <li class="nav-item">
          <a
            class="nav-link nav-link-custom {{ request()->routeIs('mahasiswa.group-chat*') ? 'active' : '' }}"
            href="{{ route('mahasiswa.group-chat') }}"
          >
            <span>Grup Chat</span>
          </a>
        </li>
        @endif
        @endauth

        @auth
        <li class="nav-item dropdown ms-1">
          <button type="button" class="notif-link" id="notifDropdownBtn" aria-expanded="false" title="Notifikasi" aria-label="Notifikasi">
            <i class="bi bi-bell" style="font-size:1rem;"></i>
            <span id="notifBadge" class="notif-badge {{ $unreadNotif > 0 ? '' : 'd-none' }}">{{ $unreadNotif > 9 ? '9+' : $unreadNotif }}</span>
          </button>
          <div class="dropdown-menu dropdown-menu-end notif-dropdown" aria-labelledby="notifDropdownBtn">
            <div class="notif-header">Notifikasi</div>
            @forelse($notifItems as $notif)
              @if(!empty($notif->is_group_invite))
              <button
                type="button"
                class="notif-item notif-readable notif-group-invite {{ $notif->status === 'dibaca' ? 'notif-read' : 'notif-unread' }}"
                data-read-url="{{ route('notifikasi.baca', $notif->id) }}"
                data-read="{{ $notif->status === 'dibaca' ? '1' : '0' }}"
                data-group-invite="1"
                data-group-title="{{ $notif->group_invite_title }}"
                data-group-description="{{ $notif->group_invite_message }}"
                data-group-name="{{ $notif->group_invite_group_name }}"
                data-group-inviter="{{ $notif->group_invite_inviter_name }}"
                data-group-reason="{{ $notif->group_invite_reason }}"
                data-group-invite-token="{{ $notif->group_invite_token ?? '' }}"
                data-group-invite-hidden="{{ $notif->group_invite_hidden_fields ?? '' }}"
              >
                <p>{{ $notif->pesan }}</p>
                <span class="notif-time">{{ $notif->created_at?->diffForHumans() ?? 'Baru saja' }}</span>
                <span class="notif-tag"><i class="bi bi-shield-check"></i> Buka Undangan Grup</span>
              </button>
            @elseif(!empty($notif->is_letter_prompt))
              <button
                type="button"
                class="notif-item notif-readable notif-item-trigger {{ $notif->status === 'dibaca' ? 'notif-read' : 'notif-unread' }}"
                data-read-url="{{ route('notifikasi.baca', $notif->id) }}"
                data-read="{{ $notif->status === 'dibaca' ? '1' : '0' }}"
                data-letter-title="{{ $notif->prompt_title }}"
                data-letter-message="{{ $notif->prompt_message }}"
                data-letter-cta="{{ $notif->prompt_cta }}"
                data-letter-action="{{ $notif->letter_action ?? $notif->cta_target }}"
                data-letter-note="{{ $notif->prompt_note }}"
                data-letter-locked="{{ !empty($notif->prompt_locked) ? '1' : '0' }}"
                data-letter-hidden="{{ $notif->letter_hidden_fields ?? '' }}"
              >
                <p>{{ $notif->pesan }}</p>
                <span class="notif-time">{{ $notif->created_at?->diffForHumans() ?? 'Baru saja' }}</span>
                <span class="notif-tag"><i class="bi bi-envelope-paper-heart"></i> Buka Undangan</span>
              </button>
            @else
              <a
                    href="{{ $notif->cta_target ?? route('riwayat') }}"
                    class="notif-item notif-readable {{ $notif->status === 'dibaca' ? 'notif-read' : 'notif-unread' }}"
                    data-read-url="{{ route('notifikasi.baca', $notif->id) }}"
                    data-read="{{ $notif->status === 'dibaca' ? '1' : '0' }}"
                >
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
@php
    $user = Auth::user();
    $mahasiswa = $user?->mahasiswa;

    $namaUser = $user?->nama
        ?? $user?->name
        ?? 'Mahasiswa';

    $namaAsliUser = $user?->nama
        ?? $user?->name
        ?? 'Mahasiswa';

    $inisialUser = strtoupper(substr($namaAsliUser, 0, 1));

    $fotoProfil = optional($user?->profil)->foto
        ? \Illuminate\Support\Facades\Storage::url($user->profil->foto)
        : null;
@endphp

<div class="profile-wrap">
    <button type="button" class="profile-btn" id="profileBtn" aria-expanded="false" onclick="toggleProfile()">
        @if($fotoProfil)
            <img 
                src="{{ $fotoProfil }}"
                alt="Profile"
                class="profile-img"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >
            <span class="profile-fallback" style="display:none;">
                {{ $inisialUser }}
            </span>
        @else
            <span class="profile-fallback">
                {{ $inisialUser }}
            </span>
        @endif

        <div class="online-dot"></div>
        <span class="mobile-profile-name">{{ $namaUser }}</span>
    </button>

    <div class="profile-dropdown" id="profileDropdown">
        <div class="pd-header">
            <div class="pd-avatar">
                @if($fotoProfil)
                    <img 
                        src="{{ $fotoProfil }}"
                        alt="Profile"
                        class="pd-avatar-img"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <span class="pd-avatar-fallback" style="display:none;">
                        {{ $inisialUser }}
                    </span>
                @else
                    <span class="pd-avatar-fallback">
                        {{ $inisialUser }}
                    </span>
                @endif
            </div>

            <div class="pd-info">
                <div class="pd-name">{{ $namaUser }}</div>
            </div>
        </div>

        <a href="{{ route('profil') }}" class="pd-item">
            <i class="bi bi-person-circle"></i>
            <span>Profil Saya</span>
            <i class="bi bi-chevron-right pd-item-arrow" aria-hidden="true"></i>
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
      </div>
    </div>
  </div>
</nav>

<script>
  (function placeMobileNotificationImmediately() {
    if (window.innerWidth >= 992) return;

    const navbarContainer = document.querySelector('#mainNav > .container');
    const toggleButton = document.getElementById('mobileMenuToggle');
    const notifItem = document.getElementById('notifDropdownBtn')?.closest('.nav-item');

    if (!navbarContainer || !toggleButton || !notifItem) return;

    notifItem.classList.add('navbar-notif-mobile');
    navbarContainer.insertBefore(notifItem, toggleButton);
  })();
</script>

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

<div class="group-consent-modal" id="groupConsentModal" aria-hidden="true">
  <div class="group-consent-backdrop" data-group-consent-close></div>
  <div class="group-consent-dialog" role="dialog" aria-modal="true" aria-labelledby="groupConsentTitle">
    <div class="group-consent-head">
      <div class="group-consent-label">
        <span>Persetujuan Grup</span>
      </div>
      <button type="button" class="group-consent-close" data-group-consent-close aria-label="Tutup">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="group-consent-body">
      <div class="group-consent-copy">
        <h1 id="groupConsentTitle">Undangan ke grup privat</h1>
        <p id="groupConsentDescription">Pastikan Anda memahami alasan undangan, tujuan grup, dan aturan komunikasi sebelum bergabung.</p>
      </div>
      <div class="group-consent-info">
        <h3>Informasi Undangan</h3>
        <div class="group-consent-info-grid">
          <div>
            <strong>Nama Grup</strong>
            <p id="groupConsentGroupName">Grup Privat</p>
          </div>
          <div>
            <strong>Pengundang</strong>
            <p id="groupConsentInviterName">Konselor</p>
          </div>
          <div>
            <strong>Alasan Diundang</strong>
            <p id="groupConsentIdentityVisibility">Anda di undang di grup privat ini relevan untuk pendampingan dan diskusi konseling.</p>
          </div>
        </div>
      </div>
      <div class="group-consent-rules-card">
        <h3>Aturan Sebelum Bergabung</h3>
        <ul>
          <li>Gunakan grup ini untuk diskusi yang relevan dengan tujuan konseling.</li>
          <li>Jangan spam atau mengirim pesan yang mengganggu anggota lain.</li>
          <li>Gunakan bahasa yang sopan, tanpa kata-kata kasar atau menghina.</li>
          <li>Jaga privasi isi percakapan dan identitas anggota grup.</li>
        </ul>
      </div>
      <form id="groupConsentForm" class="group-consent-form" method="POST" action="{{ route('mahasiswa.group-chat.join') }}">
        @csrf
        <input type="hidden" name="invite_token" id="groupConsentInviteToken" value="">
        <input type="hidden" name="consent_version" value="{{ \App\Support\GroupChatSupport::consentVersion() }}">
        <div id="groupConsentHiddenFields" hidden></div>
        <div class="group-consent-checkbox">
          <label for="groupConsentCheckbox">
            <input
              type="checkbox"
              id="groupConsentCheckbox"
              name="consent_acknowledged"
              value="1"
              required
              onchange="document.getElementById('groupConsentSubmit').disabled = !this.checked;"
              onclick="document.getElementById('groupConsentSubmit').disabled = !this.checked;"
            >
            Saya memahami bahwa grup privat ini akan menampilkan nama asli saya kepada anggota grup dan konselor, dan saya setuju untuk bergabung.
          </label>
        </div>
        <div class="group-consent-actions">
          <button type="submit" class="group-consent-submit" id="groupConsentSubmit" disabled>
            <span>Setuju dan Gabung Grup</span>
          </button>
        </div>
      </form>
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
    <div class="row g-4 footer-main-grid">
      <div class="col-lg-5">
        <div class="footer-brand d-flex align-items-center gap-3 mb-3">
          <img src="{{ asset('img/logo.png') }}" alt="Campus Care" class="footer-logo-img">
          <div>
            <div class="footer-brand-txt">Campus Care</div>
            <div class="footer-brand-subtitle">Layanan Bimbingan dan Konseling Mahasiswa · Institut Teknologi Del</div>
          </div>
        </div>

        <p class="footer-summary">
          Campus Care merupakan platform layanan bimbingan dan konseling digital yang membantu mahasiswa
          memperoleh layanan konseling, edukasi kesehatan mental, serta pendampingan secara aman, nyaman,
          dan mudah diakses.
        </p>

        <div class="footer-social">
          <a href="#" aria-label="Instagram Campus Care"><i class="bi bi-instagram"></i></a>
          <a href="https://wa.me/" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
          <a href="#" aria-label="YouTube Campus Care"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <h6>Layanan</h6>
        <a href="{{ url('/konseling') }}">Buat Jadwal Konseling</a>
        <a href="{{ url('/chat') }}">Chat Konseling</a>
        <a href="{{ url('/edukasi-mental') }}">Ruang Edukasi</a>
        <a href="{{ url('/group-chat') }}">Grup Chat</a>
      </div>

      <div class="col-sm-6 col-lg-4">
        <h6>Hubungi Kami</h6>
        <a href="mailto:bk@del.ac.id" class="footer-contact-item">
          <i class="bi bi-envelope-fill"></i><span>bk@del.ac.id</span>
        </a>
        <a href="tel:+6282283184190" class="footer-contact-item">
          <i class="bi bi-telephone-fill"></i><span>+62 822-8318-4190</span>
        </a>
        <div class="footer-contact-item">
          <i class="bi bi-geo-alt-fill"></i>
          <span>Gedung 5 Lt. 2, antara GD 525 &amp; GD 526, Institut Teknologi Del</span>
        </div>
        <div class="footer-hours">
          <strong><i class="bi bi-clock me-2"></i>Jam Operasional</strong><br>
          Senin–Jumat, 08.00–16.00 WIB
        </div>
      </div>
    </div>

    <div class="footer-copy">
      © 2026 Campus Care · Layanan Bimbingan dan Konseling Mahasiswa · Institut Teknologi Del
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('pageshow', function (event) {
  if (event.persisted) {
    // Halaman dari browser back-forward cache harus dicek ulang ke server setelah logout.
    window.location.reload();
  }
});

window.addEventListener('scroll',()=>{
  document.getElementById('mainNav').classList.toggle('scrolled',window.scrollY>20);
});
function toggleProfile(){
  const profileDropdown = document.getElementById('profileDropdown');
  const profileBtn = document.getElementById('profileBtn');
  const willOpen = !profileDropdown?.classList.contains('show');

  closeNotifDropdown();
  profileDropdown?.classList.toggle('show', willOpen);
  profileBtn?.setAttribute('aria-expanded', String(willOpen));
}
document.addEventListener('click',(e)=>{
  if(!document.getElementById('profileBtn')?.contains(e.target)&&!document.getElementById('profileDropdown')?.contains(e.target)){
    document.getElementById('profileDropdown')?.classList.remove('show');
    document.getElementById('profileBtn')?.setAttribute('aria-expanded', 'false');
  }
});

const notifDropdownBtn = document.getElementById('notifDropdownBtn');
const notifDropdownMenu = notifDropdownBtn?.parentElement?.querySelector('.notif-dropdown');
const notifBadge = document.getElementById('notifBadge');

function closeNotifDropdown() {
  notifDropdownMenu?.classList.remove('show');
  notifDropdownBtn?.setAttribute('aria-expanded', 'false');
}

if (notifDropdownBtn && notifDropdownMenu) {
  notifDropdownBtn.addEventListener('click', function (event) {
    event.preventDefault();
    event.stopPropagation();

    const isOpen = notifDropdownMenu.classList.contains('show');

    if (!isOpen && notifDropdownBtn.closest('.navbar-notif-mobile')) {
      closeMobileMenu();
    }

    document.getElementById('profileDropdown')?.classList.remove('show');
    document.getElementById('profileBtn')?.setAttribute('aria-expanded', 'false');

    document.querySelectorAll('.notif-dropdown.show').forEach(function (menu) {
      if (menu !== notifDropdownMenu) {
        menu.classList.remove('show');
      }
    });

    notifDropdownMenu.classList.toggle('show', !isOpen);
    notifDropdownBtn.setAttribute('aria-expanded', String(!isOpen));
  });

  notifDropdownMenu.addEventListener('click', function (event) {
    event.stopPropagation();
  });

  document.addEventListener('click', function (event) {
    if (
      !notifDropdownBtn.contains(event.target) &&
      !notifDropdownMenu.contains(event.target)
    ) {
      closeNotifDropdown();
    }
  });
}

function decreaseNotifBadge() {
  if (!notifBadge || notifBadge.classList.contains('d-none')) {
    return;
  }

  const text = notifBadge.textContent.trim();
  let count = text === '9+' ? 10 : parseInt(text, 10);

  if (Number.isNaN(count)) {
    return;
  }

  count -= 1;

  if (count <= 0) {
    notifBadge.textContent = '0';
    notifBadge.classList.add('d-none');
  } else {
    notifBadge.textContent = count > 9 ? '9+' : count;
  }
}

function markNotifAsRead(item) {
  const readUrl = item.dataset.readUrl;

  if (!readUrl || item.dataset.reading === '1' || item.dataset.read === '1') {
      return Promise.resolve();
  }

  item.dataset.reading = '1';

  return fetch(readUrl, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
    },
  }).then(function (response) {
    if (response.ok) {
      decreaseNotifBadge();
      
      item.dataset.read = '1';
      item.classList.add('notif-read');
      item.classList.remove('notif-unread');
    } else {
      item.dataset.reading = '0';
    }
  }).catch(function (error) {
    console.error('Gagal menandai notifikasi sebagai dibaca:', error);
    item.dataset.reading = '0';
  });
}

const letterModal = document.getElementById('letterModal');
const letterEnvelope = document.getElementById('letterEnvelope');
const envelopeStage = letterModal?.querySelector('.envelope-stage');
const letterSpeechActions = document.getElementById('letterSpeechActions');
const letterActionForm = document.getElementById('letterActionForm');
const letterActionHiddenFields = document.getElementById('letterActionHiddenFields');
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

const groupConsentModal = document.getElementById('groupConsentModal');
const groupConsentForm = document.getElementById('groupConsentForm');
const groupConsentTitle = document.getElementById('groupConsentTitle');
const groupConsentDescription = document.getElementById('groupConsentDescription');
const groupConsentGroupName = document.getElementById('groupConsentGroupName');
const groupConsentInviterName = document.getElementById('groupConsentInviterName');
const groupConsentIdentityVisibility = document.getElementById('groupConsentIdentityVisibility');
const groupConsentInviteToken = document.getElementById('groupConsentInviteToken');
const groupConsentCheckbox = document.getElementById('groupConsentCheckbox');
const groupConsentSubmit = document.getElementById('groupConsentSubmit');
const groupConsentHiddenFields = document.getElementById('groupConsentHiddenFields');
let groupConsentSubmitting = false;
let activeGroupInviteToken = '';

const syncGroupConsentSubmitState = () => {
  if (!groupConsentCheckbox || !groupConsentSubmit) {
    return;
  }

  groupConsentSubmit.disabled = !groupConsentCheckbox.checked || groupConsentSubmitting;
  groupConsentSubmit.setAttribute('aria-disabled', groupConsentSubmit.disabled ? 'true' : 'false');
};

const syncBodyScrollLock = () => {
  const hasOpenModal = letterModal?.classList.contains('show') || scheduleGuardModal?.classList.contains('show') || groupConsentModal?.classList.contains('show');
  document.body.style.overflow = hasOpenModal ? 'hidden' : '';
};

const resetLetterActionHiddenFields = () => {
  if (!letterActionHiddenFields) {
    return;
  }

  letterActionHiddenFields.innerHTML = '';
};

const setLetterActionHiddenFields = (fields = {}) => {
  if (!letterActionHiddenFields || typeof fields !== 'object' || fields === null) {
    return;
  }

  Object.entries(fields).forEach(([name, value]) => {
    if (value === null || value === undefined) {
      return;
    }

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = String(value);
    letterActionHiddenFields.appendChild(input);
  });
};

const resetGroupConsentHiddenFields = () => {
  if (!groupConsentHiddenFields) {
    return;
  }

  groupConsentHiddenFields.innerHTML = '';
};

const setGroupConsentHiddenFields = (fields = {}) => {
  if (!groupConsentHiddenFields || typeof fields !== 'object' || fields === null) {
    return;
  }

  Object.entries(fields).forEach(([name, value]) => {
    if (value === null || value === undefined) {
      return;
    }

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = String(value);
    groupConsentHiddenFields.appendChild(input);
  });
};

const parseJson = (value) => {
  if (!value) {
    return {};
  }

  try {
    return JSON.parse(value);
  } catch (error) {
    return {};
  }
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

const openLetterModal = ({ title, message, cta, action, note, locked, hiddenFields = {} }) => {
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
  resetLetterActionHiddenFields();
  setLetterActionHiddenFields(hiddenFields);
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

const openGroupConsentModal = ({ title, description, groupName, inviterName, inviteReason, identityVisibility, inviteToken, hiddenFields = {} }) => {
  if (!groupConsentModal || !groupConsentTitle || !groupConsentDescription || !groupConsentGroupName || !groupConsentInviterName || !groupConsentIdentityVisibility || !groupConsentInviteToken || !groupConsentCheckbox || !groupConsentSubmit) {
    return;
  }

  groupConsentTitle.textContent = title || 'Undangan ke grup privat';
  groupConsentDescription.textContent = description || 'Pastikan Anda memahami alasan undangan dan tujuan grup ini sebelum bergabung.';
  groupConsentGroupName.textContent = groupName || 'Grup Privat';
  groupConsentInviterName.textContent = inviterName || 'Konselor';
  groupConsentIdentityVisibility.textContent = inviteReason || identityVisibility || 'Grup privat ini relevan untuk pendampingan dan diskusi konseling.';
  groupConsentInviteToken.value = inviteToken || '';
  groupConsentCheckbox.checked = false;
  groupConsentSubmit.innerHTML = '<span>Setuju dan Gabung Grup</span>';
  groupConsentSubmitting = false;
  activeGroupInviteToken = inviteToken || '';
  syncGroupConsentSubmitState();
  resetGroupConsentHiddenFields();
  setGroupConsentHiddenFields(hiddenFields);
  closeNotifDropdown();
  closeLetterModal();
  groupConsentModal.classList.add('show');
  groupConsentModal.setAttribute('aria-hidden', 'false');
  syncBodyScrollLock();
  window.setTimeout(() => {
    groupConsentCheckbox.focus();
  }, 60);
};

const closeGroupConsentModal = () => {
  if (!groupConsentModal) {
    return;
  }

  groupConsentModal.classList.remove('show');
  groupConsentModal.setAttribute('aria-hidden', 'true');
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

document.querySelectorAll('[data-letter-close]').forEach((element) => {
  element.addEventListener('click', closeLetterModal);
});

document.querySelectorAll('[data-group-consent-close]').forEach((element) => {
  element.addEventListener('click', closeGroupConsentModal);
});

document.querySelectorAll('.notif-item-trigger').forEach((button) => {
  button.addEventListener('click', () => {
    markNotifAsRead(button);

    openLetterModal({
      title: button.dataset.letterTitle || 'Undangan Sesi Konseling',
      message: button.dataset.letterMessage || 'Saat Anda siap, mari mulai sesi konseling.',
      cta: button.dataset.letterCta || 'Mulai Sesi Konseling',
      action: button.dataset.letterAction || "{{ route('mahasiswa.chat.start') }}",
      note: button.dataset.letterNote || 'Saat siap, Anda bisa langsung masuk ke ruang chat konseling.',
      locked: button.dataset.letterLocked || '0',
      hiddenFields: parseJson(button.dataset.letterHidden),
    });
  });
});

document.querySelectorAll('[data-group-invite="1"]').forEach((button) => {
  button.addEventListener('click', (event) => {
    event.preventDefault();
    markNotifAsRead(button);

    const hiddenFields = button.dataset.groupInviteHidden ? parseJson(button.dataset.groupInviteHidden) : {};
    const inviteToken = button.dataset.groupInviteToken || hiddenFields.invite_token || '';
    const otherHiddenFields = { ...hiddenFields };
    delete otherHiddenFields.invite_token;

    openGroupConsentModal({
      title: button.dataset.groupTitle || 'Undangan Grup Privat',
      description: button.dataset.groupDescription || 'Anda telah menerima undangan grup privat. Baca aturan dan setujui sebelum bergabung.',
      groupName: button.dataset.groupName,
      inviterName: button.dataset.groupInviter,
      inviteReason: button.dataset.groupReason || button.dataset.groupVisibility,
      inviteToken: inviteToken,
      hiddenFields: otherHiddenFields,
    });
  });
});

document.querySelectorAll('a.notif-readable').forEach((item) => {
  item.addEventListener('click', function (event) {
    const targetUrl = item.getAttribute('href');

    event.preventDefault();

    if (item.dataset.letterAction) {
      markNotifAsRead(item);
      openLetterModal({
        title: item.dataset.letterTitle || 'Undangan Sesi Konseling',
        message: item.dataset.letterMessage || 'Saat Anda siap, mari mulai sesi konseling.',
        cta: item.dataset.letterCta || 'Mulai Sesi Konseling',
        action: item.dataset.letterAction || targetUrl || "{{ route('mahasiswa.chat.start') }}",
        note: item.dataset.letterNote || 'Saat siap, Anda bisa langsung masuk ke ruang chat konseling.',
        locked: item.dataset.letterLocked || '0',
        hiddenFields: parseJson(item.dataset.letterHidden),
      });
      return;
    }

    markNotifAsRead(item).finally(() => {
      if (targetUrl && targetUrl !== '#') {
        window.location.href = targetUrl;
      }
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

if (groupConsentCheckbox && groupConsentSubmit) {
  ['change', 'input', 'click'].forEach((eventName) => {
    groupConsentCheckbox.addEventListener(eventName, () => {
      window.setTimeout(syncGroupConsentSubmitState, 0);
    });
  });
}

// Listener delegasi memastikan tombol consent tetap aktif walau checkbox diklik lewat label.
document.addEventListener('change', (event) => {
  if (event.target?.id === 'groupConsentCheckbox') {
    window.setTimeout(syncGroupConsentSubmitState, 0);
  }
}, true);

document.addEventListener('click', (event) => {
  if (event.target?.id === 'groupConsentCheckbox' || event.target?.closest?.('label[for="groupConsentCheckbox"]')) {
    window.setTimeout(syncGroupConsentSubmitState, 0);
  }
}, true);

if (groupConsentForm && groupConsentSubmit) {
  groupConsentForm.addEventListener('submit', (event) => {
    if (!groupConsentCheckbox?.checked) {
      event.preventDefault();
      syncGroupConsentSubmitState();
      return;
    }

    if (groupConsentInviteToken && !groupConsentInviteToken.value && activeGroupInviteToken) {
      groupConsentInviteToken.value = activeGroupInviteToken;
    }

    if (!groupConsentInviteToken?.value?.trim()) {
      event.preventDefault();
      groupConsentSubmitting = false;
      groupConsentSubmit.disabled = false;
      groupConsentSubmit.innerHTML = '<span>Setuju dan Gabung Grup</span>';
      alert('Token undangan grup tidak ditemukan. Silakan buka ulang notifikasi undangan grup.');
      return;
    }

    if (groupConsentSubmitting) {
      event.preventDefault();
      return;
    }

    groupConsentSubmitting = true;
    groupConsentSubmit.disabled = true;
    groupConsentSubmit.innerHTML = '<span>Memproses...</span>';
  });
}

document.addEventListener('keydown', (event) => {
  if (event.key === 'Escape') {
    closeLetterModal();
    closeScheduleGuardModal();
    closeGroupConsentModal();
  }
});

function toggleMobileMenu(toggleButton) {
    const navMain = document.getElementById('navMain');

    if (!navMain) {
        return;
    }

    const willOpen = !navMain.classList.contains('show');

    if (willOpen) {
        closeNotifDropdown();
    }

    const isOpen = navMain.classList.toggle('show', willOpen);
    toggleButton.setAttribute('aria-expanded', String(isOpen));
    toggleButton.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
}

function closeMobileMenu() {
    const toggleButton = document.getElementById('mobileMenuToggle');
    const navMain = document.getElementById('navMain');

    navMain?.classList.remove('show');
    toggleButton?.setAttribute('aria-expanded', 'false');
    toggleButton?.setAttribute('aria-label', 'Buka menu navigasi');
}

document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('mobileMenuToggle');
    const navMain = document.getElementById('navMain');
    const navbarContainer = document.querySelector('#mainNav > .container');
    const navbarNav = navMain?.querySelector('.navbar-nav');
    const notifItem = document.getElementById('notifDropdownBtn')?.closest('.nav-item');

    function placeResponsiveNotification() {
        if (!notifItem || !toggleButton || !navbarContainer || !navbarNav) return;

        if (window.innerWidth < 992) {
            notifItem.classList.add('navbar-notif-mobile');
            navbarContainer.insertBefore(notifItem, toggleButton);
        } else {
            notifItem.classList.remove('navbar-notif-mobile');
            navbarNav.appendChild(notifItem);
        }
    }

    placeResponsiveNotification();

    if (!toggleButton || !navMain) return;

    navMain.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            closeMobileMenu();
        });
    });

    document.addEventListener('click', function (event) {
        if (window.innerWidth >= 992 || !navMain.classList.contains('show')) return;
        if (!navMain.contains(event.target) && !toggleButton.contains(event.target)) {
            closeMobileMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeMobileMenu();
    });

    window.addEventListener('resize', function () {
        placeResponsiveNotification();
        if (window.innerWidth >= 992) closeMobileMenu();
    });
});
</script>
@stack('scripts')
</body>
</html>
