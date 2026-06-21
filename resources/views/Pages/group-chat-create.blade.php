@extends('layouts.master')

@push('styles')
<style>
  .group-create-page {
    min-height: calc(100vh - 180px);
    background:
      radial-gradient(circle at top left, rgba(16, 185, 129, 0.16), transparent 24%),
      radial-gradient(circle at top right, rgba(253, 230, 138, 0.16), transparent 22%),
      linear-gradient(180deg, #effcf5 0%, #f8fffb 24%, #ffffff 58%);
    padding: 2.25rem 0 3rem;
  }

  .group-create-shell {
    max-width: 820px;
    margin: 0 auto;
  }

  .group-create-page-consent-only {
    min-height: 0;
    padding: 0;
    background: transparent;
  }

  .group-create-page-consent-only .group-create-shell {
    display: none;
  }

  .group-create-back {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    margin-bottom: 1rem;
    text-decoration: none;
    color: #065f46;
    font-size: .86rem;
    font-weight: 800;
  }

  .group-create-back:hover {
    color: #047857;
  }

  .group-create-card {
    border-radius: 28px;
    border: 1px solid rgba(209, 250, 229, 0.92);
    background: rgba(255, 255, 255, 0.96);
    box-shadow: 0 18px 50px rgba(6, 78, 59, 0.08);
    overflow: hidden;
  }

  .group-create-head {
    padding: 1.5rem 1.5rem 0;
  }

  .group-create-kicker {
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

  .group-create-head h1 {
    margin: 0 0 .45rem;
    font-size: clamp(1.8rem, 3vw, 2.35rem);
    font-weight: 800;
    color: #064e3b;
  }

  .group-create-head p {
    margin: 0;
    color: #475569;
    line-height: 1.8;
    max-width: 620px;
  }

  .group-create-body {
    padding: 1.35rem 1.5rem 1.5rem;
  }

  .group-create-form {
    display: grid;
    gap: 1rem;
  }

  .group-form-label {
    display: block;
    margin-bottom: .45rem;
    font-size: .8rem;
    font-weight: 800;
    color: #0f172a;
  }

  .group-form-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 18px;
    padding: .95rem 3rem .95rem 1rem;
    font-size: .92rem;
    color: #0f172a;
    background: #fff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M4 6.5L8 10.5L12 6.5' stroke='%23065f46' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.15rem center;
    background-size: 1rem;
    outline: none;
  }

  .group-form-select:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .group-form-help {
    color: #64748b;
    font-size: .84rem;
    line-height: 1.7;
  }

  .group-inline-error {
    color: #be123c;
    font-size: .8rem;
    font-weight: 700;
    margin-top: .45rem;
  }

  .group-create-note {
    margin-top: 1rem;
    padding: 1rem 1.05rem;
    border-radius: 20px;
    background: linear-gradient(135deg, #fde7c7, #fed7aa);
    color: #7c2d12;
    font-size: .84rem;
    line-height: 1.7;
  }

  .group-create-note strong {
    display: block;
    margin-bottom: .18rem;
    color: #9a3412;
  }

  .group-create-actions {
    display: flex;
    align-items: center;
    gap: .85rem;
    flex-wrap: wrap;
    margin-top: .2rem;
  }

  .group-create-submit,
  .group-create-cancel {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    min-height: 50px;
    border-radius: 16px;
    padding: .9rem 1.2rem;
    font-size: .84rem;
    font-weight: 800;
    text-decoration: none;
  }

  .group-create-submit {
    border: none;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 30px rgba(6, 95, 70, 0.18);
  }

  .group-create-cancel {
    border: 1px solid #dbece3;
    color: #065f46;
    background: #fff;
  }

  .group-consent-overlay {
    position: fixed;
    inset: 0;
    z-index: 3200;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.2rem;
    overflow-y: auto;
    background: rgba(15, 23, 42, 0.22);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
  }

  .group-consent-modal {
    width: min(100%, 520px);
    max-height: min(100vh - 2.4rem, 820px);
    display: flex;
    flex-direction: column;
    border-radius: 28px;
    border: 1px solid rgba(220, 238, 228, 0.96);
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 26px 70px rgba(15, 23, 42, 0.18);
    overflow: hidden;
  }

  .group-consent-head {
    padding: 1.3rem 1.35rem .95rem;
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
    font-size: clamp(1.35rem, 2.8vw, 1.8rem);
    font-weight: 800;
    color: #064e3b;
    line-height: 1.35;
  }

  .group-consent-head p {
    margin: 0;
    color: #475569;
    line-height: 1.75;
    font-size: .92rem;
  }

  .group-consent-body {
    padding: 1.15rem 1.35rem 1.35rem;
    overflow-y: auto;
  }

  .group-consent-body form {
    display: grid;
    gap: 1rem;
  }

  .group-consent-meta {
    padding: .95rem 1rem;
    border-radius: 18px;
    background: #f8fffb;
    border: 1px solid #dbece3;
    display: grid;
    gap: .75rem;
  }

  .group-consent-meta-item {
    display: grid;
    gap: .15rem;
    color: #475569;
    font-size: .84rem;
    line-height: 1.7;
  }

  .group-consent-meta-label {
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #047857;
  }

  .group-consent-rules {
    margin-bottom: 1rem;
    padding: 1rem 1rem 1rem 1.08rem;
    border-radius: 20px;
    border: 1px solid #dbece3;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .group-consent-rules h3 {
    margin: 0 0 .65rem;
    color: #0f172a;
    font-size: .9rem;
    font-weight: 800;
  }

  .group-consent-rules ul {
    margin: 0;
    padding-left: 1.1rem;
    color: #475569;
    font-size: .84rem;
    line-height: 1.75;
  }

  .group-consent-rules li + li {
    margin-top: .42rem;
  }

  .group-consent-check {
    display: flex;
    align-items: flex-start;
    gap: .72rem;
    padding: .95rem 1rem;
    border-radius: 18px;
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
    font-size: .86rem;
    line-height: 1.7;
    cursor: pointer;
  }

  .group-consent-actions {
    display: flex;
    gap: .8rem;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-start;
    margin-top: 0;
  }

  .group-consent-submit,
  .group-consent-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    min-height: 50px;
    border-radius: 16px;
    padding: .9rem 1.2rem;
    font-size: .84rem;
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

  @media (max-width: 767.98px) {
    .group-create-page {
      padding-top: 1.25rem;
    }

    .group-create-card {
      border-radius: 24px;
    }

    .group-create-actions {
      flex-direction: column;
      align-items: stretch;
    }

    .group-create-submit,
    .group-create-cancel {
      width: 100%;
    }

    .group-consent-modal {
      border-radius: 24px;
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
  $isPrivateInviteConsent = ($consentContext['kind'] ?? null) === 'private_invite';
@endphp
<section class="group-create-page{{ $isPrivateInviteConsent ? ' group-create-page-consent-only' : '' }}">
  <div class="container">
    <div class="group-create-shell">
      <a href="{{ route('mahasiswa.group-chat') }}" class="group-create-back">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali ke daftar grup</span>
      </a>

      <div class="group-create-card">
        <div class="group-create-head">
          <div class="group-create-kicker">
            <i class="bi bi-plus-circle-fill"></i>
            <span>Tambah Grup</span>
          </div>
          <h1>Berbagung dengan grup baru</h1>
          <p>
            Pilih topik konseling yang sesuai. Jika grup dengan topik ini sudah ada, sistem akan langsung membukanya untukmu.
            Jika belum ada, grup baru akan dibuat otomatis.
          </p>
        </div>

        <div class="group-create-body">
          @if(! $isPrivateInviteConsent)
            {{-- Form ini tetap memakai endpoint join agar alur buat grup dan masuk grup tetap satu jalur. --}}
            <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST" class="group-create-form">
              @csrf
              {{-- Halaman create ini adalah langkah persetujuan ringkas, jadi consent ditandai otomatis saat form dikirim. --}}
              <input type="hidden" name="consent_acknowledged" value="1">
              <div>
                <label for="groupTopic" class="group-form-label">Topik Konseling</label>
                <select id="groupTopic" name="topic" class="group-form-select" required>
                  <option value="">Pilih topik konseling</option>
                  @foreach($topicOptions as $topicKey => $topicLabel)
                    {{-- Prefer old input, lalu fallback ke topik dari query string agar pilihan publik tetap menempel. --}}
                    <option value="{{ $topicKey }}" {{ old('topic', request('topic')) === $topicKey ? 'selected' : '' }}>
                      {{ $topicLabel }}
                    </option>
                  @endforeach
                </select>

                @error('topic')
                  <div class="group-inline-error">{{ $message }}</div>
                @enderror

                @error('consent_acknowledged')
                  <div class="group-inline-error">{{ $message }}</div>
                @enderror
              </div>

              <div class="group-form-help">
                Topik yang tersedia: Akademik, Kehidupan di Kampus, Intrapersonal, Keluarga, Masalah di Asrama, Relasi, dan Lainnya.
              </div>

              <div class="group-create-actions">
                <button type="submit" class="group-create-submit">
                  <i class="bi bi-plus-circle-fill"></i>
                  <span>Lanjut Bergabung</span>
                </button>
                <a href="{{ route('mahasiswa.group-chat') }}" class="group-create-cancel">
                  <i class="bi bi-x-circle"></i>
                  <span>Batal</span>
                </a>
              </div>
            </form>
          @endif

          @unless($isPrivateInviteConsent)
            <div class="group-create-note">
              <strong>Catatan aman</strong>
              Gunakan grup untuk berbagi konteks yang relevan dengan topik konseling. Hindari menyebarkan data pribadi yang sensitif jika tidak diperlukan.
            </div>
          @endunless
        </div>
      </div>
    </div>
  </div>
</section>

@if($isPrivateInviteConsent)
  <div class="group-consent-overlay" aria-hidden="false">
    <div class="group-consent-modal" role="dialog" aria-modal="true" aria-labelledby="groupConsentTitle">
      <div class="group-consent-head">
        <div class="group-consent-kicker">
          <i class="bi bi-shield-check"></i>
          <span>Persetujuan Grup</span>
        </div>
        <h2 id="groupConsentTitle">Undangan ke grup privat "{{ $consentContext['room_title'] ?? 'Grup Privat' }}"</h2>
        <p>Pastikan Anda memahami identitas yang tampil, tujuan grup, dan aturan komunikasi sebelum bergabung.</p>
      </div>

      <div class="group-consent-body">
        <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST" id="groupPrivateConsentForm">
          @csrf
          @foreach(($consentContext['hidden_fields'] ?? []) as $fieldName => $fieldValue)
            @if(filled($fieldValue))
              <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
            @endif
          @endforeach

          @include('Pages.partials.group-private-consent-copy', ['consentContext' => $consentContext])

          <div class="group-consent-check">
            <input
              type="checkbox"
              id="groupConsentCheckbox"
              name="consent_acknowledged"
              value="1"
              {{ old('consent_acknowledged') ? 'checked' : '' }}
              required
            >
            <label for="groupConsentCheckbox">
              Saya memahami bahwa grup privat ini akan menampilkan nama asli saya kepada anggota grup dan konselor, dan saya setuju untuk bergabung.
            </label>
          </div>

          @error('consent_acknowledged')
            <div class="group-inline-error">{{ $message }}</div>
          @enderror

          @error('invite_token')
            <div class="group-inline-error">{{ $message }}</div>
          @enderror

          <div class="group-consent-actions">
            <button type="submit" class="group-consent-submit" id="groupConsentSubmitBtn" {{ old('consent_acknowledged') ? '' : 'disabled' }}>
              <i class="bi bi-check2-circle"></i>
              <span>{{ $consentContext['submit_label'] ?? 'Setuju dan Masuk Grup' }}</span>
            </button>
            <a href="{{ route('mahasiswa.group-chat') }}" class="group-consent-back">
              <i class="bi bi-arrow-left"></i>
              <span>Kembali</span>
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
@endsection

@if($isPrivateInviteConsent)
  @push('scripts')
  <script>
    (() => {
      const overlay = document.querySelector('.group-consent-overlay');
      const checkbox = document.getElementById('groupConsentCheckbox');
      const submitButton = document.getElementById('groupConsentSubmitBtn');

      if (overlay && overlay.parentElement !== document.body) {
        document.body.appendChild(overlay);
      }

      document.body.style.overflow = 'hidden';

      if (!checkbox || !submitButton) {
        return;
      }

      // Tombol submit baru aktif setelah persetujuan dicentang eksplisit oleh mahasiswa.
      const syncConsentState = () => {
        submitButton.disabled = !checkbox.checked;
      };

      checkbox.addEventListener('change', syncConsentState);
      syncConsentState();

      window.addEventListener('pagehide', () => {
        document.body.style.overflow = '';
      });
    })();
  </script>
  @endpush
@endif
