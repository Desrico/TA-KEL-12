@extends('layouts.admin')

@section('page-title', 'Alasan Penolakan')

@push('styles')
<style>
    .detail-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
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
        font-size: .92rem;
        font-weight: 700;
        color: #065F46;
        margin: 1.2rem 0 .6rem;
    }

    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-table td {
        padding: .75rem 0;
        border-bottom: 1px solid #edf2ef;
        font-size: .88rem;
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

    .alasan-textarea {
        width: 100%;
        min-height: 145px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 1rem;
        resize: vertical;
        outline: none;
        color: #334155;
        font-size: .88rem;
    }

    .alasan-textarea:focus {
        border-color: #0f766e;
        box-shadow: 0 0 0 3px rgba(15, 118, 110, .08);
    }

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
        padding: .72rem 1.5rem;
        font-weight: 700;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 105px;
    }

    .btn-kirim {
        background: #0d8df2;
    }

    .btn-batal {
        background: #ff2b2b;
    }

.btn-kembali-sesi {
    min-width: 260px;
    height: 52px;
    border-radius: 999px;
    background: #0b6b47;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 16px;
    font-weight: 700;
}

.btn-kembali-sesi:hover {
    background: #09573a;
    color: #fff;
}

    .confirm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .35);
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
        color: #fff;
        border-radius: 16px;
        padding: 1.8rem 1.5rem;
        text-align: center;
        animation: popFade .28s ease both;
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
        margin-bottom: .75rem;
    }

    .confirm-box p {
        font-size: .82rem;
        line-height: 1.5;
        margin-bottom: 1.3rem;
    }

    .confirm-actions {
        display: flex;
        justify-content: center;
        gap: .8rem;
    }

    .btn-confirm-danger {
        border: 0;
        background: #ff4d4d;
        color: #fff;
        font-weight: 800;
        font-size: .78rem;
        border-radius: 7px;
        padding: .5rem .9rem;
    }

    .btn-cancel {
        border: 1px solid #fff;
        background: transparent;
        color: #fff;
        font-weight: 700;
        font-size: .78rem;
        border-radius: 7px;
        padding: .5rem .9rem;
    }

    @keyframes popFade {
        from {
            opacity: 0;
            transform: translateY(18px) scale(.94);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
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

        <textarea name="alasan_penolakan"
                rows="6"
                required
                style="width:100%;border:1px solid #e5e7eb;border-radius:14px;padding:1rem;"
                placeholder="Tuliskan alasan penolakan...">{{ old('alasan_penolakan') }}</textarea>

        <div class="detail-actions">
            <button type="button" class="btn-tolak" onclick="openTolakModal()">
                Kirim Penolakan
            </button>
            <a href="{{ route('admin.sesi.detail', $jadwal->id) }}" class="btn-terima">
                Batalkan
            </a>
        </div>
    </form>
<div class="mt-4 d-flex justify-content-center">
    <a href="{{ route('admin.sesi') }}" class="btn-kembali-sesi">
        Kembali ke Sesi Konseling
    </a>
</div>
</div>


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