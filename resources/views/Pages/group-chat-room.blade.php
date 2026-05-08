@extends('layouts.master')

@push('styles')
<style>
  .group-room-page {
    min-height: calc(100vh - 180px);
    background:
      radial-gradient(circle at top left, rgba(16, 185, 129, 0.16), transparent 24%),
      radial-gradient(circle at top right, rgba(253, 230, 138, 0.16), transparent 22%),
      linear-gradient(180deg, #effcf5 0%, #f8fffb 24%, #ffffff 58%);
    padding: 2.1rem 0 3rem;
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
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 0);
    transition: grid-template-columns .28s ease;
    min-height: 760px;
  }

  .group-room-stage.is-profile-open {
    grid-template-columns: minmax(0, 1fr) minmax(300px, 340px);
  }

  .group-room-main {
    min-width: 0;
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
    flex-wrap: wrap;
    justify-content: flex-end;
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
    gap: .5rem;
    border: 1px solid rgba(209, 250, 229, 0.96);
    border-radius: 16px;
    padding: .82rem 1rem;
    background: #fff;
    color: #065f46;
    font-size: .84rem;
    font-weight: 800;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
  }

  .group-room-toggle:hover {
    transform: translateY(-1px);
    background: #f8fffb;
    box-shadow: 0 14px 24px rgba(6, 78, 59, 0.08);
  }

  .group-room-thread {
    flex: 1;
    padding: 1.4rem 1.4rem 0;
    overflow-y: auto;
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

  .group-room-send:disabled {
    opacity: .5;
    cursor: not-allowed;
    box-shadow: none;
  }

  .group-room-hint {
    margin-top: .65rem;
    color: #64748b;
    font-size: .78rem;
    padding: 0 .3rem;
  }

  .group-room-profile {
    width: 100%;
    max-width: 0;
    opacity: 0;
    overflow: hidden;
    border-left: 1px solid rgba(221, 239, 231, 0.95);
    background: linear-gradient(180deg, #fbfffd, #f7fcf9);
    transition: max-width .28s ease, opacity .2s ease;
  }

  .group-room-stage.is-profile-open .group-room-profile {
    max-width: 340px;
    opacity: 1;
  }

  .group-room-profile-head {
    padding: 1.2rem 1.2rem 1rem;
    border-bottom: 1px solid rgba(221, 239, 231, 0.95);
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 28%),
      linear-gradient(180deg, #f6fff9, #ffffff);
  }

  .group-room-profile-head h3 {
    margin: 0 0 .28rem;
    font-size: 1rem;
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
    padding: 1rem 1.15rem 1.2rem;
    overflow-y: auto;
    max-height: 760px;
  }

  .group-room-profile-topic {
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

  .group-room-profile-section {
    font-size: .74rem;
    font-weight: 800;
    color: #64748b;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: .85rem;
  }

  .group-member-list {
    display: grid;
    gap: .72rem;
  }

  .group-member-item {
    display: flex;
    align-items: center;
    gap: .85rem;
    padding: .88rem .92rem;
    border-radius: 18px;
    border: 1px solid rgba(221, 239, 231, 0.95);
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .group-member-avatar {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
  }

  .group-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .group-member-copy {
    min-width: 0;
    flex: 1;
  }

  .group-member-name {
    font-size: .92rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
    margin-bottom: .15rem;
  }

  .group-member-meta {
    color: #64748b;
    font-size: .78rem;
    line-height: 1.55;
  }

  .group-member-pill {
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
    .group-room-stage.is-profile-open {
      grid-template-columns: minmax(0, 1fr);
    }

    .group-room-profile {
      max-width: none;
      max-height: 0;
      border-left: none;
      border-top: 1px solid rgba(221, 239, 231, 0.95);
    }

    .group-room-stage.is-profile-open .group-room-profile {
      max-width: none;
      max-height: 560px;
    }
  }

  @media (max-width: 767.98px) {
    .group-room-page {
      padding-top: 1.25rem;
    }

    .group-room-shell {
      border-radius: 24px;
    }

    .group-room-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-room-actions,
    .group-room-toggle,
    .group-room-active {
      width: 100%;
      justify-content: center;
    }

    .group-message-content {
      max-width: 100%;
    }

    .group-member-item {
      align-items: flex-start;
    }
  }
</style>
@endpush

@section('konten')
<section class="group-room-page">
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
                  {{ $chatPayload['topicLabel'] }} &bull; {{ $chatPayload['memberCount'] }} anggota<br>
                  {{ implode(', ', array_slice($chatPayload['memberNames'], 0, 4)) }}{{ count($chatPayload['memberNames']) > 4 ? ' dan lainnya' : '' }}
                </p>
              </div>
            </div>

            <div class="group-room-actions">
              <button type="button" class="group-room-toggle" id="groupRoomProfileToggle" aria-expanded="false" aria-controls="groupRoomProfile">
                <i class="bi bi-layout-sidebar-inset"></i>
                <span>Lihat anggota grup</span>
              </button>
              <div class="group-room-active">Grup Aktif</div>
            </div>
          </div>

          <div class="group-room-thread" id="groupChatThread">
            <div class="group-room-date">
              {{ now('Asia/Jakarta')->translatedFormat('l, j F Y') }}
            </div>
          </div>

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
            <div class="group-room-hint" id="groupChatHint">
              Percakapan di ruang ini dapat dibaca oleh anggota grup dan konselor.
            </div>
          </div>
        </section>

        <aside class="group-room-profile" id="groupRoomProfile">
          {{-- Panel anggota grup dibuka dengan state inline, bukan modal. --}}
          <div class="group-room-profile-head">
            <h3>{{ $chatPayload['roomTitle'] }}</h3>
            <p>{{ $chatPayload['memberCount'] }} anggota dalam grup ini</p>
            <div class="group-room-profile-topic">{{ $chatPayload['topicLabel'] }}</div>
          </div>

          <div class="group-room-profile-body">
            <div class="group-room-profile-section">Anggota Grup</div>
            <div class="group-member-list">
              @foreach($activeRoom->members->sortBy(fn ($member) => optional($member->joined_at ?? $member->created_at)?->getTimestamp() ?? PHP_INT_MAX) as $member)
                @php
                  $memberUser = $member->user;
                  $memberProfile = optional($memberUser)->profil;
                  $memberJoinedAt = $member->joined_at ?? $member->created_at;
                  $isCurrentUser = (int) optional($memberUser)->id === (int) auth()->id();
                @endphp
                <div class="group-member-item">
                  <div class="group-member-avatar">
                    <img
                      src="{{ $memberProfile?->foto ? Storage::url($memberProfile->foto) : asset('img/default-avatar.png') }}"
                      alt="{{ $memberUser?->getNamaDisplay() ?? 'Pengguna' }}"
                    >
                  </div>
                  <div class="group-member-copy">
                    <div class="group-member-name">{{ $memberUser?->getNamaDisplay() ?? 'Pengguna' }}</div>
                    <div class="group-member-meta">
                      {{ $isCurrentUser ? 'Anda' : ($memberUser?->role === 'konselor' ? 'Konselor' : 'Mahasiswa') }}
                      @if($memberJoinedAt)
                        - Bergabung {{ \Carbon\Carbon::parse($memberJoinedAt)->timezone('Asia/Jakarta')->translatedFormat('j M Y, H:i') }}
                      @endif
                    </div>
                  </div>
                  @if($isCurrentUser)
                    <div class="group-member-pill">Anda</div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
  const stage = document.getElementById('groupRoomStage');
  const toggle = document.getElementById('groupRoomProfileToggle');

  if (!stage || !toggle) {
    return;
  }

  toggle.addEventListener('click', () => {
    // Toggle panel anggota tanpa pindah halaman atau membuka modal baru.
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
  const thread = document.getElementById('groupChatThread');
  const form = document.getElementById('groupChatForm');
  const input = document.getElementById('groupChatInput');
  const sendBtn = document.getElementById('groupChatSendBtn');
  const hint = document.getElementById('groupChatHint');

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

    row.className = `group-message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;

    row.innerHTML = `
      ${isMine ? '' : `
        <div class="group-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      `}
      <div class="group-message-content">
        <div class="group-message-meta">
          <span class="group-message-name">${escapeHtml(message.sender_name)}</span>
          <span>${escapeHtml(message.time)}</span>
        </div>
        <div class="group-message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
      </div>
      ${isMine ? `
        <div class="group-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      ` : ''}
    `;

    thread.appendChild(row);
  };

  const syncMessages = async () => {
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
      hint.textContent = 'Pesan terkirim ke grup konseling.';
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
