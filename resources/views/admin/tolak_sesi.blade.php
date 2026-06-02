@extends('layouts.admin')

@section('page-title', 'Alasan Penolakan')

@push('styles')
<style>
    .detail-card {
        background: #ffffff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        padding: 1.5rem 1.6rem;
        max-width: 900px;
    }

    .detail-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1rem;
    }

    .detail-section-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #065f46;
        margin: 1.2rem 0 0.6rem;
    }

    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-table td {
        padding: 0.75rem 0;
        border-bottom: 1px solid #edf2ef;
        font-size: 0.88rem;
    }

    .detail-table td:first-child {
        width: 220px;
        color: #64748b;
    }

    .detail-table td:last-child {
        color: #0f172a;
        font-weight: 500;
        text-align: right;
    }

    /* ===============================
       TEXTAREA PENOLAKAN
    =============================== */
    .rejection-box {
        margin-top: 16px;
    }

    .rejection-textarea,
    .alasan-textarea {
        width: 100%;
        min-height: 170px;
        padding: 18px 20px;
        border: 1px solid #dfe5ec;
        border-radius: 16px;
        font-size: 15px;
        color: #334155;
        background: #ffffff;
        resize: vertical;
        outline: none;
        transition: all 0.2s ease;
    }

    .rejection-textarea::placeholder,
    .alasan-textarea::placeholder {
        color: #9ca3af;
    }

    .rejection-textarea:focus,
    .alasan-textarea:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
    }

    /* ===============================
       BUTTON AKSI UTAMA
       Kirim Penolakan + Kembali
    =============================== */
    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .btn-action {
        width: 280px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: none;
        font-size: 16px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-sizing: border-box;
        line-height: 1;
    }

    .btn-kirim-penolakan {
        background: #ef4444;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(239, 68, 68, 0.22);
    }

    .btn-kirim-penolakan:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-kembali-sesi {
        background: #057a55;
        color: #ffffff;
        box-shadow: 0 10px 24px rgba(5, 122, 85, 0.22);
    }

    .btn-kembali-sesi:hover {
        background: #046c4e;
        color: #ffffff;
        transform: translateY(-1px);
    }

    .btn-action:active {
        transform: translateY(0);
    }

    /* ===============================
       BUTTON LAMA / OPSIONAL
       Untuk halaman lain jika masih dipakai
    =============================== */
    .detail-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn-kirim,
    .btn-batal {
        border: none;
        border-radius: 10px;
        padding: 0.72rem 1.5rem;
        font-weight: 700;
        color: #ffffff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 105px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-kirim {
        background: #0d8df2;
    }

    .btn-kirim:hover {
        background: #0878cf;
        color: #ffffff;
    }

    .btn-batal {
        background: #ff2b2b;
    }

    .btn-batal:hover {
        background: #dc2626;
        color: #ffffff;
    }

    /* ===============================
       MODAL KONFIRMASI PENOLAKAN
    =============================== */
    .confirm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.35);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999999;
    }

    .confirm-overlay.show {
        display: flex;
    }

    .confirm-box {
        width: 380px;
        max-width: 90%;
        background: #066847;
        color: #ffffff;
        border-radius: 18px;
        padding: 1.8rem 1.5rem;
        text-align: center;
        animation: popFade 0.28s ease both;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
    }

    .confirm-icon {
        width: 60px;
        height: 60px;
        border: 4px solid #ff4d4d;
        color: #ff4d4d;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 2rem;
        font-weight: 800;
        margin: 0 auto 1rem;
    }

    .confirm-box h3 {
        font-size: 1.15rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
    }

    .confirm-box p {
        font-size: 0.82rem;
        line-height: 1.5;
        margin-bottom: 1.3rem;
    }

    .confirm-actions {
        display: flex;
        justify-content: center;
        gap: 0.8rem;
        flex-wrap: wrap;
    }

    .btn-confirm-danger {
        border: 0;
        background: #ff4d4d;
        color: #ffffff;
        font-weight: 800;
        font-size: 0.78rem;
        border-radius: 8px;
        padding: 0.55rem 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-confirm-danger:hover {
        background: #dc2626;
    }

    .btn-cancel {
        border: 1px solid #ffffff;
        background: transparent;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.78rem;
        border-radius: 8px;
        padding: 0.55rem 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    @keyframes popFade {
        from {
            opacity: 0;
            transform: translateY(18px) scale(0.94);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* ===============================
       RESPONSIVE
    =============================== */
    @media (max-width: 768px) {
        .detail-card {
            padding: 1.2rem;
            border-radius: 18px;
        }

        .detail-table td:first-child {
            width: 150px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 14px;
        }

        .btn-action {
            width: 100%;
            max-width: 320px;
        }
    }

    @media (max-width: 480px) {
        .detail-table td {
            display: block;
            width: 100%;
            padding: 0.45rem 0;
            border-bottom: none;
        }

        .detail-table td:first-child {
            width: 100%;
            color: #64748b;
        }

        .detail-table td:last-child {
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid #edf2ef;
            padding-bottom: 0.75rem;
        }

        .confirm-actions {
            flex-direction: column;
        }

        .btn-confirm-danger,
        .btn-cancel {
            width: 100%;
        }
    }
</style>
@endpush

@section('konten')
@php
    $mahasiswa = optional($jadwal)->mahasiswa;
    $user = optional($mahasiswa)->user;

    $topik = $jadwal->topik ?? null;

    if (!$topik && !empty($jadwal->catatan)) {
        if (preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
            $topik = trim($match[1]);
        } else {
            $topik = $jadwal->catatan;
        }
    }
@endphp

<div class="detail-card">
    <div class="detail-title">Detail Penjadwalan</div>

    <div class="detail-section-title">
        <i class="ti ti-user"></i> Informasi Pribadi
    </div>
    <table class="detail-table">
        <tr>
            <td>NIM</td>
            <td>{{ $mahasiswa->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>{{ $user->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>{{ $mahasiswa->jurusan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Angkatan</td>
            <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
        </tr>
    </table>

    <div class="detail-section-title">
        <i class="ti ti-clock"></i> Detail Jadwal
    </div>
    <table class="detail-table">
        <tr>
            <td>Tanggal</td>
            <td>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y') }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>{{ substr($jadwal->waktu, 0, 5) }}</td>
        </tr>
    </table>

    <div class="detail-section-title">
        <i class="ti ti-headphones"></i> Layanan
    </div>
    <table class="detail-table">
        <tr>
            <td>Layanan Konseling</td>
            <td>{{ ucfirst($jadwal->jenis ?? 'Online') }}</td>
        </tr>
        <tr>
            <td>Topik</td>
            <td>{{ $topik ?? '-' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>Menunggu Konfirmasi</td>
        </tr>
    </table>

    <form id="formTolakSesi" action="{{ route('admin.sesi.tolak.kirim', $jadwal->id) }}" method="POST">
    @csrf

    <div class="rejection-box">
        <textarea 
            name="alasan_penolakan"
            class="rejection-textarea"
            rows="6"
            required
            placeholder="Tuliskan alasan penolakan...">{{ old('alasan_penolakan') }}</textarea>
    </div>

    <div class="action-buttons">
        <button type="button" class="btn-action btn-kirim-penolakan" onclick="openTolakModal()">
            Kirim Penolakan
        </button>

        <a href="{{ route('admin.sesi') }}" class="btn-action btn-kembali-sesi">
            Kembali ke Sesi Konseling
        </a>
    </div>
</form>

<div class="confirm-overlay" id="tolakModal">
    <div class="confirm-box">
        <div class="confirm-icon">?</div>

        <h3>Penolakan Penjadwalan</h3>
        <p>
            Apakah Anda yakin ingin menolak permintaan penjadwalan ini?<br>
            Mahasiswa akan diminta untuk memilih jadwal lain.
        </p>

        <div class="confirm-actions">
            <button type="button" class="btn-confirm-danger" onclick="submitTolakSesi()">
                Konfirmasi Penolakan
            </button>
            <button type="button" class="btn-cancel" onclick="closeTolakModal()">
                Batalkan
            </button>
        </div>
    </div>
</div>


@push('scripts')
<script>
function openTolakModal() {
    const alasan = document.querySelector('textarea[name="alasan_penolakan"]').value.trim();

    if (!alasan) {
        alert('Isi alasan penolakan terlebih dahulu.');
        return;
    }

    document.getElementById('tolakModal').classList.add('show');
}

function closeTolakModal() {
    document.getElementById('tolakModal').classList.remove('show');
}

function submitTolakSesi() {
    document.getElementById('formTolakSesi').submit();
}
</script>
@endpush
@endsection