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
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 18px;
    padding: .95rem 1rem;
    font-size: .92rem;
    color: #0f172a;
    background: #fff;
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
  $groupRules = $consentPayload['rules'] ?? \App\Support\GroupChatSupport::rules();
  $selectedTopic = old('topic', $consentPayload['topic'] ?? '');
  $selectedTopicLabel = $selectedTopic ? ($topicOptions[$selectedTopic] ?? ucfirst(str_replace('_', ' ', $selectedTopic))) : '';
  $modalShouldOpen = (bool) $consentPayload || $errors->has('consent_confirmed');
  $modalTitle = $consentPayload['title'] ?? ($selectedTopicLabel ? 'Grup Konseling ' . $selectedTopicLabel : 'Grup Konseling');
  $modalDescription = $consentPayload['description'] ?? 'Baca aturan grup terlebih dahulu. Setelah menyetujui, Anda akan masuk ke grup konseling dengan topik yang dipilih.';
  $modalVisibility = $consentPayload['visibility_label'] ?? 'Publik';
  $modalNote = $consentPayload['note'] ?? 'Jika grup dengan topik ini sudah ada, sistem akan membukanya. Jika belum ada, grup baru akan dibuat otomatis.';
@endphp
<section class="group-create-page">
  <div class="container">
    <div class="group-create-shell">
      @if(session('error'))
        <div style="margin:0 0 1rem;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
          {{ session('error') }}
        </div>
      @endif

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
          <form class="group-create-form" id="groupCreateChooserForm">
            <div>
              <label for="groupTopic" class="group-form-label">Topik Konseling</label>
              <select id="groupTopic" name="topic" class="group-form-select" required>
                <option value="">Pilih topik konseling</option>
                @foreach($topicOptions as $topicKey => $topicLabel)
                  <option value="{{ $topicKey }}" {{ old('topic') === $topicKey ? 'selected' : '' }}>
                    {{ $topicLabel }}
                  </option>
                @endforeach
              </select>

              @error('topic')
                <div class="group-inline-error">{{ $message }}</div>
              @enderror
            </div>

            <div class="group-form-help">
              Topik yang tersedia: Akademik, Kehidupan di Kampus, Intrapersonal, Keluarga, Masalah di Asrama, Relasi, dan Lainnya.
            </div>

            <div class="group-create-actions">
              <button type="button" class="group-create-submit" id="openGroupConsentModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Lanjut Bergabung</span>
              </button>
              <a href="{{ route('mahasiswa.group-chat') }}" class="group-create-cancel">
                <i class="bi bi-x-circle"></i>
                <span>Batal</span>
              </a>
            </div>
          </form>

          <div class="group-create-note">
            <strong>Catatan aman</strong>
            Gunakan grup untuk berbagi konteks yang relevan dengan topik konseling. Hindari menyebarkan data pribadi yang sensitif jika tidak diperlukan.
          </div>
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
      <p id="groupConsentDescription">{{ $modalDescription }}</p>
    </div>

    <div class="group-consent-body">
      <div class="group-consent-summary">
        <div class="group-consent-summary-item">
          <span>Nama Grup</span>
          <strong id="groupConsentTitle">{{ $modalTitle }}</strong>
        </div>
        <div class="group-consent-summary-item">
          <span>Tipe Grup</span>
          <strong id="groupConsentVisibility">{{ $modalVisibility }}</strong>
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
        <input type="hidden" name="topic" id="groupConsentTopicInput" value="{{ $selectedTopic }}">

        <div class="group-consent-checkbox">
          <input type="checkbox" id="groupConsentCheckbox" name="consent_confirmed" value="1" {{ old('consent_confirmed') ? 'checked' : '' }}>
          <label for="groupConsentCheckbox">
            Saya telah membaca dan menyetujui seluruh aturan grup konseling ini, serta memahami bahwa saya harus menjaga percakapan tetap aman, relevan, dan sopan.
          </label>
        </div>

        @error('consent_confirmed')
          <div class="group-consent-error">{{ $message }}</div>
        @enderror

        <div class="group-consent-note" id="groupConsentNote">{{ $modalNote }}</div>

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
    const topicSelect = document.getElementById('groupTopic');
    const openButton = document.getElementById('openGroupConsentModal');
    const modal = document.getElementById('groupConsentModal');
    const topicInput = document.getElementById('groupConsentTopicInput');
    const titleTarget = document.getElementById('groupConsentTitle');
    const noteTarget = document.getElementById('groupConsentNote');
    const visibilityTarget = document.getElementById('groupConsentVisibility');
    const closeButtons = document.querySelectorAll('[data-close-group-consent]');

    if (! topicSelect || ! openButton || ! modal || ! topicInput || ! titleTarget || ! noteTarget || ! visibilityTarget) {
      return;
    }

    const defaultTitle = @json($modalTitle);
    const defaultNote = @json($modalNote);
    const preserveSessionSummary = @json((bool) (($consentPayload['room_id'] ?? null) || ($consentPayload['token'] ?? null)));

    const openModal = () => {
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    const syncModalFromTopic = () => {
      const selectedOption = topicSelect.options[topicSelect.selectedIndex];
      const topicValue = topicSelect.value;
      const topicLabel = selectedOption ? selectedOption.text.trim() : '';

      topicInput.value = topicValue;

      if (! preserveSessionSummary || ! titleTarget.textContent.trim()) {
        titleTarget.textContent = topicLabel ? `Grup Konseling ${topicLabel}` : defaultTitle;
      }

      visibilityTarget.textContent = 'Publik';
      noteTarget.textContent = defaultNote;
    };

    openButton.addEventListener('click', function () {
      if (! topicSelect.value) {
        topicSelect.reportValidity();
        topicSelect.focus();
        return;
      }

      syncModalFromTopic();
      openModal();
    });

    closeButtons.forEach(function (button) {
      button.addEventListener('click', closeModal);
    });

    if (! modal.classList.contains('is-open')) {
      syncModalFromTopic();
    } else {
      openModal();
    }
  });
</script>
@endpush
