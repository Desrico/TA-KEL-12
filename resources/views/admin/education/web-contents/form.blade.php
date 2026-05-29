@extends('layouts.admin')

@section('page-title', isset($webContent) ? 'Edit Konten Edukasi Web' : 'Tambah Konten Edukasi Web')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

<style>
    :root {
        --f-bg: #f9fafb;
        --f-card: #ffffff;
        --f-border: #e5e7eb;
        --f-border-focus: #6ee7b7;
        --f-green: #059669;
        --f-green-light: #d1fae5;
        --f-text-1: #111827;
        --f-text-2: #6b7280;
        --f-text-3: #9ca3af;
        --f-shadow: 0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.04);
    }

    .pc-container {
        background: var(--f-bg) !important;
    }

    .f-wrap {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 24px 80px;
    }

    .f-page-title {
        padding: 32px 0 24px;
    }

    .f-page-title h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--f-text-1);
        margin: 0 0 6px;
        letter-spacing: -0.02em;
    }

    .f-page-title p {
        font-size: 0.875rem;
        color: var(--f-text-2);
        margin: 0;
        line-height: 1.7;
    }

    .f-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--f-text-2);
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 4px;
        transition: color 0.18s;
    }

    .f-back:hover {
        color: var(--f-green);
    }

    .f-section {
        background: var(--f-card);
        border: 1.5px solid var(--f-border);
        border-radius: 18px;
        padding: 28px 28px 24px;
        margin-bottom: 20px;
        box-shadow: var(--f-shadow);
    }

    .f-section-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1rem;
        font-weight: 800;
        color: var(--f-green);
        margin: 0 0 22px;
    }

    .f-group {
        margin-bottom: 18px;
    }

    .f-group:last-child {
        margin-bottom: 0;
    }

    .f-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--f-text-3);
        margin-bottom: 7px;
    }

    .f-input,
    .f-select,
    .f-textarea {
        width: 100%;
        background: #f3f4f6;
        border: 1.5px solid transparent;
        border-radius: 10px;
        padding: 12px 16px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.92rem;
        color: var(--f-text-1);
        outline: none;
        transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
        appearance: none;
    }

    .f-input::placeholder,
    .f-textarea::placeholder {
        color: var(--f-text-3);
    }

    .f-input:focus,
    .f-select:focus,
    .f-textarea:focus {
        border-color: var(--f-border-focus);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }

    .f-textarea {
        min-height: 120px;
        resize: vertical;
        line-height: 1.6;
    }

    .f-textarea.large {
        min-height: 190px;
    }

    .f-select-wrap {
        position: relative;
    }

    .f-select-wrap svg {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--f-text-3);
    }

    .f-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .f-help {
        font-size: 0.76rem;
        color: var(--f-text-3);
        margin-top: 6px;
        line-height: 1.6;
    }

    .f-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 38px 24px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }

    .f-upload-area:hover,
    .f-upload-area.dragover {
        border-color: var(--f-green);
        background: #f0fdf9;
    }

    .f-upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .f-upload-icon {
        width: 48px;
        height: 48px;
        background: var(--f-green-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        color: var(--f-green);
    }

    .f-upload-title {
        font-weight: 700;
        color: var(--f-green);
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .f-upload-sub {
        font-size: 0.8rem;
        color: var(--f-text-3);
    }

    .f-file-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        border: 1.5px solid var(--f-border);
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 14px;
    }

    .f-file-preview-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .f-file-icon {
        width: 38px;
        height: 38px;
        background: var(--f-green-light);
        color: var(--f-green);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .f-file-name {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--f-text-1);
    }

    .f-file-meta {
        font-size: 0.75rem;
        color: var(--f-text-3);
        margin-top: 2px;
    }

    .f-file-remove {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--f-text-3);
        padding: 4px;
        display: flex;
        align-items: center;
        transition: color 0.18s;
    }

    .f-file-remove:hover {
        color: #ef4444;
    }

    .f-thumb-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f3f4f6;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 12px;
    }

    .f-thumb-preview img {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .f-thumb-preview span {
        font-size: 0.82rem;
        color: var(--f-text-2);
        font-weight: 600;
    }

    .f-error {
        font-size: 0.78rem;
        color: #ef4444;
        font-weight: 600;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .f-submit-wrap {
        padding-top: 4px;
    }

    .f-submit {
        width: 100%;
        background: var(--f-green);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 14px 28px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
        box-shadow: 0 4px 14px rgba(5,150,105,0.3);
    }

    .f-submit:hover {
        background: #047857;
        box-shadow: 0 6px 20px rgba(5,150,105,0.38);
        transform: translateY(-1px);
    }

    .f-submit:active {
        transform: translateY(0);
    }

    @media (max-width: 580px) {
        .f-row {
            grid-template-columns: 1fr;
        }

        .f-wrap {
            padding: 0 12px 60px;
        }

        .f-section {
            padding: 22px 18px;
        }
    }
</style>
@endpush

@section('konten')

<div class="f-wrap">

    <a href="{{ route('counselor.education.web-contents.index') }}" class="f-back">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>

    <div class="f-page-title">
        <h1>{{ isset($webContent) ? 'Edit Konten Edukasi Web' : 'Tambah Konten Edukasi Web' }}</h1>
        <p>
            Lengkapi informasi berikut untuk menampilkan artikel atau video edukasi pada halaman Edukasi Mental di website.
        </p>
    </div>

    <form action="{{ isset($webContent) ? route('counselor.education.web-contents.update', $webContent->id) : route('counselor.education.web-contents.store') }}"
      method="POST"
      id="webContentForm"
      novalidate>
        @csrf
        @if(isset($webContent))
            @method('PUT')
        @endif

        {{-- Informasi Dasar --}}
        <div class="f-section">
            <div class="f-section-title">Informasi Dasar</div>

            <div class="f-group">
                <label class="f-label" for="title">Judul Konten</label>
                <input id="title"
                       class="f-input"
                       type="text"
                       name="title"
                       value="{{ old('title', $webContent->title ?? '') }}"
                       placeholder="Contoh: Menghadapi Kecemasan dan Overthinking"
                       required>
                @error('title')
                    <div class="f-error">⚠ {{ $message }}</div>
                @enderror
            </div>

            <div class="f-row">
                <div class="f-group">
                    <label class="f-label" for="topic">Topik Utama</label>
                    <div class="f-select-wrap">
                        <select id="topic" class="f-input f-select" name="topic" required>
                            <option value="">Pilih Topik</option>
                            <option value="Akademik" {{ old('topic', $webContent->topic ?? '') === 'Akademik' ? 'selected' : '' }}>Akademik</option>
                            <option value="Intrapersonal" {{ old('topic', $webContent->topic ?? '') === 'Intrapersonal' ? 'selected' : '' }}>Intrapersonal</option>
                            <option value="Kehidupan di Kampus" {{ old('topic', $webContent->topic ?? '') === 'Kehidupan di Kampus' ? 'selected' : '' }}>Kehidupan di Kampus</option>
                            <option value="Keluarga" {{ old('topic', $webContent->topic ?? '') === 'Keluarga' ? 'selected' : '' }}>Keluarga</option>
                            <option value="Masalah di Asrama" {{ old('topic', $webContent->topic ?? '') === 'Masalah di Asrama' ? 'selected' : '' }}>Masalah di Asrama</option>
                            <option value="Relasi" {{ old('topic', $webContent->topic ?? '') === 'Relasi' ? 'selected' : '' }}>Relasi</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                    @error('topic')
                        <div class="f-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                <div class="f-group">
                    <label class="f-label" for="type">Jenis Konten</label>
                    <div class="f-select-wrap">
                        <select id="type" class="f-input f-select" name="type" required>
                            <option value="Artikel" {{ old('type', $webContent->type ?? 'Artikel') === 'Artikel' ? 'selected' : '' }}>Artikel</option>
                            <option value="Video" {{ old('type', $webContent->type ?? '') === 'Video' ? 'selected' : '' }}>Video</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                    @error('type')
                        <div class="f-error">⚠ {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="f-row">
                <div class="f-group">
                    <label class="f-label" for="reading_time">Estimasi Waktu</label>
                    <input id="reading_time"
                            class="f-input"
                            type="text"
                            name="reading_time"
                            value="{{ old('reading_time', $webContent->reading_time ?? '') }}"
                            placeholder="Contoh: 5 menit baca / 3 menit tonton">
                    @error('reading_time')
                        <div class="f-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                <div class="f-group">
                    <label class="f-label" for="status">Status Publikasi</label>
                    <div class="f-select-wrap">
                        <select id="status" class="f-input f-select" name="status" required>
                            <option value="1" {{ old('status', $webContent->status ?? 1) == 1 ? 'selected' : '' }}>Aktif / Tampil di Website</option>
                            <option value="0" {{ old('status', $webContent->status ?? 1) == 0 ? 'selected' : '' }}>Draft / Tidak Tampil</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                    @error('status')
                        <div class="f-error">⚠ {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="f-group">
                <label class="f-label" for="source_url">Link Video / Referensi</label>
                <input id="source_url"
                       class="f-input"
                       type="url"
                       name="source_url"
                       value="{{ old('source_url', $webContent->source_url ?? '') }}"
                       placeholder="https://youtube.com/... atau link artikel referensi">
                <div class="f-help">
                    Opsional. Gunakan jika konten berupa video YouTube, sumber artikel, atau referensi eksternal.
                </div>
                @error('source_url')
                    <div class="f-error">⚠ {{ $message }}</div>
                @enderror
            </div>

            <div class="f-group">
                <label class="f-label" for="thumbnail">URL Thumbnail / Cover</label>
                <input id="thumbnail"
                    class="f-input"
                    type="url"
                    name="thumbnail"
                    value="{{ old('thumbnail', $webContent->thumbnail ?? '') }}"
                    placeholder="https://contoh.com/gambar-cover.jpg">

                <div class="f-help">
                    Opsional. Untuk video YouTube boleh dikosongkan karena thumbnail bisa diambil otomatis.
                    Untuk artikel, isi dengan URL gambar cover dari artikel tersebut.
                </div>

                @error('thumbnail')
                    <div class="f-error">⚠ {{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Isi Konten --}}
        <div class="f-section">
            <div class="f-section-title">Isi Konten</div>

            <div class="f-group">
                <label class="f-label" for="excerpt">Deskripsi Singkat</label>
                <textarea id="excerpt"
                          class="f-input f-textarea"
                          name="excerpt"
                          placeholder="Ringkasan singkat yang akan tampil di card konten edukasi..."
                          required>{{ old('excerpt', $webContent->excerpt ?? '') }}</textarea>
                @error('excerpt')
                    <div class="f-error">⚠ {{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="f-submit-wrap">
            <button type="submit" class="f-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                {{ isset($webContent) ? 'Simpan Perubahan' : 'Terbitkan Konten' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('webContentForm');

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                const invalidFields = this.querySelectorAll(':invalid');

                if (invalidFields.length > 0) {
                    const firstInvalid = invalidFields[0];
                    let fieldName = 'Kolom ini';

                    if (firstInvalid.id) {
                        const label = document.querySelector(`label[for="${firstInvalid.id}"]`);

                        if (label) {
                            fieldName = label.innerText;
                        }
                    }

                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap!',
                        text: `Mohon isi atau perbaiki bagian: ${fieldName}`,
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'Baik, lengkapi'
                    });

                    firstInvalid.focus();
                }
            }
        });
    });
</script>
@endpush