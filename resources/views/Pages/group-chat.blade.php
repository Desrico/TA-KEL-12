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

  .group-my-heading {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: .38rem;
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

  .group-consent-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    z-index: 1200;
  }

  .group-consent-modal.is-open {
    display: flex;
  }

  .group-consent-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.34);
    backdrop-filter: blur(10px);
  }

  .group-consent-dialog {
    position: relative;
    width: min(680px, calc(100vw - 2rem));
    max-height: min(78vh, 620px);
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(209, 250, 229, 0.92);
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 24px 60px rgba(6, 78, 59, 0.18);
  }

  .group-consent-head {
    padding: 1.05rem 1.15rem 0;
  }

  .group-consent-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: .68rem;
    padding: .34rem .66rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .66rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .group-consent-head h2 {
    margin: 0 0 .46rem;
    font-size: clamp(1.08rem, 2vw, 1.42rem);
    font-weight: 800;
    color: #064e3b;
  }

  .group-consent-head p {
    margin: 0;
    color: #475569;
    font-size: .78rem;
    line-height: 1.5;
  }

  .group-consent-body {
    padding: .88rem 1.15rem 1.05rem;
    display: grid;
    gap: .62rem;
  }

  .group-consent-summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .62rem;
  }

  .group-consent-summary-item {
    border-radius: 16px;
    border: 1px solid #edf4ef;
    background: linear-gradient(180deg, #fcfffd, #f6fbf8);
    padding: .78rem .85rem;
  }

  .group-consent-summary-item span {
    display: block;
    margin-bottom: .28rem;
    color: #64748b;
    font-size: .67rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
  }

  .group-consent-summary-item strong {
    color: #0f172a;
    font-size: .83rem;
    font-weight: 800;
    line-height: 1.35;
  }

  .group-consent-rules {
    padding: .74rem .82rem .78rem;
    border-radius: 18px;
    border: 1px solid #dbece3;
    background: #fff;
  }

  .group-consent-rules h3 {
    margin: 0 0 .18rem;
    font-size: .86rem;
    font-weight: 800;
    color: #0f172a;
  }

  .group-consent-rules p {
    margin: 0 0 .45rem;
    color: #64748b;
    font-size: .71rem;
    line-height: 1.4;
  }

  .group-consent-rules-list {
    display: grid;
    gap: .28rem;
  }

  .group-consent-rule {
    display: flex;
    align-items: flex-start;
    gap: .45rem;
    padding: 0;
    border: none;
    background: transparent;
  }

  .group-consent-rule-badge {
    width: 18px;
    height: 18px;
    border-radius: 999px;
    background: #dff8ea;
    color: #047857;
    display: grid;
    place-items: center;
    font-size: .58rem;
    font-weight: 900;
    flex-shrink: 0;
    margin-top: .1rem;
  }

  .group-consent-rule-text {
    color: #334155;
    font-size: .72rem;
    line-height: 1.34;
  }

  .group-consent-form {
    display: grid;
    gap: .68rem;
  }

  .group-consent-checkbox {
    display: flex;
    align-items: flex-start;
    gap: .66rem;
    padding: .78rem .85rem;
    border-radius: 16px;
    border: 1px solid #dbece3;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .group-consent-checkbox input[type="checkbox"] {
    margin-top: .08rem;
    width: 16px;
    height: 16px;
    accent-color: #0A523A;
    flex-shrink: 0;
  }

  .group-consent-checkbox label {
    color: #334155;
    font-size: .75rem;
    line-height: 1.42;
    font-weight: 600;
  }

  .group-consent-error {
    color: #be123c;
    font-size: .72rem;
    font-weight: 700;
  }

  .group-consent-note {
    padding: .58rem .72rem;
    border-radius: 14px;
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    font-size: .69rem;
    line-height: 1.34;
  }

  .group-consent-actions {
    display: flex;
    align-items: center;
    gap: .62rem;
    flex-wrap: wrap;
  }

  .group-consent-submit,
  .group-consent-cancel {
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

  .group-consent-cancel {
    border: 1px solid #dbece3;
    color: #065f46;
    background: #fff;
  }

  @media (max-width: 991.98px) {
    .group-card-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-card-head-actions {
      justify-content: flex-start;
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
    .group-my-footer {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-card-action-link,
    .group-card-cta-secondary {
      width: 100%;
      justify-content: center;
    }

    .anonim-toggle-row {
      flex-direction: column;
    }

    .anonim-toggle-switch {
      align-self: flex-end;
    }

    .group-consent-dialog {
      border-radius: 24px;
    }

    .group-consent-summary {
      grid-template-columns: 1fr;
    }

    .group-consent-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .group-consent-submit,
    .group-consent-cancel {
      width: 100%;
    }
  }
</style>
@endpush

@section('konten')
@php
  $user = Auth::user();
  $isAnonim = $user ? $user->isAnonim() : false;
  $hasJoinedRooms = $joinedRooms->isNotEmpty();
  $consentFallbackRoom = old('room_id') ? \App\Models\GroupChatRoom::find(old('room_id')) : null;
  $consentPayload = $consentPayload ?? null;
  $consentMode = old('token', $consentPayload['token'] ?? null)
      ? 'private'
      : ($consentPayload['mode'] ?? ((optional($consentFallbackRoom)->isPrivate() ?? false) ? 'private' : 'public'));
  $groupRules = $consentPayload['rules'] ?? \App\Support\GroupChatSupport::rules();
  $modalShouldOpen = (bool) $consentPayload || $errors->has('consent_confirmed');
  $consentTopic = old('topic', $consentPayload['topic'] ?? optional($consentFallbackRoom)->topic ?? '');
  $consentTitle = $consentPayload['title']
      ?? optional($consentFallbackRoom)->title
      ?? ($consentTopic ? 'Grup Konseling ' . (\App\Models\GroupChatRoom::topicOptions()[$consentTopic] ?? ucfirst(str_replace('_', ' ', $consentTopic))) : 'Grup Konseling');
  $consentVisibility = $consentPayload['visibility_label']
      ?? (optional($consentFallbackRoom)->isPrivate() ? 'Privat' : 'Publik');
  $consentDescription = $consentPayload['description']
      ?? 'Baca aturan grup terlebih dahulu. Setelah menyetujui, Anda akan langsung masuk ke ruang grup yang dituju.';
  $consentNote = $consentPayload['note']
      ?? 'Setelah menyetujui aturan, Anda akan langsung masuk ke grup yang dituju.';
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
            @if($hasJoinedRooms)
              <a href="{{ route('mahasiswa.group-chat.create') }}" class="group-card-action-link">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Gabung Grup</span>
              </a>
            @endif
          </div>
        </div>

        <div class="group-card-body">
          {{-- Daftar ini sengaja hanya menampilkan grup yang sudah pernah diikuti mahasiswa. --}}
          @if($joinedRooms->isEmpty())
            <div class="group-empty">
              Kamu belum bergabung ke grup konseling mana pun.
              <div class="group-empty-actions">
                <a href="{{ route('mahasiswa.group-chat.create') }}" class="group-card-action-link">
                  <i class="bi bi-plus-circle-fill"></i>
                  <span>Gabung Grup</span>
                </a>
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
                    <div class="group-my-heading">
                      <h3 class="group-my-name">{{ $room->title }}</h3>
                      <span class="group-topic-pill">{{ $room->isPrivate() ? 'Grup Privat' : 'Grup Publik' }}</span>
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
    </div>
  </div>
</section>

<div
  class="group-consent-modal {{ $modalShouldOpen ? 'is-open' : '' }}"
  id="groupConsentModal"
  aria-hidden="{{ $modalShouldOpen ? 'false' : 'true' }}"
>
  <div class="group-consent-backdrop" data-close-group-consent></div>

  <div class="group-consent-dialog" role="dialog" aria-modal="true" aria-labelledby="groupConsentHeading">
    <div class="group-consent-head">
      <div class="group-consent-kicker">
        <i class="bi bi-shield-check"></i>
        <span>Persetujuan Grup</span>
      </div>
      <h2 id="groupConsentHeading">Konfirmasi sebelum bergabung ke grup</h2>
      <p>{{ $consentDescription }}</p>
    </div>

    <div class="group-consent-body">
      <div class="group-consent-summary">
        <div class="group-consent-summary-item">
          <span>Nama Grup</span>
          <strong>{{ $consentTitle }}</strong>
        </div>
        <div class="group-consent-summary-item">
          <span>Tipe Grup</span>
          <strong>{{ $consentVisibility }}</strong>
        </div>
      </div>

      <div class="group-consent-rules">
        <h3>Aturan Grup Konseling</h3>
        <p>Baca singkat aturannya sebelum masuk grup.</p>

        <div class="group-consent-rules-list">
          @foreach($groupRules as $index => $rule)
            <div class="group-consent-rule">
              <div class="group-consent-rule-badge">{{ $index + 1 }}</div>
              <div class="group-consent-rule-text">{{ $rule }}</div>
            </div>
          @endforeach
        </div>
      </div>

      <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST" class="group-consent-form">
        @csrf

        @if(old('token', $consentPayload['token'] ?? ''))
          <input type="hidden" name="token" value="{{ old('token', $consentPayload['token'] ?? '') }}">
        @endif

        @if(old('room_id', $consentPayload['room_id'] ?? ''))
          <input type="hidden" name="room_id" value="{{ old('room_id', $consentPayload['room_id'] ?? '') }}">
        @endif

        @if($consentTopic && $consentMode !== 'private')
          <input type="hidden" name="topic" value="{{ $consentTopic }}">
        @endif

        <div class="group-consent-checkbox">
          <input type="checkbox" id="groupConsentCheckbox" name="consent_confirmed" value="1" {{ old('consent_confirmed') ? 'checked' : '' }}>
          <label for="groupConsentCheckbox">
            Saya telah membaca dan menyetujui seluruh aturan grup konseling ini, serta memahami bahwa saya harus menjaga percakapan tetap aman, relevan, dan sopan.
          </label>
        </div>

        @error('consent_confirmed')
          <div class="group-consent-error">{{ $message }}</div>
        @enderror

        <div class="group-consent-note">{{ $consentNote }}</div>

        <div class="group-consent-actions">
          <button type="submit" class="group-consent-submit">
            <i class="bi bi-check-circle-fill"></i>
            <span>Setuju dan Gabung Grup</span>
          </button>
          <button type="button" class="group-consent-cancel" data-close-group-consent>
            <i class="bi bi-x-circle"></i>
            <span>Batal</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('groupConsentModal');
    const closeButtons = document.querySelectorAll('[data-close-group-consent]');

    if (! modal) {
      return;
    }

    const closeModal = () => {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    if (modal.classList.contains('is-open')) {
      document.body.style.overflow = 'hidden';
    }

    closeButtons.forEach(function (button) {
      button.addEventListener('click', closeModal);
    });
  });
</script>
@endpush
