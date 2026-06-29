@extends('layouts.master')

@push('styles')
<style>
  .group-room-page {
    min-height: calc(100vh - 88px);
    background:
      radial-gradient(circle at top left, rgba(16, 185, 129, 0.16), transparent 24%),
      radial-gradient(circle at top right, rgba(253, 230, 138, 0.16), transparent 22%),
      linear-gradient(180deg, #effcf5 0%, #f8fffb 24%, #ffffff 58%);
    padding: 1rem 0;
    overflow: hidden;
  }

  .group-room-back {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    margin-bottom: 1rem;
    text-decoration: none;
    color: #065f46;
    font-size: .86rem;
    font-weight: 800;
  }

  .group-room-back:hover {
    color: #047857;
  }

  .group-room-switcher {
    display: flex;
    gap: .75rem;
    overflow-x: auto;
    padding-bottom: .3rem;
    margin-bottom: 1rem;
  }

  .group-room-chip {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    flex-shrink: 0;
    text-decoration: none;
    border-radius: 999px;
    border: 1px solid rgba(209, 250, 229, 0.92);
    background: rgba(255, 255, 255, 0.92);
    color: #065f46;
    padding: .72rem 1rem;
    font-size: .82rem;
    font-weight: 700;
    box-shadow: 0 12px 26px rgba(6, 78, 59, 0.06);
  }

  .group-room-chip.active {
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    border-color: transparent;
  }

  .group-room-shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    border-radius: 30px;
    border: 1px solid rgba(209, 250, 229, 0.92);
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 18px 50px rgba(6, 78, 59, 0.08);
    overflow: hidden;
  }

  .group-room-stage {
    position: relative;
    min-height: 0;
  }

  .group-room-main {
    min-width: 0;
    height: calc(100vh - 160px);
    min-height: 520px;
    max-height: calc(100vh - 160px);
    display: flex;
    flex-direction: column;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 24%),
      linear-gradient(180deg, #f6fff9 0%, #ffffff 18%, #ffffff 100%);
  }

  .group-room-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.2rem 1.35rem;
    border-bottom: 1px solid rgba(221, 239, 231, 0.95);
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 6;
  }

  .group-room-head-main {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
  }

  .group-room-avatar {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    font-size: 1.4rem;
    box-shadow: 0 10px 24px rgba(6, 78, 59, 0.16);
    flex-shrink: 0;
  }

  .group-room-title {
    font-size: 1.18rem;
    font-weight: 800;
    color: #064e3b;
    margin: 0 0 .18rem;
  }

  .group-room-subtitle {
    margin: 0;
    color: #4b7a68;
    font-size: .9rem;
    line-height: 1.6;
  }

  .group-room-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    justify-content: flex-end;
    position: relative;
    min-width: 0;
  }

  .group-room-active {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .7rem 1rem;
    border-radius: 999px;
    background: #e7fff0;
    color: #047857;
    font-size: .8rem;
    font-weight: 700;
    white-space: nowrap;
    justify-content: center;
    width: 100%;
  }

  .group-room-active::before {
    content: "";
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
  }

  .group-room-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .22rem;
    width: 52px;
    height: 44px;
    border: 1px solid rgba(209, 250, 229, 0.96);
    border-radius: 16px;
    padding: 0;
    background: #fff;
    color: #065f46;
    font-size: 1.16rem;
    font-weight: 800;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
  }

  .group-room-toggle:hover {
    transform: translateY(-1px);
    background: #f8fffb;
    box-shadow: 0 14px 24px rgba(6, 78, 59, 0.08);
  }

  .group-room-toggle-chevron {
    font-size: .78rem;
    transition: transform .18s ease;
  }

  .group-room-stage.is-profile-open .group-room-toggle-chevron {
    transform: rotate(180deg);
  }

  .group-room-toggle-text {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
  }

  .group-room-thread {
    flex: 1;
    min-height: 0;
    padding: 1.4rem 1.4rem 4.75rem;
    overflow-y: auto;
    overscroll-behavior: contain;
    background:
      linear-gradient(180deg, rgba(248, 255, 251, 0.72), rgba(255, 255, 255, 0.98)),
      radial-gradient(circle at center, rgba(209, 250, 229, 0.34), transparent 42%);
  }

  .group-room-date {
    width: fit-content;
    margin: 0 auto 1.4rem;
    padding: .55rem 1rem;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid rgba(221, 239, 231, 0.95);
    color: #64748b;
    font-size: .76rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
  }

  .group-message-row {
    display: flex;
    gap: .8rem;
    margin-bottom: 1rem;
    align-items: flex-end;
  }

  .group-message-row.mine {
    justify-content: flex-end;
  }

  .group-message-row.mine .group-message-meta {
    justify-content: flex-end;
  }

  .group-message-row.mine .group-message-bubble {
    background: linear-gradient(135deg, #047857, #059669);
    color: #fff;
    border-bottom-right-radius: 10px;
    box-shadow: 0 16px 34px rgba(5, 150, 105, 0.2);
  }

  .group-message-row.other .group-message-bubble {
    background: #ffffff;
    color: #1f2937;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-bottom-left-radius: 10px;
  }

  .group-message-row.system {
    justify-content: center;
    margin: .85rem 0 1.05rem;
  }

  .group-message-row.system .group-message-content {
    max-width: min(92%, 520px);
  }

  .group-message-row.system .group-message-bubble {
    border-radius: 999px;
    border: 1px solid rgba(187, 247, 208, .95);
    background: #f0fdf4;
    color: #166534;
    padding: .55rem .9rem;
    font-size: .78rem;
    font-weight: 800;
    text-align: center;
    box-shadow: none;
  }

  .group-message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    flex-shrink: 0;
  }

  .group-message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .group-message-content {
    max-width: min(78%, 640px);
    position: relative;
  }

  .group-message-meta {
    display: flex;
    align-items: center;
    gap: .55rem;
    margin: 0 .35rem .35rem;
    color: #64748b;
    font-size: .76rem;
  }

  .group-message-name {
    font-weight: 700;
    color: #064e3b;
  }

  .group-message-bubble {
    padding: .95rem 1.15rem 1rem;
    border-radius: 24px;
    font-size: .95rem;
    line-height: 1.72;
    word-break: break-word;
  }

  .group-message-bubble-shell {
    position: relative;
  }

  .group-message-edited {
    font-size: .68rem;
    color: #94a3b8;
    font-weight: 600;
  }

  .group-message-actions {
    position: absolute;
    top: .55rem;
    right: .7rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity .18s ease;
  }

  .group-message-row.mine:hover .group-message-actions,
  .group-message-row.mine.is-menu-open .group-message-actions {
    opacity: 1;
    pointer-events: auto;
  }

  .group-message-action-toggle {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    color: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .group-message-action-menu {
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

  .group-message-row:last-child .group-message-action-menu {
    top: calc(100% + .45rem);
  }

  .group-message-row.is-menu-open .group-message-action-menu {
    display: block;
  }

  .group-message-action-item {
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

  .group-message-action-item:hover {
    background: #f8fffb;
  }

  .group-message-action-item.delete {
    color: #b91c1c;
  }

  .group-message-row.is-editing .group-message-actions {
    display: none;
  }

  .group-message-editor-shell {
    display: grid;
    gap: .7rem;
  }

  .group-message-editor-input {
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

  .group-message-editor-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .group-message-editor-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .group-message-editor-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .group-message-editor-btn.save {
    background: #065f46;
    color: #fff;
  }

  .group-message-delete-confirm {
    display: grid;
    gap: .75rem;
  }

  .group-message-delete-preview {
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

  .group-message-delete-confirm-text {
    font-size: .83rem;
    line-height: 1.6;
    color: #334155;
  }

  .group-message-delete-confirm-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .group-message-delete-confirm-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .group-message-delete-confirm-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .group-message-delete-confirm-btn.delete {
    background: #b91c1c;
    color: #fff;
  }

  .group-room-compose {
    padding: 1rem 1.2rem 1.2rem;
    border-top: 1px solid rgba(221, 239, 231, 0.95);
    background: rgba(255, 255, 255, 0.95);
  }

  .group-room-form {
    display: flex;
    align-items: flex-end;
    gap: .9rem;
    padding: .75rem;
    border-radius: 24px;
    border: 1px solid rgba(209, 250, 229, 0.96);
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .group-room-input {
    flex: 1;
    border: none;
    resize: none;
    background: transparent;
    min-height: 56px;
    max-height: 160px;
    padding: .6rem .35rem;
    color: #0f172a;
    font-size: .95rem;
    outline: none;
  }

  .group-room-send {
    width: 56px;
    height: 56px;
    border: none;
    border-radius: 18px;
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    font-size: 1.4rem;
    box-shadow: 0 16px 30px rgba(6, 95, 70, 0.24);
  }

  .group-message-avatar-fallback {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #b7ebc9;
    color: #065f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
  }

  .group-room-send:disabled {
    opacity: .5;
    cursor: not-allowed;
    box-shadow: none;
  }

  .group-room-profile {
    position: absolute;
    top: calc(100% + .65rem);
    right: 0;
    width: 320px;
    max-width: calc(100vw - 2rem);
    max-height: 0;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-6px);
    overflow: hidden;
    border: 1px solid rgba(221, 239, 231, 0.95);
    border-radius: 18px;
    background: linear-gradient(180deg, #fbfffd, #f7fcf9);
    box-shadow: 0 18px 38px rgba(15, 23, 42, .12);
    transition: max-height .24s ease, opacity .18s ease, transform .18s ease;
    z-index: 30;
  }

  .group-room-stage.is-profile-open .group-room-profile {
    max-height: min(540px, 70vh);
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
  }

  .group-room-profile-head {
    padding: 1rem 1.1rem .85rem;
    border-bottom: 1px solid rgba(221, 239, 231, 0.95);
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 28%),
      linear-gradient(180deg, #f6fff9, #ffffff);
  }

  .group-room-profile-head h3 {
    margin: 0 0 .28rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0f172a;
  }

  .group-room-profile-head p {
    margin: 0;
    color: #4b7a68;
    font-size: .84rem;
    line-height: 1.6;
  }

  .group-room-profile-body {
    padding: .95rem 1.1rem 1.05rem;
    overflow-y: auto;
    max-height: min(430px, 58vh);
  }

  .group-member-search {
    position: relative;
    margin-bottom: .9rem;
  }

  .group-member-search i {
    position: absolute;
    left: .82rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
    pointer-events: none;
  }

  .group-member-search input {
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 13px;
    padding: .68rem .85rem .68rem 2.35rem;
    background: #fff;
    color: #0f172a;
    font-size: .84rem;
    outline: none;
  }

  .group-member-search input:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .group-member-list {
    display: grid;
    gap: .58rem;
  }

  .group-member-avatar-fallback {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #b7ebc9;
    color: #065f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    font-weight: 800;
    flex-shrink: 0;
  }

  .group-member-item {
    display: flex;
    align-items: center;
    gap: .72rem;
    padding: .3rem 0;
  }

  .group-member-avatar {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    background: #e8fff1;
  }

  .group-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .group-member-name {
    font-size: .92rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
  }

  .group-member-empty {
    display: none;
    padding: .25rem 0;
    color: #64748b;
    font-size: .84rem;
    line-height: 1.6;
  }

  .group-leave-wrap {
    margin-top: .9rem;
    padding-top: .9rem;
    border-top: 1px solid rgba(221, 239, 231, 0.95);
  }

  .group-leave-button {
    width: 100%;
    border: 1px solid #fecdd3;
    border-radius: 14px;
    background: #fff1f2;
    color: #be123c;
    padding: .72rem .9rem;
    font-size: .84rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
  }

  .group-leave-modal {
    position: fixed;
    inset: 0;
    z-index: 2200;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, .45);
  }

  .group-leave-modal.show {
    display: flex;
  }

  .group-leave-dialog {
    width: min(420px, 100%);
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid rgba(254, 205, 211, .95);
    box-shadow: 0 24px 60px rgba(15, 23, 42, .2);
    padding: 1.2rem;
  }

  .group-leave-dialog h3 {
    margin: 0 0 .5rem;
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 900;
  }

  .group-leave-dialog p {
    margin: 0;
    color: #475569;
    font-size: .9rem;
    line-height: 1.65;
  }

  .group-leave-actions {
    display: flex;
    justify-content: flex-end;
    gap: .7rem;
    margin-top: 1rem;
    flex-wrap: wrap;
  }

  .group-leave-actions button,
  .group-leave-actions form button {
    border: none;
    border-radius: 999px;
    padding: .62rem 1rem;
    font-size: .82rem;
    font-weight: 800;
  }

  .group-leave-cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .group-leave-confirm {
    background: #be123c;
    color: #ffffff;
  }

  .group-animal-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #dcfce7;
    border: 3px solid #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 6px 18px rgba(15, 118, 110, 0.12);
    flex-shrink: 0;
  }

  .group-member-avatar,
  .group-member-avatar-fallback,
  .group-animal-avatar {
    width: 42px !important;
    height: 42px !important;
    border-radius: 50% !important;
    background: #d1fae5;
    color: #065f46;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 18px;
    border: 3px solid #ffffff;
    box-shadow: 0 8px 20px rgba(6, 78, 59, 0.12);
    flex-shrink: 0;
    overflow: hidden;
  }

  .group-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  @media (max-width: 767.98px) {
    .group-room-page {
      padding-top: 1.25rem;
    }

    .group-room-shell {
      border-radius: 24px;
    }

    .group-room-main {
      min-height: 680px;
    }

    .group-room-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-room-actions,
    .group-room-active {
      width: 100%;
      justify-content: flex-start;
    }

    .group-room-profile {
      left: 0;
      right: auto;
      width: 100%;
      max-width: 100%;
    }

    .group-message-content {
      max-width: 100%;
    }
  }
</style>
@endpush

@section('konten')

@php
    $isPrivateRoom = ($chatPayload['isPrivate'] ?? false)
        || ($chatPayload['is_private'] ?? false)
        || str_contains(strtolower($chatPayload['topicLabel'] ?? ''), 'privat');
    $counselorName = trim((string) ($chatPayload['counselorName'] ?? env('CIS_KONSELOR_NAME', 'Konselor'))) ?: 'Konselor';

    $cleanMemberName = function ($name) use ($isPrivateRoom) {
        $name = trim((string) $name);

        if ($name === '') {
            return $isPrivateRoom ? 'Mahasiswa' : 'Mahasiswa Anonim';
        }

        if ($isPrivateRoom) {
            return trim(preg_replace('/\s+Anonim$/i', '', $name));
        }

        if (str_contains(strtolower($name), 'anonim')) {
            return $name;
        }

        return $name . ' Anonim';
    };

    $animalIcon = function ($name) {
        $name = strtolower((string) $name);

        $icons = [
            'beruang' => '🐻',
            'kucing' => '🐱',
            'kelinci' => '🐰',
            'rubah' => '🦊',
            'panda' => '🐼',
            'koala' => '🐨',
            'harimau' => '🐯',
            'singa' => '🦁',
            'anjing' => '🐶',
            'burung' => '🐦',
            'kura' => '🐢',
            'monyet' => '🐵',
        ];

        foreach ($icons as $keyword => $icon) {
            if (str_contains($name, $keyword)) {
                return $icon;
            }
        }

        return '👤';
    };

    $displayMemberNames = collect($chatPayload['memberNames'] ?? [])
        ->map(fn ($name) => $cleanMemberName($name))
        ->filter()
        ->values()
        ->all();
@endphp

<section class="group-room-page">
  <div class="container">

    @if(session('error'))
      <div style="max-width:1180px;margin:0 auto 1rem;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
        {{ session('error') }}
      </div>
    @endif

    <a href="{{ route('mahasiswa.group-chat') }}" class="group-room-back">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali ke daftar grup</span>
    </a>

    <div class="group-room-shell">
      <div class="group-room-stage" id="groupRoomStage">
        <section class="group-room-main">
          <div class="group-room-head">
            <div class="group-room-head-main">
              <div class="group-room-avatar">
                <i class="bi bi-people-fill"></i>
              </div>
              <div>
                <h1 class="group-room-title">{{ $chatPayload['roomTitle'] }}</h1>
                <p class="group-room-subtitle">
                  {{ implode(', ', array_slice($displayMemberNames, 0, 4)) }}{{ count($displayMemberNames) > 4 ? ' dan lainnya' : '' }}
                </p>
              </div>
            </div>

            <div class="group-room-actions">
              <button type="button" class="group-room-toggle" id="groupRoomProfileToggle" aria-expanded="false" aria-controls="groupRoomProfile" aria-label="Lihat anggota grup" title="Lihat anggota grup">
                <i class="bi bi-people"></i>
                <i class="bi bi-chevron-down group-room-toggle-chevron"></i>
                <span class="group-room-toggle-text">Lihat anggota grup</span>
              </button>

              <aside class="group-room-profile" id="groupRoomProfile">
                <div class="group-room-profile-head">
                  <h3>Anggota Grup</h3>
                </div>

                <div class="group-room-profile-body">
                  <div class="group-member-search">
                    <i class="bi bi-search"></i>
                    <input
                      type="search"
                      id="groupRoomMemberSearchInput"
                      placeholder="Cari anggota..."
                      autocomplete="off"
                    >
                  </div>

                  <div class="group-member-list" id="groupRoomMemberList">
                    <div class="group-member-item" data-member-name="{{ \Illuminate\Support\Str::lower($counselorName) }}">
                      <div class="group-member-avatar-fallback">K</div>
                      <div class="group-member-name">{{ $counselorName }}</div>
                    </div>

                    @foreach(($chatPayload['memberProfiles'] ?? []) as $memberProfile)
                      @php
                          $memberName = $cleanMemberName($memberProfile['name'] ?? 'Mahasiswa');
                          $memberRole = strtolower((string) ($memberProfile['role'] ?? ''));
                          $isKonselorMember = in_array($memberRole, ['konselor', 'admin'], true);
                          $avatarUrl = $memberProfile['avatar_url'] ?? null;
                          $initial = strtoupper(mb_substr($memberName, 0, 1));
                      @endphp

                      <div class="group-member-item" data-member-name="{{ \Illuminate\Support\Str::lower($memberName) }}">
                        @if($isKonselorMember)
                          <div class="group-member-avatar-fallback">K</div>
                        @elseif(! $isPrivateRoom)
                          <div class="group-animal-avatar">
                            {{ $animalIcon($memberName) }}
                          </div>
                        @elseif(!empty($avatarUrl))
                          <div class="group-member-avatar">
                            <img src="{{ $avatarUrl }}" alt="{{ $memberName }}">
                          </div>
                        @else
                          <div class="group-member-avatar-fallback">
                            {{ $initial }}
                          </div>
                        @endif

                        <div class="group-member-name">{{ $memberName }}</div>
                      </div>
                    @endforeach
                  </div>

                  <div class="group-member-empty" id="groupRoomMemberEmpty">Anggota tidak ditemukan.</div>

                  @if(! $isPrivateRoom)
                    <div class="group-leave-wrap">
                      <button type="button" class="group-leave-button" id="groupLeaveOpenBtn">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar Grup</span>
                      </button>
                    </div>
                  @endif
                </div>
              </aside>
            </div>
          </div>

          <div class="group-room-thread" id="groupChatThread"></div>

          <div class="group-room-compose">
            <form id="groupChatForm" class="group-room-form">
              <textarea
                id="groupChatInput"
                class="group-room-input"
                rows="1"
                maxlength="2000"
                placeholder="Tulis pesan untuk grup konseling ini..."
              ></textarea>

              <button type="submit" class="group-room-send" id="groupChatSendBtn">
                <i class="bi bi-send-fill"></i>
              </button>
            </form>
          </div>
        </section>
      </div>
    </div>
  </div>
</section>

@if(! $isPrivateRoom)
  <div class="group-leave-modal" id="groupLeaveModal" aria-hidden="true">
    <div class="group-leave-dialog" role="dialog" aria-modal="true" aria-labelledby="groupLeaveTitle">
      <h3 id="groupLeaveTitle">Keluar dari grup publik?</h3>
      <p>Anda tidak akan lagi melihat grup ini di daftar grup saya. Riwayat pesan yang sudah terkirim tetap tersimpan.</p>
      <div class="group-leave-actions">
        <button type="button" class="group-leave-cancel" id="groupLeaveCancelBtn">Batal</button>
        <form method="POST" action="{{ $chatPayload['leaveUrl'] ?? '#' }}">
          @csrf
          <button type="submit" class="group-leave-confirm">Keluar Grup</button>
        </form>
      </div>
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
(() => {
  const stage = document.getElementById('groupRoomStage');
  const toggle = document.getElementById('groupRoomProfileToggle');
  const profile = document.getElementById('groupRoomProfile');
  const memberSearch = document.getElementById('groupRoomMemberSearchInput');
  const memberList = document.getElementById('groupRoomMemberList');
  const memberEmpty = document.getElementById('groupRoomMemberEmpty');
  const leaveOpenBtn = document.getElementById('groupLeaveOpenBtn');
  const leaveModal = document.getElementById('groupLeaveModal');
  const leaveCancelBtn = document.getElementById('groupLeaveCancelBtn');

  if (!stage || !toggle || !profile || !memberList) {
    return;
  }

  const syncDropdownState = (open) => {
    stage.classList.toggle('is-profile-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  };

  const syncMemberSearch = () => {
    const keyword = String(memberSearch?.value || '').trim().toLowerCase();
    const items = Array.from(memberList.querySelectorAll('.group-member-item'));
    let visibleCount = 0;

    items.forEach((item) => {
      const name = String(item.dataset.memberName || '').toLowerCase();
      const isVisible = !keyword || name.includes(keyword);

      item.style.display = isVisible ? '' : 'none';

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (memberEmpty) {
      memberEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  toggle.addEventListener('click', () => {
    syncDropdownState(!stage.classList.contains('is-profile-open'));
    setTimeout(() => memberSearch?.focus(), 120);
  });

  memberSearch?.addEventListener('input', syncMemberSearch);

  document.addEventListener('click', function (event) {
    if (!stage.classList.contains('is-profile-open')) {
      return;
    }

    if (toggle.contains(event.target) || profile.contains(event.target)) {
      return;
    }

    syncDropdownState(false);
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      syncDropdownState(false);
      leaveModal?.classList.remove('show');
      leaveModal?.setAttribute('aria-hidden', 'true');
    }
  });

  leaveOpenBtn?.addEventListener('click', () => {
    syncDropdownState(false);
    leaveModal?.classList.add('show');
    leaveModal?.setAttribute('aria-hidden', 'false');
  });

  leaveCancelBtn?.addEventListener('click', () => {
    leaveModal?.classList.remove('show');
    leaveModal?.setAttribute('aria-hidden', 'true');
  });

  leaveModal?.addEventListener('click', (event) => {
    if (event.target === leaveModal) {
      leaveModal.classList.remove('show');
      leaveModal.setAttribute('aria-hidden', 'true');
    }
  });

  syncMemberSearch();
})();
</script>
@endpush

@push('scripts')
<script>
(() => {
  const payload = @json($chatPayload);
  const currentUserId = {{ auth()->id() }};
  const counselorName = @json($counselorName);
  const isPrivateRoom = Boolean(
    payload.isPrivate ||
    payload.is_private ||
    String(payload.topicLabel || '').toLowerCase().includes('privat')
  );

  const thread = document.getElementById('groupChatThread');
  const form = document.getElementById('groupChatForm');
  const input = document.getElementById('groupChatInput');
  const sendBtn = document.getElementById('groupChatSendBtn');

  if (!thread || !form || !input || !sendBtn) {
    return;
  }

  const cleanPrivateName = (name = '') => {
    const value = String(name || '').trim();

    if (!value) {
      return isPrivateRoom ? 'Mahasiswa' : 'Mahasiswa Anonim';
    }

    if (value.toLowerCase() === 'konselor') {
      return counselorName;
    }

    if (isPrivateRoom) {
      return value.replace(/\s+Anonim$/i, '');
    }

    if (value.toLowerCase().includes('anonim')) {
      return value;
    }

    return `${value} Anonim`;
  };

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  const notifyError = (message) => {
    if (message) {
      alert(message);
    }
  };

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
    const keyParts = Object.fromEntries(
      dateKeyFormatter.formatToParts(date).map((part) => [part.type, part.value])
    );

    return {
      key: `${keyParts.year}-${keyParts.month}-${keyParts.day}`,
      label: dateFormatter.format(date).toUpperCase(),
    };
  };

  const scrollToBottom = () => {
    thread.scrollTop = thread.scrollHeight;
  };

  const autoResize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
  };

  const lastRenderedDateKey = () => {
    return Array.from(thread.querySelectorAll('[data-date-key]')).pop()?.dataset.dateKey || null;
  };

  const renderDateSeparator = (label, key) => {
    const separator = document.createElement('div');
    separator.className = 'group-room-date';
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

  const messageUpdateUrl = (messageId) => {
    return String(payload.updateUrlTemplate || '').replace('__MESSAGE_ID__', String(messageId));
  };

  const messageDeleteUrl = (messageId) => {
    return String(payload.deleteUrlTemplate || '').replace('__MESSAGE_ID__', String(messageId));
  };

  const closeAllMenus = () => {
    thread.querySelectorAll('.group-message-row.is-menu-open').forEach((element) => {
      element.classList.remove('is-menu-open');
    });
  };

  const buildMessageBubbleMarkup = (message, isMine) => `
    <div class="group-message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
    ${isMine ? `
      <div class="group-message-actions">
        <button type="button" class="group-message-action-toggle" data-action="toggle-menu" aria-label="Opsi pesan">
          <i class="bi bi-three-dots"></i>
        </button>
        <div class="group-message-action-menu">
          <button type="button" class="group-message-action-item" data-action="edit-message" data-message-id="${message.id}">
            <i class="bi bi-pencil-square"></i>
            <span>Edit pesan</span>
          </button>
          <button type="button" class="group-message-action-item delete" data-action="delete-message" data-message-id="${message.id}">
            <i class="bi bi-trash3"></i>
            <span>Hapus pesan</span>
          </button>
        </div>
      </div>
    ` : ''}
  `;

  const buildInlineEditorMarkup = (text, messageId) => `
    <div class="group-message-editor-shell" data-editing-message-id="${messageId}">
      <textarea class="group-message-editor-input" maxlength="2000">${escapeHtml(text)}</textarea>
      <div class="group-message-editor-actions">
        <button type="button" class="group-message-editor-btn cancel" data-action="cancel-edit" data-message-id="${messageId}">Batal</button>
        <button type="button" class="group-message-editor-btn save" data-action="save-edit" data-message-id="${messageId}">Simpan</button>
      </div>
    </div>
  `;

  const buildDeleteConfirmMarkup = (messageId, text = '') => `
    <div class="group-message-delete-confirm" data-delete-message-id="${messageId}">
      <div class="group-message-delete-preview">${escapeHtml(text).replace(/\n/g, '<br>')}</div>
      <div class="group-message-delete-confirm-text">Hapus pesan ini secara permanen?</div>
      <div class="group-message-delete-confirm-actions">
        <button type="button" class="group-message-delete-confirm-btn cancel" data-action="cancel-delete" data-message-id="${messageId}">Batal</button>
        <button type="button" class="group-message-delete-confirm-btn delete" data-action="confirm-delete" data-message-id="${messageId}">Hapus</button>
      </div>
    </div>
  `;

  const hasActiveInlineState = () => {
    return Boolean(thread.querySelector('.group-message-row.is-editing, [data-delete-message-id]'));
  };

  const restoreMessageBubble = (row) => {
    const bubbleShell = row.querySelector('.group-message-bubble-shell');
    const isMine = row.classList.contains('mine');

    if (!bubbleShell) {
      return;
    }

    bubbleShell.innerHTML = buildMessageBubbleMarkup({
      id: row.dataset.messageId,
      text: row.dataset.messageText ?? '',
    }, isMine);

    row.classList.remove('is-editing');
    row.classList.remove('is-menu-open');
  };

  function animalIcon(name) {
    const value = String(name || '').toLowerCase();

    const icons = {
      beruang: '🐻',
      kucing: '🐱',
      kelinci: '🐰',
      rubah: '🦊',
      panda: '🐼',
      koala: '🐨',
      harimau: '🐯',
      singa: '🦁',
      anjing: '🐶',
      burung: '🐦',
      kura: '🐢',
      monyet: '🐵'
    };

    for (const key in icons) {
      if (value.includes(key)) {
        return icons[key];
      }
    }

    return '👤';
  }

  const renderMessage = (message) => {
    const row = document.createElement('div');
    const isSystemMessage = Boolean(message.is_system);
    const isMine = Boolean(message.is_mine ?? (Number(message.sender_id) === Number(currentUserId)));
    const dateParts = resolveDateParts(message.sent_at);
    const senderRole = String(message.sender_role || '').toLowerCase();

    const isCounselorMessage =
      senderRole === 'konselor' ||
      senderRole === 'admin' ||
      message.is_counselor === true;

    const rawName =
      message.sender_name ||
      message.anonymous_name ||
      message.sender_anonymous_name ||
      message.member_anonymous_name ||
      'Anonim';

    const displaySenderName = isCounselorMessage
      ? counselorName
      : cleanPrivateName(rawName);

    const avatarInitial = String(displaySenderName || 'M').charAt(0).toUpperCase();

    const avatarHtml = isCounselorMessage
      ? `<div class="group-message-avatar-fallback">K</div>`
      : isPrivateRoom
        ? (
            message.avatar_url
              ? `<div class="group-message-avatar"><img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(displaySenderName)}"></div>`
              : `<div class="group-message-avatar-fallback">${escapeHtml(avatarInitial)}</div>`
          )
        : `<div class="group-animal-avatar">${animalIcon(displaySenderName)}</div>`;

    ensureDateSeparator(dateParts.key, dateParts.label);

    if (isSystemMessage) {
      row.className = 'group-message-row system';
      row.dataset.messageId = String(message.id);
      row.dataset.messageText = message.text ?? '';
      row.dataset.messageEdited = '0';

      row.innerHTML = `
        <div class="group-message-content">
          <div class="group-message-bubble-shell">
            <div class="group-message-bubble">${escapeHtml(message.text || '').replace(/\n/g, '<br>')}</div>
          </div>
        </div>
      `;

      thread.appendChild(row);
      return row;
    }

    row.className = `group-message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;
    row.dataset.messageText = message.text ?? '';
    row.dataset.messageEdited = message.is_edited ? '1' : '0';

    row.innerHTML = `
      ${isMine ? '' : avatarHtml}

      <div class="group-message-content">
        <div class="group-message-meta">
          <span class="group-message-name">${escapeHtml(displaySenderName)}</span>
          <span>${escapeHtml(message.time ?? '')}</span>
          ${message.is_edited ? '<span class="group-message-edited">telah diedit</span>' : ''}
        </div>

        <div class="group-message-bubble-shell">${buildMessageBubbleMarkup(message, isMine)}</div>
      </div>

      ${isMine ? avatarHtml : ''}
    `;

    thread.appendChild(row);
    return row;
  };

  const renderMessages = (messages, force = false) => {
    if (!force && hasActiveInlineState()) {
      return;
    }

    thread.innerHTML = '';
    messages.forEach((message) => renderMessage(message));
    closeAllMenus();
    scrollToBottom();
  };

  const syncMessages = async (force = false) => {
    try {
      const response = await fetch(`${payload.messagesUrl}?group_id=${payload.roomId}`, {
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

  renderMessages(payload.messages || []);
  autoResize();

  if (window.Echo) {
    window.Echo.private(payload.channel)
      .listen('.group.chat.message.sent', (event) => {
        if (!event?.message) {
          return;
        }

        const exists = thread.querySelector(`[data-message-id="${event.message.id}"]`);

        if (exists) {
          return;
        }

        renderMessage({
          ...event.message,
          is_mine: Number(event.message.sender_id) === Number(currentUserId),
        });

        scrollToBottom();
      });
  }

  syncMessages();
  window.setInterval(syncMessages, 10000);

  input.addEventListener('input', autoResize);

  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      form.requestSubmit();
    }
  });

  document.addEventListener('click', (event) => {
    if (!thread.contains(event.target)) {
      closeAllMenus();
    }
  });

  thread.addEventListener('click', async (event) => {
    const toggleButton = event.target.closest('[data-action="toggle-menu"]');
    const editButton = event.target.closest('[data-action="edit-message"]');
    const deleteButton = event.target.closest('[data-action="delete-message"]');
    const saveButton = event.target.closest('[data-action="save-edit"]');
    const cancelButton = event.target.closest('[data-action="cancel-edit"]');
    const cancelDeleteButton = event.target.closest('[data-action="cancel-delete"]');
    const confirmDeleteButton = event.target.closest('[data-action="confirm-delete"]');

    if (toggleButton) {
      const row = toggleButton.closest('.group-message-row');

      if (!row) {
        return;
      }

      const willOpen = !row.classList.contains('is-menu-open');
      closeAllMenus();
      row.classList.toggle('is-menu-open', willOpen);
      return;
    }

    if (editButton) {
      const messageId = editButton.dataset.messageId;
      const row = editButton.closest('.group-message-row');
      const bubbleShell = row?.querySelector('.group-message-bubble-shell');
      const currentText = row?.dataset.messageText ?? '';

      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      row.classList.add('is-editing');
      bubbleShell.innerHTML = buildInlineEditorMarkup(currentText, messageId);

      const textarea = bubbleShell.querySelector('.group-message-editor-input');

      if (textarea) {
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
      }

      return;
    }

    if (cancelButton) {
      const row = cancelButton.closest('.group-message-row');

      if (row) {
        restoreMessageBubble(row);
      }

      return;
    }

    if (cancelDeleteButton) {
      const row = cancelDeleteButton.closest('.group-message-row');

      if (row) {
        restoreMessageBubble(row);
      }

      return;
    }

    if (saveButton) {
      const messageId = saveButton.dataset.messageId;
      const row = saveButton.closest('.group-message-row');
      const textarea = row?.querySelector('.group-message-editor-input');
      const currentText = row?.dataset.messageText ?? '';
      const pesan = textarea?.value?.trim() ?? '';

      if (!row || !textarea) {
        return;
      }

      if (!pesan) {
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
          credentials: 'same-origin',
          body: JSON.stringify({ pesan }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
          syncMessages(true);
        } else {
          notifyError(data.message ?? 'Pesan gagal diedit.');
        }
      } catch (error) {
        console.error(error);
        notifyError('Terjadi kendala saat mengedit pesan.');
      }

      return;
    }

    if (deleteButton) {
      const messageId = deleteButton.dataset.messageId;
      const row = deleteButton.closest('.group-message-row');
      const bubbleShell = row?.querySelector('.group-message-bubble-shell');
      const currentText = row?.dataset.messageText ?? '';

      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      bubbleShell.innerHTML = buildDeleteConfirmMarkup(messageId, currentText);
      return;
    }

    if (confirmDeleteButton) {
      const messageId = confirmDeleteButton.dataset.messageId;

      try {
        const response = await fetch(messageDeleteUrl(messageId), {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
        });

        const data = await response.json();

        if (response.ok && data.success) {
          syncMessages(true);
        } else {
          notifyError(data.message ?? 'Pesan gagal dihapus.');
        }
      } catch (error) {
        console.error(error);
        notifyError('Terjadi kendala saat menghapus pesan.');
      }

      return;
    }
  });

  let isSendingGroupMessage = false;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (isSendingGroupMessage) {
      return;
    }

    const pesan = input.value.trim();

    if (!pesan) {
      input.focus();
      return;
    }

    isSendingGroupMessage = true;
    sendBtn.disabled = true;

    const tempId = `temp-${Date.now()}`;

    const tempMessage = {
      id: tempId,
      room_id: payload.roomId,
      sender_id: currentUserId,
      sender_name: cleanPrivateName(payload.currentMemberName || 'Mahasiswa Anonim'),
      sender_anonymous_name: cleanPrivateName(payload.currentMemberName || 'Mahasiswa Anonim'),
      sender_role: 'mahasiswa',
      text: pesan,
      time: new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta',
      }),
      sent_at: new Date().toISOString(),
      is_edited: false,
      is_mine: true,
    };

    const tempRow = renderMessage(tempMessage);
    scrollToBottom();

    input.value = '';
    autoResize();

    try {
      const response = await fetch(payload.sendUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          group_id: payload.roomId,
          pesan,
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
        notifyError('Server mengembalikan response tidak valid. Cek log Laravel.');
        return;
      }

      if (!response.ok || !data.success) {
        tempRow?.remove();
        input.value = pesan;
        autoResize();
        notifyError(data.message ?? 'Pesan gagal dikirim.');
        return;
      }

      tempRow?.remove();

      if (data.message) {
        renderMessage({
          ...data.message,
          is_mine: true,
        });
        scrollToBottom();
      } else {
        syncMessages(true);
      }
    } catch (error) {
      console.error(error);
      tempRow?.remove();
      input.value = pesan;
      autoResize();
      notifyError('Terjadi kendala saat mengirim pesan.');
    } finally {
      isSendingGroupMessage = false;
      sendBtn.disabled = false;
    }
  });
})();
</script>
@endpush
