@extends('layouts.admin')

@php
    $jadwalList = $jadwalList ?? collect();
    $activeJadwal = $activeJadwal ?? null;
    $activeSession = $activeSession ?? null;
    $scheduledStartLabel = $scheduledStartLabel ?? '-';

    $jadwal = $activeJadwal;
    $mahasiswa = optional($jadwal)->mahasiswa;
    $studentUser = optional($mahasiswa)->user;
    $topik = null;
    $isBlockedBySchedule = $isBlockedBySchedule ?? false;
    $chatAccessGranted = $chatAccessGranted ?? false;
@endphp

@section('page-title', 'Chat Konseling')

@push('styles')
<style>
  .admin-chat-page {
    display: grid;
    grid-template-columns: 340px minmax(0, 1fr);
    gap: 1.35rem;
  }

  .admin-chat-card,
  .admin-chat-list {
    background: #fff;
    border: 1px solid #dceee4;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(6, 78, 59, .06);
  }

  .admin-chat-list {
    overflow: hidden;
    align-self: start;
  }

  .admin-chat-list-head {
    padding: 1rem 1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background: linear-gradient(180deg, #f3fff8, #ffffff);
  }

  .admin-chat-list-head h4 {
    margin: 0 0 .25rem;
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
  }

  .admin-chat-list-head p {
    margin: 0;
    color: #64748b;
    font-size: .82rem;
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
  }

  .admin-chat-session-status.disetujui {
    background: #e8fff1;
    color: #047857;
  }

  .admin-chat-session-status.berlangsung {
    background: #def7ec;
    color: #065f46;
  }

  .admin-chat-session-status.terjadwal {
    background: #eff6ff;
    color: #1d4ed8;
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
    overflow: hidden;
    background: #dff3e8;
    flex-shrink: 0;
    box-shadow: 0 10px 25px rgba(6, 95, 70, .12);
  }

  .admin-chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
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
    line-height: 1.5;
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

  .admin-chat-head-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .admin-chat-video-btn {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .72rem 1rem;
    border-radius: 999px;
    border: 1px solid #d8eee2;
    background: #fff;
    color: #065f46;
    text-decoration: none;
    font-size: .84rem;
    font-weight: 800;
    box-shadow: 0 10px 20px rgba(6, 95, 70, .06);
  }

  .admin-chat-video-btn:hover {
    color: #047857;
    background: #f8fffb;
  }

  .admin-chat-thread {
    flex: 1;
    overflow-y: auto;
    padding: 1.35rem 1.35rem 0;
    background:
      radial-gradient(circle at center, rgba(209, 250, 229, 0.28), transparent 42%),
      linear-gradient(180deg, rgba(246,255,249,.7), rgba(255,255,255,.98));
    min-height: 0;
  }

  .admin-chat-thread-sticky-date {
    position: sticky;
    top: .75rem;
    z-index: 2;
    width: fit-content;
    margin: 0 auto 1rem;
    padding: .5rem .95rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, .92);
    border: 1px solid #e4f3eb;
    color: #64748b;
    font-size: .74rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
    box-shadow: 0 10px 24px rgba(6, 78, 59, .06);
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
    margin-top: auto;
    flex-shrink: 0;
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

  .admin-chat-input:disabled {
    color: #94a3b8;
    cursor: not-allowed;
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

  .admin-chat-gate {
    min-height: 540px;
    display: grid;
    place-items: center;
    padding: 2.2rem;
  }

  .admin-chat-gate-card {
    width: min(100%, 620px);
    border-radius: 28px;
    padding: 2rem 1.7rem;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.2), transparent 30%),
      linear-gradient(180deg, #f7fff9, #ffffff);
    border: 1px solid #dceee4;
    box-shadow: 0 22px 60px rgba(6, 78, 59, .08);
    text-align: center;
  }

  .admin-chat-gate-icon {
    width: 84px;
    height: 84px;
    margin: 0 auto 1rem;
    border-radius: 28px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #047857;
    font-size: 2rem;
    box-shadow: 0 18px 36px rgba(16,185,129,.16);
  }

  .admin-chat-gate-card h3 {
    font-size: 1.3rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .55rem;
  }

  .admin-chat-gate-card p {
    margin: 0 auto 1.15rem;
    max-width: 460px;
    color: #64748b;
    line-height: 1.8;
  }

  .admin-chat-start {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    border: none;
    border-radius: 16px;
    padding: .92rem 1.25rem;
    color: #fff;
    font-weight: 800;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 32px rgba(6,95,70,.2);
  }

  .admin-chat-start:disabled {
    opacity: .55;
    cursor: not-allowed;
    box-shadow: none;
  }

  @media (max-width: 1199.98px) {
    .admin-chat-page {
      grid-template-columns: 1fr;
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
  }
</style>
@endpush

@section('konten')
<div class="admin-chat-page">
  <aside class="admin-chat-list">
    <div class="admin-chat-list-head">
      <h4>Daftar Sesi Online</h4>
      <div class="admin-chat-search">
        <i class="ti ti-search"></i>
        <input
          type="search"
          id="adminChatSearchInput"
          placeholder="Cari mahasiswa, topik, atau tanggal sesi..."
          autocomplete="off"
        >
      </div>
    </div>

    @forelse($jadwalList as $item)
      @php
        $itemUser = optional(optional($item)->mahasiswa)->user;
        $isSelected = optional($activeJadwal)->id === $item->id;
        $itemScheduledAt = \Carbon\Carbon::parse(trim($item->tanggal . ' ' . ($item->waktu ?? '00:00:00')), 'Asia/Jakarta');
        $itemIsBlockedBySchedule = now('Asia/Jakarta')->lt($itemScheduledAt);
        $itemStatusKey = $item->display_status_key ?? ($itemIsBlockedBySchedule ? 'terjadwal' : strtolower($item->status ?? ''));
        $itemStatusLabel = $item->display_status_label ?? ($itemIsBlockedBySchedule ? 'Terjadwal' : ucfirst($item->status ?? '-'));
        $itemTopik = $item->catatan && preg_match('/Topik:\s*([^|]+)/i', $item->catatan, $match) ? trim($match[1]) : 'Topik belum tersedia';
        $sessionSearchText = strtolower(trim(implode(' ', [
            $itemUser?->getNamaDisplay() ?? 'Mahasiswa',
            \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j F Y'),
            substr($item->waktu, 0, 5),
            $itemTopik,
            $itemStatusLabel,
        ])));
      @endphp
      <a href="{{ route('admin.chat', ['jadwal' => $item->id]) }}" class="admin-chat-session {{ $isSelected ? 'active' : '' }}" data-session-search="{{ $sessionSearchText }}">
        <div class="admin-chat-session-top">
          <div class="admin-chat-session-name">{{ $itemUser?->getNamaDisplay() ?? 'Mahasiswa' }}</div>
          <span class="admin-chat-session-status {{ $itemStatusKey }}">{{ $itemStatusLabel }}</span>
        </div>
        <div class="admin-chat-session-meta">
          {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j M Y') }} • {{ substr($item->waktu, 0, 5) }} WIB<br>
          {{ $itemTopik }}
          @if(!empty($item->conversation_dates_label))
            <br>{{ $item->conversation_dates_label }}
          @endif
        </div>
      </a>
    @empty
      <div style="padding:1.1rem 1rem;color:#64748b;font-size:.84rem;">
        Belum ada sesi konseling online yang disetujui atau sedang berlangsung.
      </div>
    @endforelse
    @if($jadwalList->isNotEmpty())
      <div class="admin-chat-search-empty" id="adminChatSearchEmpty">
        Tidak ada percakapan yang cocok dengan kata kunci pencarian.
      </div>
    @endif
  </aside>

  <section class="admin-chat-card">
    @if(session('success'))
      <div style="margin:1rem 1rem 0;background:#e8fff1;border:1px solid #bbf7d0;color:#166534;padding:1rem 1.1rem;border-radius:18px;font-weight:700;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="margin:1rem 1rem 0;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:1rem 1.1rem;border-radius:18px;font-weight:700;">
        {{ session('error') }}
      </div>
    @endif

    @if(!$activeJadwal)
      <div class="admin-chat-empty">
        <div>
          <div class="admin-chat-empty-icon"><i class="ti ti-message-heart"></i></div>
          <h3>Belum Ada Ruang Chat Aktif</h3>
          <p>
            Pilih salah satu sesi konseling online yang sudah disetujui dari daftar di samping untuk membuka ruang percakapan dengan mahasiswa.
          </p>
        </div>
      </div>
    @elseif($isBlockedBySchedule)
      <div class="admin-chat-gate">
        <div class="admin-chat-gate-card">
          <div class="admin-chat-gate-icon">
            <i class="ti ti-clock-hour-4"></i>
          </div>
          <h3>Sesi Akan Aktif Sesuai Jadwal</h3>
          <p>
            Jadwal konseling online dengan <strong>{{ $studentUser?->getNamaDisplay() ?? 'Mahasiswa' }}</strong>
            sudah tercatat, tetapi ruang chat dan video call baru bisa diakses pada
            <strong>{{ $scheduledStartLabel }}</strong>.
          </p>
          <button type="button" class="admin-chat-start" disabled>
            <i class="ti ti-lock"></i>
            <span>Menunggu Jadwal Sesi</span>
          </button>
        </div>
      </div>
    @elseif($isReadyToStart)
      <div class="admin-chat-gate">
        <div class="admin-chat-gate-card">
          <div class="admin-chat-gate-icon">
            <i class="ti {{ $canStartNow ? 'ti-message-chatbot' : 'ti-clock-hour-4' }}"></i>
          </div>
          <h3>{{ $canStartNow ? 'Sesi Siap Dimulai' : 'Sesi Belum Bisa Dimulai' }}</h3>
          <p>
            @if($canStartNow)
              Jadwal konseling online dengan <strong>{{ $studentUser?->getNamaDisplay() ?? 'Mahasiswa' }}</strong> sudah siap.
              Klik tombol di bawah untuk masuk ke ruang chat realtime.
            @else
              Jadwal sudah disetujui, tetapi sesi chat baru bisa dimulai pada
              <strong>{{ $scheduledStartLabel }}</strong>.
            @endif
          </p>
          <form action="{{ route('admin.chat.start') }}" method="POST">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $activeJadwal->id }}">
            <button type="submit" class="admin-chat-start" {{ $canStartNow ? '' : 'disabled' }}>
              <i class="ti ti-player-play-filled"></i>
              <span>Mulai Sesi</span>
            </button>
          </form>
        </div>
      </div>
    @else
      @if($chatPayload)
      <div class="admin-chat-head">
        <div class="admin-chat-person">
          <div class="admin-chat-avatar">
            <img src="{{ $chatPayload['studentAvatar'] }}" alt="{{ $chatPayload['studentName'] }}">
          </div>
          <div>
            <div class="admin-chat-title">{{ $chatPayload['studentName'] }}</div>
            <p class="admin-chat-subtitle">
              {{ $topik ?: 'Konseling online aktif' }}<br>
              {{ \Carbon\Carbon::parse($activeJadwal->tanggal)->translatedFormat('l, j F Y') }} • {{ substr($activeJadwal->waktu, 0, 5) }} WIB
            </p>
          </div>
        </div>
        <div class="admin-chat-head-actions">
          <a href="{{ $chatPayload['videoCallUrl'] }}" target="_blank" rel="noopener noreferrer" class="admin-chat-video-btn">
            <i class="ti ti-video"></i>
            <span>Video Call</span>
          </a>
        </div>
      </div>

      <div
        class="admin-chat-thread"
        id="adminChatThread"
        data-last-date-key=""
      >
        <div class="admin-chat-thread-sticky-date" id="adminChatStickyDate">
          {{ $activeJadwal ? \Carbon\Carbon::parse($activeJadwal->tanggal)->translatedFormat('l, j F Y') : '' }}
        </div>
      </div>
      @endif

      <div class="admin-chat-compose">
        <form id="adminChatForm" class="admin-chat-form">
          <textarea
            id="adminChatInput"
            class="admin-chat-input"
            rows="1"
            maxlength="2000"
            placeholder="{{ $chatAccessGranted ? 'Tulis respons konseling Anda di sini...' : 'Ruang chat belum aktif.' }}"
            {{ $chatAccessGranted ? '' : 'disabled' }}
          ></textarea>
          <button type="submit" class="admin-chat-send" id="adminChatSendBtn" {{ $chatAccessGranted ? '' : 'disabled' }}>
            <i class="ti ti-send"></i>
          </button>
        </form>
        <div id="adminChatHint" class="admin-chat-hint">
          {{ $chatAccessGranted ? 'Pesan akan langsung terkirim ke mahasiswa.' : 'Klik tombol "Mulai Sesi" di atas untuk mengaktifkan ruang chat.' }}
        </div>
      </div>
    @endif
  </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const searchInput = document.getElementById('adminChatSearchInput');
  const searchEmpty = document.getElementById('adminChatSearchEmpty');
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

@if($activeSession && $chatPayload)
@push('scripts')
<script>
(() => {
  const payload = @json($chatPayload);
  const chatAccessGranted = {{ $chatAccessGranted ? 'true' : 'false' }};
  const currentUserId = {{ auth()->id() }};
  const fallbackAvatar = @json(asset('img/default-avatar.png'));
  const currentUserName = @json(auth()->user()->getNamaDisplay());
  const thread = document.getElementById('adminChatThread');
  const stickyDate = document.getElementById('adminChatStickyDate');
  const form = document.getElementById('adminChatForm');
  const input = document.getElementById('adminChatInput');
  const sendBtn = document.getElementById('adminChatSendBtn');
  const hint = document.getElementById('adminChatHint');

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

  const setStickyDate = (label) => {
    if (!stickyDate || !label) {
      return;
    }

    stickyDate.textContent = label;
    stickyDate.style.display = 'block';
  };

  const autoResize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
  };

  const renderMessage = (message) => {
    const dateKey = String(message.date_key ?? '').trim();
    const dateLabel = String(message.date_label ?? '').trim();

    if (dateKey) {
      const lastDateKey = thread.dataset.lastDateKey || '';

      if (lastDateKey !== dateKey) {
        const dateDivider = document.createElement('div');
        dateDivider.className = 'admin-chat-date';
        dateDivider.dataset.dateKey = dateKey;
        dateDivider.textContent = dateLabel || dateKey;
        thread.appendChild(dateDivider);
        thread.dataset.lastDateKey = dateKey;
      }
    }

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
    if (dateLabel) {
      setStickyDate(dateLabel);
    }
  };

  const getVisibleDateLabel = () => {
    const threadRect = thread.getBoundingClientRect();
    const dateNodes = Array.from(thread.querySelectorAll('.admin-chat-date'));

    for (const node of dateNodes) {
      const rect = node.getBoundingClientRect();
      if (rect.bottom >= threadRect.top + 80) {
        return node.textContent?.trim() || '';
      }
    }

    return dateNodes.at(-1)?.textContent?.trim() || '';
  };

  const refreshStickyDate = () => {
    const label = getVisibleDateLabel();
    if (label) {
      setStickyDate(label);
    }
  };

  const syncMessages = async () => {
    try {
      const response = await fetch(`${payload.messagesUrl}?sesi_id=${payload.sessionId}`, {
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
  refreshStickyDate();
  autoResize();

  let pendingMessageNode = null;

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
  thread.addEventListener('scroll', refreshStickyDate, { passive: true });

  input.addEventListener('input', autoResize);
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      form.requestSubmit();
    }
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    if (!chatAccessGranted) {
      hint.textContent = 'Ruang chat belum aktif. Klik "Mulai Sesi" untuk memulai.';
      return;
    }

    const pesan = input.value.trim();
    if (!pesan) {
      return;
    }

    sendBtn.disabled = true;
    hint.textContent = 'Mengirim pesan ke mahasiswa...';

    const tempId = `pending-${Date.now()}`;
    const pendingRow = document.createElement('div');
    pendingRow.className = 'admin-message-row mine';
    pendingRow.dataset.messageId = tempId;
    pendingRow.innerHTML = `
      <div class="admin-message-content">
        <div class="admin-message-meta">
          <span class="admin-message-name">${escapeHtml(currentUserName)}</span>
          <span>Sedang mengirim...</span>
        </div>
        <div class="admin-message-bubble">${escapeHtml(pesan).replace(/\n/g, '<br>')}</div>
      </div>
      <div class="admin-message-avatar">
        <img src="${escapeHtml(fallbackAvatar)}" alt="Anda">
      </div>
    `;

    thread.appendChild(pendingRow);
    pendingMessageNode = pendingRow;
    scrollToBottom();

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
          pesan,
        }),
      });

      let data = null;
      try {
        data = await response.json();
      } catch (parseError) {
        const txt = await response.text().catch(() => '(no body)');
        console.error('Failed to parse JSON response', parseError, txt);
        hint.textContent = `Server error: ${response.status} ${response.statusText}`;
        sendBtn.disabled = false;
        return;
      }

      if (!response.ok || !data?.success) {
        console.warn('Send failed', response.status, data);
        hint.textContent = data?.message ?? `Pesan gagal dikirim (status ${response.status}).`;
        pendingMessageNode?.remove();
        pendingMessageNode = null;
        sendBtn.disabled = false;
        return;
      }

      if (pendingMessageNode) {
        pendingMessageNode.dataset.messageId = String(data.message.id);
        pendingMessageNode.innerHTML = `
          <div class="admin-message-content">
            <div class="admin-message-meta">
              <span class="admin-message-name">${escapeHtml(data.message.sender_name)}</span>
              <span>${escapeHtml(data.message.time)}</span>
            </div>
            <div class="admin-message-bubble">${escapeHtml(data.message.text).replace(/\n/g, '<br>')}</div>
          </div>
          <div class="admin-message-avatar">
            <img src="${escapeHtml(data.message.avatar_url)}" alt="${escapeHtml(data.message.sender_name)}">
          </div>
        `;
        pendingMessageNode = null;
      } else {
        renderMessage(data.message);
      }

      scrollToBottom();
      input.value = '';
      autoResize();
      hint.textContent = 'Pesan terkirim dan langsung masuk ke ruang chat mahasiswa.';
    } catch (error) {
      console.error(error);
      pendingMessageNode?.remove();
      pendingMessageNode = null;
      hint.textContent = 'Terjadi kendala saat mengirim pesan.';
    } finally {
      sendBtn.disabled = false;
    }
  });
})();
</script>
@endpush
@endif