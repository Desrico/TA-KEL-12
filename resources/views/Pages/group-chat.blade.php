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

  .group-my-title-row {
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .group-my-visibility {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    border-radius: 999px;
    padding: .28rem .62rem;
    font-size: .68rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
  }

  .group-my-visibility.is-private {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
  }

  .group-my-visibility.is-public {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #bbf7d0;
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

  .group-member-animal {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    font-size: 1.15rem;
    background: #ecfdf5;
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

  .group-lobby-modal {
    position: fixed;
    inset: 0;
    z-index: 2200;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, .45);
  }

  .group-lobby-modal.show {
    display: flex;
  }

  .group-lobby-modal-dialog {
    width: min(390px, 100%);
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid rgba(187, 247, 208, .95);
    box-shadow: 0 24px 60px rgba(15, 23, 42, .2);
    padding: 1.25rem;
    text-align: center;
  }

  .group-lobby-modal-icon {
    width: 54px;
    height: 54px;
    margin: 0 auto .85rem;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: #dcfce7;
    color: #047857;
    font-size: 1.55rem;
  }

  .group-lobby-modal-dialog h3 {
    margin: 0 0 .5rem;
    color: #0f172a;
    font-size: 1.08rem;
    font-weight: 900;
  }

  .group-lobby-modal-dialog p {
    margin: 0;
    color: #475569;
    font-size: .9rem;
    line-height: 1.65;
  }

  .group-lobby-modal-close {
    margin-top: 1rem;
    border: none;
    border-radius: 999px;
    background: #047857;
    color: #ffffff;
    padding: .68rem 1.2rem;
    font-size: .84rem;
    font-weight: 800;
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
      border-radius: 20px;
    }

    .group-card-head {
      padding: 1rem 1rem 0;
    }

    .group-card-head,
    .group-my-item-top,
    .group-my-footer {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-card-head-actions {
      width: 100%;
      justify-content: flex-start;
    }

    .group-card-body {
      padding: 1rem;
    }

    .group-my-item {
      min-width: 0;
      padding: .9rem;
      border-radius: 18px;
      overflow: hidden;
    }

    .group-my-item-top,
    .group-my-item-top > div,
    .group-my-title-row,
    .group-member-preview,
    .group-member-text {
      width: 100%;
      min-width: 0;
    }

    .group-my-title-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      align-items: start;
      gap: .45rem;
    }

    .group-my-name {
      width: 100%;
      overflow-wrap: anywhere;
      line-height: 1.4;
    }

    .group-my-visibility {
      width: max-content;
      max-width: 100%;
    }

    .group-my-meta {
      overflow-wrap: anywhere;
    }

    .group-my-footer {
      width: 100%;
    }

    .group-member-preview {
      align-items: flex-start;
    }

    .group-member-avatars {
      flex: 0 0 auto;
    }

    .group-member-text strong {
      white-space: normal;
      overflow: visible;
      text-overflow: clip;
      overflow-wrap: anywhere;
      line-height: 1.45;
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
  }

  @media (max-width: 420px) {
    .group-lobby-page .container {
      padding-inline: .85rem;
    }

    .group-card-head,
    .group-card-body {
      padding-inline: .85rem;
    }

    .group-member-preview {
      flex-direction: column;
      gap: .55rem;
    }

    .group-member-text span {
      margin-top: .2rem;
    }
  }
</style>
@endpush

@section('konten')
@php
  $user = Auth::user();
  $isAnonim = $user ? $user->isAnonim() : false;
  [$privateRooms, $publicRooms] = $joinedRooms->partition(fn ($room) => method_exists($room, 'isPrivate')
      ? $room->isPrivate()
      : (($room->visibility ?? 'public') === 'private'));
  $groupSections = [
      [
          'title' => 'Grup Privat',
          'copy' => 'Grup undangan dari konselor yang menampilkan identitas asli anggota.',
          'rooms' => $privateRooms,
          'empty' => 'Kamu belum bergabung ke grup privat mana pun.',
          'show_join_action' => false,
      ],
      [
          'title' => 'Grup Publik',
          'copy' => 'Grup konseling berdasarkan topik yang bisa kamu ikuti dengan identitas anonim.',
          'rooms' => $publicRooms,
          'empty' => 'Kamu belum bergabung ke grup publik mana pun.',
          'show_join_action' => true,
      ],
  ];
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
      @foreach($groupSections as $section)
      <div class="group-card">
        <div class="group-card-head">
          <div>
            <h2 class="group-card-title">{{ $section['title'] }}</h2>
            <p class="group-card-copy">{{ $section['copy'] }}</p>
          </div>
          <div class="group-card-head-actions">
            <div class="group-card-badge">{{ $section['rooms']->count() }} grup</div>
            @if($section['show_join_action'])
            <a href="{{ route('mahasiswa.group-chat.create') }}" class="group-card-action-link">
              <span>Gabung Grup</span>
            </a>
            @endif
          </div>
        </div>

        <div class="group-card-body">
          {{-- Daftar ini sengaja hanya menampilkan grup yang sudah pernah diikuti mahasiswa. --}}
          @if($section['rooms']->isEmpty())
            <div class="group-empty">
              {{ $section['empty'] }}
              <div class="group-empty-actions">
              </div>
            </div>
          @else
            <div class="group-my-list">
              @foreach($section['rooms'] as $room)
                @php
                  $isPrivateRoom = method_exists($room, 'isPrivate') ? $room->isPrivate() : ($room->visibility === 'private');

                  $previewMembers = $room->members
                    ->sortBy(fn ($member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX)
                    ->take(3);

                  $remainingMembers = max(($room->members_count ?? $room->members->count()) - $previewMembers->count(), 0);

                  $latestPreview = optional($room->latestMessage)->pesan
                    ? \Illuminate\Support\Str::limit($room->latestMessage->pesan, 92)
                    : 'Belum ada pesan di grup ini.';

                  $resolveMemberName = function ($member) use ($isPrivateRoom) {
                      $memberUser = $member->user ?? null;

                      if ($isPrivateRoom) {
                          return $memberUser?->nama
                              ?? $memberUser?->name
                              ?? $memberUser?->username_cis
                              ?? $memberUser?->email
                              ?? 'Mahasiswa';
                      }

                      return $memberUser?->getAnonimDisplayName() ?? 'Mahasiswa Anonim';
                  };

                  $resolveMemberAvatar = function ($member) use ($isPrivateRoom, $resolveMemberName) {
                      $memberUser = $member->user ?? null;

                      if ($isPrivateRoom) {
                          $name = $resolveMemberName($member);

                          return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=d9f7e7&color=065f46';
                      }

                      return $memberUser?->getAnonimAvatarSvg();
                  };

                  $animalIcon = function ($name) {
                      $name = strtolower((string) $name);
                      $icons = [
                          'lobster' => '🦞',
                          'kanguru' => '🦘',
                          'gajah' => '🐘',
                          'serigala' => '🐺',
                          'kuda' => '🐴',
                          'zebra' => '🦓',
                          'badak' => '🦏',
                          'jerapah' => '🦒',
                          'bison' => '🦬',
                          'paus' => '🐋',
                          'hiu' => '🦈',
                          'gurita' => '🐙',
                          'kepiting' => '🦀',
                          'penyu' => '🐢',
                          'elang' => '🦅',
                          'flamingo' => '🦩',
                          'bebek' => '🦆',
                          'kupu' => '🦋',
                          'kelelawar' => '🦇',
                          'landak' => '🦔',
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
                      ];

                      foreach ($icons as $keyword => $icon) {
                          if (str_contains($name, $keyword)) {
                              return $icon;
                          }
                      }

                      return '🐾';
                  };
                @endphp
                <a href="{{ route('mahasiswa.group-chat.room', ['group' => $room->id]) }}" class="group-my-item">
                  <div class="group-my-item-top">
                    <div>
                      <div class="group-my-title-row">
                        <h3 class="group-my-name">{{ $room->title }}</h3>
                        <span class="group-my-visibility {{ $isPrivateRoom ? 'is-private' : 'is-public' }}">
                          <span>{{ $isPrivateRoom ? 'Privat' : 'Publik' }}</span>
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="group-my-meta">
                    {{ $latestPreview }}
                  </div>

                  <div class="group-my-footer">
                    <div class="group-member-preview">
                      <div class="group-member-avatars">
                       @forelse($previewMembers as $member)
                          @php
                            $memberName = $resolveMemberName($member);
                            $memberAvatar = $resolveMemberAvatar($member);
                          @endphp

                          <div class="group-member-avatar">
                            @if(! $isPrivateRoom)
                              <div class="group-member-animal" title="{{ $memberName }}">{{ $animalIcon($memberName) }}</div>
                            @elseif($memberAvatar)
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
                          {{ $previewMembers->map(fn ($member) => $resolveMemberName($member))->take(2)->implode(', ') ?: 'Belum ada anggota tampil' }}
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
      @endforeach
    </div>
  </div>
</section>

@if(session('group_left_success_modal'))
  @php($groupLeftModal = session('group_left_success_modal'))
  <div class="group-lobby-modal show" id="groupLeftSuccessModal" aria-hidden="false">
    <div class="group-lobby-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="groupLeftSuccessTitle">
      <div class="group-lobby-modal-icon">
        <i class="bi bi-check2"></i>
      </div>
      <h3 id="groupLeftSuccessTitle">{{ $groupLeftModal['title'] ?? 'Berhasil Keluar dari Grup' }}</h3>
      <p>{{ $groupLeftModal['message'] ?? 'Anda berhasil keluar dari grup.' }}</p>
      <button type="button" class="group-lobby-modal-close" id="groupLeftSuccessClose">Tutup</button>
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
(() => {
  const modal = document.getElementById('groupLeftSuccessModal');
  const closeBtn = document.getElementById('groupLeftSuccessClose');

  if (!modal || !closeBtn) {
    return;
  }

  const closeModal = () => {
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
  };

  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeModal();
    }
  });
})();
</script>
@endpush

@push('scripts')

@endpush
