@extends('layouts.admin')

@php
    $groupList = $groupList ?? collect();
    $activeRoom = $activeRoom ?? null;
    $chatPayload = $chatPayload ?? null;
@endphp

@section('page-title', 'Grup Chat')

@push('styles')
<style>
  .admin-chat-page {
    display: grid;
    grid-template-columns: 340px minmax(0, 1fr);
    gap: 0;
    min-height: 760px;
    background: #fff;
    border: 1px solid #dceee4;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 18px 44px rgba(6, 78, 59, .08);
  }

  .admin-chat-card,
  .admin-chat-list {
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
  }

  .admin-chat-list {
    overflow: hidden;
    align-self: stretch;
    border-right: 1px solid #edf7f1;
    background: linear-gradient(180deg, #f7fff9, #ffffff 18%);
  }

  .admin-chat-list-head {
    padding: 1rem 1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background: rgba(255, 255, 255, .92);
  }

  .admin-chat-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    padding: .12rem;
    border-radius: 14px;
    background: #ffffff;
    border: 1px solid #dceee4;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
  }

  .admin-chat-tab {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .18rem;
    min-height: 52px;
    padding: .48rem .52rem .42rem;
    border-radius: 10px;
    text-decoration: none;
    font-size: .73rem;
    font-weight: 800;
    text-align: center;
    border: 1px solid transparent;
    background: transparent;
    color: #64748b;
    position: relative;
    transition: all .22s ease;
  }

  .admin-chat-tab:hover {
    background: linear-gradient(180deg, #f7fffb, #f1fcf6);
    color: #065f46;
    border-color: rgba(16, 185, 129, .12);
  }

  .admin-chat-tab.active {
    background: linear-gradient(180deg, #ffffff, #f4fcf7);
    color: #065f46;
    border-color: transparent;
    box-shadow: inset 0 -3px 0 #10b981;
  }

  .admin-chat-tab.active::after {
    content: "";
    position: absolute;
    left: 18%;
    right: 18%;
    bottom: 0;
    height: 3px;
    border-radius: 999px 999px 0 0;
    background: linear-gradient(135deg, #059669, #34d399);
  }

  .admin-chat-tab-icon {
    font-size: .88rem;
    line-height: 1;
  }

  .admin-chat-search {
    margin-top: .85rem;
    position: relative;
  }

  .admin-chat-search i {
    position: absolute;
    left: .95rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1rem;
    pointer-events: none;
  }

  .admin-chat-search input {
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 16px;
    padding: .78rem 1rem .78rem 2.65rem;
    font-size: .9rem;
    color: #0f172a;
    background: #fff;
    outline: none;
  }

  .admin-chat-search input:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .admin-chat-search-empty {
    display: none;
    padding: 1rem;
    color: #64748b;
    font-size: .84rem;
    border-top: 1px solid #f4f8f6;
  }

  .admin-chat-session {
    display: block;
    padding: .95rem 1rem;
    border-top: 1px solid #f4f8f6;
    text-decoration: none;
    transition: background .18s ease, transform .18s ease;
  }

  .admin-chat-session:hover {
    background: #f8fffb;
  }

  .admin-chat-session.active {
    background: linear-gradient(135deg, rgba(209, 250, 229, .76), rgba(239, 252, 245, .92));
  }

  .admin-chat-session-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .45rem;
  }

  .admin-chat-session-name {
    font-weight: 800;
    color: #0f172a;
    font-size: .92rem;
  }

  .admin-chat-session-meta {
    color: #64748b;
    font-size: .78rem;
    line-height: 1.55;
  }

  .admin-chat-session-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .28rem .62rem;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
    background: #e8fff1;
    color: #047857;
  }

  .admin-chat-card {
    overflow: hidden;
    min-height: 760px;
    display: flex;
    flex-direction: column;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 22%),
      linear-gradient(180deg, #f6fff9 0%, #ffffff 16%, #ffffff 100%);
  }

  .admin-chat-empty {
    padding: 2.6rem 2rem;
    display: grid;
    place-items: center;
    text-align: center;
    min-height: 540px;
  }

  .admin-chat-empty-icon {
    width: 84px;
    height: 84px;
    border-radius: 28px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #047857;
    font-size: 2rem;
    box-shadow: 0 20px 40px rgba(16, 185, 129, .16);
    margin-bottom: 1rem;
  }

  .admin-chat-empty h3 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .55rem;
  }

  .admin-chat-empty p {
    max-width: 520px;
    margin: 0 auto;
    color: #64748b;
    line-height: 1.8;
  }

  /* Panel anggota dibuat dropdown agar ruang chat tidak menyusut ke samping. */
  .admin-chat-stage {
    position: relative;
    min-height: 760px;
  }

  .admin-chat-main {
    min-width: 0;
    min-height: 760px;
    display: flex;
    flex-direction: column;
  }

  .admin-chat-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.15rem 1.3rem;
    border-bottom: 1px solid #edf7f1;
    background: rgba(255,255,255,.88);
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 6;
  }

  .admin-chat-person {
    display: flex;
    align-items: center;
    gap: .95rem;
    min-width: 0;
  }

  .admin-chat-avatar {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 10px 25px rgba(6, 95, 70, .12);
  }

  .admin-chat-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: .14rem;
  }

  .admin-chat-subtitle {
    margin: 0;
    color: #64748b;
    font-size: .84rem;
    line-height: 1.6;
  }

  .admin-chat-head-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    justify-content: flex-end;
    position: relative;
    min-width: 0;
  }

  .admin-chat-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .22rem;
    width: 52px;
    height: 44px;
    border: 1px solid #d8eee2;
    border-radius: 14px;
    padding: 0;
    background: #fff;
    color: #065f46;
    font-size: 1.16rem;
    font-weight: 800;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
  }

  .admin-chat-toggle:hover {
    transform: translateY(-1px);
    background: #f8fffb;
    box-shadow: 0 12px 22px rgba(6, 78, 59, .08);
  }

  .admin-chat-toggle-chevron {
    font-size: .78rem;
    transition: transform .18s ease;
  }

  .admin-chat-stage.is-profile-open .admin-chat-toggle-chevron {
    transform: rotate(180deg);
  }

  .admin-chat-toggle-text {
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
  .admin-chat-thread {
    flex: 1;
    overflow-y: auto;
    padding: 1.35rem 1.35rem 0;
    background:
      radial-gradient(circle at center, rgba(209, 250, 229, 0.28), transparent 42%),
      linear-gradient(180deg, rgba(246,255,249,.7), rgba(255,255,255,.98));
  }

  .admin-chat-date {
    width: fit-content;
    margin: 0 auto 1.35rem;
    padding: .52rem .95rem;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid #e4f3eb;
    color: #64748b;
    font-size: .74rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .admin-message-row {
    display: flex;
    gap: .8rem;
    margin-bottom: .95rem;
    align-items: flex-end;
  }

  .admin-message-row.mine {
    justify-content: flex-end;
  }

  .admin-message-row.mine .admin-message-meta {
    justify-content: flex-end;
  }

  .admin-message-row.mine .admin-message-bubble {
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    border-bottom-right-radius: 10px;
    box-shadow: 0 16px 32px rgba(6, 95, 70, .2);
  }

  .admin-message-row.other .admin-message-bubble {
    background: #fff;
    color: #1f2937;
    border: 1px solid #e6eef3;
    border-bottom-left-radius: 10px;
  }

  .admin-message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 10px 20px rgba(15, 23, 42, .08);
  }

  .admin-message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .admin-message-content {
    max-width: min(76%, 620px);
    position: relative;
  }

  .admin-message-meta {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin: 0 .3rem .3rem;
    color: #64748b;
    font-size: .75rem;
  }

  .admin-message-name {
    font-weight: 800;
    color: #064e3b;
  }

  .admin-message-bubble {
    padding: .92rem 1.1rem .98rem;
    border-radius: 24px;
    font-size: .93rem;
    line-height: 1.7;
    word-break: break-word;
  }

  .admin-message-bubble-shell {
    position: relative;
  }

  .admin-message-edited {
    font-size: .68rem;
    color: #94a3b8;
    font-weight: 600;
  }

  .admin-message-actions {
    position: absolute;
    top: .55rem;
    right: .7rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity .18s ease;
  }

  .admin-message-row.mine:hover .admin-message-actions,
  .admin-message-row.mine.is-menu-open .admin-message-actions {
    opacity: 1;
    pointer-events: auto;
  }

  .admin-message-action-toggle {
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

  .admin-message-action-menu {
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

  .admin-message-row.is-menu-open .admin-message-action-menu {
    display: block;
  }

  .admin-message-action-item {
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

  .admin-message-action-item:hover {
    background: #f8fffb;
  }

  .admin-message-action-item.delete {
    color: #b91c1c;
  }

  .admin-message-row.is-editing .admin-message-actions {
    display: none;
  }

  .admin-message-editor-shell {
    display: grid;
    gap: .7rem;
  }

  .admin-message-editor-input {
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

  .admin-message-editor-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .admin-message-editor-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .admin-message-editor-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .admin-message-editor-btn.save {
    background: #065f46;
    color: #fff;
  }

  .admin-message-delete-confirm {
    display: grid;
    gap: .75rem;
  }

  .admin-message-delete-confirm-text {
    font-size: .83rem;
    line-height: 1.6;
    color: #334155;
  }

  .admin-message-delete-confirm-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .admin-message-delete-confirm-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .admin-message-delete-confirm-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .admin-message-delete-confirm-btn.delete {
    background: #b91c1c;
    color: #fff;
  }

  .admin-chat-compose {
    padding: 1rem 1.2rem 1.2rem;
    border-top: 1px solid #edf7f1;
    background: rgba(255,255,255,.95);
  }

  .admin-chat-form {
    display: flex;
    align-items: flex-end;
    gap: .8rem;
    padding: .75rem;
    border-radius: 24px;
    border: 1px solid #d8eee2;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .admin-chat-input {
    flex: 1;
    border: none;
    resize: none;
    outline: none;
    background: transparent;
    min-height: 56px;
    max-height: 160px;
    font-size: .94rem;
    color: #0f172a;
    padding: .6rem .3rem;
  }

  .admin-chat-send {
    width: 54px;
    height: 54px;
    border: none;
    border-radius: 18px;
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    font-size: 1.28rem;
    box-shadow: 0 16px 28px rgba(6,95,70,.22);
  }

  .admin-chat-send:disabled {
    opacity: .45;
    cursor: not-allowed;
    box-shadow: none;
  }

  .admin-chat-hint {
    margin-top: .65rem;
    color: #64748b;
    font-size: .78rem;
    padding: 0 .25rem;
  }

  /* Dropdown anggota berada di bawah ikon header dan tidak ikut mengambil kolom chat. */
  .admin-chat-profile {
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
    border: 1px solid #dceee4;
    border-radius: 18px;
    background: linear-gradient(180deg, #fbfffd, #f7fcf9);
    box-shadow: 0 18px 38px rgba(15, 23, 42, .12);
    transition: max-height .24s ease, opacity .18s ease, transform .18s ease;
    z-index: 30;
  }

  .admin-chat-stage.is-profile-open .admin-chat-profile {
    max-height: min(540px, 70vh);
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
  }

  .admin-chat-profile-head {
    padding: 1rem 1.1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 28%),
      linear-gradient(180deg, #f6fff9, #ffffff);
  }

  .admin-chat-profile-head h3 {
    margin: 0 0 .28rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0f172a;
  }

  .admin-chat-profile-head p {
    margin: 0;
    color: #4b7a68;
    font-size: .84rem;
    line-height: 1.6;
  }

  .admin-chat-profile-body {
    padding: .95rem 1.1rem 1.05rem;
    overflow-y: auto;
    max-height: min(430px, 58vh);
  }

  .admin-member-search {
    position: relative;
    margin-bottom: .9rem;
  }

  .admin-member-search i {
    position: absolute;
    left: .82rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
    pointer-events: none;
  }

  .admin-member-search input {
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 13px;
    padding: .68rem .85rem .68rem 2.35rem;
    background: #fff;
    color: #0f172a;
    font-size: .84rem;
    outline: none;
  }

  .admin-member-search input:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .admin-member-list {
    display: grid;
    gap: .58rem;
  }

  /* Item anggota menampilkan foto kecil dan nama tanpa metadata tambahan. */
  .admin-member-item {
    display: flex;
    align-items: center;
    gap: .72rem;
    padding: .3rem 0;
  }

  .admin-member-avatar {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    background: #e8fff1;
  }

  .admin-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .admin-member-name {
    font-size: .92rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
  }

  .admin-member-empty {
    display: none;
    padding: .25rem 0;
    color: #64748b;
    font-size: .84rem;
    line-height: 1.6;
  }

  @media (max-width: 1199.98px) {
    .admin-chat-page {
      grid-template-columns: 1fr;
      min-height: 0;
    }

    .admin-chat-list {
      border-right: none;
      border-bottom: 1px solid #edf7f1;
    }
  }

  @media (max-width: 767.98px) {
    .admin-chat-card {
      min-height: 680px;
    }

    .admin-chat-main {
      min-height: 680px;
    }

    .admin-chat-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .admin-chat-head-actions {
      width: 100%;
      justify-content: flex-start;
    }

    .admin-chat-profile {
      left: 0;
      right: auto;
      width: 100%;
      max-width: 100%;
    }

    .admin-message-content {
      max-width: 100%;
    }
  }
</style>
@endpush

@section('konten')
<div class="admin-chat-page">
  <aside class="admin-chat-list">
    <div class="admin-chat-list-head">
      <div class="admin-chat-tabs">
        <a href="{{ route('admin.chat') }}" class="admin-chat-tab">
          <i class="ti ti-message-heart admin-chat-tab-icon"></i>
          <span>Chat Konseling</span>
        </a>
        <a href="{{ route('admin.group-chat') }}" class="admin-chat-tab active">
          <i class="ti ti-users-group admin-chat-tab-icon"></i>
          <span>Grup Chat</span>
        </a>
      </div>
      <div class="admin-chat-search">
        <i class="ti ti-search"></i>
        <input
          type="search"
          id="adminGroupChatSearchInput"
          placeholder="Cari topik, grup, atau nama anggota..."
          autocomplete="off"
        >
      </div>
    </div>

    @forelse($groupList as $room)
      @php
        $isSelected = optional($activeRoom)->id === $room->id;
        $memberNames = $room->members->map(fn ($member) => $member->user?->getNamaDisplay())->filter()->values();
        $latestMessagePreview = optional($room->latestMessage)->pesan
            ? \Illuminate\Support\Str::limit($room->latestMessage->pesan, 56)
            : 'Belum ada pesan pada grup ini.';
        $sessionSearchText = strtolower(trim(implode(' ', [
            $room->title,
            $room->topicLabel(),
            $memberNames->implode(' '),
            $latestMessagePreview,
        ])));
      @endphp
      <a href="{{ route('admin.group-chat', ['group' => $room->id]) }}" class="admin-chat-session {{ $isSelected ? 'active' : '' }}" data-session-search="{{ $sessionSearchText }}">
        <div class="admin-chat-session-top">
          <div class="admin-chat-session-name">{{ $room->title }}</div>
          <span class="admin-chat-session-status">{{ $room->topicLabel() }}</span>
        </div>
        <div class="admin-chat-session-meta">
          {{ $room->members_count }} anggota<br>
          {{ $latestMessagePreview }}
        </div>
      </a>
    @empty
      <div style="padding:1.1rem 1rem;color:#64748b;font-size:.84rem;">
        Belum ada grup chat yang dibuat mahasiswa.
      </div>
    @endforelse

    @if($groupList->isNotEmpty())
      <div class="admin-chat-search-empty" id="adminGroupChatSearchEmpty">
        Tidak ada grup chat yang cocok dengan kata kunci pencarian.
      </div>
    @endif
  </aside>

  <section class="admin-chat-card">
    @if(!$activeRoom || !$chatPayload)
      <div class="admin-chat-empty">
        <div>
          <div class="admin-chat-empty-icon"><i class="ti ti-users-group"></i></div>
          <h3>Belum Ada Grup Chat Aktif</h3>
          <p>
            Saat mahasiswa membuat grup konseling berdasarkan topik, daftar grup tersebut akan muncul di panel kiri dan bisa langsung Anda buka dari sini.
          </p>
        </div>
      </div>
    @else
      <div class="admin-chat-stage" id="adminGroupChatStage">
        <div class="admin-chat-main">
          <div class="admin-chat-head">
            <div class="admin-chat-person">
              <div class="admin-chat-avatar">
                <i class="ti ti-users-group"></i>
              </div>
              <div>
                <div class="admin-chat-title">{{ $chatPayload['roomTitle'] }}</div>
                <p class="admin-chat-subtitle">
                  {{ $chatPayload['topicLabel'] }} &bull; {{ $chatPayload['memberCount'] }} anggota<br>
                  {{ implode(', ', array_slice($chatPayload['memberNames'], 0, 5)) }}{{ count($chatPayload['memberNames']) > 5 ? ' dan lainnya' : '' }}
                </p>
              </div>
            </div>
            <div class="admin-chat-head-actions">
              <button type="button" class="admin-chat-toggle" id="adminGroupMemberToggle" aria-expanded="false" aria-controls="adminGroupChatProfile" aria-label="Lihat anggota grup" title="Lihat anggota grup">
                <i class="ti ti-users"></i>
                <i class="ti ti-chevron-down admin-chat-toggle-chevron"></i>
                <span class="admin-chat-toggle-text">Lihat anggota grup</span>
              </button>

              {{-- Dropdown anggota dibuka dari ikon agar ruang chat tetap penuh. --}}
              <aside class="admin-chat-profile" id="adminGroupChatProfile">
                <div class="admin-chat-profile-head">
                  <h3>Anggota Grup</h3>
                </div>

                <div class="admin-chat-profile-body">
                  {{-- Search lokal membantu admin menemukan anggota tanpa menunggu request baru. --}}
                  <div class="admin-member-search">
                    <i class="ti ti-search"></i>
                    <input
                      type="search"
                      id="adminGroupMemberSearchInput"
                      placeholder="Cari anggota..."
                      autocomplete="off"
                    >
                  </div>
                  <div class="admin-member-list" id="adminGroupMemberList">
                    @foreach($chatPayload['memberProfiles'] as $memberProfile)
                      <div class="admin-member-item" data-member-name="{{ \Illuminate\Support\Str::lower($memberProfile['name']) }}">
                        <div class="admin-member-avatar">
                          <img src="{{ $memberProfile['avatar_url'] }}" alt="{{ $memberProfile['name'] }}">
                        </div>
                        <div class="admin-member-name">{{ $memberProfile['name'] }}</div>
                      </div>
                    @endforeach
                  </div>
                  <div class="admin-member-empty" id="adminGroupMemberEmpty">Anggota tidak ditemukan.</div>
                </div>
              </aside>
            </div>
          </div>

          <div class="admin-chat-thread" id="adminGroupChatThread"></div>

          <div class="admin-chat-compose">
            <form id="adminGroupChatForm" class="admin-chat-form">
              <textarea
                id="adminGroupChatInput"
                class="admin-chat-input"
                rows="1"
                maxlength="2000"
                placeholder="Tulis pesan untuk grup konseling ini..."
              ></textarea>
              <button type="submit" class="admin-chat-send" id="adminGroupChatSendBtn">
                <i class="ti ti-send"></i>
              </button>
            </form>
            <div class="admin-chat-hint" id="adminGroupChatHint">
              Pesan akan langsung tampil untuk seluruh anggota grup dan konselor lain yang membuka ruang ini.
            </div>
          </div>
        </div>
      </div>
    @endif
  </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const searchInput = document.getElementById('adminGroupChatSearchInput');
  const searchEmpty = document.getElementById('adminGroupChatSearchEmpty');
  const sessionItems = Array.from(document.querySelectorAll('[data-session-search]'));

  if (!searchInput || sessionItems.length === 0) {
    return;
  }

  const syncSearch = () => {
    const keyword = searchInput.value.trim().toLowerCase();
    let visibleCount = 0;

    sessionItems.forEach((item) => {
      const haystack = item.dataset.sessionSearch || '';
      const isMatch = !keyword || haystack.includes(keyword);
      item.style.display = isMatch ? '' : 'none';
      if (isMatch) {
        visibleCount += 1;
      }
    });

    if (searchEmpty) {
      searchEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  searchInput.addEventListener('input', syncSearch);
})();
</script>
@endpush

@if($activeRoom && $chatPayload)
@push('scripts')
<script>
(() => {
  const stage = document.getElementById('adminGroupChatStage');
  const toggle = document.getElementById('adminGroupMemberToggle');
  const profile = document.getElementById('adminGroupChatProfile');
  const memberSearch = document.getElementById('adminGroupMemberSearchInput');
  const memberList = document.getElementById('adminGroupMemberList');
  const memberEmpty = document.getElementById('adminGroupMemberEmpty');
  const memberProfiles = @json($chatPayload['memberProfiles']);

  if (!stage || !toggle || !profile || !memberList) {
    return;
  }

  const renderMembers = () => {
    // Daftar anggota dirender dari payload yang sama dengan header agar tidak kosong saat relasi Blade belum stabil.
    memberList.innerHTML = '';

    memberProfiles.forEach((member) => {
      const item = document.createElement('div');
      const avatar = document.createElement('div');
      const image = document.createElement('img');
      const label = document.createElement('div');
      const name = member?.name || 'Pengguna';
      item.className = 'admin-member-item';
      item.dataset.memberName = String(name).toLowerCase();
      avatar.className = 'admin-member-avatar';
      image.src = member?.avatar_url || '{{ asset('img/default-avatar.png') }}';
      image.alt = name;
      label.className = 'admin-member-name';
      label.textContent = name;
      avatar.appendChild(image);
      item.appendChild(avatar);
      item.appendChild(label);
      memberList.appendChild(item);
    });
  };

  const syncMemberSearch = () => {
    const keyword = (memberSearch?.value || '').trim().toLowerCase();
    const items = Array.from(memberList.querySelectorAll('.admin-member-item'));
    let visibleCount = 0;

    items.forEach((item) => {
      const isMatch = !keyword || (item.dataset.memberName || '').includes(keyword);
      item.style.display = isMatch ? '' : 'none';

      if (isMatch) {
        visibleCount += 1;
      }
    });

    if (memberEmpty) {
      memberEmpty.textContent = keyword ? 'Anggota tidak ditemukan.' : 'Belum ada anggota dalam grup ini.';
      memberEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  const syncDropdownState = (isOpen) => {
    // Sinkronkan label aksesibilitas saat dropdown anggota dibuka atau ditutup.
    const label = isOpen ? 'Sembunyikan anggota grup' : 'Lihat anggota grup';
    stage.classList.toggle('is-profile-open', isOpen);
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    toggle.setAttribute('aria-label', label);
    toggle.setAttribute('title', label);
    toggle.querySelector('.admin-chat-toggle-text').textContent = label;
  };

  toggle.addEventListener('click', (event) => {
    event.stopPropagation();
    renderMembers();
    syncMemberSearch();
    syncDropdownState(!stage.classList.contains('is-profile-open'));
  });

  memberSearch?.addEventListener('input', syncMemberSearch);

  document.addEventListener('click', (event) => {
    if (!stage.classList.contains('is-profile-open')) {
      return;
    }

    if (toggle.contains(event.target) || profile.contains(event.target)) {
      return;
    }

    syncDropdownState(false);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      syncDropdownState(false);
    }
  });
})();
</script>
@endpush

@push('scripts')
<script>
(() => {
  const payload = @json($chatPayload);
  const currentUserId = {{ auth()->id() }};
  const thread = document.getElementById('adminGroupChatThread');
  const form = document.getElementById('adminGroupChatForm');
  const input = document.getElementById('adminGroupChatInput');
  const sendBtn = document.getElementById('adminGroupChatSendBtn');
  const hint = document.getElementById('adminGroupChatHint');

  if (!thread || !form || !input || !sendBtn) {
    return;
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

  const scrollToBottom = () => {
    thread.scrollTop = thread.scrollHeight;
  };

  const autoResize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
  };
  const lastRenderedDateKey = () => Array.from(thread.querySelectorAll('[data-date-key]')).pop()?.dataset.dateKey || null;
  const renderDateSeparator = (label, key) => {
    const separator = document.createElement('div');
    separator.className = 'admin-chat-date';
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
  const messageUpdateUrl = (messageId) => payload.updateUrlTemplate.replace('__MESSAGE_ID__', String(messageId));
  const messageDeleteUrl = (messageId) => payload.deleteUrlTemplate.replace('__MESSAGE_ID__', String(messageId));

  const closeAllMenus = () => {
    thread.querySelectorAll('.admin-message-row.is-menu-open').forEach((element) => {
      element.classList.remove('is-menu-open');
    });
  };

  // Bubble dan editor inline dipisah agar edit tetap terasa menyatu di percakapan grup.
  const buildMessageBubbleMarkup = (message, isMine) => `
    <div class="admin-message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
    ${isMine ? `
      <div class="admin-message-actions">
        <button type="button" class="admin-message-action-toggle" data-action="toggle-menu" aria-label="Opsi pesan">
          <i class="ti ti-dots"></i>
        </button>
        <div class="admin-message-action-menu">
          <button type="button" class="admin-message-action-item" data-action="edit-message" data-message-id="${message.id}">
            <i class="ti ti-edit"></i>
            <span>Edit pesan</span>
          </button>
          <button type="button" class="admin-message-action-item delete" data-action="delete-message" data-message-id="${message.id}">
            <i class="ti ti-trash"></i>
            <span>Hapus pesan</span>
          </button>
        </div>
      </div>
    ` : ''}
  `;

  const buildInlineEditorMarkup = (text, messageId) => `
    <div class="admin-message-editor-shell" data-editing-message-id="${messageId}">
      <textarea class="admin-message-editor-input" maxlength="2000">${escapeHtml(text)}</textarea>
      <div class="admin-message-editor-actions">
        <button type="button" class="admin-message-editor-btn cancel" data-action="cancel-edit" data-message-id="${messageId}">Batal</button>
        <button type="button" class="admin-message-editor-btn save" data-action="save-edit" data-message-id="${messageId}">Simpan</button>
      </div>
    </div>
  `;

  const buildDeleteConfirmMarkup = (messageId) => `
    <div class="admin-message-delete-confirm" data-delete-message-id="${messageId}">
      <div class="admin-message-delete-confirm-text">Hapus pesan ini secara permanen?</div>
      <div class="admin-message-delete-confirm-actions">
        <button type="button" class="admin-message-delete-confirm-btn cancel" data-action="cancel-delete" data-message-id="${messageId}">Batal</button>
        <button type="button" class="admin-message-delete-confirm-btn delete" data-action="confirm-delete" data-message-id="${messageId}">Hapus</button>
      </div>
    </div>
  `;

  // Sinkronisasi grup admin ditahan saat ada state inline aktif agar isi tidak kembali sendiri.
  const hasActiveInlineState = () => Boolean(
    thread.querySelector('.admin-message-row.is-editing, [data-delete-message-id]')
  );

  // Bubble asli dikembalikan jika admin batal edit atau batal hapus pesan grup.
  const restoreMessageBubble = (row) => {
    const bubbleShell = row.querySelector('.admin-message-bubble-shell');
    const isMine = row.classList.contains('mine');

    if (!bubbleShell) {
      return;
    }

    bubbleShell.innerHTML = buildMessageBubbleMarkup({
      id: Number(row.dataset.messageId),
      text: row.dataset.messageText ?? '',
    }, isMine);
    row.classList.remove('is-editing');
    row.classList.remove('is-menu-open');
  };

  const renderMessage = (message) => {
    const row = document.createElement('div');
    const isMine = Boolean(message.is_mine ?? (message.sender_id === currentUserId));
    const dateParts = resolveDateParts(message.sent_at);

    ensureDateSeparator(dateParts.key, dateParts.label);

    row.className = `admin-message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;
    row.dataset.messageText = message.text ?? '';
    row.dataset.messageEdited = message.is_edited ? '1' : '0';

    row.innerHTML = `
      ${isMine ? '' : `
        <div class="admin-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      `}
      <div class="admin-message-content">
        <div class="admin-message-meta">
          <span class="admin-message-name">${escapeHtml(message.sender_name)}</span>
          <span>${escapeHtml(message.time)}</span>
          ${message.is_edited ? '<span class="admin-message-edited">telah diedit</span>' : ''}
        </div>
        <div class="admin-message-bubble-shell">${buildMessageBubbleMarkup(message, isMine)}</div>
      </div>
      ${isMine ? `
        <div class="admin-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      ` : ''}
    `;

    thread.appendChild(row);
  };

  const renderMessages = (messages, force = false) => {
    // Render ulang penuh agar edit dan delete cepat sinkron di ruang grup admin.
    if (!force && hasActiveInlineState()) {
      return;
    }

    thread.innerHTML = '';
    messages.forEach((message) => renderMessage(message));
    closeAllMenus();
    scrollToBottom();
  };

  // Force dipakai setelah aksi sukses agar daftar pesan grup langsung diperbarui dari server.
  const syncMessages = async (force = false) => {
    try {
      const response = await fetch(`${payload.messagesUrl}?group_id=${payload.roomId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
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
          is_mine: Number(event.message.sender_id) === currentUserId,
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
      const row = toggleButton.closest('.admin-message-row');
      const willOpen = !row.classList.contains('is-menu-open');
      closeAllMenus();
      row.classList.toggle('is-menu-open', willOpen);
      return;
    }

    if (editButton) {
      const messageId = Number(editButton.dataset.messageId);
      const row = editButton.closest('.admin-message-row');
      const bubbleShell = row?.querySelector('.admin-message-bubble-shell');
      const currentText = row?.dataset.messageText ?? '';

      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      row.classList.add('is-editing');
      bubbleShell.innerHTML = buildInlineEditorMarkup(currentText, messageId);
      const textarea = bubbleShell.querySelector('.admin-message-editor-input');
      if (textarea) {
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
      }
      return;
    }

    if (cancelButton) {
      const row = cancelButton.closest('.admin-message-row');
      if (row) {
        restoreMessageBubble(row);
      }
      return;
    }

    if (cancelDeleteButton) {
      const row = cancelDeleteButton.closest('.admin-message-row');
      if (row) {
        restoreMessageBubble(row);
      }
      return;
    }

    if (saveButton) {
      const messageId = Number(saveButton.dataset.messageId);
      const row = saveButton.closest('.admin-message-row');
      const textarea = row?.querySelector('.admin-message-editor-input');
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
      const row = deleteButton.closest('.admin-message-row');
      const bubbleShell = row?.querySelector('.admin-message-bubble-shell');
      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      bubbleShell.innerHTML = buildDeleteConfirmMarkup(messageId);
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

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const pesan = input.value.trim();
    if (!pesan) {
      return;
    }

    sendBtn.disabled = true;
    hint.textContent = 'Mengirim pesan ke grup...';

    try {
      const response = await fetch(payload.sendUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
          'X-Requested-With': 'XMLHttpRequest',
          'X-Socket-ID': window.Echo?.socketId?.() ?? '',
        },
        body: JSON.stringify({
          group_id: payload.roomId,
          pesan,
        }),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        hint.textContent = data.message ?? 'Pesan gagal dikirim.';
        sendBtn.disabled = false;
        return;
      }

      renderMessage(data.message);
      scrollToBottom();
      input.value = '';
      autoResize();
      hint.textContent = 'Pesan terkirim ke seluruh anggota grup.';
    } catch (error) {
      console.error(error);
      hint.textContent = 'Terjadi kendala saat mengirim pesan.';
    } finally {
      sendBtn.disabled = false;
    }
  });
})();
</script>
@endpush
@endif
