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
</style>
@endpush

@section('konten')
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
          <h1>Berbagung dengan grup baru</h1>
          <p>
            Pilih topik konseling yang sesuai. Jika grup dengan topik ini sudah ada, sistem akan langsung membukanya untukmu.
            Jika belum ada, grup baru akan dibuat otomatis.
          </p>
        </div>

        <div class="group-create-body">
          @if($errors->any())
              <div class="group-inline-error" style="margin-bottom: 16px;">
                  {{ $errors->first() }}
              </div>
          @endif

          @if(session('error'))
              <div class="group-inline-error" style="margin-bottom: 16px;">
                  {{ session('error') }}
              </div>
          @endif
          {{-- Form ini tetap memakai endpoint join agar alur buat grup dan masuk grup tetap satu jalur. --}}
          @php
              $topicOptions = $topicOptions ?? \App\Models\GroupChatRoom::topicOptions();
          @endphp
          <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST" class="group-create-form">
            @csrf
              <input type="hidden" name="consent_acknowledged" value="1">
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
@endsection
