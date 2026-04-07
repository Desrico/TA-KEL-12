@extends('layouts.master')

@push('styles')
<style>
.profil-hero {
    background: linear-gradient(135deg, #071825 0%, #0d2d4a 50%, #0e5c3d 100%);
    padding: 2.5rem 0;
    position: relative;
    overflow: hidden;
}
.profil-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 80% 50%, rgba(15,184,122,.12) 0%, transparent 55%);
}
.profil-card {
    background: white;
    border-radius: 20px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
}
.profil-card-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    padding: 2rem;
    position: relative;
}
.avatar-circle {
    width: 80px; height: 80px; border-radius: 50%;
    background: rgba(255,255,255,.2);
    border: 3px solid rgba(255,255,255,.5);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: white; flex-shrink: 0;
}
.status-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(15,184,122,.2); border: 1px solid rgba(15,184,122,.4);
    color: #52e8a6; border-radius: 50px; padding: .2rem .75rem;
    font-size: .72rem; font-weight: 700;
}
.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .6rem 0; font-size: .875rem;
    border-bottom: 1px dashed rgba(26,58,92,.08);
}
.info-row:last-child { border-bottom: none; }
.info-label { color: var(--text-light); font-size: .8rem; }
.info-val { font-weight: 600; color: var(--text-dark); }
.stat-card {
    background: var(--surface); border-radius: 14px;
    padding: 1.2rem; text-align: center;
}
.stat-number {
    font-family: 'Fraunces', serif; font-size: 2rem;
    font-weight: 700; color: var(--primary); line-height: 1;
}
.stat-label { font-size: .75rem; color: var(--text-light); margin-top: .3rem; }
.anonim-toggle {
    background: var(--surface); border-radius: 14px;
    padding: 1.2rem; display: flex; align-items: center; gap: 1rem;
}
.toggle-switch { position: relative; width: 48px; height: 26px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; inset: 0; background: #ccc;
    border-radius: 50px; cursor: pointer; transition: .3s;
}
.toggle-slider:before {
    content: ''; position: absolute;
    width: 20px; height: 20px; left: 3px; bottom: 3px;
    background: white; border-radius: 50%; transition: .3s;
}
.toggle-switch input:checked + .toggle-slider { background: var(--accent); }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }
.edit-field {
    border: 1.5px solid rgba(26,58,92,.13);
    border-radius: 10px; padding: .5rem .85rem;
    font-size: .875rem; width: 100%;
    transition: all .2s; background: white;
}
.edit-field:focus {
    border-color: var(--primary-light);
    box-shadow: 0 0 0 3px rgba(46,134,193,.12);
    outline: none;
}
</style>
@endpush

@section('konten')

@php
    $mahasiswa = $user->mahasiswa;
    $profil    = $user->profil;
@endphp

<!-- HERO -->
<section class="profil-hero">
    <div class="container position-relative" style="z-index:1">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb" style="background:none;padding:0">
                <li class="breadcrumb-item">
                    <a href="/" style="color:rgba(255,255,255,.5);font-size:.82rem">Beranda</a>
                </li>
                <li class="breadcrumb-item active" style="color:rgba(255,255,255,.8);font-size:.82rem">Profil</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">

            <!-- Avatar + tombol ganti foto -->
            <div style="position:relative;flex-shrink:0">

                <!-- Klik avatar untuk ganti foto -->
                <div class="avatar-circle" id="avatar-preview-wrap"
                    onclick="document.getElementById('foto-input').click()"
                    style="cursor:pointer;position:relative;overflow:hidden">

                    @if(optional($profil)->foto)
                        <img src="{{ Storage::url($profil->foto) }}"
                             id="avatar-img"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover">
                    @else
                        <i class="bi bi-person-fill" id="avatar-icon"></i>
                    @endif

                    <!-- Overlay saat hover -->
                    <div id="avatar-overlay"
                        style="position:absolute;inset:0;border-radius:50%;
                               background:rgba(0,0,0,.4);
                               display:flex;align-items:center;justify-content:center;
                               opacity:0;transition:.2s">
                        <i class="bi bi-camera-fill" style="color:white;font-size:1.2rem"></i>
                    </div>
                </div>

                <!-- Ikon pensil di pojok -->
                <div onclick="document.getElementById('foto-input').click()"
                    style="display:flex;position:absolute;bottom:0;right:0;
                           width:28px;height:28px;border-radius:50%;
                           background:var(--accent);border:2px solid white;
                           cursor:pointer;align-items:center;justify-content:center;
                           box-shadow:0 2px 8px rgba(0,0,0,.2)">
                    <i class="bi bi-pencil-fill" style="font-size:.65rem;color:white"></i>
                </div>

                <!-- Input file tersembunyi -->
                <input type="file" id="foto-input" name="foto"
                       accept="image/jpg,image/jpeg,image/png"
                       style="display:none"
                       onchange="previewDanSimpanFoto(this)">
            </div>

            <div>
                <span class="status-badge mb-1 d-inline-flex">
                    <span style="width:7px;height:7px;border-radius:50%;background:#52e8a6;display:inline-block"></span>
                    Mahasiswa Aktif
                </span>
                <h1 id="nama-header" style="font-family:'Fraunces',serif;color:white;font-size:1.8rem;font-weight:700;margin:0">
                    {{ $user->isAnonim() ? 'Mahasiswa Anonim' : $user->nama }}
                </h1>
                <p style="color:rgba(255,255,255,.6);font-size:.85rem;margin:0">
                    {{ $mahasiswa->jurusan ?? '-' }} · Angkatan {{ $mahasiswa->angkatan ?? '-' }}
                </p>
            </div>

        </div>
    </div>
</section>

<!-- KONTEN -->
<div class="container" style="margin-top:2rem;padding-bottom:3rem">

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="row g-4">

        <!-- KIRI -->
        <div class="col-lg-4">
            <div class="profil-card mb-4">
                <div class="profil-card-header">
                    <h6 style="color:rgba(255,255,255,.6);font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.8rem">
                        Ringkasan Sesi
                    </h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-number">{{ $totalKonseling }}</div>
                                <div class="stat-label">Total Konseling</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-number">{{ $sesiBerlangsung }}</div>
                                <div class="stat-label">Disetujui</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('riwayat') }}"
                       class="btn w-100 mt-3 rounded-pill"
                       style="background:rgba(255,255,255,.15);color:white;font-size:.83rem;font-weight:600;border:1px solid rgba(255,255,255,.2)">
                        <i class="bi bi-clock-history me-1"></i>Buka Riwayat
                    </a>
                </div>

                <!-- Mode Anonim -->
                <div class="p-3">
                    <div class="anonim-toggle">
                        <div class="flex-grow-1">
                            <div style="font-weight:700;font-size:.85rem;color:var(--accent)">
                                <i class="bi bi-incognito me-1"></i>Mode Anonim
                            </div>
                            <div style="font-size:.75rem;color:var(--text-light);margin-top:.2rem">
                                Sembunyikan identitas dari konselor
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" {{ optional($profil)->anonim ? 'checked' : '' }}
                                   onchange="toggleAnonim(this)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- KANAN -->
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="font-weight:700;margin:0">Informasi Profil</h5>
                <button type="button" class="btn rounded-pill px-4"
                    style="background:var(--primary);color:white;font-size:.83rem;font-weight:600"
                    onclick="toggleEdit()">
                    <i class="bi bi-pencil-fill me-1" id="edit-icon"></i>
                    <span id="edit-btn-text">Edit Profil</span>
                </button>
            </div>

            <form method="POST" action="{{ route('profil.update') }}" id="profil-form">
                @csrf

                <div class="profil-card mb-4">
                    <div class="p-4">
                        <h6 style="font-weight:700;margin-bottom:1rem">
                            <i class="bi bi-mortarboard-fill me-2" style="color:var(--primary)"></i>Informasi Akademik
                        </h6>

                        <!-- Nama -->
                        <div class="info-row">
                            <span class="info-label" style="min-width:130px">Nama Lengkap</span>
                            <span class="info-val" id="view-nama">
                                {{ $user->isAnonim() ? 'Mahasiswa Anonim' : $user->nama }}
                            </span>
                            <input type="text" name="nama" id="edit-nama"
                                class="edit-field" style="display:none"
                                value="{{ $user->nama }}">
                        </div>

                        <!-- NIM -->
                        <div class="info-row">
                            <span class="info-label" style="min-width:130px">NIM</span>
                            <span class="info-val" id="view-nim">
                                {{ $user->isAnonim() ? '••••••••' : ($mahasiswa->nim ?? '-') }}
                            </span>
                            <input type="text" name="nim" id="edit-nim"
                                class="edit-field" style="display:none"
                                value="{{ $mahasiswa->nim ?? '' }}">
                        </div>

                        <!-- Program Studi -->
                        <div class="info-row">
                            <span class="info-label" style="min-width:130px">Program Studi</span>
                            <span class="info-val" id="view-jurusan">{{ $mahasiswa->jurusan ?? '-' }}</span>
                            <select name="jurusan" id="edit-jurusan" class="edit-field" style="display:none">
                                <option value="D3 Teknologi Informasi"                {{ ($mahasiswa->jurusan ?? '') == 'D3 Teknologi Informasi' ? 'selected' : '' }}>D3 Teknologi Informasi</option>
                                <option value="D3 Teknologi Komputer"                 {{ ($mahasiswa->jurusan ?? '') == 'D3 Teknologi Komputer' ? 'selected' : '' }}>D3 Teknologi Komputer</option>
                                <option value="D4 Teknologi Rekayasa Perangkat Lunak" {{ ($mahasiswa->jurusan ?? '') == 'D4 Teknologi Rekayasa Perangkat Lunak' ? 'selected' : '' }}>D4 Teknologi Rekayasa Perangkat Lunak</option>
                                <option value="S1 Sistem Informasi"                   {{ ($mahasiswa->jurusan ?? '') == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                                <option value="S1 Manajemen Rekayasa"                 {{ ($mahasiswa->jurusan ?? '') == 'S1 Manajemen Rekayasa' ? 'selected' : '' }}>S1 Manajemen Rekayasa</option>
                                <option value="S1 Teknik Elektro"                     {{ ($mahasiswa->jurusan ?? '') == 'S1 Teknik Elektro' ? 'selected' : '' }}>S1 Teknik Elektro</option>
                                <option value="S1 Informatika"                        {{ ($mahasiswa->jurusan ?? '') == 'S1 Informatika' ? 'selected' : '' }}>S1 Informatika</option>
                                <option value="S1 Teknik Bioproses"                   {{ ($mahasiswa->jurusan ?? '') == 'S1 Teknik Bioproses' ? 'selected' : '' }}>S1 Teknik Bioproses</option>
                                <option value="S1 Bioteknologi"                       {{ ($mahasiswa->jurusan ?? '') == 'S1 Bioteknologi' ? 'selected' : '' }}>S1 Bioteknologi</option>
                                <option value="S1 Metalurgi"                          {{ ($mahasiswa->jurusan ?? '') == 'S1 Metalurgi' ? 'selected' : '' }}>S1 Metalurgi</option>
                            </select>
                        </div>

                        <!-- Angkatan -->
                        <div class="info-row">
                            <span class="info-label" style="min-width:130px">Angkatan</span>
                            <span class="info-val" id="view-angkatan">{{ $mahasiswa->angkatan ?? '-' }}</span>
                            <select name="angkatan" id="edit-angkatan" class="edit-field" style="display:none">
                                @foreach(['2021','2022','2023','2024','2025'] as $tahun)
                                    <option value="{{ $tahun }}" {{ ($mahasiswa->angkatan ?? '') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Email -->
                        <div class="info-row">
                            <span class="info-label" style="min-width:130px">Email</span>
                            <span class="info-val">{{ $user->email }}</span>
                        </div>

                        <!-- Status -->
                        <div class="info-row">
                            <span class="info-label" style="min-width:130px">Status</span>
                            <span class="info-val">
                                <span style="background:var(--accent-soft);color:var(--accent);border-radius:50px;padding:.15rem .6rem;font-size:.75rem;font-weight:700">
                                    Mahasiswa Aktif
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan — hanya muncul saat edit mode -->
                <div id="save-btn-wrap" style="display:none" class="gap-2">
                    <button type="submit" class="btn rounded-pill px-4"
                        style="background:var(--accent);color:white;font-size:.83rem;font-weight:600">
                        <i class="bi bi-check2 me-1"></i>Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let isEditMode = false;

// Pastikan saat halaman dimuat semua edit field tersembunyi
document.addEventListener('DOMContentLoaded', function() {
    // Paksa sembunyikan semua elemen edit saat halaman dimuat
    const hideIds = ['edit-nama','edit-nim','edit-jurusan','edit-angkatan','save-btn-wrap'];
    hideIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.setAttribute('style', 'display:none !important');
        }
    });

    // Pastikan view fields terlihat
    ['view-nama','view-nim','view-jurusan','view-angkatan'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = '';
    });

    isEditMode = false;
});

function toggleEdit() {
    isEditMode = !isEditMode;

    ['nama','nim','jurusan','angkatan'].forEach(f => {
        const viewEl = document.getElementById(`view-${f}`);
        const editEl = document.getElementById(`edit-${f}`);
        if (viewEl) viewEl.style.display = isEditMode ? 'none' : '';
        if (editEl) editEl.style.display = isEditMode ? 'block' : 'none';
    });

    const saveBtn = document.getElementById('save-btn-wrap');
    if (saveBtn) saveBtn.style.display = isEditMode ? 'flex' : 'none';

    document.getElementById('edit-icon').className      = isEditMode ? 'bi bi-x me-1' : 'bi bi-pencil-fill me-1';
    document.getElementById('edit-btn-text').textContent = isEditMode ? 'Batal Edit' : 'Edit Profil';
}

function previewDanSimpanFoto(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    // Preview dulu
    const reader = new FileReader();
    reader.onload = function(e) {
        const wrap = document.getElementById('avatar-preview-wrap');
        wrap.innerHTML = `
            <img src="${e.target.result}"
                style="width:80px;height:80px;border-radius:50%;object-fit:cover">
            <div id="avatar-overlay"
                style="position:absolute;inset:0;border-radius:50%;
                       background:rgba(0,0,0,.4);
                       display:flex;align-items:center;justify-content:center;
                       opacity:0;transition:.2s">
                <i class="bi bi-camera-fill" style="color:white;font-size:1.2rem"></i>
            </div>
        `;
    };
    reader.readAsDataURL(file);

    // Langsung upload via AJAX
    const formData = new FormData();
    formData.append('foto', file);
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'POST');
    // Kirim data lain yang required
    formData.append('nama', '{{ $user->nama }}');
    formData.append('nim', '{{ optional($mahasiswa)->nim ?? "" }}');
    formData.append('jurusan', '{{ optional($mahasiswa)->jurusan ?? "" }}');
    formData.append('angkatan', '{{ optional($mahasiswa)->angkatan ?? "" }}');

    fetch('{{ route("profil.update") }}', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (res.ok) {
            showToast('📸 Foto profil berhasil diperbarui!', true);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Gagal mengupload foto', false);
    });
}

async function toggleAnonim(el) {
    const isAnonim = el.checked;
    try {
        const res  = await fetch('{{ route("profil.anonim") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ anonim: isAnonim ? 1 : 0 })
        });
        const data = await res.json();
        if (data.success) {
            const namaHeader = document.getElementById('nama-header');
            if (namaHeader) namaHeader.textContent = isAnonim ? 'Mahasiswa Anonim' : '{{ $user->nama }}';

            const viewNama = document.getElementById('view-nama');
            if (viewNama) viewNama.textContent = isAnonim ? 'Mahasiswa Anonim' : '{{ $user->nama }}';

            const viewNim = document.getElementById('view-nim');
            if (viewNim) viewNim.textContent = isAnonim ? '••••••••' : '{{ optional($mahasiswa)->nim ?? "-" }}';

            const pdName = document.querySelector('.pd-name');
            if (pdName) pdName.textContent = isAnonim ? '🎭 Mahasiswa Anonim' : '{{ $user->nama }}';

            const pdNim = document.querySelector('.pd-nim');
            if (pdNim) {
                pdNim.textContent = isAnonim
                    ? '{{ optional(Auth::user()->mahasiswa)->jurusan ?? "" }} {{ optional(Auth::user()->mahasiswa)->angkatan ?? "" }}'
                    : '{{ optional(Auth::user()->mahasiswa)->nim ?? "" }} · {{ optional(Auth::user()->mahasiswa)->jurusan ?? "" }} {{ optional(Auth::user()->mahasiswa)->angkatan ?? "" }}';
            }
            showToast(isAnonim ? '🎭 Mode anonim aktif' : '👤 Mode anonim nonaktif', isAnonim);
        }
    } catch(err) {
        console.error(err);
        el.checked = !isAnonim;
    }
}

function showToast(msg, isAnonim) {
    const existing = document.getElementById('toast-notif');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.id = 'toast-notif';
    toast.style.cssText = `
        position:fixed;bottom:2rem;right:2rem;z-index:9999;
        background:${isAnonim ? 'var(--accent)' : 'var(--primary)'};
        color:white;padding:.75rem 1.2rem;border-radius:12px;
        font-size:.85rem;font-weight:600;
        box-shadow:0 8px 24px rgba(0,0,0,.2);
        animation:slideIn .3s ease;
    `;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<style>
@keyframes slideIn {
    from { opacity:0; transform:translateY(20px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>
@endpush