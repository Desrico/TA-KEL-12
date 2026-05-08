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

  .admin-chat-stage {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 0);
    min-height: 760px;
    transition: grid-template-columns .28s ease;
  }

  .admin-chat-stage.is-profile-open {
    grid-template-columns: minmax(0, 1fr) minmax(300px, 340px);
  }

  .admin-chat-main {
    min-width: 0;
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
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .admin-chat-toggle {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    border: 1px solid #d8eee2;
    border-radius: 14px;
    padding: .72rem .9rem;
    background: #fff;
    color: #065f46;
    font-size: .8rem;
    font-weight: 800;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
  }

  .admin-chat-toggle:hover {
    transform: translateY(-1px);
    background: #f8fffb;
    box-shadow: 0 12px 22px rgba(6, 78, 59, .08);
  }

  .admin-chat-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .65rem .95rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .78rem;
    font-weight: 800;
  }

  .admin-chat-badge::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.13);
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

  .admin-chat-profile {
    width: 100%;
    max-width: 0;
    opacity: 0;
    overflow: hidden;
    border-left: 1px solid #edf7f1;
    background: linear-gradient(180deg, #fbfffd, #f7fcf9);
    transition: max-width .28s ease, opacity .2s ease;
  }

  .admin-chat-stage.is-profile-open .admin-chat-profile {
    max-width: 340px;
    opacity: 1;
  }

  .admin-chat-profile-head {
    padding: 1.2rem 1.2rem 1rem;
    border-bottom: 1px solid #edf7f1;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 28%),
      linear-gradient(180deg, #f6fff9, #ffffff);
  }

  .admin-chat-profile-head h3 {
    margin: 0 0 .28rem;
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
  }

  .admin-chat-profile-head p {
    margin: 0;
    color: #4b7a68;
    font-size: .84rem;
    line-height: 1.6;
  }

  .admin-chat-profile-topic {
    display: inline-flex;
    align-items: center;
    gap: .38rem;
    margin-top: .7rem;
    padding: .42rem .8rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
  }

  .admin-chat-profile-body {
    padding: 1rem 1.15rem 1.2rem;
    overflow-y: auto;
    max-height: 760px;
  }

  .admin-chat-profile-section {
    font-size: .74rem;
    font-weight: 800;
    color: #64748b;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: .85rem;
  }

  .admin-member-list {
    display: grid;
    gap: .72rem;
  }

  .admin-member-item {
    display: flex;
    align-items: center;
    gap: .85rem;
    padding: .88rem .92rem;
    border-radius: 18px;
    border: 1px solid rgba(221, 239, 231, 0.95);
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .admin-member-avatar {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
  }

  .admin-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .admin-member-copy {
    min-width: 0;
    flex: 1;
  }

  .admin-member-name {
    font-size: .92rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
    margin-bottom: .15rem;
  }

  .admin-member-meta {
    color: #64748b;
    font-size: .78rem;
    line-height: 1.55;
  }

  .admin-member-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .34rem .62rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .68rem;
    font-weight: 800;
    white-space: nowrap;
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

    .admin-chat-stage.is-profile-open {
      grid-template-columns: minmax(0, 1fr);
    }

    .admin-chat-profile {
      max-width: none;
      max-height: 0;
      border-left: none;
      border-top: 1px solid #edf7f1;
    }

    .admin-chat-stage.is-profile-open .admin-chat-profile {
      max-width: none;
      max-height: 560px;
    }
  }

  @media (max-width: 767.98px) {
    .admin-chat-card {
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

    .admin-message-content {
      max-width: 100%;
    }

    .admin-chat-toggle,
    .admin-chat-badge {
      width: 100%;
      justify-content: center;
    }

    .admin-member-item {
      align-items: flex-start;
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
              <button type="button" class="admin-chat-toggle" id="adminGroupMemberToggle" aria-expanded="false" aria-controls="adminGroupChatProfile">
                <i class="ti ti-layout-sidebar-right-expand"></i>
                <span>Lihat anggota grup</span>
              </button>
              <div class="admin-chat-badge">Grup Aktif</div>
            </div>
          </div>

          <div class="admin-chat-thread" id="adminGroupChatThread">
            <div class="admin-chat-date">
              {{ now('Asia/Jakarta')->translatedFormat('l, j F Y') }}
            </div>
          </div>

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

        <aside class="admin-chat-profile" id="adminGroupChatProfile">
          {{-- Admin bisa melihat daftar anggota grup dari panel samping yang sama seperti sisi mahasiswa. --}}
          <div class="admin-chat-profile-head">
            <h3>{{ $chatPayload['roomTitle'] }}</h3>
            <p>{{ $chatPayload['memberCount'] }} anggota dalam grup ini</p>
            <div class="admin-chat-profile-topic">{{ $chatPayload['topicLabel'] }}</div>
          </div>

          <div class="admin-chat-profile-body">
            <div class="admin-chat-profile-section">Anggota Grup</div>
            <div class="admin-member-list">
              @foreach($activeRoom->members->sortBy(fn ($member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX) as $member)
                @php
                  $memberUser = $member->user;
                  $memberProfile = optional($memberUser)->profil;
                  $memberJoinedAt = $member->joined_at ?? $member->created_at;
                  $isCurrentUser = (int) optional($memberUser)->id === (int) auth()->id();
                @endphp
                <div class="admin-member-item">
                  <div class="admin-member-avatar">
                    <img
                      src="{{ $memberProfile?->foto ? Storage::url($memberProfile->foto) : asset('img/default-avatar.png') }}"
                      alt="{{ $memberUser?->getNamaDisplay() ?? 'Pengguna' }}"
                    >
                  </div>
                  <div class="admin-member-copy">
                    <div class="admin-member-name">{{ $memberUser?->getNamaDisplay() ?? 'Pengguna' }}</div>
                    <div class="admin-member-meta">
                      {{ $isCurrentUser ? 'Anda' : ($memberUser?->role === 'konselor' ? 'Konselor' : 'Mahasiswa') }}
                      @if($memberJoinedAt)
                        - Bergabung {{ \Carbon\Carbon::parse($memberJoinedAt)->timezone('Asia/Jakarta')->translatedFormat('j M Y, H:i') }}
                      @endif
                    </div>
                  </div>
                  @if($isCurrentUser)
                    <div class="admin-member-pill">Anda</div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </aside>
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

  if (!stage || !toggle) {
    return;
  }

  toggle.addEventListener('click', () => {
    // Toggle panel anggota grup secara inline agar admin tetap berada di ruang chat yang sama.
    const isOpen = stage.classList.toggle('is-profile-open');
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    toggle.querySelector('span').textContent = isOpen ? 'Sembunyikan anggota grup' : 'Lihat anggota grup';
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

  const scrollToBottom = () => {
    thread.scrollTop = thread.scrollHeight;
  };

  const autoResize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
  };

  const renderMessage = (message) => {
    const row = document.createElement('div');
    const isMine = Boolean(message.is_mine ?? (message.sender_id === currentUserId));

    row.className = `admin-message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;

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
        </div>
        <div class="admin-message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
      </div>
      ${isMine ? `
        <div class="admin-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      ` : ''}
    `;

    thread.appendChild(row);
  };

  const syncMessages = async () => {
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

      const knownIds = new Set(Array.from(thread.querySelectorAll('[data-message-id]')).map((element) => Number(element.dataset.messageId)));

      data.messages.forEach((message) => {
        if (!knownIds.has(Number(message.id))) {
          renderMessage(message);
        }
      });

      scrollToBottom();
    } catch (error) {
      console.error(error);
    }
  };

  (payload.messages || []).forEach((message) => renderMessage(message));
  scrollToBottom();
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
