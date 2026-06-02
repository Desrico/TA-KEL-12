@extends('layouts.master')

@push('styles')
<style>
.detail-riwayat-page{
    background:#f3faf5;
    min-height:100vh;
    padding:32px 0 90px;
}

.detail-wrapper{
    width:min(1200px,92%);
    margin:auto;
}

.back-icon{
    width:36px;
    height:36px;
    border-radius:10px;
    background:#ffffff;
    color:#1f2937;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
    margin-bottom:24px;
    transition:.2s ease;
}

.back-icon:hover{
    transform:translateY(-2px);
    color:#1f2937;
}

.detail-hero{
    margin-bottom:32px;
}

.detail-hero h1{
    font-size:64px;
    line-height:1.04;
    font-weight:900;
    color:#202020;
    max-width:760px;
    margin-bottom:20px;
    letter-spacing:-2px;
}

.detail-hero h1 span{
    color:#55765f;
}

.detail-hero p{
    max-width:540px;
    font-size:18px;
    line-height:1.8;
    color:#6b7280;
    margin:0;
}

.detail-grid{
    display:grid;
    grid-template-columns:320px 1fr;
    gap:42px;
    align-items:start;
}

.side-card,
.main-card{
    background:#ffffff;
    border-radius:28px;
    border:1px solid #e5e7eb;
    box-shadow:0 12px 24px rgba(15,23,42,.08);
}

.side-card{
    padding:28px;
}

.counselor-profile{
    display:flex;
    align-items:center;
    gap:16px;
    margin-bottom:26px;
}

.counselor-profile img{
    width:72px;
    height:72px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #dff5e5;
}

.counselor-profile h5{
    margin:0;
    font-size:22px;
    font-weight:800;
    color:#202020;
}

.counselor-profile p{
    margin:4px 0 0;
    color:#65806c;
    font-size:15px;
}

.side-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    padding:16px 0;
    border-bottom:1px solid #edf1f3;
}

.side-row:last-child{
    border-bottom:none;
}

.side-row span{
    color:#607165;
    font-size:15px;
    display:flex;
    align-items:center;
    gap:8px;
}

.side-row i{
    color:#4e6f59;
    font-size:15px;
}

.side-row strong{
    color:#202020;
    font-size:15px;
    text-align:right;
}

.note-card{
    margin-top:24px;
    background:#c9f0d3;
    border-radius:26px;
    padding:28px;
    color:#066344;
}

.note-card h5{
    font-size:26px;
    font-weight:800;
    margin-bottom:12px;
}

.note-card p{
    margin:0;
    line-height:1.9;
    font-size:16px;
}

.main-card{
    padding:34px;
}

.main-card h3{
    font-size:42px;
    font-weight:900;
    color:#202020;
    margin-bottom:28px;
    letter-spacing:-1px;
}

.section-label{
    display:flex;
    align-items:center;
    gap:12px;
    margin-top:26px;
    margin-bottom:10px;
    font-size:28px;
    font-weight:800;
    color:#111827;
}

.section-label:first-of-type{
    margin-top:0;
}

.section-label i{
    font-size:22px;
    color:#111827;
}

.detail-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    padding:18px 0;
    border-bottom:1px solid #edf1f3;
}

.detail-row:last-child{
    border-bottom:none;
}

.detail-row span{
    color:#505050;
    font-size:18px;
}

.detail-row strong{
    color:#202020;
    font-size:18px;
    font-weight:700;
    text-align:right;
}

.summary-box{
    margin-top:18px;
    background:#f9fbfa;
    border:1px solid #e5e7eb;
    border-radius:18px;
    padding:22px;
}

.summary-box p{
    margin:0;
    color:#374151;
    line-height:1.8;
    font-size:16px;
}

.status-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:10px 18px;
    border-radius:999px;
    font-size:14px;
    font-weight:700;
}

.status-menunggu{
    background:#fff7d6;
    color:#a16207;
}

.status-disetujui{
    background:#dcfce7;
    color:#166534;
}

.status-ditolak{
    background:#fee2e2;
    color:#991b1b;
}

.status-selesai{
    background:#dbeafe;
    color:#1d4ed8;
}

.detail-action{
    margin-top:32px;
    display:flex;
    justify-content:center;
}

.detail-action .btn{
    min-width:260px;
    height:52px;
    border-radius:999px;
    font-weight:700;
    border:1px solid #d1d5db;
    background:#fff;
    color:#202020;
    transition:.2s ease;
}

.detail-action .btn:hover{
    background:#0f5f43;
    border-color:#0f5f43;
    color:#fff;
}

.btn-back-riwayat{
    min-width:260px;
    height:54px;
    border-radius:999px;
    background:#0f5f43;
    color:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    text-decoration:none;
    font-size:16px;
    font-weight:700;
    transition:.2s ease;
    box-shadow:0 10px 20px rgba(15,95,67,.18);
}

.btn-back-riwayat:hover{
    background:#0c4f38;
    color:#fff;
    transform:translateY(-2px);
}

.btn-back-riwayat i{
    font-size:15px;
}

.detail-action-buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.btn-detail {
    min-width: 260px;
    height: 56px;
    border-radius: 999px;
    font-size: 16px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-back {
    background: #047857;
    color: #ffffff;
    border: 1px solid #047857;
    box-shadow: 0 10px 24px rgba(4, 120, 87, 0.18);
}

.btn-back:hover {
    background: #065f46;
    color: #ffffff;
    transform: translateY(-1px);
}

.btn-reschedule {
    background: #ffffff;
    color: #064e3b;
    border: 2px solid #064e3b;
}

.btn-reschedule:hover {
    background: #ecfdf5;
    border-color: #10b981;
    color: #065f46;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .detail-action-buttons {
        flex-direction: column;
        gap: 12px;
    }

    .btn-detail {
        width: 100%;
        min-width: unset;
    }
}

@media (max-width:1200px){
    .detail-hero h1{
        font-size:54px;
    }
}

@media (max-width:992px){
    .detail-grid{
        grid-template-columns:1fr;
    }

    .detail-hero h1{
        font-size:44px;
    }

    .main-card h3{
        font-size:34px;
    }
}

@media (max-width:768px){
    .detail-riwayat-page{
        padding:24px 0 70px;
    }

    .detail-wrapper{
        width:94%;
    }

    .detail-hero h1{
        font-size:36px;
        line-height:1.1;
    }

    .detail-hero p{
        font-size:16px;
    }

    .main-card,
    .side-card,
    .note-card{
        border-radius:22px;
    }

    .main-card{
        padding:24px;
    }

    .section-label{
        font-size:22px;
    }

    .detail-row{
        flex-direction:column;
        align-items:flex-start;
        gap:8px;
    }

    .detail-row strong{
        text-align:left;
    }
}
</style>
@endpush

@section('konten')
@php
    use Carbon\Carbon;

    $mahasiswa = $jadwal->mahasiswa;
    $userMahasiswa = optional($mahasiswa)->user;
    $konselorUser = optional(optional($jadwal->konselor)->user);

    $tanggal = $jadwal->tanggal
        ? Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y')
        : '-';

    $waktu = $jadwal->waktu
        ? Carbon::parse($jadwal->waktu)->format('H.i')
        : '-';

    $metode = $jadwal->metode ?? $jadwal->jenis ?? 'Online';

    // Tampilkan tanggal lanjutan hanya jika admin memilih perlu sesi lanjutan.
    $tanggalLanjutLabel = $tanggalLanjut
        ? Carbon::parse($tanggalLanjut)->translatedFormat('d F Y')
        : '-';
@endphp

<section class="detail-riwayat-page">
    <div class="detail-wrapper">

        <a href="{{ route('riwayat') }}" class="back-icon">
            <i class="bi bi-arrow-left"></i>
        </a>

        <div class="detail-hero">
            <h1>
                Detail <span>Riwayatmu</span><br>
                Bersama Konselor.
            </h1>
            <p>
                Lihat kembali detail jadwal konseling yang pernah Anda ajukan.
                Informasi ini tersimpan dengan aman di sistem Campus Care.
            </p>
        </div>

        <div class="detail-grid">

            <aside>
                <div class="side-card">
                    <div class="counselor-profile">
                        <img src="{{ asset('assets/img/avatar-konselor.png') }}" alt="Konselor">
                        <div>
                            <h5>{{ $konselorUser->nama ?? 'Konselor' }}</h5>
                            <p>Konselor Utama</p>
                        </div>
                    </div>

                    <div class="side-row">
                        <span><i class="bi bi-stopwatch"></i> Durasi</span>
                        <strong>{{ $jadwal->durasi ?? '60 Menit' }}</strong>
                    </div>

                    <div class="side-row">
                        <span><i class="bi bi-camera-video"></i> Media</span>
                        <strong>{{ $metode }}</strong>
                    </div>

                    <div class="side-row">
                        <span><i class="bi bi-geo-alt-fill"></i> Lokasi</span>
                        <strong>{{ $jadwal->lokasi ?? '-' }}</strong>
                    </div>
                </div>

                <div class="note-card">
                    <h5>Catatan Sesi</h5>
                    <p>
                        Informasi ini merupakan riwayat konseling Anda.
                        Jika membutuhkan dukungan lanjutan, Anda dapat menjadwalkan sesi baru.
                    </p>
                </div>
            </aside>

            <main class="main-card">
                <h3>Detail Riwayat Konseling</h3>

                <div class="section-label">
                    <i class="bi bi-person-fill"></i>
                    <span>Informasi Pribadi</span>
                </div>

                <div class="detail-row">
                    <span>NIM</span>
                    <strong>{{ $mahasiswa->nim ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Nama</span>
                    <strong>{{ $userMahasiswa->nama ?? 'Mahasiswa' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Program Studi</span>
                    <strong>{{ $mahasiswa->jurusan ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Angkatan</span>
                    <strong>{{ $mahasiswa->angkatan ?? '-' }}</strong>
                </div>

                <div class="section-label mt-4">
                    <i class="bi bi-clock"></i>
                    <span>Detail Jadwal</span>
                </div>

                <div class="detail-row">
                    <span>Tanggal</span>
                    <strong>{{ $tanggal }}</strong>
                </div>

                <div class="detail-row">
                    <span>Waktu</span>
                    <strong>{{ $waktu }}</strong>
                </div>

                <div class="section-label mt-4">
                    <i class="bi bi-headset"></i>
                    <span>Layanan</span>
                </div>

                <div class="detail-row">
                    <span>Layanan Konseling</span>
                    <strong>{{ ucfirst($metode) }}</strong>
                </div>

                <div class="detail-row">
                    <span>Topik</span>
                    <strong>{{ $jadwal->topik ?? '-' }}</strong>
                </div>

                <div class="detail-row">
                    <span>Status</span>
                    <strong>{{ ucwords(str_replace('_', ' ', $jadwal->status ?? '-')) }}</strong>
                </div>

                <div class="section-label mt-4">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Tindak Lanjut</span>
                </div>

                <div class="detail-row">
                    <span>Status Tindak Lanjut</span>
                    <strong>{{ $tindakLanjut }}</strong>
                </div>

                @if($perluSesiLanjutan)
                    <div class="detail-row">
                        <span>Tanggal Sesi Lanjutan</span>
                        <strong>{{ $tanggalLanjutLabel }}</strong>
                    </div>
                @endif

                <div class="detail-action-buttons">
    <a href="{{ route('riwayat') }}" class="btn-detail btn-back">
        Kembali ke Riwayat
    </a>

    @if ($jadwal->status === 'perlu_penjadwalan_ulang')
        <a href="{{ route('konseling.jadwal_ulang.edit', $jadwal->id) }}" class="btn-detail btn-reschedule">
            Jadwalkan Ulang
        </a>
    @endif
</div>
            </main>

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
