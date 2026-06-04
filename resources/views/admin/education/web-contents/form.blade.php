@extends('layouts.admin')

@section('page-title', isset($webContent) ? 'Edit Konten Edukasi Web' : 'Tambah Konten Edukasi Web')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

<style>
    :root {
        --f-bg: #FBF8F3;
        --f-card: #FFFFFF;
        --f-border: #E5E7EB;
        --f-input: #F3F4F6;
        --f-green: #059669;
        --f-green-dark: #047857;
        --f-green-title: #064E3B;
        --f-green-soft: #D1FAE5;
        --f-text-main: #111827;
        --f-text-muted: #6B7280;
        --f-text-light: #9CA3AF;
        --f-danger: #EF4444;
        --f-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--f-bg);
    }

    .pc-container,
    .pc-content,
    .main-content,
    .content-wrapper {
        background: var(--f-bg) !important;
    }

    /* =========================
       WRAPPER HALAMAN
    ========================= */

    .f-wrap {
        width: 100%;
        max-width: 1120px;
        margin: 0;
        padding: 28px 64px 80px;
        box-sizing: border-box;
    }

    .f-wrap form {
        width: 100%;
        max-width: 1120px;
        display: block;
    }

    /* =========================
       DESKRIPSI HALAMAN
    ========================= */

    .f-page-title {
        margin: 0 0 28px 0;
        padding: 0;
    }

    .f-page-title h1 {
        margin: 0 0 10px;
        color: var(--f-green-title);
        font-size: 32px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.035em;
    }

    .f-page-title p {
        margin: 0;
        max-width: 760px;
        color: var(--f-text-muted);
        font-size: 15px;
        line-height: 1.65;
        text-align: left;
    }

    .f-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 10px;
        color: var(--f-text-muted);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.18s ease;
    }

    .f-back:hover {
        color: var(--f-green);
    }

    /* =========================
       SECTION / CARD FORM
    ========================= */

    .f-section {
        width: 100%;
        display: block;
        background: var(--f-card);
        border: 1.5px solid var(--f-border);
        border-radius: 18px;
        padding: 32px;
        margin: 0 0 24px 0;
        box-shadow: var(--f-shadow);
        box-sizing: border-box;
    }

    .f-section-title {
        margin: 0 0 26px;
        color: var(--f-green);
        font-size: 18px;
        font-weight: 800;
        line-height: 1.3;
    }

    /* =========================
       FORM GROUP & GRID
    ========================= */

    .f-group {
        width: 100%;
        min-width: 0;
        margin-bottom: 22px;
        box-sizing: border-box;
    }

    .f-group:last-child {
        margin-bottom: 0;
    }

    .f-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px;
        align-items: start;
        margin-bottom: 24px;
    }

    .f-row .f-group {
        margin-bottom: 0;
    }

    .f-full {
        grid-column: 1 / -1;
    }

    .f-label {
        display: block;
        margin-bottom: 9px;
        color: var(--f-text-light);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        line-height: 1.3;
    }

    /* =========================
       INPUT, SELECT, TEXTAREA
    ========================= */

    .f-input,
    .f-select,
    .f-textarea {
        width: 100%;
        max-width: none;
        min-height: 56px;
        background: var(--f-input);
        border: 1.5px solid transparent;
        border-radius: 12px;
        padding: 14px 18px;
        color: var(--f-text-main);
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.4;
        outline: none;
        box-shadow: none;
        box-sizing: border-box;
        transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .f-input::placeholder,
    .f-textarea::placeholder {
        color: var(--f-text-light);
        font-weight: 400;
    }

    .f-input:focus,
    .f-select:focus,
    .f-textarea:focus {
        background: #FFFFFF;
        border-color: #6EE7B7;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
    }

    .f-textarea {
        min-height: 150px;
        resize: vertical;
        line-height: 1.6;
    }

    .f-textarea.large {
        min-height: 240px;
    }

    /* =========================
       SELECT
    ========================= */

    .f-select-wrap {
        position: relative;
        width: 100%;
    }

    .f-select-wrap select,
    .f-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 46px;
        cursor: pointer;
    }

    .f-select-wrap svg,
    .f-select-wrap i {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--f-text-light);
    }

    /* =========================
       RADIO BIASA
    ========================= */

    .f-radio-simple-group {
        display: flex;
        align-items: center;
        gap: 30px;
        min-height: 56px;
        padding-top: 2px;
    }

    .f-radio-simple {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        cursor: pointer;
        color: var(--f-text-main);
        font-size: 15px;
        font-weight: 700;
        line-height: 1;
    }

    .f-radio-simple input[type="radio"] {
        width: 17px;
        height: 17px;
        margin: 0;
        accent-color: var(--f-green);
        cursor: pointer;
    }

    .f-radio-simple span {
        line-height: 1;
    }

    /* =========================
       HELP & ERROR
    ========================= */

    .f-help {
        margin-top: 8px;
        max-width: 100%;
        color: var(--f-text-light);
        font-size: 13px;
        line-height: 1.5;
    }

    .f-error {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 7px;
        color: var(--f-danger);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
    }

    /* =========================
       UPLOAD AREA
    ========================= */

    .f-upload-area {
        position: relative;
        width: 100%;
        border: 2px dashed #D1D5DB;
        border-radius: 16px;
        padding: 36px 24px;
        text-align: center;
        background: #F9FAFB;
        cursor: pointer;
        box-sizing: border-box;
        transition: background 0.2s ease, border-color 0.2s ease;
    }

    .f-upload-area:hover,
    .f-upload-area.dragover {
        border-color: var(--f-green);
        background: #F0FDF9;
    }

    .f-upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .f-upload-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 14px;
        background: var(--f-green-soft);
        color: var(--f-green);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .f-upload-title {
        margin-bottom: 5px;
        color: var(--f-green);
        font-size: 15px;
        font-weight: 800;
    }

    .f-upload-sub {
        color: var(--f-text-light);
        font-size: 13px;
        line-height: 1.5;
    }

    .f-file-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 14px;
        padding: 14px 16px;
        background: #FFFFFF;
        border: 1.5px solid var(--f-border);
        border-radius: 12px;
        box-sizing: border-box;
    }

    .f-file-preview-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .f-file-icon {
        width: 40px;
        height: 40px;
        background: var(--f-green-soft);
        color: var(--f-green);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .f-file-name {
        color: var(--f-text-main);
        font-size: 14px;
        font-weight: 800;
        word-break: break-word;
    }

    .f-file-meta {
        margin-top: 3px;
        color: var(--f-text-light);
        font-size: 12px;
    }

    .f-file-remove {
        background: none;
        border: none;
        color: var(--f-text-light);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        transition: color 0.18s ease;
    }

    .f-file-remove:hover {
        color: var(--f-danger);
    }

    .f-thumb-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding: 10px 14px;
        background: #F3F4F6;
        border-radius: 12px;
    }

    .f-thumb-preview img {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border: 1px solid var(--f-border);
        border-radius: 10px;
    }

    .f-thumb-preview span {
        color: var(--f-text-muted);
        font-size: 13px;
        font-weight: 700;
    }

    /* =========================
       ACTION BUTTONS
    ========================= */

    .f-actions {
        width: 100%;
        max-width: 1120px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 14px;
        margin-top: 4px;
        padding-top: 24px;
        border-top: 1.5px solid var(--f-border);
        box-sizing: border-box;
    }

    .f-btn,
    .f-submit {
        min-width: 180px;
        min-height: 52px;
        border: none;
        border-radius: 12px;
        padding: 13px 22px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .f-btn-draft {
        background: #F3F4F6;
        color: #374151;
        border: 2px solid #D1D5DB;
     }

    .f-btn-draft:hover {
        background: #E5E7EB;
        color: #111827;
        transform: translateY(-1px);
    }

    .f-btn-publish,
    .f-submit {
        background: var(--f-green);
        color: #FFFFFF;
        box-shadow: 0 10px 22px rgba(5, 150, 105, 0.24);
    }

    .f-btn-publish:hover,
    .f-submit:hover {
        background: var(--f-green-dark);
        color: #FFFFFF;
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgba(5, 150, 105, 0.32);
    }

    .f-submit-wrap {
        width: 100%;
        margin-top: 8px;
    }

    .f-submit {
        width: 100%;
    }

    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 1200px) {
        .f-wrap {
            max-width: none;
            padding: 28px 42px 76px;
        }
    }

    @media (max-width: 768px) {
        .f-wrap {
            padding: 24px 20px 64px;
        }

        .f-section {
            padding: 24px 20px;
            border-radius: 16px;
        }

        .f-row {
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .f-row .f-group {
            margin-bottom: 0;
        }

        .f-radio-simple-group {
            gap: 24px;
            min-height: auto;
            padding-top: 6px;
        }

        .f-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .f-btn,
        .f-submit {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .f-wrap {
            padding: 22px 16px 56px;
        }

        .f-page-title h1 {
            font-size: 24px;
        }

        .f-page-title p {
            font-size: 14px;
        }

        .f-section-title {
            font-size: 16px;
        }

        .f-input,
        .f-select,
        .f-textarea {
            font-size: 14px;
            padding: 13px 15px;
        }

        .f-radio-simple-group {
            flex-wrap: wrap;
            gap: 18px;
        }
    }
</style>
@endpush

@section('konten')

<div class="f-wrap">

    <div class="f-page-title">
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

        <input type="hidden"
               name="status"
               id="status"
               value="{{ old('status', $webContent->status ?? 0) }}">

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
                    <label class="f-label">Jenis Konten</label>

                    <div class="f-radio-simple-group">
                        <label class="f-radio-simple">
                            <input type="radio"
                                   name="type"
                                   value="Artikel"
                                   {{ old('type', $webContent->type ?? 'Artikel') === 'Artikel' ? 'checked' : '' }}>
                            <span>Artikel</span>
                        </label>

                        <label class="f-radio-simple">
                            <input type="radio"
                                   name="type"
                                   value="Video"
                                   {{ old('type', $webContent->type ?? '') === 'Video' ? 'checked' : '' }}>
                            <span>Video</span>
                        </label>
                    </div>

                    @error('type')
                        <div class="f-error">⚠ {{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="f-row">
                <div class="f-group">
    <label class="f-label" for="source_url">Link Video / Referensi</label>

    <input id="source_url"
           class="f-input @error('source_url') is-invalid @enderror"
           type="url"
           name="source_url"
           value="{{ old('source_url', $webContent->url_sumber ?? '') }}"
           placeholder="https://youtube.com/... atau link artikel referensi">

    <div class="f-help">
        Gunakan link YouTube/Vimeo jika konten berupa video, atau link artikel jika konten berupa artikel.
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
                        Opsional. Untuk video YouTube boleh dikosongkan. Untuk artikel, isi dengan URL gambar cover.
                    </div>

                    @error('thumbnail')
                        <div class="f-error">⚠ {{ $message }}</div>
                    @enderror
                </div>
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

        <div class="f-actions">
            <button type="submit"
                    class="f-btn f-btn-draft"
                    data-status-submit="0">
                Simpan sebagai Draft
            </button>

            <button type="submit"
                    class="f-btn f-btn-publish"
                    data-status-submit="1">
                {{ isset($webContent) ? 'Simpan & Publikasikan' : 'Publikasikan' }}
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

        const statusInput = document.getElementById('status');
const statusButtons = document.querySelectorAll('[data-status-submit]');

statusButtons.forEach(button => {
    button.addEventListener('click', function () {
        if (statusInput) {
            statusInput.value = this.dataset.statusSubmit;
        }
    });
});

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