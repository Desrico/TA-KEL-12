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
  }
  .group-consent-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  min-height: 100vh;
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(15, 23, 42, 0.28);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.group-consent-card {
  position: relative;
  width: min(100%, 980px);
  max-height: calc(100vh - 4rem);
  overflow-y: auto;
  border-radius: 28px;
  background: rgba(255, 255, 255, 0.98);
  border: 1px solid rgba(209, 250, 229, 0.95);
  box-shadow: 0 32px 90px rgba(15, 23, 42, 0.25);
}

.group-consent-close {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  width: 48px;
  height: 48px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #065f46;
  background: #ffffff;
  border: 1px solid #dbece3;
  text-decoration: none;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
  z-index: 3;
}

.group-consent-head {
  padding: 1.35rem 1.6rem 1rem;
}

.group-consent-head h1 {
  margin: 0.55rem 3.5rem 0.45rem 0;
  font-size: clamp(1.6rem, 2.6vw, 2.15rem);
  line-height: 1.15;
}

.group-consent-head p {
  line-height: 1.55;
  font-size: 0.95rem;
}

.group-consent-body {
  padding: 1.25rem 1.6rem 1.4rem;
}

.group-consent-info {
  align-items: start;
  gap: 0.85rem;
  margin-bottom: 0.85rem;
}

.group-consent-box {
  padding: 0.95rem;
}


.group-consent-box h3 {
  margin: 0 0 0.9rem;
  color: #0f172a;
  font-size: 1rem;
  font-weight: 900;
}

.group-consent-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.9rem;
}

.group-consent-grid span {
  display: block;
  margin-bottom: 0.35rem;
  color: #059669;
  font-size: 0.78rem;
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.group-consent-grid strong {
  display: block;
  color: #334155;
  font-size: 0.92rem;
  line-height: 1.55;
}

.group-consent-rules {
  line-height: 1.55;
  font-size: 0.92rem;
}

.group-consent-rules li + li {
  margin-top: 0.35rem;
}

.group-consent-form {
  display: grid;
  gap: 1rem;
}

.group-consent-check {
  padding: 0.85rem 1rem;
  line-height: 1.5;
  font-size: 0.92rem;
}

.group-consent-check input {
  width: 22px;
  height: 22px;
  margin-top: 0.15rem;
  accent-color: #059669;
  flex: 0 0 auto;
}

.group-consent-card .group-create-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

@media (max-width: 991.98px) {
  .group-consent-overlay {
    padding: 1rem;
  }

  .group-consent-card {
    max-height: calc(100vh - 2rem);
    border-radius: 24px;
  }

  .group-consent-info {
    grid-template-columns: 1fr;
  }

  .group-consent-grid {
    grid-template-columns: 1fr;
  }

  .group-consent-head h1 {
    margin-right: 3rem;
  }
}
</style>
@endpush

@section('konten')
@php
    $isConsentMode = isset($consentContext) && is_array($consentContext);
@endphp

@if($isConsentMode)
<section class="group-create-page">
  <div class="container">
    <div class="group-consent-overlay">
      <div class="group-consent-card">
        <a href="{{ route('mahasiswa.group-chat') }}" class="group-consent-close" aria-label="Tutup">
          <i class="bi bi-x-lg"></i>
        </a>

        <div class="group-consent-head">
          <div class="group-create-kicker">
            <i class="bi bi-shield-check"></i>
            <span>{{ $consentContext['headline'] ?? 'Persetujuan Grup' }}</span>
          </div>

          <h1>{{ $consentContext['title'] ?? 'Undangan Grup' }}</h1>

          <p>
            {{ $consentContext['description'] ?? 'Pastikan Anda memahami aturan grup sebelum bergabung.' }}
          </p>
        </div>

        <div class="group-consent-body">
          <div class="group-consent-info">
            <div class="group-consent-box">
              <h3>Informasi Undangan</h3>

              <div class="group-consent-grid">
                <div>
                  <span>Nama Grup</span>
                  <strong>{{ $consentContext['group_name'] ?? '-' }}</strong>
                </div>

                <div>
                  <span>Pengundang</span>
                  <strong>{{ $consentContext['inviter_name'] ?? 'Pengundang' }}</strong>
                </div>

                <div>
                  <span>Visibilitas Identitas</span>
                  <strong>{{ $consentContext['identity_visibility'] ?? 'Identitas Anda akan ditampilkan sesuai aturan grup.' }}</strong>
                </div>
              </div>
            </div>

            <div class="group-consent-box">
              <h3>Aturan Sebelum Bergabung</h3>

              <ul class="group-consent-rules">
                @foreach($groupRules ?? [] as $rule)
                  <li>{{ $rule }}</li>
                @endforeach
              </ul>
            </div>
          </div>

          <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST" class="group-consent-form">
            @csrf

            @foreach(($consentContext['hidden_fields'] ?? []) as $fieldName => $fieldValue)
              @if(!is_null($fieldValue))
                <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
              @endif
            @endforeach

            <input type="hidden" name="consent_version" value="{{ $consentVersion ?? 'v1' }}">
            <input type="hidden" name="consent_acknowledged" value="1">

            @error('invite_token')
              <div class="group-inline-error">{{ $message }}</div>
            @enderror

            <div class="group-create-actions">
              <button type="submit" class="group-create-submit">
                <span>{{ $consentContext['submit_label'] ?? 'Setuju dan Gabung Grup' }}</span>
              </button>

              <a href="{{ route('mahasiswa.group-chat') }}" class="group-create-cancel">
                <span>Tutup</span>
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@else
<section class="group-create-page">
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

          <h1>Bergabung dengan grup baru</h1>

          <p>
            Pilih topik konseling yang sesuai. Jika grup dengan topik ini sudah ada, sistem akan langsung membukanya untukmu.
            Jika belum ada, grup baru akan dibuat otomatis.
          </p>
        </div>

        <div class="group-create-body">
          <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST" class="group-create-form">
            @csrf

            @if ($errors->any())
                <div class="group-inline-error" style="margin-bottom: 1rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <input type="hidden" name="consent_acknowledged" value="1">
            <input type="hidden" name="consent_version" value="{{ $consentVersion ?? '1.0' }}">

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
              <button type="submit" class="group-create-submit">
                <span>Gabung Bergabung</span>
              </button>

              <a href="{{ route('mahasiswa.group-chat') }}" class="group-create-cancel">
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
@endif
@endsection
