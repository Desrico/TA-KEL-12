@extends('layouts.master')

@php
    $jadwal = optional($activeSession)->jadwalKonseling;
    $konselor = optional(optional($jadwal)->konselor)->user;
    $topik = null;
    $startReady = $isReadyToStart ?? false;
    $isBlockedBySchedule = $isBlockedBySchedule ?? false;
    $chatAccessGranted = $chatAccessGranted ?? false;
    $canStartNow = $canStartNow ?? false;
    $scheduledStartLabel = $scheduledStartLabel ?? null;

    $chatPayload = $chatPayload ?? null;

    $readOnlyChat = (bool) (
        $chatPayload['readOnly'] ??
        $chatPayload['chatSelesai'] ??
        false
    );

    $canSendChat = $activeSession
        && $chatPayload
        && $chatAccessGranted
        && ! $isBlockedBySchedule
        && ! $startReady
        && ! $readOnlyChat;

    if (!empty($jadwal?->catatan) && preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
        $topik = trim($match[1]);
    }

    $statusLabel = match (true) {
        $isBlockedBySchedule => 'Terjadwal',
        $chatAccessGranted => 'Sedang Berlangsung',
        $startReady => 'Siap Dimulai',
        default => match ($jadwal->status ?? null) {
            'berlangsung' => 'Sedang Berlangsung',
            'disetujui' => 'Siap Dimulai',
            default => 'Menunggu Persetujuan',
        },
    };

    $namaKonselorTampil = env(
    'CIS_KONSELOR_NAME',
    $konselor?->getNamaDisplay()
        ?? $konselor?->nama
        ?? $konselor?->name
        ?? 'Konselor'
);

$inisialKonselorTampil = strtoupper(mb_substr($namaKonselorTampil, 0, 1));
@endphp

@push('styles')
<style>
  .chat-page {
      height: 100%;
      min-height: 0;
      background:
        radial-gradient(circle at top left, rgba(16, 185, 129, 0.18), transparent 30%),
        radial-gradient(circle at top right, rgba(110, 231, 183, 0.18), transparent 24%),
        linear-gradient(180deg, #effcf5 0%, #eefbf4 26%, #e2f7ec 100%);
      width: 100dvw;
      margin-left: calc(50% - 50dvw);
      margin-right: calc(50% - 50dvw);
      padding: 0;
      overflow: hidden;
  }

  .chat-page-inner {
    width: 100%;
    height: 100%;
    min-height: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
  }

  .page-in:has(.chat-page:not(.is-empty-state)) {
    height: calc(100dvh - var(--chat-nav-height, 82px));
    overflow: hidden;
  }

  .page-in:has(.chat-page:not(.is-empty-state)) + footer {
    display: none;
  }

  .chat-shell {
    width: 100%;
    max-width: none;
    margin: 0;
    min-height: 0;
    flex: 1;
    display: flex;
  }

  .chat-main,
  .chat-empty {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(209, 250, 229, 0.9);
    border-radius: 26px;
    box-shadow: 0 16px 42px rgba(6, 78, 59, 0.07);
    backdrop-filter: blur(12px);
  }

  .chat-main {
      overflow: hidden;
      display: flex;
      flex-direction: column;
      min-height: 0;
      height: 100%;
      max-height: 100%;
      flex: 1;
      border-radius: 0;
      border-inline: 0;
      box-shadow: none;
  }

  .chat-topbar {
    position: relative;
    z-index: 3;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: .85rem 1.5rem;
    background: linear-gradient(135deg, rgba(239, 252, 245, 0.98), rgba(255, 255, 255, 0.94));
    border-bottom: 1px solid rgba(221, 239, 231, 0.95);
  }

  .chat-counselor {
    display: flex;
    align-items: center;
    gap: .85rem;
    min-width: 0;
  }

  .chat-avatar {
    width: 50px;
    height: 50px;
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.96);
    box-shadow: 0 8px 20px rgba(6, 78, 59, 0.14);
    flex-shrink: 0;
  }

  .chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .chat-title {
    font-size: 1.02rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .08rem;
  }

  .chat-subtitle {
    color: #4b7a68;
    font-size: .82rem;
    margin: 0;
  }

  .chat-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .58rem .85rem;
    border-radius: 999px;
    background: #e7fff0;
    color: #047857;
    font-size: .74rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .chat-badge::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.12);
  }

  .chat-thread {
      flex: 1 1 auto;
      min-height: 0;
      padding: 1rem 1.5rem .75rem;
      overflow-y: auto;
      overflow-x: hidden;
      overscroll-behavior: contain;
      scroll-behavior: smooth;
      background:
        linear-gradient(180deg, rgba(248, 255, 251, 0.72), rgba(255, 255, 255, 0.98)),
        radial-gradient(circle at center, rgba(209, 250, 229, 0.34), transparent 42%);
  }

  .chat-date-pill {
    width: fit-content;
    margin: 0 auto 1rem;
    padding: .45rem .88rem;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid rgba(221, 239, 231, 0.95);
    color: #64748b;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
  }

  .chat-avatar-fallback {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #b7ebc9;
    color: #065f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 800;
    border: 3px solid #ffffff;
    box-shadow: 0 8px 22px rgba(6, 78, 59, 0.14);
    flex-shrink: 0;
}

.message-avatar-fallback {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 50%;
    background: #b7ebc9;
    color: #065f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}

  .message-row {
    display: flex;
    gap: .65rem;
    margin-bottom: .78rem;
    align-items: flex-start;
  }

  .message-row.mine {
    justify-content: flex-end;
  }

  .message-row.other {
      justify-content: flex-start;
  }

  .message-row.mine .message-meta {
    justify-content: flex-end;
  }

  .message-row.mine .message-bubble {
    background: linear-gradient(135deg, #047857, #059669);
    color: #fff;
    border-radius: 10px;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 10px;
    box-shadow: 0 1px 1px rgba(15, 23, 42, 0.12);
    overflow: visible;
  }

  .message-row.mine .message-bubble::after {
    content: "";
    position: absolute;
    top: 0;
    right: -7px;
    width: 9px;
    height: 10px;
    background: #059669;
    clip-path: polygon(0 0, 100% 0, 0 100%);
    pointer-events: none;
  }

  .message-row.other .message-bubble {
    background: #ffffff;
    color: #1f2937;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-radius: 10px;
    border-top-left-radius: 0;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
    overflow: visible;
  }

  .message-row.other .message-bubble::before {
    content: "";
    position: absolute;
    top: -1px;
    left: -9px;
    width: 9px;
    height: 11px;
    background: rgba(226, 232, 240, 0.9);
    clip-path: polygon(0 0, 100% 0, 100% 100%);
    pointer-events: none;
  }

  .message-row.other .message-bubble::after {
    content: "";
    position: absolute;
    top: 0;
    left: -7px;
    width: 7px;
    height: 9px;
    background: #ffffff;
    clip-path: polygon(0 0, 100% 0, 100% 100%);
    pointer-events: none;
  }

  /*
   * Legacy overrides below are intentionally neutralized by the WhatsApp-style
   * bubble rules above.
   */
  .message-row.mine .message-bubble.legacy-unused {
    background: linear-gradient(135deg, #047857, #059669);
    color: #fff;
    border-bottom-right-radius: 10px;
    box-shadow: 0 10px 24px rgba(5, 150, 105, 0.16);
    overflow: visible;
  }

  .message-row.mine .message-bubble.legacy-unused::after {
    content: "";
    position: absolute;
    top: -1px;
    right: -1px;
    width: 14px;
    height: 14px;
    background: linear-gradient(135deg, #047857, #059669);
    border-top-right-radius: 4px;
    transform: skewX(45deg);
    transform-origin: top right;
    z-index: 1;
    pointer-events: none;
  }

  .message-row.other .message-bubble.legacy-unused {
    background: #ffffff;
    color: #1f2937;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-bottom-left-radius: 10px;
    overflow: visible;
  }

  .message-row.other .message-bubble.legacy-unused::before {
    content: "";
    position: absolute;
    top: -1px;
    left: -1px;
    width: 14px;
    height: 14px;
    background: #ffffff;
    border-top-left-radius: 4px;
    transform: skewX(-45deg);
    transform-origin: top left;
    box-shadow: -1px 0 0 rgba(226, 232, 240, 0.9);
    z-index: 1;
    pointer-events: none;
  }

  .message-avatar {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 7px 16px rgba(15, 23, 42, 0.08);
    flex-shrink: 0;
  }

  .message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .message-content {
    max-width: min(72%, 420px);
    width: fit-content;
    position: relative;
}

.message-row.mine .message-content {
    margin-left: 0;
    margin-right: 0;
    max-width: min(70%, 420px);
    width: fit-content;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}


.message-row.other .message-content {
    margin-left: 0;
    margin-right: 0;
    max-width: min(70%, 420px);
    width: fit-content;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

  .message-meta {
    display: flex;
    align-items: center;
    gap: .45rem;
    margin: 0 .3rem .24rem;
    color: #64748b;
    font-size: .7rem;
  }

  .message-name {
    font-weight: 700;
    color: #064e3b;
  }

  .message-bubble {
    display: inline-block;
    width: fit-content;
    max-width: 100%;
    padding: .36rem .58rem;
    border-radius: 10px;
    font-size: .84rem;
    line-height: 1.28;
    word-break: break-word;
    white-space: pre-wrap;
    position: relative;
    overflow: visible;
}

  .message-bubble-shell {
    position: relative;
  }

  .message-edited {
    font-size: .68rem;
    color: #94a3b8;
    font-weight: 600;
  }

  .message-actions {
    position: absolute;
    top: .55rem;
    right: .7rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity .18s ease;
  }

  .message-row.mine .message-actions {
    position: relative;
    top: auto;
    right: auto;
    margin-left: -.2rem;
  }

  .message-row.other .message-actions {
    position: relative;
    top: auto;
    right: auto;
    margin-left: -.2rem;
  }

  .message-row.mine:hover .message-actions,
  .message-row.other:hover .message-actions,
  .message-row.mine.is-menu-open .message-actions,
  .message-row.other.is-menu-open .message-actions {
    opacity: 1;
    pointer-events: auto;
  }

  .message-action-toggle {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    color: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }

  .message-row.other .message-action-toggle {
    width: 22px;
    height: 22px;
    background: transparent;
    color: #64748b;
  }

  .message-row.mine .message-action-toggle {
    width: 22px;
    height: 22px;
    background: transparent;
    color: #64748b;
  }

  .message-action-menu {
    position: absolute;
    top: calc(100% + .3rem);
    right: 0;
    min-width: 150px;
    padding: .4rem;
    border-radius: 14px;
    background: #fff;
    border: 1px solid rgba(221, 239, 231, 0.96);
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
    display: none;
    z-index: 4;
  }

  .message-row:last-child .message-action-menu {
    top: calc(100% + .45rem);
  }

  .message-row.is-menu-open .message-action-menu {
    display: block;
  }

  .message-action-item {
    width: 100%;
    border: none;
    background: transparent;
    border-radius: 10px;
    padding: .55rem .7rem;
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    color: #0f172a;
    font-size: .8rem;
    font-weight: 700;
    text-align: left;
  }

  .message-action-item:hover {
    background: #f8fffb;
  }

  .message-action-item.delete {
    color: #b91c1c;
  }

  .message-reply-preview {
    margin-bottom: .08rem;
    padding: .38rem .48rem;
    border-left: 3px solid #10b981;
    border-radius: 8px;
    background: rgba(236, 253, 245, .92);
    color: #334155;
    font-size: .72rem;
    line-height: 1.25;
    max-width: 230px;
    width: fit-content;
    white-space: normal;
  }

  .message-row.mine .message-reply-preview {
    background: rgba(255, 255, 255, .18);
    color: rgba(255, 255, 255, .86);
    border-left-color: rgba(255, 255, 255, .8);
  }

  .message-reply-name {
    display: block;
    margin-bottom: .08rem;
    font-weight: 800;
    color: #047857;
  }

  .message-bubble:has(.message-reply-preview) {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0;
    width: fit-content;
    padding: .28rem .52rem .16rem;
    line-height: 1.25;
  }

  .message-text {
    display: block;
    margin: 0;
    padding: 0;
  }

  .message-row.mine .message-bubble:has(.message-reply-preview) {
    padding-right: .58rem;
  }

  .message-row.mine .message-reply-name {
    color: #ffffff;
  }

  .chat-reply-bar {
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: .8rem;
    margin-bottom: .65rem;
    padding: .72rem .85rem;
    border-left: 4px solid #10b981;
    border-radius: 16px;
    background: #ecfdf5;
    color: #334155;
  }

  .chat-reply-bar.is-active {
    display: flex;
  }

  .chat-reply-label {
    display: block;
    font-size: .72rem;
    font-weight: 800;
    color: #047857;
    margin-bottom: .12rem;
  }

  .chat-reply-text {
    font-size: .82rem;
    line-height: 1.35;
  }

  .chat-reply-cancel {
    width: 30px;
    height: 30px;
    border: none;
    border-radius: 999px;
    background: rgba(6, 95, 70, .1);
    color: #065f46;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .message-row.is-editing .message-actions {
    display: none;
  }

  .message-editor-shell {
    display: grid;
    gap: .7rem;
  }

  .message-editor-input {
    width: 100%;
    min-height: 92px;
    border: 1px solid rgba(209, 250, 229, 0.96);
    border-radius: 18px;
    padding: .8rem .9rem;
    resize: vertical;
    outline: none;
    font-size: .92rem;
    line-height: 1.65;
    color: #0f172a;
    background: rgba(255, 255, 255, 0.98);
  }

  .message-editor-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .message-editor-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .message-editor-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .message-editor-btn.save {
    background: #065f46;
    color: #fff;
  }

  .message-delete-confirm {
    display: grid;
    gap: .75rem;
  }

  .message-delete-preview {
    padding: .64rem .75rem;
    border-radius: 12px;
    background: rgba(248, 250, 252, .94);
    border: 1px solid rgba(226, 232, 240, .95);
    color: #334155;
    font-size: .8rem;
    line-height: 1.45;
    white-space: pre-wrap;
    word-break: break-word;
  }

  .message-delete-confirm-text {
    font-size: .83rem;
    line-height: 1.6;
    color: #334155;
  }

  .message-delete-confirm-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .message-delete-confirm-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .message-delete-confirm-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .message-delete-confirm-btn.delete {
    background: #b91c1c;
    color: #fff;
  }

  .chat-composer {
    padding: 1rem 1.35rem;
    border-top: 1px solid #dceee4;
    background: #ffffff;
    flex-shrink: 0;
}

.chat-form {
    width: 100%;
    display: flex;
    align-items: center;
    gap: .9rem;
    border: 1.5px solid #d1fae5;
    border-radius: 22px;
    padding: .55rem .65rem .55rem 1rem;
    background: #ffffff;
}

.chat-form.is-disabled {
    opacity: .78;
    border-style: dashed;
}

.chat-input {
    flex: 1;
    height: 44px;
    min-height: 44px;
    max-height: 100px;
    padding: .65rem 0;
    border: none;
    outline: none;
    resize: none;
    overflow-y: auto;
    font-size: .95rem;
    line-height: 1.35;
    background: transparent;
}

.chat-input:disabled {
    color: #94a3b8;
    cursor: not-allowed;
}

.chat-send {
    width: 52px;
    height: 52px;
    border: none;
    border-radius: 18px;
    background: #10b981;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
    cursor: pointer;
}

.chat-send:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 20px 36px rgba(6, 95, 70, 0.3);
}

.chat-send:disabled {
    opacity: .65;
    cursor: not-allowed;
}

.chat-hint {
    min-height: 16px;
    margin-top: .35rem;
    color: #64748b;
    font-size: .72rem;
    padding: 0 .2rem;
}

  .chat-empty {
    max-width: 880px;
    min-height: calc(100vh - 220px);
    margin: 0 auto;
    padding: 3.2rem 2rem;
    text-align: center;
    display: grid;
    align-content: center;
  }

  /* Footer tidak diberi jarak ekstra agar background chat menyatu penuh ke bawah. */
  .page-in:has(.chat-page) + footer {
    margin-top: 0;
  }

  .chat-empty-icon {
    width: 88px;
    height: 88px;
    margin: 0 auto 1.2rem;
    border-radius: 28px;
    display: grid;
    place-items: center;
    font-size: 2rem;
    color: #047857;
    background: linear-gradient(135deg, rgba(209, 250, 229, 0.92), rgba(236, 253, 245, 1));
    box-shadow: 0 18px 35px rgba(16, 185, 129, 0.16);
  }

  .chat-empty h2 {
    font-size: 1.55rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .7rem;
  }

  .chat-empty p {
    max-width: 560px;
    margin: 0 auto 1.4rem;
    color: #475569;
    line-height: 1.8;
  }

  .chat-empty .btn-empty {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    padding: .9rem 1.3rem;
    width: fit-content;
    margin: 0 auto;
    border-radius: 16px;
    text-decoration: none;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 18px 35px rgba(6, 95, 70, 0.2);
  }

  .chat-gate {
    flex: 1;
    display: grid;
    place-items: center;
    padding: 2rem 1.4rem;
  }

  .chat-gate-card {
    width: min(100%, 560px);
    text-align: center;
    border-radius: 28px;
    border: 1px solid rgba(221, 239, 231, 0.96);
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.14), transparent 28%),
      linear-gradient(180deg, #f7fff9, #ffffff);
    box-shadow: 0 22px 48px rgba(6, 78, 59, 0.08);
    padding: 2rem 1.5rem 1.6rem;
  }

  .chat-gate-icon {
    width: 86px;
    height: 86px;
    margin: 0 auto 1rem;
    border-radius: 28px;
    display: grid;
    place-items: center;
    font-size: 2rem;
    color: #047857;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    box-shadow: 0 16px 34px rgba(16, 185, 129, 0.16);
  }

  .chat-gate-card h2 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .6rem;
  }

  .chat-gate-card p {
    margin: 0 auto 1.1rem;
    max-width: 460px;
    color: #475569;
    line-height: 1.8;
  }

  .chat-start-btn {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    padding: .9rem 1.2rem;
    border: none;
    border-radius: 16px;
    color: #fff;
    font-weight: 800;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 18px 35px rgba(6, 95, 70, 0.2);
  }

  .chat-start-btn:disabled {
    opacity: .55;
    cursor: not-allowed;
    box-shadow: none;
  }

  @media (max-width: 991.98px) {
      .chat-main {
        min-height: 0;
        max-height: 100%;
      }
  }

  @media (max-width: 767.98px) {
    .chat-page {
      height: 100%;
      min-height: 0;
      padding: 0;
    }

    .chat-main,
    .chat-empty {
      border-radius: 0;
    }

    .chat-topbar {
      align-items: flex-start;
      flex-direction: column;
      padding: .85rem 1rem;
    }

    .message-content {
      max-width: 100%;
    }

    .message-row {
        display: flex;
        align-items: flex-end;
        gap: .45rem;
        margin-bottom: .9rem;
    }

    .message-avatar {
      width: 30px;
      height: 30px;
      border-radius: 10px;
    }

    .chat-main {
      min-height: 0;
      max-height: 100%;
    }

    .chat-thread {
      padding-inline: 1rem;
    }

    .chat-composer {
      padding: .7rem 1rem .8rem;
    }

    .chat-empty {
      min-height: calc(100vh - 170px);
      padding-inline: 1.15rem;
    }
  }

  .chat-page.is-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 96px);
    padding: 2rem 1rem;
}

.chat-page.is-empty-state .chat-page-inner {
    width: 100%;
    min-height: calc(100vh - 160px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-page.is-empty-state .chat-empty {
    width: min(100%, 880px);
    margin: 0 auto;
}

html,
body {
    overflow-y: hidden !important;
}

body:has(.chat-page.is-empty-state) {
    overflow-y: auto !important;
}

body:has(.chat-page:not(.is-empty-state)) {
    overflow-y: hidden !important;
}

.chat-main {
    overflow: hidden !important;
    max-height: 100% !important;
}

.chat-thread {
    overflow-y: auto !important;
    max-height: none !important;
    scrollbar-width: thin;
    scrollbar-color: rgba(6, 95, 70, .28) transparent;
}

.chat-thread::-webkit-scrollbar {
    width: 8px;
}

.chat-thread::-webkit-scrollbar-thumb {
    background: rgba(6, 95, 70, .24);
    border-radius: 999px;
}

.chat-thread::-webkit-scrollbar-track {
    background: transparent;
}
</style>
@endpush

@section('konten')
<section class="chat-page {{ !$activeSession ? 'is-empty-state' : '' }}">
  <div class="chat-page-inner">
    @if(session('success'))
      <div style="max-width:880px;margin:0 auto 1rem;background:#e8fff1;border:1px solid #bbf7d0;color:#166534;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="max-width:880px;margin:0 auto 1rem;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
        {{ session('error') }}
      </div>
    @endif

    @if(!$activeSession)
      <div class="chat-empty">
        <div class="chat-empty-icon">
          <i class="bi bi-chat-heart"></i>
        </div>
        <h2>Belum Ada Sesi Konseling Online Aktif</h2>
        <p>
          Menu chat akan aktif ketika pengajuan konseling online Anda sudah disetujui oleh konselor.
          Setelah itu, Anda bisa memulai percakapan langsung dari halaman ini.
        </p>
        <a href="{{ route('konseling') }}" class="btn-empty">
          <i class="bi bi-calendar2-check"></i>
          <span>Ajukan Konseling</span>
        </a>
      </div>
    @elseif($isBlockedBySchedule && ! $chatPayload)
      <div class="chat-shell">
        <div class="chat-main">
          <div class="chat-topbar">
            <div class="chat-counselor">
              <div class="chat-avatar-fallback">
                  {{ $inisialKonselorTampil }}
              </div>
              <div>
                  <div class="chat-title">{{ $namaKonselorTampil }}</div>
                  <p class="chat-subtitle">
                      Konseling online aktif
                  </p>
              </div>
            </div>
          </div>

          <div class="chat-gate">
            <div class="chat-gate-card">
              <div class="chat-gate-icon">
                <i class="bi bi-clock-history"></i>
              </div>
              <h2>Sesi Akan Dimulai Sesuai Jadwal</h2>
              <p>
                Sesi konseling online Anda sudah tercatat, tetapi ruang chat belum dapat diakses
                sebelum <strong>{{ $scheduledStartLabel }}</strong>.
                Setelah waktu tersebut tiba, Anda bisa kembali ke halaman ini untuk mulai masuk ke percakapan.
              </p>
              <button type="button" class="chat-start-btn" disabled>
                <i class="bi bi-hourglass-split"></i>
                <span>Menunggu Jadwal Sesi</span>
              </button>
            </div>
          </div>

          <!-- <div class="chat-composer is-locked">
            <form class="chat-form is-disabled">
              <textarea
                class="chat-input"
                rows="1"
                placeholder="Kolom chat akan aktif saat waktu sesi sudah tiba..."
                disabled
              ></textarea>
              <button type="button" class="chat-send" disabled>
                <i class="bi bi-send-fill"></i>
              </button>
            </form>
            <div class="chat-hint">
              Demi menjaga alur konseling, Anda belum bisa masuk ke kolom chat sebelum jadwal sesi dimulai.
            </div>
          </div>
        </div> -->

      </div>
    @elseif($startReady)
      <div class="chat-shell">
        <div class="chat-main">
          <div class="chat-topbar">
            <div class="chat-counselor">
              <div class="chat-avatar-fallback">
                  {{ $inisialKonselorTampil }}
              </div>
              <div>
                  <div class="chat-title">{{ $namaKonselorTampil }}</div>
                  <p class="chat-subtitle">
                      Konseling online aktif
                  </p>
              </div>
</div>

          <div class="chat-gate">
            <div class="chat-gate-card">
              <div class="chat-gate-icon">
                <i class="bi {{ $canStartNow ? 'bi-play-circle' : 'bi-clock-history' }}"></i>
              </div>
              <h2>{{ $canStartNow ? 'Sesi Sudah Disetujui' : 'Sesi Belum Bisa Dimulai' }}</h2>
              <p>
                @if($canStartNow)
                  Konselor sudah menyetujui sesi konseling online Anda. Tekan tombol <strong>Mulai Sesi</strong>
                  untuk mengaktifkan ruang chat realtime bersama konselor.
                @endif
              </p>
              <form action="{{ route('mahasiswa.chat.start') }}" method="POST">
                @csrf
                <button type="submit" class="chat-start-btn" {{ $canStartNow ? '' : 'disabled' }}>
                  <i class="bi bi-chat-dots-fill"></i>
                  <span>Mulai Sesi</span>
                </button>
              </form>
            </div>
          </div>

          <!-- <div class="chat-composer is-locked">
            <form class="chat-form is-disabled">
              <textarea
                class="chat-input"
                rows="1"
                placeholder="{{ $canStartNow ? 'Klik Mulai Sesi untuk mengaktifkan percakapan...' : 'Kolom chat terkunci sampai jadwal sesi dimulai...' }}"
                disabled
              ></textarea>
              <button type="button" class="chat-send" disabled>
                <i class="bi bi-send-fill"></i>
              </button>
            </form>
            <div class="chat-hint">
              {{ $canStartNow ? 'Chat akan aktif sesaat setelah Anda memulai sesi.' : 'Demi menjaga alur konseling, chat baru aktif saat waktu sesi sudah tiba.' }}
            </div>
          </div> -->
        </div>

      </div>
    @else
      <div class="chat-shell">
        <div class="chat-main">
          <div class="chat-topbar">
            <div class="chat-counselor">
              <div class="chat-avatar-fallback">
                  {{ $inisialKonselorTampil }}
              </div>
              <div>
                  <div class="chat-title">{{ $namaKonselorTampil }}</div>
                  <p class="chat-subtitle">
                      Konseling online aktif
                  </p>
              </div>
            </div>
          </div>

          <div class="chat-thread" id="chatThread"></div>

          <div class="chat-composer">
            <div id="chatReplyBar" class="chat-reply-bar">
                <div>
                    <span class="chat-reply-label" id="chatReplyLabel">Membalas pesan</span>
                    <div class="chat-reply-text" id="chatReplyText"></div>
                </div>
                <button type="button" class="chat-reply-cancel" id="chatReplyCancel" aria-label="Batalkan reply">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form id="chatForm" class="chat-form {{ $canSendChat ? '' : 'is-disabled' }}">
                <textarea
                    id="chatInput"
                    class="chat-input"
                    rows="1"
                    maxlength="2000"
                    placeholder="{{ $readOnlyChat ? 'Sesi konseling sudah selesai.' : ($isBlockedBySchedule ? 'Chat bisa dilakukan pada jam konseling yang sudah ditentukan...' : 'Tulis pesan Anda di sini...') }}"
                    {{ $canSendChat ? '' : 'disabled' }}
                ></textarea>

                <button type="submit" class="chat-send" id="chatSendBtn" {{ $canSendChat ? '' : 'disabled' }}>
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>

            <div id="chatHint" class="chat-hint">
                @if($readOnlyChat)
                    Pesan baru tidak dapat dikirim karena sesi konseling sudah selesai atau telah melewati batas waktu.
                @elseif($isBlockedBySchedule)
                    Anda tetap bisa melihat riwayat chat sebelumnya. Melakukan chat bisa dilakukan pada saat jam konseling yang sudah ditentukan oleh mahasiswa.
                @elseif(! $canSendChat)
                    Pesan baru dapat dikirim setelah penjadwalan konseling diterima.
                @endif
            </div>
        </div>
        </div>

      </div>
    @endif
  </div>
</section>
@endsection

@if($activeSession && $chatPayload)
@push('scripts')
<script>
(() => {
  const payload = @json($chatPayload);
  const isReadOnly = Boolean(payload.readOnly || payload.chatSelesai);
  const canSendMessage = @json($canSendChat);
  const currentUserName = @json(
      filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN)
          ? (
              method_exists(auth()->user(), 'getAnonimDisplayName')
                  ? trim(auth()->user()->getAnonimDisplayName())
                  : 'Anonim'
            )
          : (auth()->user()->nama ?? auth()->user()->name ?? 'Mahasiswa')
  );

  const currentUserAvatar = @json(asset('img/default-avatar.png'));
  const currentUserId = {{ auth()->id() }};
  let isSending = false;
  const thread = document.getElementById('chatThread');
  const form = document.getElementById('chatForm');
  const input = document.getElementById('chatInput');
  const sendBtn = document.getElementById('chatSendBtn');
  const hint = document.getElementById('chatHint');
  const replyBar = document.getElementById('chatReplyBar');
  const replyLabel = document.getElementById('chatReplyLabel');
  const replyText = document.getElementById('chatReplyText');
  const replyCancel = document.getElementById('chatReplyCancel');
  let replyTarget = null;

  const syncChatViewport = () => {
    const nav = document.getElementById('mainNav');
    const navHeight = Math.ceil(nav?.getBoundingClientRect().height || 82);
    document.documentElement.style.setProperty('--chat-nav-height', `${navHeight}px`);
  };

  syncChatViewport();
  window.addEventListener('resize', syncChatViewport);
  window.scrollTo({ top: 0, left: 0, behavior: 'auto' });

  if (!thread || !form || !input || !sendBtn) {
    return;
  }

  if (!canSendMessage || isReadOnly) {
      input.disabled = true;
      sendBtn.disabled = true;
      form.classList.add('is-disabled');

      if (hint) {
          hint.textContent = isReadOnly
              ? 'Pesan baru tidak dapat dikirim karena sesi konseling sudah selesai atau telah melewati batas waktu.'
              : @json($isBlockedBySchedule
                  ? 'Anda tetap bisa melihat riwayat chat sebelumnya. Melakukan chat bisa dilakukan pada saat jam konseling yang sudah ditentukan oleh mahasiswa.'
                  : 'Pesan baru dapat dikirim setelah penjadwalan konseling diterima.'
              );
      }
  }

  if (!canSendMessage && !isReadOnly && payload.scheduledStartAt) {
      const scheduledStartTime = new Date(payload.scheduledStartAt).getTime();
      const waitUntilStart = scheduledStartTime - Date.now();

      if (waitUntilStart > 0 && waitUntilStart <= 2147483647) {
          window.setTimeout(() => {
              window.location.reload();
          }, waitUntilStart + 1000);
      }
  }

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
  const dateFormatter = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    timeZone: 'Asia/Jakarta',
  });
  const dateKeyFormatter = new Intl.DateTimeFormat('en-CA', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    timeZone: 'Asia/Jakarta',
  });

  const resolveDateParts = (value) => {
    const date = value ? new Date(value) : new Date();
    const keyParts = Object.fromEntries(dateKeyFormatter.formatToParts(date).map((part) => [part.type, part.value]));

    return {
      key: `${keyParts.year}-${keyParts.month}-${keyParts.day}`,
      label: dateFormatter.format(date).toUpperCase(),
    };
  };

  const scrollToBottom = (behavior = 'auto') => {
    requestAnimationFrame(() => {
      thread.scrollTo({ top: thread.scrollHeight, behavior });
    });
  };

  const isNearThreadBottom = (threshold = 120) => (
    thread.scrollHeight - thread.scrollTop - thread.clientHeight <= threshold
  );

  const restoreThreadOffsetFromBottom = (offsetFromBottom) => {
    requestAnimationFrame(() => {
      thread.scrollTop = Math.max(0, thread.scrollHeight - thread.clientHeight - offsetFromBottom);
    });
  };

  const resetInputHeight = () => {
    input.style.height = '44px';
  };

  const autoResize = () => {
      input.style.height = '44px';

      if (input.value.trim() !== '') {
          input.style.height = `${Math.min(input.scrollHeight, 100)}px`;
      }
  };

  const lastRenderedDateKey = () => Array.from(thread.querySelectorAll('[data-date-key]')).pop()?.dataset.dateKey || null;

  const renderDateSeparator = (label, key) => {
    const separator = document.createElement('div');
    separator.className = 'chat-date-pill';
    separator.dataset.dateKey = key;
    separator.textContent = label;
    thread.appendChild(separator);
  };

  const ensureDateSeparator = (key, label) => {
    if (!key || lastRenderedDateKey() === key) {
      return;
    }

    renderDateSeparator(label, key);
  };

  const messageUpdateUrl = (messageId) => payload.updateUrlTemplate.replace('__CHAT_ID__', String(messageId));
  const messageDeleteUrl = (messageId) => payload.deleteUrlTemplate.replace('__CHAT_ID__', String(messageId));
  const limitPreview = (value, max = 68) => {
    const text = String(value ?? '').replace(/\s+/g, ' ').trim();
    return text.length > max ? `${text.slice(0, max - 1)}…` : text;
  };

  const closeAllMenus = () => {
    thread.querySelectorAll('.message-row.is-menu-open').forEach((element) => {
      element.classList.remove('is-menu-open');
    });
  };

  const clearReplyTarget = () => {
    replyTarget = null;
    replyBar?.classList.remove('is-active');
    if (replyText) {
      replyText.textContent = '';
    }
  };

  const setReplyTarget = (message) => {
    if (!message?.id || !canSendMessage || isReadOnly) {
      return;
    }

    replyTarget = {
      id: message.id,
      sender_name: message.sender_name || 'Pengguna',
      text: message.text || '',
    };

    if (replyLabel) {
      replyLabel.textContent = `Membalas ${replyTarget.sender_name}`;
    }

    if (replyText) {
      replyText.textContent = limitPreview(replyTarget.text);
    }

    replyBar?.classList.add('is-active');
    input.focus();
  };

  replyCancel?.addEventListener('click', clearReplyTarget);

  const buildReplyPreviewMarkup = (replyTo) => replyTo
    ? `<div class="message-reply-preview"><span class="message-reply-name">${escapeHtml(replyTo.sender_name || 'Pengguna')}</span><span>${escapeHtml(limitPreview(replyTo.text || '')).replace(/\n/g, '<br>')}</span></div>`
    : '';

  const buildMessageActionsMarkup = (message, isMine) => `
    ${!message.is_pending && canSendMessage && !isReadOnly ? `
      <div class="message-actions">
        <button type="button" class="message-action-toggle" data-action="toggle-menu" aria-label="Opsi pesan">
          <i class="bi bi-chevron-down"></i>
        </button>
        <div class="message-action-menu">
          <button type="button" class="message-action-item" data-action="reply-message" data-message-id="${message.id}">
            <i class="bi bi-reply-fill"></i>
            <span>Reply</span>
          </button>
          ${isMine ? `
          <button type="button" class="message-action-item" data-action="edit-message" data-message-id="${message.id}">
            <i class="bi bi-pencil-square"></i>
            <span>Edit</span>
          </button>
          <button type="button" class="message-action-item delete" data-action="delete-message" data-message-id="${message.id}">
            <i class="bi bi-trash3"></i>
            <span>Hapus</span>
          </button>
          ` : ''}
        </div>
      </div>
    ` : ''}
  `;

  // Bubble dan editor inline dipisah supaya mode edit terasa seperti chat modern, bukan popup.
  const buildMessageBubbleMarkup = (message, isMine) => `
    <div class="message-bubble">${buildReplyPreviewMarkup(message.reply_to)}<span class="message-text">${escapeHtml(message.text).replace(/\n/g, '<br>')}</span></div>
  `;

  const buildInlineEditorMarkup = (text, messageId) => `
    <div class="message-editor-shell" data-editing-message-id="${messageId}">
      <textarea class="message-editor-input" maxlength="2000">${escapeHtml(text)}</textarea>
      <div class="message-editor-actions">
        <button type="button" class="message-editor-btn cancel" data-action="cancel-edit" data-message-id="${messageId}">Batal</button>
        <button type="button" class="message-editor-btn save" data-action="save-edit" data-message-id="${messageId}">Simpan</button>
      </div>
    </div>
  `;

  const buildDeleteConfirmMarkup = (messageId, text = '') => `
    <div class="message-delete-confirm" data-delete-message-id="${messageId}">
      <div class="message-delete-preview">${escapeHtml(text).replace(/\n/g, '<br>')}</div>
      <div class="message-delete-confirm-text">Hapus pesan ini secara permanen?</div>
      <div class="message-delete-confirm-actions">
        <button type="button" class="message-delete-confirm-btn cancel" data-action="cancel-delete" data-message-id="${messageId}">Batal</button>
        <button type="button" class="message-delete-confirm-btn delete" data-action="confirm-delete" data-message-id="${messageId}">Hapus</button>
      </div>
    </div>
  `;

  // Polling ditahan saat user sedang edit atau konfirmasi hapus agar isi bubble tidak tertimpa.
  const hasActiveInlineState = () => Boolean(
    thread.querySelector('.message-row.is-editing, [data-delete-message-id]')
  );

  // Bubble asli bisa dipulihkan lagi setelah batal edit atau batal hapus.
  const restoreMessageBubble = (row) => {
    const bubbleShell = row.querySelector('.message-bubble-shell');
    const isMine = row.classList.contains('mine');

    if (!bubbleShell) {
      return;
    }

    bubbleShell.innerHTML = buildMessageBubbleMarkup({
      id: Number(row.dataset.messageId),
      text: row.dataset.messageText ?? '',
      reply_to: row.dataset.replyTo ? JSON.parse(row.dataset.replyTo) : null,
    }, isMine);
    row.classList.remove('is-editing');
    row.classList.remove('is-menu-open');
  };

  const renderMessage = (message) => {
    const row = document.createElement('div');
    const isMine = Boolean(message.is_mine ?? (message.sender_id === currentUserId));
    const dateParts = resolveDateParts(message.sent_at);

    ensureDateSeparator(dateParts.key, dateParts.label);

    row.className = `message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;
    row.dataset.messageText = message.text ?? '';
    row.dataset.senderName = message.sender_name || (isMine ? 'Anda' : 'Pengguna');
    row.dataset.replyTo = message.reply_to ? JSON.stringify(message.reply_to) : '';
    row.dataset.messageEdited = message.is_edited ? '1' : '0';

    const counselorDisplayName = payload.counselorName || @json(env('CIS_KONSELOR_NAME', 'Konselor'));

    const displaySenderName = isMine
        ? ''
        : counselorDisplayName;

    const avatarInitial = isMine
        ? ''
        : String(displaySenderName || 'K').charAt(0).toUpperCase();

    const senderNameHtml = isMine
        ? ''
        : `<span class="message-name">${escapeHtml(displaySenderName)}</span>`;

    row.innerHTML = `
      ${isMine ? '' : `
        <div class="message-avatar-fallback">
          ${escapeHtml(avatarInitial)}
        </div>
      `}

      <div class="message-content">
        <div class="message-meta">
          ${senderNameHtml}
          <span>${escapeHtml(message.time)}</span>
          ${message.is_pending ? '<span class="admin-message-edited">mengirim...</span>' : (message.is_edited ? '<span class="admin-message-edited">telah diedit</span>' : '')}
          ${buildMessageActionsMarkup(message, isMine)}
        </div>

        <div class="message-bubble-shell">${buildMessageBubbleMarkup(message, isMine)}</div>
      </div>
    `;

    thread.appendChild(row);
    return row;
};

  const renderInitialMessages = () => {
    renderMessages(payload.messages || [], true, { scrollToBottom: true });
  };

  const renderMessages = (messages, force = false, options = {}) => {
    // Render ulang penuh agar edit dan delete tersinkron untuk semua client.
    if (!force && hasActiveInlineState()) {
      return;
    }

    const shouldScrollToBottom = Boolean(options.scrollToBottom) || isNearThreadBottom();
    const offsetFromBottom = thread.scrollHeight - thread.scrollTop - thread.clientHeight;

    thread.innerHTML = '';
    messages.forEach((message) => renderMessage(message));
    closeAllMenus();

    if (shouldScrollToBottom) {
      scrollToBottom();
      return;
    }

    restoreThreadOffsetFromBottom(offsetFromBottom);
  };

  // Force dipakai setelah simpan/hapus sukses supaya hasil server langsung mengganti state inline.
  const syncMessages = async (force = false) => {
    try {
      const response = await fetch(`${payload.messagesUrl}?sesi_id=${payload.sessionId}&jadwal_id=${payload.jadwalId ?? ''}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      });

      if (!response.ok) {
        return;
      }

      const data = await response.json();

      if (!data.success || !Array.isArray(data.messages)) {
        return;
      }

      renderMessages(data.messages, force);
    } catch (error) {
      console.error(error);
    }
  };

  renderInitialMessages();
  autoResize();

  if (window.Echo) {
    window.Echo.private(payload.channel)
      .listen('.chat.message.sent', (event) => {
        if (!event?.message) {
          return;
        }

        const exists = thread.querySelector(`[data-message-id="${event.message.id}"]`);
        if (exists) {
          return;
        }

        const shouldScrollToBottom = isNearThreadBottom();

        renderMessage({
          ...event.message,
          is_mine: Number(event.message.sender_id) === currentUserId,
        });

        if (shouldScrollToBottom || Number(event.message.sender_id) === currentUserId) {
          scrollToBottom();
        }
      })                          // ← pakai koma, BUKAN titik koma
      .listen('.sesi.selesai', (event) => {
        input.disabled = true;
        input.placeholder = 'Sesi konseling telah selesai.';
        sendBtn.disabled = true;

        const chatForm = document.getElementById('chatForm');
        if (chatForm) {
            chatForm.classList.add('is-disabled');
        }

        const badge = document.querySelector('.chat-badge');
        if (badge) {
            badge.textContent = 'Selesai';
            badge.style.background = '#f1f5f9';
            badge.style.color = '#475569';
        }

        const composer = document.querySelector('.chat-composer');
        if (composer) {
            const notice = document.createElement('div');
            notice.style.cssText = 'margin-top:.5rem;padding:.65rem .9rem;background:#f1f5f9;border-radius:12px;color:#475569;font-size:.78rem;font-weight:600;text-align:center;';
            notice.textContent = 'Sesi konseling telah diselesaikan oleh konselor. Terima kasih.';
            composer.appendChild(notice);
        }
      });
}

  syncMessages();
  window.setInterval(syncMessages, 10000);

  if (canSendMessage && !isReadOnly) {
    input.addEventListener('input', autoResize);

    const submitChatMessage = async () => {
        const pesan = input.value.trim();

        if (!pesan) {
            input.value = '';
            autoResize();
            return;
        }

        if (isSending) {
            return;
        }

        isSending = true;
        sendBtn.disabled = true;
        const activeReplyTarget = replyTarget;

        const tempId = `temp-${Date.now()}`;
        const now = new Date();

        const tempMessage = {
            id: tempId,
            sesi_id: payload.sessionId,
            sender_id: currentUserId,
            sender_name: 'Anda',
            text: pesan,
            time: now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
            }),
            sent_at: now.toISOString(),
            updated_at: now.toISOString(),
            is_edited: false,
            is_mine: true,
            is_pending: true,
            reply_to: activeReplyTarget,
        };

        const tempRow = renderMessage(tempMessage);
        scrollToBottom();

        input.value = '';
        autoResize();

        if (hint) {
            hint.textContent = '';
        }

        try {
            const response = await fetch(payload.sendUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    sesi_id: payload.sessionId,
                    jadwal_id: payload.jadwalId,
                    pesan: pesan,
                    reply_to_id: activeReplyTarget?.id ?? null,
                }),
            });

            const rawText = await response.text();
            let data = {};

            try {
                data = rawText ? JSON.parse(rawText) : {};
            } catch (error) {
                console.error(rawText);
                tempRow?.remove();
                input.value = pesan;
                autoResize();

                if (hint) {
                    hint.textContent = 'Server mengembalikan response tidak valid.';
                }

                return;
            }

            if (!response.ok || !data.success) {
                tempRow?.remove();
                input.value = pesan;
                autoResize();

                if (hint) {
                    hint.textContent = data.message ?? 'Pesan gagal dikirim.';
                }

                return;
            }

            tempRow?.remove();
            renderMessage(data.message);
            clearReplyTarget();
            scrollToBottom();

        } catch (error) {
            console.error(error);
            tempRow?.remove();
            input.value = pesan;
            autoResize();

            if (hint) {
                hint.textContent = 'Terjadi kendala saat mengirim pesan.';
            }
        } finally {
            isSending = false;
            sendBtn.disabled = false;
            input.focus();
        }
    };

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            submitChatMessage();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submitChatMessage();
    });
} else {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
    });
}
  

  document.addEventListener('click', (event) => {
    if (!thread.contains(event.target)) {
      closeAllMenus();
    }
  });


  thread.addEventListener('click', async (event) => {
    const toggleButton = event.target.closest('[data-action="toggle-menu"]');
    const replyButton = event.target.closest('[data-action="reply-message"]');
    const editButton = event.target.closest('[data-action="edit-message"]');
    const deleteButton = event.target.closest('[data-action="delete-message"]');
    const saveButton = event.target.closest('[data-action="save-edit"]');
    const cancelButton = event.target.closest('[data-action="cancel-edit"]');
    const cancelDeleteButton = event.target.closest('[data-action="cancel-delete"]');
    const confirmDeleteButton = event.target.closest('[data-action="confirm-delete"]');

    if (toggleButton) {
      const row = toggleButton.closest('.message-row');
      const willOpen = !row.classList.contains('is-menu-open');
      closeAllMenus();
      row.classList.toggle('is-menu-open', willOpen);
      return;
    }

    if (replyButton) {
      const row = replyButton.closest('.message-row');
      closeAllMenus();

      if (!row) {
        return;
      }

      setReplyTarget({
        id: Number(row.dataset.messageId),
        sender_name: row.dataset.senderName || (row.classList.contains('mine') ? 'Anda' : 'Pengguna'),
        text: row.dataset.messageText ?? '',
      });
      return;
    }

    if (editButton) {
      const messageId = Number(editButton.dataset.messageId);
      const row = editButton.closest('.message-row');
      const bubbleShell = row?.querySelector('.message-bubble-shell');
      const currentText = row?.dataset.messageText ?? '';

      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      row.classList.add('is-editing');
      bubbleShell.innerHTML = buildInlineEditorMarkup(currentText, messageId);
      const textarea = bubbleShell.querySelector('.message-editor-input');
      if (textarea) {
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
      }
      return;
    }

    if (cancelButton) {
      const row = cancelButton.closest('.message-row');
      if (row) {
        restoreMessageBubble(row);
      }
      return;
    }

    if (cancelDeleteButton) {
      const row = cancelDeleteButton.closest('.message-row');
      if (row) {
        restoreMessageBubble(row);
      }
      return;
    }

    if (saveButton) {
      const messageId = Number(saveButton.dataset.messageId);
      const row = saveButton.closest('.message-row');
      const textarea = row?.querySelector('.message-editor-input');
      const currentText = row?.dataset.messageText ?? '';
      const pesan = textarea?.value?.trim() ?? '';

      if (!row || !textarea) {
        return;
      }

      if (!pesan) {
        hint.textContent = 'Pesan tidak boleh kosong.';
        textarea.focus();
        return;
      }

      if (pesan === currentText.trim()) {
        restoreMessageBubble(row);
        return;
      }

      try {
        const response = await fetch(messageUpdateUrl(messageId), {
          method: 'PATCH',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({ pesan }),
        });

        const data = await response.json();
        hint.textContent = response.ok && data.success
          ? 'Pesan berhasil diedit.'
          : (data.message ?? 'Pesan gagal diedit.');

        if (response.ok && data.success) {
          syncMessages(true);
        }
      } catch (error) {
        console.error(error);
        hint.textContent = 'Terjadi kendala saat mengedit pesan.';
      }

      return;
    }

    if (deleteButton) {
      const messageId = Number(deleteButton.dataset.messageId);
      const row = deleteButton.closest('.message-row');
      const bubbleShell = row?.querySelector('.message-bubble-shell');
      const currentText = row?.dataset.messageText ?? '';
      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      bubbleShell.innerHTML = buildDeleteConfirmMarkup(messageId, currentText);
      return;
    }

    if (confirmDeleteButton) {
      const messageId = Number(confirmDeleteButton.dataset.messageId);

      try {
        const response = await fetch(messageDeleteUrl(messageId), {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        const data = await response.json();
        hint.textContent = response.ok && data.success
          ? 'Pesan berhasil dihapus.'
          : (data.message ?? 'Pesan gagal dihapus.');

        if (response.ok && data.success) {
          syncMessages(true);
        }
      } catch (error) {
        console.error(error);
        hint.textContent = 'Terjadi kendala saat menghapus pesan.';
      }

      return;
    }
  });
})();
</script>
@endpush
@endif
