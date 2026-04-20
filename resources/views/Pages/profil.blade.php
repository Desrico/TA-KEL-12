@extends('layouts.master')

@push('styles')
<style>
  .profil-page {
    background: linear-gradient(180deg, var(--navbar-bg) 0%, #ffffff 28%);
    padding: 2.5rem 0 4rem;
  }

  .profil-page-head {
    margin-bottom: 1.5rem;
  }

  .profil-breadcrumb {
    font-size: .82rem;
    color: var(--text-light);
    margin-bottom: .7rem;
  }

  .profil-breadcrumb a {
    color: var(--text-light);
    text-decoration: none;
  }

  .profil-breadcrumb a:hover {
    color: var(--primary);
  }

  .profil-page-title {
    font-family: 'Fraunces', serif;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    color: var(--text-dark);
    margin-bottom: .45rem;
  }

  .profil-page-desc {
    color: var(--text-mid);
    font-size: .95rem;
    line-height: 1.8;
    margin: 0;
    max-width: 700px;
  }

  .profil-layout {
    row-gap: 1.5rem;
  }

  .profil-side-card,
  .profil-main-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 22px;
    box-shadow: var(--shadow-sm);
  }

  .profil-side-card {
    padding: 1.25rem;
  }

  .profil-main-card {
    padding: 1.5rem;
  }

  .profil-user-box {
    text-align: center;
    padding-bottom: 1.2rem;
    border-bottom: 1px solid #eef5f1;
    margin-bottom: 1.2rem;
  }

  .profil-avatar-wrap {
    position: relative;
    width: 96px;
    height: 96px;
    margin: 0 auto 1rem;
  }

  .profil-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    overflow: hidden;
    background: #f1f5f9;
    border: 3px solid #fff;
    box-shadow: var(--shadow-sm);
    cursor: pointer;
    position: relative;
  }

  .profil-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .profil-avatar-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-soft);
    color: var(--primary);
    font-size: 2rem;
  }

  .profil-avatar-edit {
    position: absolute;
    right: 2px;
    bottom: 2px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    border: 2px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .78rem;
    cursor: pointer;
    box-shadow: var(--shadow-sm);
  }

  .profil-user-name {
    font-weight: 700;
    font-size: 1.05rem;
    color: var(--text-dark);
    margin-bottom: .2rem;
  }

  .profil-user-meta {
    color: var(--text-light);
    font-size: .84rem;
    line-height: 1.6;
    margin-bottom: .8rem;
  }

  .profil-status-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 999px;
    padding: .3rem .8rem;
    font-size: .74rem;
    font-weight: 700;
  }

  .profil-status-badge .dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--primary-500);
  }

  .profil-stat-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: .8rem;
    margin-bottom: 1rem;
  }

  .profil-stat-box {
    background: #f8fffb;
    border: 1px solid #e6f2ec;
    border-radius: 16px;
    padding: 1rem;
    text-align: center;
  }

  .profil-stat-number {
    font-family: 'Fraunces', serif;
    font-size: 1.8rem;
    line-height: 1;
    color: var(--primary);
    margin-bottom: .25rem;
  }

  .profil-stat-label {
    font-size: .76rem;
    color: var(--text-light);
  }

  .profil-link-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    border: 1px solid var(--border);
    background: #fff;
    color: var(--primary);
    border-radius: 12px;
    padding: .75rem 1rem;
    font-size: .86rem;
    font-weight: 700;
    text-decoration: none;
    transition: all .2s ease;
  }

  .profil-link-btn:hover {
    background: #f8fffb;
    color: var(--primary);
  }

  .profil-anon-card {
    margin-top: 1rem;
    background: #f8fffb;
    border: 1px solid #e6f2ec;
    border-radius: 18px;
    padding: 1rem;
  }

  .profil-anon-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .7rem;
  }

  .profil-anon-title {
    font-weight: 700;
    font-size: .92rem;
    color: var(--text-dark);
    margin-bottom: .15rem;
  }

  .profil-anon-desc {
    font-size: .8rem;
    color: var(--text-light);
    line-height: 1.6;
    margin: 0;
  }

  .toggle-switch {
    position: relative;
    width: 48px;
    height: 26px;
    flex-shrink: 0;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-slider {
    position: absolute;
    inset: 0;
    background: #d1d5db;
    border-radius: 999px;
    cursor: pointer;
    transition: .25s ease;
  }

  .toggle-slider:before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    left: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: .25s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,.12);
  }

  .toggle-switch input:checked + .toggle-slider {
    background: var(--primary);
  }

  .toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(22px);
  }

  .profil-main-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
  }

  .profil-main-title {
    font-weight: 700;
    font-size: 1.05rem;
    color: var(--text-dark);
    margin: 0;
  }

  .profil-main-sub {
    font-size: .84rem;
    color: var(--text-light);
    margin: .2rem 0 0;
  }

  .profil-edit-btn {
    border: 1px solid var(--primary);
    background: var(--primary);
    color: white;
    border-radius: 12px;
    padding: .7rem 1rem;
    font-size: .84rem;
    font-weight: 700;
    transition: all .2s ease;
  }

  .profil-edit-btn:hover {
    background: var(--primary-700);
    border-color: var(--primary-700);
    color: white;
  }

  .profil-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem 1.1rem;
  }

  .profil-field {
    display: flex;
    flex-direction: column;
    gap: .45rem;
  }

  .profil-field.full {
    grid-column: 1 / -1;
  }

  .profil-label {
    font-size: .82rem;
    font-weight: 600;
    color: var(--text-dark);
  }

  .profil-value-box {
    min-height: 46px;
    border: 1px solid #d8e8df;
    border-radius: 12px;
    background: #fff;
    padding: .72rem .9rem;
    font-size: .9rem;
    color: var(--text-mid);
    display: flex;
    align-items: center;
  }

  .edit-field {
    min-height: 46px;
    border: 1px solid #d8e8df;
    border-radius: 12px;
    background: #fff;
    padding: .72rem .9rem;
    font-size: .9rem;
    width: 100%;
    transition: all .2s ease;
  }

  .edit-field:focus {
    border-color: #9ccdb5;
    box-shadow: 0 0 0 3px rgba(6, 78, 59, 0.08);
    outline: none;
  }

  .profil-status-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 999px;
    padding: .32rem .8rem;
    font-size: .75rem;
    font-weight: 700;
  }

  .profil-actions {
    margin-top: 1.2rem;
    display: none;
    gap: .75rem;
    flex-wrap: wrap;
  }

  .profil-save-btn {
    border: 1px solid var(--primary);
    background: var(--primary);
    color: white;
    border-radius: 12px;
    padding: .75rem 1.1rem;
    font-size: .84rem;
    font-weight: 700;
  }

  .profil-save-btn:hover {
    background: var(--primary-700);
    border-color: var(--primary-700);
    color: white;
  }

  .profil-note-box {
    margin-top: 1rem;
    background: #f8fffb;
    border: 1px solid #e6f2ec;
    border-radius: 16px;
    padding: 1rem;
    color: var(--text-mid);
    font-size: .84rem;
    line-height: 1.7;
  }

  @media (max-width: 991.98px) {
    .profil-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('konten')

@php
    $mahasiswa = $user->mahasiswa;
    $profil = $user->profil;
@endphp

<section class="profil-page">
  <div class="container">
    @if(session('success'))
      <div class="alert alert-success rounded-3 mb-4">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      </div>
    @endif

    <div class="row profil-layout">
      <div class="col-lg-4">
        <div class="profil-side-card">
          <div class="profil-user-box">
            <div class="profil-avatar-wrap">
                <div class="profil-avatar" id="avatar-preview-wrap">
                    @if(optional($profil)->foto)
                    <img src="{{ Storage::url($profil->foto) }}" id="avatar-img" alt="Foto Profil">
                    @else
                    <div class="profil-avatar-fallback">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    @endif

                    <div class="profil-avatar-overlay" id="avatar-overlay">
                    <i class="bi bi-camera"></i>
                    <span>Ganti Foto</span>
                    </div>
                </div>

                <input
                    type="file"
                    id="foto-input"
                    name="foto"
                    accept="image/jpg,image/jpeg,image/png"
                    style="display:none"
                    onchange="previewFoto(this)"
                >
                </div>

            <div class="profil-user-name" id="nama-header">
              {{ $user->isAnonim() ? 'Mahasiswa Anonim' : $user->nama }}
            </div>

            <div class="profil-user-meta">
              {{ $mahasiswa->nim ?? '-' }}<br>
              {{ $mahasiswa->jurusan ?? '-' }} · Angkatan {{ $mahasiswa->angkatan ?? '-' }}
            </div>
          </div>

          <div class="profil-stat-grid">
            <div class="profil-stat-box">
              <div class="profil-stat-number">{{ $totalKonseling }}</div>
              <div class="profil-stat-label">Total Konseling</div>
            </div>
            <div class="profil-stat-box">
              <div class="profil-stat-number">{{ $sesiBerlangsung }}</div>
              <div class="profil-stat-label">Sesi Disetujui</div>
            </div>
          </div>

          <a href="{{ route('riwayat') }}" class="profil-link-btn">
            <i class="bi bi-clock-history me-2"></i>Lihat Riwayat
          </a>

          <div class="profil-anon-card">
            <div class="profil-anon-top">
              <div>
                <div class="profil-anon-title">Mode Anonim</div>
                <p class="profil-anon-desc">
                  Sembunyikan identitas dari konselor pada kondisi tertentu.
                </p>
              </div>

              <label class="toggle-switch">
                <input type="checkbox" {{ optional($profil)->anonim ? 'checked' : '' }} onchange="toggleAnonim(this)">
                <span class="toggle-slider"></span>
              </label>
            </div>

            <div class="profil-note-box">
              Saat mode anonim aktif, identitas mahasiswa tidak ditampilkan secara penuh pada alur layanan tertentu sehingga pengguna dapat merasa lebih nyaman saat memulai proses konseling.
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <form method="POST" action="{{ route('profil.update') }}" id="profil-form" enctype="multipart/form-data">
            @csrf

          <div class="profil-main-card">
            <div class="profil-main-head">
              <div>
                <h2 class="profil-main-title">Informasi Profil</h2>
                <p class="profil-main-sub">Data ini digunakan untuk kebutuhan akun dan layanan konseling.</p>
              </div>

              <button type="button" class="profil-edit-btn" onclick="toggleEdit()">
                <i class="bi bi-pencil-fill me-1" id="edit-icon"></i>
                <span id="edit-btn-text">Edit Profil</span>
                </button>
            </div>

            <div class="profil-grid">
              <div class="profil-field full">
                <label class="profil-label">Nama Lengkap</label>
                <div class="profil-value-box" id="view-nama">
                  {{ $user->isAnonim() ? 'Mahasiswa Anonim' : $user->nama }}
                </div>
                <input type="text" name="nama" id="edit-nama" class="edit-field" style="display:none" value="{{ $user->nama }}">
              </div>

              <div class="profil-field">
                <label class="profil-label">NIM</label>
                <div class="profil-value-box" id="view-nim">
                  {{ $user->isAnonim() ? '••••••••' : ($mahasiswa->nim ?? '-') }}
                </div>
                <input type="text" name="nim" id="edit-nim" class="edit-field" style="display:none" value="{{ $mahasiswa->nim ?? '' }}">
              </div>

              <div class="profil-field">
                <label class="profil-label">Angkatan</label>
                <div class="profil-value-box" id="view-angkatan">{{ $mahasiswa->angkatan ?? '-' }}</div>
                <select name="angkatan" id="edit-angkatan" class="edit-field" style="display:none">
                  @foreach(['2021','2022','2023','2024','2025'] as $tahun)
                    <option value="{{ $tahun }}" {{ ($mahasiswa->angkatan ?? '') == $tahun ? 'selected' : '' }}>
                      {{ $tahun }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="profil-field full">
                <label class="profil-label">Program Studi</label>
                <div class="profil-value-box" id="view-jurusan">{{ $mahasiswa->jurusan ?? '-' }}</div>
                <select name="jurusan" id="edit-jurusan" class="edit-field" style="display:none">
                  <option value="D3 Teknologi Informasi" {{ ($mahasiswa->jurusan ?? '') == 'D3 Teknologi Informasi' ? 'selected' : '' }}>D3 Teknologi Informasi</option>
                  <option value="D3 Teknologi Komputer" {{ ($mahasiswa->jurusan ?? '') == 'D3 Teknologi Komputer' ? 'selected' : '' }}>D3 Teknologi Komputer</option>
                  <option value="D4 Teknologi Rekayasa Perangkat Lunak" {{ ($mahasiswa->jurusan ?? '') == 'D4 Teknologi Rekayasa Perangkat Lunak' ? 'selected' : '' }}>D4 Teknologi Rekayasa Perangkat Lunak</option>
                  <option value="S1 Sistem Informasi" {{ ($mahasiswa->jurusan ?? '') == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                  <option value="S1 Manajemen Rekayasa" {{ ($mahasiswa->jurusan ?? '') == 'S1 Manajemen Rekayasa' ? 'selected' : '' }}>S1 Manajemen Rekayasa</option>
                  <option value="S1 Teknik Elektro" {{ ($mahasiswa->jurusan ?? '') == 'S1 Teknik Elektro' ? 'selected' : '' }}>S1 Teknik Elektro</option>
                  <option value="S1 Informatika" {{ ($mahasiswa->jurusan ?? '') == 'S1 Informatika' ? 'selected' : '' }}>S1 Informatika</option>
                  <option value="S1 Teknik Bioproses" {{ ($mahasiswa->jurusan ?? '') == 'S1 Teknik Bioproses' ? 'selected' : '' }}>S1 Teknik Bioproses</option>
                  <option value="S1 Bioteknologi" {{ ($mahasiswa->jurusan ?? '') == 'S1 Bioteknologi' ? 'selected' : '' }}>S1 Bioteknologi</option>
                  <option value="S1 Metalurgi" {{ ($mahasiswa->jurusan ?? '') == 'S1 Metalurgi' ? 'selected' : '' }}>S1 Metalurgi</option>
                </select>
              </div>

              <div class="profil-field full">
                <label class="profil-label">Email</label>
                <div class="profil-value-box">{{ $user->email }}</div>
              </div>

              <div class="profil-field full">
                <label class="profil-label">Status</label>
                <div class="profil-value-box">
                  <span class="profil-status-pill">
                    <i class="bi bi-check-circle"></i> Mahasiswa Aktif
                  </span>
                </div>
              </div>
            </div>

            <div class="profil-actions" id="save-btn-wrap">
              <button type="submit" class="profil-save-btn">
                <i class="bi bi-check2 me-1"></i>Simpan Perubahan
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
let isEditMode = false;

document.addEventListener('DOMContentLoaded', function () {
    const hideIds = ['edit-nama', 'edit-nim', 'edit-jurusan', 'edit-angkatan'];
    hideIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    const saveBtn = document.getElementById('save-btn-wrap');
    if (saveBtn) saveBtn.style.display = 'none';

    ['view-nama', 'view-nim', 'view-jurusan', 'view-angkatan'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'flex';
    });

    setAvatarEditState(false);
    isEditMode = false;
});

function toggleEdit() {
    isEditMode = !isEditMode;

    ['nama', 'nim', 'jurusan', 'angkatan'].forEach(field => {
        const viewEl = document.getElementById(`view-${field}`);
        const editEl = document.getElementById(`edit-${field}`);

        if (viewEl) viewEl.style.display = isEditMode ? 'none' : 'flex';
        if (editEl) editEl.style.display = isEditMode ? 'block' : 'none';
    });

    const saveBtn = document.getElementById('save-btn-wrap');
    if (saveBtn) saveBtn.style.display = isEditMode ? 'flex' : 'none';

    document.getElementById('edit-icon').className = isEditMode ? 'bi bi-x-lg me-1' : 'bi bi-pencil-fill me-1';
    document.getElementById('edit-btn-text').textContent = isEditMode ? 'Batal Edit' : 'Edit Profil';

    setAvatarEditState(isEditMode);
}

function setAvatarEditState(editable) {
    const avatar = document.getElementById('avatar-preview-wrap');
    const fotoInput = document.getElementById('foto-input');

    if (!avatar || !fotoInput) return;

    if (editable) {
        avatar.classList.add('editable');
        avatar.onclick = function () {
            fotoInput.click();
        };
    } else {
        avatar.classList.remove('editable');
        avatar.onclick = null;
    }
}

function previewFoto(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const wrap = document.getElementById('avatar-preview-wrap');
        wrap.innerHTML = `
            <img src="${e.target.result}" alt="Preview Foto Profil">
            <div class="profil-avatar-overlay" id="avatar-overlay">
              <i class="bi bi-camera"></i>
              <span>Ganti Foto</span>
            </div>
        `;

        if (isEditMode) {
            wrap.classList.add('editable');
            wrap.onclick = function () {
                document.getElementById('foto-input').click();
            };
        }
    };

    reader.readAsDataURL(file);
}
</script>
@endpush