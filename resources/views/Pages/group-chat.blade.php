@extends('layouts.master')

@push('styles')
<style>
  .group-lobby-page {
    min-height: calc(100vh - 180px);
    background:
      radial-gradient(circle at top left, rgba(16, 185, 129, 0.16), transparent 24%),
      radial-gradient(circle at top right, rgba(253, 230, 138, 0.16), transparent 22%),
      linear-gradient(180deg, #effcf5 0%, #f8fffb 24%, #ffffff 58%);
    padding: 2.25rem 0 3rem;
  }

  .group-lobby-hero {
    max-width: 1180px;
    margin: 0 auto 1.25rem;
    padding: 1.5rem 1.6rem;
    border: 1px solid rgba(209, 250, 229, 0.92);
    border-radius: 30px;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 26%),
      linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 255, 251, 0.96));
    box-shadow: 0 18px 50px rgba(6, 78, 59, 0.08);
  }

  .group-lobby-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: .9rem;
    padding: .42rem .78rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .group-lobby-hero h1 {
    margin: 0 0 .45rem;
    font-size: clamp(1.9rem, 3vw, 2.6rem);
    font-weight: 800;
    color: #064e3b;
  }

  .group-lobby-hero p {
    max-width: 760px;
    margin: 0;
    color: #475569;
    line-height: 1.8;
  }

  .anonim-toggle-card {
    max-width: 420px;
    margin-top: 1rem;
    border: 1px solid #D8E7E0;
    border-radius: 20px;
    background: linear-gradient(180deg, #FCFFFD, #F6FBF8);
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
    color: #047857;
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
    background: #0A523A;
  }

  .anonim-toggle-switch input:checked + .anonim-toggle-slider::before {
    transform: translateX(22px);
  }

  .anonim-toggle-switch input:disabled + .anonim-toggle-slider {
    cursor: wait;
    opacity: .72;
  }

  .group-lobby-grid {
    max-width: 1180px;
    margin: 0 auto;
    display: grid;
    gap: 1.25rem;
  }

  .group-card {
    border-radius: 28px;
    border: 1px solid rgba(209, 250, 229, 0.92);
    background: rgba(255, 255, 255, 0.94);
    box-shadow: 0 18px 50px rgba(6, 78, 59, 0.08);
    overflow: hidden;
  }

  .group-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.3rem 0;
  }

  .group-card-title {
    margin: 0;
    font-size: 1.08rem;
    font-weight: 800;
    color: #064e3b;
  }

  .group-card-copy {
    margin: .35rem 0 0;
    color: #64748b;
    font-size: .88rem;
    line-height: 1.7;
  }

  .group-card-head-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .group-card-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    padding: .45rem .8rem;
    border-radius: 999px;
    background: #ecfdf5;
    color: #047857;
    font-size: .72rem;
    font-weight: 800;
  }

  .group-card-action-link {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    text-decoration: none;
    border-radius: 16px;
    padding: .82rem 1rem;
    font-size: .84rem;
    font-weight: 800;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 30px rgba(6, 95, 70, 0.18);
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .group-card-action-link:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 18px 32px rgba(6, 95, 70, 0.22);
  }

  .group-card-body {
    padding: 1.1rem 1.3rem 1.3rem;
  }

  .group-empty {
    border: 1px dashed #cfe9db;
    border-radius: 20px;
    padding: 1.15rem;
    background: linear-gradient(180deg, #fbfffd, #f7fcf9);
    color: #64748b;
    font-size: .88rem;
    line-height: 1.75;
  }

  .group-empty-actions {
    margin-top: .95rem;
  }

  .group-my-list {
    display: grid;
    gap: .95rem;
  }

  .group-my-item {
    display: block;
    width: 100%;
    text-align: left;
    text-decoration: none;
    border: 1px solid rgba(221, 239, 231, 0.95);
    border-radius: 24px;
    padding: 1rem 1.05rem;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .group-my-item:hover {
    transform: translateY(-2px);
    border-color: rgba(16, 185, 129, 0.28);
    box-shadow: 0 16px 28px rgba(6, 78, 59, 0.08);
  }

  .group-my-item-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .9rem;
    margin-bottom: .65rem;
  }

  .group-my-name {
    margin: 0 0 .2rem;
    font-size: .96rem;
    font-weight: 800;
    color: #0f172a;
  }

  .group-topic-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .28rem .58rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .67rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
  }

  .group-my-meta {
    color: #64748b;
    font-size: .8rem;
    line-height: 1.7;
  }

  .group-my-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .9rem;
    margin-top: .85rem;
  }

  .group-member-preview {
    display: flex;
    align-items: center;
    gap: .8rem;
    min-width: 0;
  }

  .group-member-avatars {
    display: flex;
    align-items: center;
  }

  .group-member-avatar {
    width: 38px;
    height: 38px;
    margin-left: -10px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #fff;
    background: #d1fae5;
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
  }

  .group-member-avatar:first-child {
    margin-left: 0;
  }

  .group-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .group-member-fallback {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    color: #047857;
    font-size: .95rem;
  }

  .group-member-text {
    min-width: 0;
  }

  .group-member-text strong {
    display: block;
    font-size: .78rem;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .group-member-text span {
    display: block;
    margin-top: .08rem;
    color: #64748b;
    font-size: .74rem;
    line-height: 1.5;
  }

  .group-card-cta-secondary {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    border: none;
    border-radius: 16px;
    padding: .82rem 1rem;
    font-size: .84rem;
    font-weight: 800;
    white-space: nowrap;
    color: #065f46;
    background: #ecfdf5;
  }

  .group-public-list {
    display: grid;
    gap: .95rem;
  }

  .group-public-item {
    border: 1px solid rgba(221, 239, 231, 0.95);
    border-radius: 24px;
    padding: 1rem 1.05rem;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .group-public-item-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .9rem;
    margin-bottom: .55rem;
  }

  .group-public-name {
    margin: 0 0 .2rem;
    font-size: .96rem;
    font-weight: 800;
    color: #0f172a;
  }

  .group-public-meta {
    color: #64748b;
    font-size: .8rem;
    line-height: 1.7;
  }

  .group-public-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .9rem;
    margin-top: .9rem;
    flex-wrap: wrap;
  }

  .group-public-form {
    margin: 0;
  }

  .group-public-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    min-height: 46px;
    border: none;
    border-radius: 16px;
    padding: .82rem 1rem;
    font-size: .84rem;
    font-weight: 800;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 30px rgba(6, 95, 70, 0.18);
  }

  .group-consent-overlay {
    position: fixed;
    inset: 0;
    z-index: 3200;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.2rem;
    overflow-y: auto;
    background: rgba(15, 23, 42, 0.22);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
  }

  .group-consent-overlay.is-open {
    display: flex;
  }

  .group-consent-modal {
    position: relative;
    width: min(100%, 780px);
    border-radius: 22px;
    border: 1px solid rgba(220, 238, 228, 0.96);
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 26px 70px rgba(15, 23, 42, 0.18);
    overflow: hidden;
  }

  .group-consent-head {
    position: relative;
    padding: 1rem 1.05rem .82rem;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 28%),
      linear-gradient(180deg, #f5fff9, #ffffff);
    border-bottom: 1px solid rgba(220, 238, 228, 0.96);
  }

  .group-consent-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: .8rem;
    padding: .42rem .78rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .group-consent-head h2 {
    margin: 0 0 .35rem;
    font-size: clamp(1.08rem, 2.1vw, 1.38rem);
    font-weight: 800;
    color: #064e3b;
    line-height: 1.35;
    max-width: 620px;
  }

  .group-consent-head p {
    margin: 0;
    color: #475569;
    line-height: 1.58;
    font-size: .8rem;
    max-width: 620px;
  }

  .group-consent-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 38px;
    height: 38px;
    border-radius: 999px;
    border: 1px solid #dbece3;
    background: rgba(255, 255, 255, 0.88);
    color: #065f46;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
  }

  .group-consent-body {
    padding: .92rem 1.05rem 1.05rem;
  }

  .group-consent-body form {
    display: grid;
    gap: .72rem;
  }

  .group-consent-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr);
    gap: .72rem;
    align-items: start;
  }

  .group-consent-meta,
  .group-consent-rules {
    padding: .8rem .85rem;
    border-radius: 16px;
    border: 1px solid #dbece3;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .group-consent-meta h3,
  .group-consent-rules h3 {
    margin: 0 0 .42rem;
    color: #0f172a;
    font-size: .82rem;
    font-weight: 800;
  }

  .group-consent-meta-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .55rem;
  }

  .group-consent-meta-item {
    display: grid;
    gap: .08rem;
    color: #475569;
    font-size: .76rem;
    line-height: 1.45;
    min-width: 0;
  }

  .group-consent-meta-label {
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #047857;
  }

  .group-consent-meta-item strong {
    color: #475569;
    font-size: .76rem;
    font-weight: 800;
  }

  .group-consent-rules ul {
    margin: 0;
    padding-left: 1rem;
    color: #475569;
    font-size: .75rem;
    line-height: 1.5;
  }

  .group-consent-rules li + li {
    margin-top: .22rem;
  }

  .group-consent-check {
    display: flex;
    align-items: flex-start;
    gap: .72rem;
    padding: .74rem .82rem;
    border-radius: 16px;
    border: 1px solid #dbece3;
    background: #fff;
  }

  .group-consent-check input {
    width: 18px;
    height: 18px;
    margin-top: .08rem;
    accent-color: #047857;
    flex-shrink: 0;
  }

  .group-consent-check label {
    color: #334155;
    font-size: .78rem;
    line-height: 1.5;
    cursor: pointer;
  }

  .group-consent-actions {
    display: flex;
    gap: .8rem;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-start;
  }

  .group-consent-submit,
  .group-consent-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    min-height: 44px;
    border-radius: 14px;
    padding: .72rem 1rem;
    font-size: .78rem;
    font-weight: 800;
    text-decoration: none;
  }

  .group-consent-submit {
    border: none;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 30px rgba(6, 95, 70, 0.18);
  }

  .group-consent-submit:disabled {
    opacity: .55;
    cursor: not-allowed;
    box-shadow: none;
  }

  .group-consent-back {
    border: 1px solid #dbece3;
    color: #065f46;
    background: #fff;
  }

  .group-inline-error {
    color: #be123c;
    font-size: .8rem;
    font-weight: 700;
    margin-top: .2rem;
  }

  @media (max-width: 991.98px) {
    .group-card-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-card-head-actions {
      justify-content: flex-start;
    }

    .group-consent-grid,
    .group-consent-meta-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 767.98px) {
    .group-lobby-page {
      padding-top: 1.25rem;
    }

    .group-lobby-hero,
    .group-card {
      border-radius: 24px;
    }

    .group-card-head,
    .group-my-item-top,
    .group-my-footer,
    .group-public-item-top,
    .group-public-actions {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-card-action-link,
    .group-card-cta-secondary,
    .group-public-submit {
      width: 100%;
      justify-content: center;
    }

    .anonim-toggle-row {
      flex-direction: column;
    }

    .anonim-toggle-switch {
      align-self: flex-end;
    }

    .group-consent-modal {
      border-radius: 24px;
    }

    .group-consent-head {
      padding: 1rem .95rem .82rem;
    }

    .group-consent-close {
      top: .9rem;
      right: .9rem;
    }

    .group-consent-body {
      padding: .92rem .95rem .95rem;
    }

    .group-consent-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .group-consent-submit,
    .group-consent-back {
      width: 100%;
    }
  }
</style>
@endpush

@section('konten')
@php
  $user = Auth::user();
  $isAnonim = $user ? $user->isAnonim() : false;
  $customPublicTopics = collect($publicTopics ?? [])->where('kind', 'custom')->values();
  $shouldReopenPublicConsent = $errors->has('consent_acknowledged') && !old('invite_token');
@endphp
<section class="group-lobby-page">
  <div class="container">
    @if(session('success'))
      <div style="max-width:1180px;margin:0 auto 1rem;background:#e8fff1;border:1px solid #bbf7d0;color:#166534;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="max-width:1180px;margin:0 auto 1rem;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
        {{ session('error') }}
      </div>
    @endif

    <div class="group-lobby-hero">
      <div class="group-lobby-kicker">
        <i class="bi bi-people-fill"></i>
        <span>Lobby Grup Konseling</span>
      </div>
      <h1>Pilih grup yang ingin kamu ikuti</h1>
      <p>
        Halaman ini difokuskan untuk melihat grup yang sudah kamu ikuti dan membuka kembali ruang chat saat diperlukan.
        Jika ingin topik baru, gunakan tombol tambah grup dari kartu daftar grupmu.
      </p>
    </div>

    <div class="group-lobby-grid">
      <div class="group-card">
        <div class="group-card-head">
          <div>
            <h2 class="group-card-title">Grup Saya</h2>
            <p class="group-card-copy">Buka kembali ruang chat yang sudah pernah kamu ikuti tanpa perlu masuk dari awal.</p>
          </div>
          <div class="group-card-head-actions">
            <div class="group-card-badge">{{ $joinedRooms->count() }} grup</div>
            <a href="{{ route('mahasiswa.group-chat.create') }}" class="group-card-action-link">
              <i class="bi bi-plus-circle-fill"></i>
              <span>Gabung Grup</span>
            </a>
          </div>
        </div>

        <div class="group-card-body">
          {{-- Daftar ini sengaja hanya menampilkan grup yang sudah pernah diikuti mahasiswa. --}}
          @if($joinedRooms->isEmpty())
            <div class="group-empty">
              Kamu belum bergabung ke grup konseling mana pun.
              <div class="group-empty-actions">
              </div>
            </div>
          @else
            <div class="group-my-list">
              @foreach($joinedRooms as $room)
                @php
                  $previewMembers = $room->members
                    ->sortBy(fn ($member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
                    ->take(3);
                  $remainingMembers = max(($room->members_count ?? $room->members->count()) - $previewMembers->count(), 0);
                  $latestPreview = optional($room->latestMessage)->pesan
                    ? \Illuminate\Support\Str::limit($room->latestMessage->pesan, 92)
                    : 'Belum ada pesan di grup ini.';
                @endphp
                <a href="{{ route('mahasiswa.group-chat.room', ['group' => $room->id]) }}" class="group-my-item">
                  <div class="group-my-item-top">
                    <div>
                      <h3 class="group-my-name">{{ $room->title }}</h3>
                      <span class="group-topic-pill">{{ $room->topicLabel() }}</span>
                    </div>
                    <span class="group-card-badge">{{ $room->members_count }} anggota</span>
                  </div>

                  <div class="group-my-meta">
                    {{ $latestPreview }}
                  </div>

                  <div class="group-my-footer">
                    <div class="group-member-preview">
                      <div class="group-member-avatars">
                        @forelse($previewMembers as $member)
                            @php
                              $memberUser = $member->user;
                              $memberName = $memberUser?->getAnonimDisplayName() ?? 'Mahasiswa Anonim';
                              $memberAvatar = $memberUser?->getAnonimAvatarSvg();
                            @endphp

                            <div class="group-member-avatar">
                              @if($memberAvatar)
                                <img src="{{ $memberAvatar }}" alt="{{ $memberName }}">
                              @else
                                <div class="group-member-fallback">
                                  <i class="bi bi-person-fill"></i>
                                </div>
                              @endif
                            </div>
                          @empty
                          <div class="group-member-avatar">
                            <div class="group-member-fallback">
                              <i class="bi bi-people-fill"></i>
                            </div>
                          </div>
                        @endforelse
                      </div>
                      <div class="group-member-text">
                        <strong>
                         {{ $previewMembers->pluck('user')->filter()->map(fn ($user) => $user->getAnonimDisplayName())->take(2)->implode(', ') ?: 'Belum ada anggota tampil' }}
                        </strong>
                        <span>
                          @if($remainingMembers > 0)
                            +{{ $remainingMembers }} anggota lainnya
                          @else
                            Semua anggota tampil di preview ini
                          @endif
                        </span>
                      </div>
                    </div>
                    <span class="group-card-cta-secondary">
                      <i class="bi bi-chat-dots-fill"></i>
                      <span>Buka ruang chat</span>
                    </span>
                  </div>
                </a>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      <div class="group-card">
        <div class="group-card-head">
          <div>
            <h2 class="group-card-title">Grup Publik</h2>
            <p class="group-card-copy">Lihat grup publik yang dibuat oleh konselor dan gabung langsung ke topik yang tersedia.</p>
          </div>
          <div class="group-card-head-actions">
            <div class="group-card-badge">{{ $customPublicTopics->count() }} topik</div>
          </div>
        </div>

        <div class="group-card-body">
          @if($customPublicTopics->isEmpty())
            <div class="group-empty">
              Belum ada grup publik custom dari konselor saat ini.
            </div>
          @else
            <div class="group-public-list">
              @foreach($customPublicTopics as $topic)
                <div class="group-public-item">
                  <div class="group-public-item-top">
                    <div>
                      <h3 class="group-public-name">{{ $topic['topic_label'] }}</h3>
                      <span class="group-topic-pill">Publik</span>
                    </div>
                    <span class="group-card-badge">{{ $topic['member_count'] }} anggota</span>
                  </div>

                  <div class="group-public-meta">
                    {{ $topic['description'] }}
                  </div>

                  <div class="group-public-actions">
                    <span class="group-public-meta">
                      {{ $topic['joined'] ? 'Kamu sudah tergabung di grup ini.' : 'Grup publik bisa diikuti langsung oleh mahasiswa.' }}
                    </span>

                    @if($topic['joined'] && !empty($topic['room_url']))
                      <a href="{{ $topic['room_url'] }}" class="group-card-cta-secondary">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span>Buka ruang chat</span>
                      </a>
                    @else
                      <div class="group-public-form">
                        <button
                          type="button"
                          class="group-public-submit"
                          data-public-consent-open
                          data-room-id="{{ $topic['room_id'] ?? '' }}"
                          data-topic-key="{{ $topic['topic_key'] ?? '' }}"
                          data-topic-label="{{ $topic['topic_label'] ?? '' }}"
                          data-description="{{ $topic['description'] ?? '' }}"
                          data-member-count="{{ $topic['member_count'] ?? 0 }}"
                        >
                          <i class="bi bi-box-arrow-in-right"></i>
                          <span>Gabung Grup</span>
                        </button>
                      </div>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<div class="group-consent-overlay{{ $shouldReopenPublicConsent ? ' is-open' : '' }}" id="groupPublicConsentOverlay" aria-hidden="{{ $shouldReopenPublicConsent ? 'false' : 'true' }}">
  <div class="group-consent-modal" role="dialog" aria-modal="true" aria-labelledby="groupPublicConsentTitle">
    <div class="group-consent-head">
      <div class="group-consent-head-main">
        <div class="group-consent-kicker">
          <i class="bi bi-shield-check"></i>
          <span>Persetujuan Grup</span>
        </div>
        <h2 id="groupPublicConsentTitle">Gabung ke grup publik</h2>
        <p id="groupPublicConsentDescription">Pastikan Anda memahami identitas yang tampil, tujuan grup, dan aturan komunikasi sebelum bergabung.</p>
      </div>
      <button type="button" class="group-consent-close" id="groupPublicConsentClose" aria-label="Tutup persetujuan grup">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div class="group-consent-body">
      <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST" id="groupPublicConsentForm">
        @csrf
        <input type="hidden" name="room_id" id="groupPublicConsentRoomId" value="{{ old('room_id') }}">
        <input type="hidden" name="topic" id="groupPublicConsentTopicKey" value="{{ old('topic') }}">

        <div class="group-consent-grid">
          <div class="group-consent-meta">
            <h3>Informasi Grup</h3>
            <div class="group-consent-meta-grid">
              <div class="group-consent-meta-item">
                <span class="group-consent-meta-label">Nama Grup</span>
                <strong id="groupPublicConsentTopicLabel">Grup Publik</strong>
              </div>
              <div class="group-consent-meta-item">
                <span class="group-consent-meta-label">Pengelola</span>
                <strong>Konselor</strong>
              </div>
              <div class="group-consent-meta-item">
                <span class="group-consent-meta-label">Visibilitas Identitas</span>
                <strong>Nama anonim Anda tampil di grup publik.</strong>
                <span>Mahasiswa lain melihat alias anonim, bukan nama asli Anda.</span>
              </div>
            </div>
          </div>

          <div class="group-consent-rules">
            <h3>Aturan Sebelum Bergabung</h3>
            <ul>
              @foreach(($groupRules ?? []) as $rule)
                <li>{{ $rule }}</li>
              @endforeach
            </ul>
          </div>
        </div>

        <div class="group-consent-check">
          <input
            type="checkbox"
            id="groupPublicConsentCheckbox"
            name="consent_acknowledged"
            value="1"
            {{ old('consent_acknowledged') ? 'checked' : '' }}
            required
          >
          <label for="groupPublicConsentCheckbox">
            Saya memahami bahwa grup publik ini akan menampilkan identitas anonim saya kepada anggota lain, dan saya setuju untuk bergabung sesuai aturan yang berlaku.
          </label>
        </div>

        @error('consent_acknowledged')
          <div class="group-inline-error">{{ $message }}</div>
        @enderror

        @error('room_id')
          <div class="group-inline-error">{{ $message }}</div>
        @enderror

        @error('topic')
          <div class="group-inline-error">{{ $message }}</div>
        @enderror

        <div class="group-consent-actions">
          <button type="submit" class="group-consent-submit" id="groupPublicConsentSubmitBtn" {{ old('consent_acknowledged') ? '' : 'disabled' }}>
            <i class="bi bi-check2-circle"></i>
            <span>Setuju dan Masuk Grup</span>
          </button>
          <button type="button" class="group-consent-back" id="groupPublicConsentBackBtn">
            <i class="bi bi-arrow-left"></i>
            <span>Tutup</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (() => {
    const overlay = document.getElementById('groupPublicConsentOverlay');
    const closeBtn = document.getElementById('groupPublicConsentClose');
    const backBtn = document.getElementById('groupPublicConsentBackBtn');
    const checkbox = document.getElementById('groupPublicConsentCheckbox');
    const submitBtn = document.getElementById('groupPublicConsentSubmitBtn');
    const roomIdInput = document.getElementById('groupPublicConsentRoomId');
    const topicKeyInput = document.getElementById('groupPublicConsentTopicKey');
    const topicLabelEl = document.getElementById('groupPublicConsentTopicLabel');
    const titleEl = document.getElementById('groupPublicConsentTitle');
    const descriptionEl = document.getElementById('groupPublicConsentDescription');
    const triggers = Array.from(document.querySelectorAll('[data-public-consent-open]'));

    if (!overlay || !checkbox || !submitBtn) {
      return;
    }

    if (overlay.parentElement !== document.body) {
      document.body.appendChild(overlay);
    }

    const defaultBodyOverflow = document.body.style.overflow;

    const syncConsentState = () => {
      submitBtn.disabled = !checkbox.checked;
    };

    const openOverlay = () => {
      overlay.classList.add('is-open');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };

    const closeOverlay = () => {
      overlay.classList.remove('is-open');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = defaultBodyOverflow;
    };

    const hydrateConsent = (trigger) => {
      const roomId = trigger.dataset.roomId || '';
      const topicKey = trigger.dataset.topicKey || '';
      const topicLabel = trigger.dataset.topicLabel || 'Grup Publik';
      const description = trigger.dataset.description || 'Pastikan Anda memahami identitas yang tampil, tujuan grup, dan aturan komunikasi sebelum bergabung.';
      const memberCount = Number(trigger.dataset.memberCount || 0);

      if (roomIdInput) {
        roomIdInput.value = roomId;
      }

      if (topicKeyInput) {
        topicKeyInput.value = topicKey;
      }

      if (topicLabelEl) {
        topicLabelEl.textContent = topicLabel;
      }

      if (titleEl) {
        titleEl.textContent = `Gabung ke grup publik "${topicLabel}"`;
      }

      if (descriptionEl) {
        descriptionEl.textContent = memberCount > 0
          ? `${description} Saat ini ada ${memberCount} anggota aktif di grup ini.`
          : description;
      }

      checkbox.checked = false;
      syncConsentState();
    };

    triggers.forEach((trigger) => {
      trigger.addEventListener('click', () => {
        hydrateConsent(trigger);
        openOverlay();
      });
    });

    checkbox.addEventListener('change', syncConsentState);
    closeBtn?.addEventListener('click', closeOverlay);
    backBtn?.addEventListener('click', closeOverlay);

    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) {
        closeOverlay();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
        closeOverlay();
      }
    });

    const previousRoomId = @json((string) old('room_id'));
    const previousTopic = @json((string) old('topic'));
    const shouldReopen = @json($shouldReopenPublicConsent);

    if (shouldReopen) {
      const matchedTrigger = triggers.find((trigger) => {
        const sameRoom = previousRoomId !== '' && trigger.dataset.roomId === previousRoomId;
        const sameTopic = previousTopic !== '' && trigger.dataset.topicKey === previousTopic;
        return sameRoom || sameTopic;
      });

      if (matchedTrigger) {
        hydrateConsent(matchedTrigger);
      }

      checkbox.checked = {{ old('consent_acknowledged') ? 'true' : 'false' }};
      syncConsentState();
      openOverlay();
    } else {
      syncConsentState();
    }

    window.addEventListener('pagehide', () => {
      document.body.style.overflow = defaultBodyOverflow;
    });
  })();
</script>
@endpush
