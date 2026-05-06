@extends('layouts.admin')

@section('page-title', isset($module) ? 'Edit Modul' : 'Tambah Modul')

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
        --f-radius: 14px;
        --f-shadow: 0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.04);
    }

    .pc-container { background: var(--f-bg) !important; }

    .f-wrap {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 24px 80px;
    }

    /* ---- Page title ---- */
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
    }

    /* ---- Back link ---- */
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
    .f-back:hover { color: var(--f-green); }

    /* ---- Section Card ---- */
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

    /* ---- Form fields ---- */
    .f-group { margin-bottom: 18px; }
    .f-group:last-child { margin-bottom: 0; }

    .f-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--f-text-3);
        margin-bottom: 7px;
    }

    .f-input, .f-select, .f-textarea {
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
    .f-input::placeholder, .f-textarea::placeholder { color: var(--f-text-3); }
    .f-input:focus, .f-select:focus, .f-textarea:focus {
        border-color: var(--f-border-focus);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }
    .f-textarea { min-height: 120px; resize: vertical; line-height: 1.6; }

    /* Select with custom arrow */
    .f-select-wrap { position: relative; }
    .f-select-wrap svg {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--f-text-3);
    }

    /* Input with icon prefix */
    .f-input-icon-wrap { position: relative; }
    .f-input-icon-wrap .f-icon-prefix {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--f-text-3);
        display: flex;
        align-items: center;
    }
    .f-input-icon-wrap .f-input { padding-left: 42px; }

    /* Row: two cols */
    .f-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* ---- Drag & Drop Upload ---- */
    .f-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 40px 24px;
        text-align: center;
        background: #f9fafb;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }
    .f-upload-area:hover, .f-upload-area.dragover {
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
        width: 48px; height: 48px;
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
    .f-upload-sub { font-size: 0.8rem; color: var(--f-text-3); }

    .f-upload-hint {
        text-align: right;
        font-size: 0.75rem;
        color: var(--f-text-3);
        margin-bottom: 10px;
    }

    /* File preview */
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
        width: 38px; height: 38px;
        background: #fee2e2;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .f-file-name { font-weight: 700; font-size: 0.85rem; color: var(--f-text-1); }
    .f-file-meta { font-size: 0.75rem; color: var(--f-text-3); margin-top: 2px; }
    .f-file-remove {
        background: none; border: none; cursor: pointer;
        color: var(--f-text-3); padding: 4px;
        display: flex; align-items: center;
        transition: color 0.18s;
    }
    .f-file-remove:hover { color: #ef4444; }

    /* Current uploaded thumb */
    .f-thumb-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f3f4f6;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 12px;
    }
    .f-thumb-preview img { width: 44px; height: 44px; object-fit: cover; border-radius: 8px; }
    .f-thumb-preview span { font-size: 0.82rem; color: var(--f-text-2); font-weight: 600; }

    /* Error message */
    .f-error { font-size: 0.78rem; color: #ef4444; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px; }

    /* ---- Submit button ---- */
    .f-submit-wrap { padding-top: 4px; }
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
    .f-submit:hover { background: #047857; box-shadow: 0 6px 20px rgba(5,150,105,0.38); transform: translateY(-1px); }
    .f-submit:active { transform: translateY(0); }

    @media (max-width: 580px) {
        .f-row { grid-template-columns: 1fr; }
        .f-wrap { padding: 0 12px 60px; }
    }
</style>
@endpush

@section('konten')
<div class="f-wrap">

    <a href="{{ route('counselor.education.modules.index') }}" class="f-back">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
    </a>

    <div class="f-page-title">
        <h1>{{ isset($module) ? 'Edit Modul Edukasi' : 'Submit Modul Edukasi Baru' }}</h1>
        <p>Lengkapi informasi di bawah untuk mempublikasikan materi edukasi kepada mahasiswa.</p>
    </div>

    <form action="{{ isset($module) ? route('counselor.education.modules.update', $module->id) : route('counselor.education.modules.store') }}"
          method="POST" enctype="multipart/form-data" id="moduleForm" novalidate>
        @csrf
        @if(isset($module)) @method('PUT') @endif

        {{-- Section 1: Informasi Dasar --}}
        <div class="f-section">
            <div class="f-section-title">Informasi Dasar</div>

            {{-- Judul --}}
            <div class="f-group">
                <label class="f-label" for="title">Judul Modul</label>
                <input id="title" class="f-input" type="text" name="title"
                       value="{{ old('title', $module->title ?? '') }}"
                       placeholder="Masukkan judul materi edukasi..." required>
                @error('title') <div class="f-error">⚠ {{ $message }}</div> @enderror
            </div>

            {{-- Kategori & Target Audiens --}}
            <div class="f-row">
                <div class="f-group">
                    <label class="f-label" for="kategori">Kategori</label>
                    <div class="f-select-wrap">
                        <select id="kategori" class="f-input f-select" name="kategori">
                            <option value="">Pilih Kategori</option>
                            <option value="Kesehatan Mental" {{ old('kategori', $module->kategori ?? '') === 'Kesehatan Mental' ? 'selected' : '' }}>Kesehatan Mental</option>
                            <option value="Manajemen Stres" {{ old('kategori', $module->kategori ?? '') === 'Manajemen Stres' ? 'selected' : '' }}>Manajemen Stres</option>
                            <option value="Kecemasan" {{ old('kategori', $module->kategori ?? '') === 'Kecemasan' ? 'selected' : '' }}>Kecemasan</option>
                            <option value="Motivasi" {{ old('kategori', $module->kategori ?? '') === 'Motivasi' ? 'selected' : '' }}>Motivasi</option>
                            <option value="Mindfulness" {{ old('kategori', $module->kategori ?? '') === 'Mindfulness' ? 'selected' : '' }}>Mindfulness</option>
                            <option value="Lainnya" {{ old('kategori', $module->kategori ?? '') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
                <div class="f-group">
                    <label class="f-label" for="target_audiens">Target Audiens</label>
                    <div class="f-select-wrap">
                        <select id="target_audiens" class="f-input f-select" name="target_audiens">
                            <option value="Semua Mahasiswa" {{ old('target_audiens', $module->target_audiens ?? 'Semua Mahasiswa') === 'Semua Mahasiswa' ? 'selected' : '' }}>Semua Mahasiswa</option>
                            <option value="Semester 1-2" {{ old('target_audiens', $module->target_audiens ?? '') === 'Semester 1-2' ? 'selected' : '' }}>Semester 1-2</option>
                            <option value="Semester 3-4" {{ old('target_audiens', $module->target_audiens ?? '') === 'Semester 3-4' ? 'selected' : '' }}>Semester 3-4</option>
                            <option value="Semester 5-6" {{ old('target_audiens', $module->target_audiens ?? '') === 'Semester 5-6' ? 'selected' : '' }}>Semester 5-6</option>
                            <option value="Semester 7-8..." {{ old('target_audiens', $module->target_audiens ?? '') === 'Tingkat Akhir' ? 'selected' : '' }}>Semester 7-8...</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>

            {{-- Link Referensi --}}
            <div class="f-group">
                <label class="f-label" for="content_url">Link Materi / Tautan Referensi</label>
                <div class="f-input-icon-wrap">
                    <span class="f-icon-prefix">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </span>
                    <input id="content_url" class="f-input" type="url" name="content_url"
                           value="{{ old('content_url', isset($module) && $module->content_url && !Str::startsWith($module->content_url, 'modules/') ? $module->content_url : '') }}"
                           placeholder="https://example.com/materi-tambahan">
                </div>
                @error('content_url') <div class="f-error">⚠ {{ $message }}</div> @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="f-group">
                <label class="f-label" for="description">Deskripsi Modul</label>
                <textarea id="description" class="f-input f-textarea" name="description"
                          placeholder="Berikan penjelasan singkat mengenai isi dan tujuan modul ini..." required>{{ old('description', $module->description ?? '') }}</textarea>
                @error('description') <div class="f-error">⚠ {{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Section 2: Konten Materi --}}
        <div class="f-section">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                <div class="f-section-title" style="margin:0;">Konten Materi</div>
                <span style="font-size:0.75rem; color:var(--f-text-3);">Mendukung PDF, MP4, PNG, JPG (Max 50MB)</span>
            </div>

            {{-- Existing content preview --}}
            @if(isset($module) && $module->content_url && Str::startsWith($module->content_url, 'modules/'))
                <div class="f-thumb-preview">
                    <span style="font-size:1.4rem;">📄</span>
                    <span>File saat ini: {{ basename($module->content_url) }}</span>
                </div>
            @endif

            {{-- Drag & Drop area --}}
            <div class="f-upload-area" id="uploadArea">
                <input type="file" name="content_file" id="contentFile" accept=".pdf,.mp4,.png,.jpg,.jpeg" onchange="handleFileSelect(this)">
                <div class="f-upload-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                </div>
                <div class="f-upload-title">Tarik dan lepas file di sini</div>
                <div class="f-upload-sub">Atau klik untuk menelusuri dokumen komputer Anda</div>
            </div>

            {{-- File preview after selection --}}
            <div id="filePreviewWrap" style="display:none;">
                <div class="f-file-preview">
                    <div class="f-file-preview-left">
                        <div class="f-file-icon">📄</div>
                        <div>
                            <div class="f-file-name" id="fileName">—</div>
                            <div class="f-file-meta" id="fileMeta">—</div>
                        </div>
                    </div>
                    <button type="button" class="f-file-remove" onclick="clearFile()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>

            @error('content_file') <div class="f-error" style="margin-top:10px;">⚠ {{ $message }}</div> @enderror
        </div>

        {{-- Section 3: Thumbnail (opsional) --}}
        <div class="f-section">
            <div class="f-section-title">Thumbnail / Gambar Sampul </div>

            @if(isset($module) && $module->thumbnail)
                <div class="f-thumb-preview">
                    @if(Str::startsWith($module->thumbnail, 'modules/thumbnails'))
                        <img src="{{ Storage::url($module->thumbnail) }}" alt="Thumbnail">
                    @else
                        <img src="{{ $module->thumbnail }}" alt="Thumbnail" onerror="this.style.display='none'">
                    @endif
                    <span>Thumbnail saat ini tersimpan</span>
                </div>
            @endif

            <div class="f-group">
                <label class="f-label">Upload File Gambar</label>
                <input class="f-input" type="file" name="thumbnail_file" accept="image/*" style="padding:10px 14px; background:#f3f4f6; font-size:0.85rem;">
                @error('thumbnail_file') <div class="f-error">⚠ {{ $message }}</div> @enderror
            </div>

            <div class="f-group">
                <label class="f-label" for="thumbnail_url">Atau Gunakan URL Gambar</label>
                <input id="thumbnail_url" class="f-input" type="url" name="thumbnail_url"
                       value="{{ old('thumbnail_url', isset($module) && $module->thumbnail && !Str::startsWith($module->thumbnail, 'modules/') ? $module->thumbnail : '') }}"
                       placeholder="https://example.com/gambar.jpg">
                @error('thumbnail_url') <div class="f-error">⚠ {{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Section 4: Pengaturan --}}
        <div class="f-section">
            <div class="f-section-title">Pengaturan</div>
            <div class="f-row">
                <div class="f-group">
                    <label class="f-label" for="reward_point">Reward Point</label>
                    <input id="reward_point" class="f-input" type="number" name="reward_point"
                           value="{{ old('reward_point', $module->reward_point ?? 50) }}" min="0" required>
                    @error('reward_point') <div class="f-error">⚠ {{ $message }}</div> @enderror
                </div>
                <div class="f-group">
                    <label class="f-label" for="status">Status Publikasi</label>
                    <div class="f-select-wrap">
                        <select id="status" class="f-input f-select" name="status" required>
                            <option value="1" {{ old('status', $module->status ?? 1) == 1 ? 'selected' : '' }}>Aktif (Publis)</option>
                            <option value="0" {{ old('status', $module->status ?? 1) == 0 ? 'selected' : '' }}>Nonaktif (Draft)</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="f-submit-wrap">
            <button type="submit" class="f-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                {{ isset($module) ? 'Simpan Perubahan' : 'Terbitkan Modul' }}
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    function formatBytes(bytes) {
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;

        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileMeta').textContent = formatBytes(file.size) + ' • Siap diupload';
        document.getElementById('filePreviewWrap').style.display = 'block';
        document.getElementById('uploadArea').style.borderColor = '#059669';
        document.getElementById('uploadArea').style.background = '#f0fdf9';
    }

    function clearFile() {
        document.getElementById('contentFile').value = '';
        document.getElementById('filePreviewWrap').style.display = 'none';
        document.getElementById('uploadArea').style.borderColor = '';
        document.getElementById('uploadArea').style.background = '';
    }

    // Drag & drop visual feedback
    const area = document.getElementById('uploadArea');
    if (area) {
        area.addEventListener('dragover', (e) => { e.preventDefault(); area.classList.add('dragover'); });
        area.addEventListener('dragleave', () => area.classList.remove('dragover'));
        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            const dt = e.dataTransfer;
            if (dt.files.length) {
                document.getElementById('contentFile').files = dt.files;
                handleFileSelect(document.getElementById('contentFile'));
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('moduleForm');
        
        form.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Cari input pertama yang tidak valid
                const invalidFields = this.querySelectorAll(':invalid');
                if (invalidFields.length > 0) {
                    const firstInvalid = invalidFields[0];
                    let fieldName = 'Kolom ini';
                    
                    // Mencoba mencari label yang terkait dengan input tersebut
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

                    // Fokuskan kursor ke input yang salah
                    firstInvalid.focus();
                }
            }
        });
    });
</script>
@endpush
