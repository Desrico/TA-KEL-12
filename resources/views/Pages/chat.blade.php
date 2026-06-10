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
@endphp

@push('styles')
<style>
  .chat-page {
    min-height: calc(100vh - 82px);
    background:
      radial-gradient(circle at top left, rgba(16, 185, 129, 0.18), transparent 30%),
      radial-gradient(circle at top right, rgba(110, 231, 183, 0.18), transparent 24%),
      linear-gradient(180deg, #effcf5 0%, #eefbf4 26%, #e2f7ec 100%);
    width: 100vw;
    margin-left: calc(50% - 50vw);
    margin-right: calc(50% - 50vw);
    padding: 1.2rem 0 2.4rem;
  }

  .chat-page-inner {
    width: 100%;
    min-height: inherit;
    padding: 0;
  }

  .chat-shell {
    width: 100%;
    max-width: none;
    margin: 0;
    display: block;
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
    min-height: calc(100vh - 96px);
    max-height: calc(100vh - 96px);
    border-radius: 0;
    border-inline: 0;
    box-shadow: none;
  }

  .chat-topbar {
    position: sticky;
    top: 0;
    z-index: 3;
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
    flex: 1;
    min-height: 0;
    padding: 1rem 1.5rem .75rem;
    overflow-y: auto;
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

  .message-row {
    display: flex;
    gap: .65rem;
    margin-bottom: .78rem;
    align-items: flex-end;
  }

  .message-row.mine {
    justify-content: flex-end;
  }

  .message-row.mine .message-meta {
    justify-content: flex-end;
  }

  .message-row.mine .message-bubble {
    background: linear-gradient(135deg, #047857, #059669);
    color: #fff;
    border-bottom-right-radius: 10px;
    box-shadow: 0 16px 34px rgba(5, 150, 105, 0.2);
  }

  .message-row.other .message-bubble {
    background: #ffffff;
    color: #1f2937;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-bottom-left-radius: 10px;
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
    max-width: min(88%, 920px);
    position: relative;
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
    padding: .72rem .92rem .78rem;
    border-radius: 20px;
    font-size: .88rem;
    line-height: 1.58;
    word-break: break-word;
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

  .message-row.mine:hover .message-actions,
  .message-row.mine.is-menu-open .message-actions {
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
    position: sticky;
    bottom: 0;
    z-index: 3;
    padding: .7rem 1.5rem .8rem;
    border-top: 1px solid rgba(221, 239, 231, 0.95);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
  }

  .chat-composer.is-locked {
    background: linear-gradient(180deg, rgba(248, 255, 251, 0.96), rgba(255, 255, 255, 0.96));
  }

  .chat-form {
    display: flex;
    align-items: flex-end;
    gap: .75rem;
    padding: .62rem;
    border-radius: 20px;
    border: 1px solid rgba(209, 250, 229, 0.96);
    background: linear-gradient(180deg, #ffffff, #f8fffb);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
  }

  .chat-form.is-disabled {
    opacity: .78;
    border-style: dashed;
  }

  .chat-input {
    flex: 1;
    border: none;
    resize: none;
    background: transparent;
    min-height: 46px;
    max-height: 140px;
    padding: .45rem .25rem;
    color: #0f172a;
    font-size: .88rem;
    outline: none;
  }

  .chat-input:disabled {
    color: #94a3b8;
    cursor: not-allowed;
  }

  .chat-send {
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    font-size: 1.18rem;
    box-shadow: 0 12px 24px rgba(6, 95, 70, 0.22);
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .chat-send:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 20px 36px rgba(6, 95, 70, 0.3);
  }

  .chat-send:disabled {
    opacity: .5;
    cursor: not-allowed;
    box-shadow: none;
  }

  .chat-hint {
    margin-top: .5rem;
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
      min-height: calc(100vh - 88px);
      max-height: calc(100vh - 88px);
    }
  }

  @media (max-width: 767.98px) {
    .chat-page {
      min-height: calc(100vh - 78px);
      padding: .95rem 0 2rem;
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
      align-items: flex-start;
    }

    .message-avatar {
      width: 30px;
      height: 30px;
      border-radius: 10px;
    }

    .chat-main {
      min-height: calc(100vh - 78px);
      max-height: calc(100vh - 78px);
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
    @elseif($isBlockedBySchedule)
      <div class="chat-shell">
        <div class="chat-main">
          <div class="chat-topbar">
            <div class="chat-counselor">
              <div class="chat-avatar">
                <img src="{{ $chatPayload['counselorAvatar'] }}" alt="{{ $chatPayload['counselorName'] }}">
              </div>
              <div>
                <div class="chat-title">{{ $chatPayload['counselorName'] }}</div>
                <p class="chat-subtitle">
                  {{ $topik ?: 'Konseling online terjadwal' }}
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

          <div class="chat-composer is-locked">
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
        </div>

      </div>
    @elseif($startReady)
      <div class="chat-shell">
        <div class="chat-main">
          <div class="chat-topbar">
            <div class="chat-counselor">
              <div class="chat-avatar">
                <img src="{{ $chatPayload['counselorAvatar'] }}" alt="{{ $chatPayload['counselorName'] }}">
              </div>
              <div>
                <div class="chat-title">{{ $chatPayload['counselorName'] }}</div>
                <p class="chat-subtitle">
                  {{ $topik ?: 'Konseling online terjadwal' }}
                </p>
              </div>
            </div>
            <div class="chat-badge">{{ $statusLabel }}</div>
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

          <div class="chat-composer is-locked">
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
          </div>
        </div>

      </div>
    @else
      <div class="chat-shell">
        <div class="chat-main">
          <div class="chat-topbar">
            <div class="chat-counselor">
              <div class="chat-avatar">
                <img src="{{ $chatPayload['counselorAvatar'] }}" alt="{{ $chatPayload['counselorName'] }}">
              </div>
              <div>
                <div class="chat-title">{{ $chatPayload['counselorName'] }}</div>
                <p class="chat-subtitle">
                  {{ $topik ?: 'Konseling online aktif' }}
                </p>
              </div>
            </div>
            <div class="chat-badge">{{ $statusLabel }}</div>
          </div>

          <div class="chat-thread" id="chatThread"></div>

          <div class="chat-composer">
            <form id="chatForm" class="chat-form">
              <textarea
                id="chatInput"
                class="chat-input"
                rows="1"
                maxlength="2000"
                placeholder="Tulis pesan Anda di sini..."
              ></textarea>
              <button type="submit" class="chat-send" id="chatSendBtn">
                <i class="bi bi-send-fill"></i>
              </button>
            </form>

          </div>
        </div>

      </div>
    @endif
  </div>
</section>
@endsection

@if($activeSession && $chatPayload && $chatAccessGranted)
@push('scripts')
<script>
(() => {
  const payload = @json($chatPayload);
  const currentUserId = {{ auth()->id() }};
  const thread = document.getElementById('chatThread');
  const form = document.getElementById('chatForm');
  const input = document.getElementById('chatInput');
  const sendBtn = document.getElementById('chatSendBtn');
  const hint = document.getElementById('chatHint');

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

  const scrollToBottom = (behavior = 'auto') => {
    requestAnimationFrame(() => {
      thread.scrollTo({ top: thread.scrollHeight, behavior });
    });
  };

  const autoResize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
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

  const closeAllMenus = () => {
    thread.querySelectorAll('.message-row.is-menu-open').forEach((element) => {
      element.classList.remove('is-menu-open');
    });
  };

  // Bubble dan editor inline dipisah supaya mode edit terasa seperti chat modern, bukan popup.
  const buildMessageBubbleMarkup = (message, isMine) => `
    <div class="message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
    ${isMine ? `
      <div class="message-actions">
        <button type="button" class="message-action-toggle" data-action="toggle-menu" aria-label="Opsi pesan">
          <i class="bi bi-three-dots"></i>
        </button>
        <div class="message-action-menu">
          <button type="button" class="message-action-item" data-action="edit-message" data-message-id="${message.id}">
            <i class="bi bi-pencil-square"></i>
            <span>Edit pesan</span>
          </button>
          <button type="button" class="message-action-item delete" data-action="delete-message" data-message-id="${message.id}">
            <i class="bi bi-trash3"></i>
            <span>Hapus pesan</span>
          </button>
        </div>
      </div>
    ` : ''}
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

  const buildDeleteConfirmMarkup = (messageId) => `
    <div class="message-delete-confirm" data-delete-message-id="${messageId}">
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
    row.dataset.messageEdited = message.is_edited ? '1' : '0';

    row.innerHTML = `
      ${isMine ? '' : `
        <div class="message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      `}
      <div class="message-content">
        <div class="message-meta">
          <span class="message-name">${escapeHtml(message.sender_name)}</span>
          <span>${escapeHtml(message.time)}</span>
          ${message.is_edited ? '<span class="message-edited">telah diedit</span>' : ''}
        </div>
        <div class="message-bubble-shell">${buildMessageBubbleMarkup(message, isMine)}</div>
      </div>
      ${isMine ? `
        <div class="message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      ` : ''}
    `;

    thread.appendChild(row);
  };

  const renderInitialMessages = () => {
    renderMessages(payload.messages || []);
  };

  const renderMessages = (messages, force = false) => {
    // Render ulang penuh agar edit dan delete tersinkron untuk semua client.
    if (!force && hasActiveInlineState()) {
      return;
    }

    thread.innerHTML = '';
    messages.forEach((message) => renderMessage(message));
    closeAllMenus();
    scrollToBottom();
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

        renderMessage({
          ...event.message,
          is_mine: Number(event.message.sender_id) === currentUserId,
        });

        scrollToBottom();
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
      const row = toggleButton.closest('.message-row');
      const willOpen = !row.classList.contains('is-menu-open');
      closeAllMenus();
      row.classList.toggle('is-menu-open', willOpen);
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
    hint.textContent = 'Mengirim pesan...';

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
          sesi_id: payload.sessionId,
          jadwal_id: payload.jadwalId,
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
      hint.textContent = 'Pesan terkirim. Konselor akan menerima pembaruan secara realtime.';
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
