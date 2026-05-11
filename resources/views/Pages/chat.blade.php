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
    min-height: calc(100vh - 180px);
    background:
      radial-gradient(circle at top left, rgba(16, 185, 129, 0.18), transparent 30%),
      radial-gradient(circle at top right, rgba(110, 231, 183, 0.18), transparent 24%),
      linear-gradient(180deg, #effcf5 0%, #f8fffb 20%, #ffffff 60%);
    padding: 2.25rem 0 3rem;
  }

  .chat-shell {
    max-width: 1180px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(280px, .95fr);
    gap: 1.5rem;
  }

  .chat-main,
  .chat-side,
  .chat-empty {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(209, 250, 229, 0.9);
    border-radius: 30px;
    box-shadow: 0 18px 50px rgba(6, 78, 59, 0.08);
    backdrop-filter: blur(12px);
  }

  .chat-main {
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 760px;
  }

  .chat-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.2rem 1.4rem;
    background: linear-gradient(135deg, rgba(239, 252, 245, 0.98), rgba(255, 255, 255, 0.94));
    border-bottom: 1px solid rgba(221, 239, 231, 0.95);
  }

  .chat-counselor {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 0;
  }

  .chat-avatar {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.96);
    box-shadow: 0 10px 24px rgba(6, 78, 59, 0.16);
    flex-shrink: 0;
  }

  .chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .chat-title {
    font-size: 1.2rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .14rem;
  }

  .chat-subtitle {
    color: #4b7a68;
    font-size: .92rem;
    margin: 0;
  }

  .chat-badge {
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
  }

  .chat-badge::before {
    content: "";
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
  }

  .chat-thread {
    flex: 1;
    padding: 1.4rem 1.4rem 0;
    overflow-y: auto;
    background:
      linear-gradient(180deg, rgba(248, 255, 251, 0.72), rgba(255, 255, 255, 0.98)),
      radial-gradient(circle at center, rgba(209, 250, 229, 0.34), transparent 42%);
  }

  .chat-date-pill {
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

  .message-row {
    display: flex;
    gap: .8rem;
    margin-bottom: 1rem;
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
    width: 40px;
    height: 40px;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    flex-shrink: 0;
  }

  .message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .message-content {
    max-width: min(76%, 620px);
  }

  .message-meta {
    display: flex;
    align-items: center;
    gap: .55rem;
    margin: 0 .35rem .35rem;
    color: #64748b;
    font-size: .76rem;
  }

  .message-name {
    font-weight: 700;
    color: #064e3b;
  }

  .message-bubble {
    padding: .95rem 1.15rem 1rem;
    border-radius: 24px;
    font-size: .95rem;
    line-height: 1.72;
    word-break: break-word;
  }

  .chat-composer {
    padding: 1rem 1.2rem 1.2rem;
    border-top: 1px solid rgba(221, 239, 231, 0.95);
    background: rgba(255, 255, 255, 0.95);
  }

  .chat-composer.is-locked {
    background: linear-gradient(180deg, rgba(248, 255, 251, 0.96), rgba(255, 255, 255, 0.96));
  }

  .chat-form {
    display: flex;
    align-items: flex-end;
    gap: .9rem;
    padding: .75rem;
    border-radius: 24px;
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
    min-height: 56px;
    max-height: 160px;
    padding: .6rem .35rem;
    color: #0f172a;
    font-size: .95rem;
    outline: none;
  }

  .chat-input:disabled {
    color: #94a3b8;
    cursor: not-allowed;
  }

  .chat-send {
    width: 56px;
    height: 56px;
    border: none;
    border-radius: 18px;
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    font-size: 1.4rem;
    box-shadow: 0 16px 30px rgba(6, 95, 70, 0.24);
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
    margin-top: .65rem;
    color: #64748b;
    font-size: .78rem;
    padding: 0 .3rem;
  }

  .chat-side {
    padding: 1.3rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .side-card {
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    padding: 1.2rem 1.2rem 1.15rem;
    color: #083344;
  }

  .side-card::after {
    content: "";
    position: absolute;
    inset: auto -36px -36px auto;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.16);
  }

  .side-card-title {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    padding: .38rem .72rem;
    border-radius: 999px;
    font-size: .73rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: .9rem;
  }

  .side-card h3 {
    position: relative;
    z-index: 1;
    font-size: 1.26rem;
    font-weight: 800;
    margin-bottom: .55rem;
    color: #064e3b;
  }

  .side-card p {
    position: relative;
    z-index: 1;
    margin: 0;
    line-height: 1.7;
    color: rgba(15, 23, 42, 0.8);
    font-size: .92rem;
  }

  .side-card.mint {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
  }

  .side-card.mint .side-card-title {
    background: rgba(4, 120, 87, 0.12);
    color: #047857;
  }

  .side-card.peach {
    background: linear-gradient(135deg, #fde7c7, #fed7aa);
  }

  .side-card.peach .side-card-title {
    background: rgba(154, 52, 18, 0.08);
    color: #9a3412;
  }

  .session-card {
    padding: 1.1rem 1.15rem;
    border-radius: 24px;
    background: #ffffff;
    border: 1px solid rgba(221, 239, 231, 0.95);
  }

  .session-card h4 {
    font-size: .98rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: .85rem;
  }

  .session-list {
    display: grid;
    gap: .8rem;
  }

  .session-item-label {
    font-size: .74rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: .22rem;
  }

  .session-item-value {
    color: #0f172a;
    font-size: .9rem;
    font-weight: 600;
    line-height: 1.55;
  }

  .chat-empty {
    max-width: 880px;
    margin: 0 auto;
    padding: 3.2rem 2rem;
    text-align: center;
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
    gap: .55rem;
    padding: .9rem 1.3rem;
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
    .chat-shell {
      grid-template-columns: 1fr;
    }

    .chat-main {
      min-height: 680px;
    }

    .chat-side {
      order: -1;
    }
  }

  @media (max-width: 767.98px) {
    .chat-page {
      padding-top: 1.25rem;
    }

    .chat-main,
    .chat-side,
    .chat-empty {
      border-radius: 24px;
    }

    .chat-topbar {
      align-items: flex-start;
      flex-direction: column;
    }

    .message-content {
      max-width: 100%;
    }

    .message-row {
      align-items: flex-start;
    }

    .message-avatar {
      width: 34px;
      height: 34px;
      border-radius: 12px;
    }
  }
</style>
@endpush

@section('konten')
<section class="chat-page">
  <div class="container">
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
            <div class="chat-badge">{{ $statusLabel }}</div>
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

        <aside class="chat-side">
          <div class="session-card">
            <h4>Detail Sesi</h4>
            <div class="session-list">
              <div>
                <div class="session-item-label">Konselor</div>
                <div class="session-item-value">{{ $chatPayload['counselorName'] }}</div>
              </div>
              <div>
                <div class="session-item-label">Jadwal</div>
                <div class="session-item-value">
                  {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y') }}<br>
                  {{ substr($jadwal->waktu, 0, 5) }} WIB
                </div>
              </div>
              <div>
                <div class="session-item-label">Topik</div>
                <div class="session-item-value">{{ $topik ?: 'Belum ada topik khusus' }}</div>
              </div>
              <div>
                <div class="session-item-label">Akses Chat</div>
                <div class="session-item-value">Aktif mulai {{ $scheduledStartLabel }}</div>
              </div>
            </div>
          </div>

          <div class="side-card peach">
            <div class="side-card-title">Persiapan Ringan</div>
            <h3>Tarik Napas 4-4</h3>
            <p>Ambil napas selama 4 detik, tahan 4 detik, lalu hembuskan perlahan. Ulang 4 kali agar tubuh lebih tenang sebelum sesi dimulai.</p>
          </div>

          <div class="side-card mint">
            <div class="side-card-title">Catatan Aman</div>
            <h3>Mulai dari Satu Hal</h3>
            <p>Anda tidak harus menceritakan semuanya sekaligus. Saat waktu sesi tiba, cukup mulai dari hal yang paling ingin Anda sampaikan lebih dulu.</p>
          </div>
        </aside>
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
                <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
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

        <aside class="chat-side">
          <div class="session-card">
            <h4>Detail Sesi</h4>
            <div class="session-list">
              <div>
                <div class="session-item-label">Konselor</div>
                <div class="session-item-value">{{ $chatPayload['counselorName'] }}</div>
              </div>
              <div>
                <div class="session-item-label">Jadwal</div>
                <div class="session-item-value">
                  {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y') }}<br>
                  {{ substr($jadwal->waktu, 0, 5) }} WIB
                </div>
              </div>
              <div>
                <div class="session-item-label">Topik</div>
                <div class="session-item-value">{{ $topik ?: 'Belum ada topik khusus' }}</div>
              </div>
              <div>
                <div class="session-item-label">Status</div>
                <div class="session-item-value">{{ $statusLabel }}</div>
              </div>
            </div>
          </div>

          <div class="side-card peach">
            <div class="side-card-title">Persiapan Ringan</div>
            <h3>Tarik Napas 4-4</h3>
            <p>Ambil napas selama 4 detik, tahan 4 detik, lalu hembuskan perlahan. Ulang 4 kali agar tubuh lebih tenang sebelum sesi dimulai.</p>
          </div>

          <div class="side-card mint">
            <div class="side-card-title">Catatan Aman</div>
            <h3>Mulai dari Satu Hal</h3>
            <p>Anda tidak harus menceritakan semuanya sekaligus. Saat sesi aktif, cukup mulai dari hal yang paling ingin Anda sampaikan lebih dulu.</p>
          </div>
        </aside>
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

          <div class="chat-thread" id="chatThread">
            <div class="chat-date-pill">
              {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, j F Y') }}
            </div>
          </div>

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
            <div class="chat-hint" id="chatHint">
              Ruang ini dipakai untuk percakapan konseling online Anda dengan konselor.
            </div>
          </div>
        </div>

        <aside class="chat-side">
          <div class="session-card">
            <h4>Detail Sesi</h4>
            <div class="session-list">
              <div>
                <div class="session-item-label">Konselor</div>
                <div class="session-item-value">{{ $chatPayload['counselorName'] }}</div>
              </div>
              <div>
                <div class="session-item-label">Jadwal</div>
                <div class="session-item-value">
                  {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y') }}<br>
                  {{ substr($jadwal->waktu, 0, 5) }} WIB
                </div>
              </div>
              <div>
                <div class="session-item-label">Topik</div>
                <div class="session-item-value">{{ $topik ?: 'Belum ada topik khusus' }}</div>
              </div>
              <div>
                <div class="session-item-label">Status</div>
                <div class="session-item-value">{{ $statusLabel }}</div>
              </div>
            </div>
          </div>

          <div class="side-card peach">
            <div class="side-card-title">Tips Cepat</div>
            <h3>Tarik Napas 4-4</h3>
            <p>Ambil napas selama 4 detik, tahan 4 detik, lalu hembuskan perlahan. Ulang 4 kali sebelum mulai bercerita.</p>
          </div>

          <div class="side-card mint">
            <div class="side-card-title">Misi Hari Ini</div>
            <h3>Ceritakan Satu Hal Dulu</h3>
            <p>Anda tidak perlu menjelaskan semuanya sekaligus. Mulai dari hal yang paling ingin Anda sampaikan saat ini.</p>
          </div>
        </aside>
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

    row.className = `message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;

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
        </div>
        <div class="message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
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
    payload.messages.forEach((message) => renderMessage(message));
    scrollToBottom();
  };

  const syncMessages = async () => {
    try {
      const response = await fetch(payload.messagesUrl, {
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
        body: JSON.stringify({ pesan }),
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