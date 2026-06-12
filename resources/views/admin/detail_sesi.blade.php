@extends('layouts.admin')

@section('page-title', 'Detail Sesi Konseling')

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

    .detail-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn-terima,
    .btn-tolak,
    .btn-laporan {
        border: none;
        border-radius: 10px;
        padding: .75rem 1.5rem;
        font-weight: 600;
        color: #fff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-terima {
        background: #065F46;
    }

    .btn-tolak {
        background: #ff2b2b;
    }

    .btn-laporan {
        background: #0f766e;
    }

    .btn-laporan:hover {
        background: #115e59;
        color: #fff;
    }

    .alasan-box {
        margin-top: .5rem;
        padding: 1rem 1.2rem;
        border-radius: 16px;
        background: #f8fafb;
        border: 1px solid #edf2ef;
        color: #334155;
        font-size: .88rem;
        line-height: 1.5;
    }
</style>
@endpush

@section('konten')
@php
    $mahasiswa = optional($jadwal)->mahasiswa;
    $user = optional($mahasiswa)->user;
    $status = strtolower($jadwal->status ?? 'menunggu');

    $isAnonim = filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

    $namaTampil = $isAnonim
        ? (
            method_exists($user, 'getAnonimDisplayName')
                ? trim($user->getAnonimDisplayName())
                : 'Anonim'
        )
        : ($user->nama ?? '-');

    $nimTampil = $isAnonim
        ? '-'
        : ($mahasiswa->nim ?? '-');

    $statusLabel = match ($status) {
        'menunggu' => 'Menunggu Konfirmasi',
        'disetujui', 'diterima' => 'Diterima',
        'berlangsung' => 'Sedang Berlangsung',
        'selesai' => 'Selesai',
        'ditolak', 'dibatalkan' => 'Ditolak',
        default => ucwords(str_replace('_', ' ', $jadwal->status ?? 'Menunggu')),
    };

    $topik = $jadwal->topik ?? null;
    $canStartNow = $status === 'disetujui' && $jadwal->isChatWindowOpen(null, 'Asia/Jakarta');
    $scheduledStartLabel = $jadwal->scheduledStartLabel('Asia/Jakarta');
    // Cek laporan aktual agar sesi selesai tetap bisa dibuatkan laporan.
    $sudahAdaLaporan = trim((string) ($jadwal->laporan ?? '')) !== ''
        || trim((string) ($jadwal->ringkasan_masalah ?? '')) !== ''
        || trim((string) ($jadwal->observasi_konselor ?? '')) !== ''
        || trim((string) optional($jadwal->sesiKonseling?->laporan)->isi_laporan) !== '';

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

    <div class="detail-section-title">Informasi Pribadi</div>
    <table class="detail-table">
        <tr>
            <td>NIM</td>
            <td>{{ $nimTampil }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>{{ $namaTampil }}</td>
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

    <div class="detail-section-title">Detail Jadwal</div>
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

    <div class="detail-section-title">Layanan</div>
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
            <td>{{ $statusLabel }}</td>
        </tr>
    </table>

    @if($status === 'menunggu')
        <div class="detail-actions">
            <form id="formTerimaSesi" action="{{ route('admin.sesi.terima', $jadwal->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn-terima">Terima</button>
        </form>

            <a href="{{ route('admin.sesi.tolak', $jadwal->id) }}" class="btn-tolak">
                Tolak
            </a>
        </div>

    @elseif($status === 'ditolak')
    <div class="detail-section-title">Alasan Penolakan</div>
    <div class="alasan-box">
        {{ $jadwal->alasan_penolakan ?? '-' }}
    </div>

    <div class="detail-actions">
        <a href="{{ url('/admin/sesi') }}" class="btn-laporan" style="min-width:220px;text-align:center;">
            Kembali
        </a>
    </div>

    @elseif($status === 'disetujui')
        <div class="detail-actions">
            @if($canStartNow)
                <a href="{{ route('admin.chat', ['jadwal' => $jadwal->id]) }}" class="btn-terima" style="min-width:220px;text-align:center;">
                    Mulai Sesi
                </a>
            @else
                <button type="button" class="btn-terima" style="min-width:220px;text-align:center;opacity:.6;cursor:not-allowed;" disabled>
                    Menunggu Jadwal Sesi
                </button>
            @endif
        </div>
    @elseif($status === 'berlangsung')
        <div class="detail-actions">
            <a href="{{ route('admin.chat', ['jadwal' => $jadwal->id]) }}" class="btn-terima" style="min-width:220px;text-align:center;">
                Lanjutkan Chat
            </a>

            <form action="{{ route('admin.sesi.selesai', $jadwal->id) }}" method="POST">
                @csrf
                <!-- Tandai selesai agar laporan bisa dibuat. -->
                <button type="submit" class="btn-laporan" style="min-width:220px;text-align:center;">
                    Tandai Selesai
                </button>
            </form>
        </div>
    @elseif($status === 'selesai')
        <div class="detail-actions">
            <a href="{{ url('/admin/sesi') }}" class="btn-laporan" style="min-width:220px;text-align:center;">
                Kembali
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formTerima = document.getElementById('formTerimaSesi');

    if (formTerima) {
        formTerima.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                html: `
                    <div style="display:flex;flex-direction:column;align-items:center;text-align:center;">
                        <div style="
                            width:64px;
                            height:64px;
                            border:4px solid #FDE68A;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            color:#FDE68A;
                            font-size:38px;
                            font-weight:800;
                            margin-bottom:18px;
                        ">?</div>

                        <h3 style="
                            color:#ffffff;
                            font-size:26px;
                            font-weight:800;
                            margin:0 0 14px;
                            white-space: nowrap;
                        ">
                            Konfirmasi Penerimaan
                        </h3>

                        <p style="
                            color:#ffffff;
                            font-size:14px;
                            line-height:1.5;
                            margin:0;
                            max-width:310px;
                        ">
                            Apakah kamu yakin ingin menerima sesi konseling ini?
                            <br>
                            Pastikan data jadwal sudah sesuai sebelum dikonfirmasi.
                        </p>
                    </div>
                `,
                background: '#065F46',
                showCancelButton: true,
                confirmButtonText: 'Terima',
                cancelButtonText: 'Kembali',
                reverseButtons: false,
                buttonsStyling: false,
                customClass: {
                    popup: 'campuscare-swal-popup',
                    actions: 'campuscare-swal-actions',
                    confirmButton: 'campuscare-swal-confirm',
                    cancelButton: 'campuscare-swal-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formTerima.submit();
                }
            });
        });
    }

    @if(session('terima_success'))
        Swal.fire({
            html: `
                <div style="display:flex;flex-direction:column;align-items:center;text-align:center;">
                    <div style="
                        width:64px;
                        height:64px;
                        border:4px solid #FDE68A;
                        border-radius:50%;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#FDE68A;
                        font-size:34px;
                        font-weight:800;
                        margin-bottom:18px;
                    ">✓</div>

                    <h3 style="
                        color:#ffffff;
                        font-size:22px;
                        font-weight:800;
                        margin:0 0 14px;
                    ">
                        Berhasil
                    </h3>

                    <p style="
                        color:#ffffff;
                        font-size:14px;
                        line-height:1.5;
                        margin:0;
                        max-width:310px;
                    ">
                        Jadwal berhasil diterima.
                    </p>
                </div>
            `,
            background: '#065F46',
                confirmButtonText: 'OK',
                showConfirmButton: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                buttonsStyling: false,
                customClass: {
                    popup: 'campuscare-swal-popup',
                    confirmButton: 'campuscare-swal-confirm'
                }
        }).then(() => {
            window.location.href = "{{ route('admin.sesi') }}";
        });
    @endif
});
</script>

<style>
.campuscare-swal-popup {
    border-radius: 18px !important;
    padding: 2rem 1.8rem !important;
    width: 430px !important;
}

.campuscare-swal-actions {
    gap: .75rem !important;
    margin-top: 1.4rem !important;
}

.campuscare-swal-confirm {
    background: #FDE68A !important;
    color: #065F46 !important;
    border: none !important;
    border-radius: 8px !important;
    padding: .65rem 1.25rem !important;
    font-weight: 800 !important;
    font-size: .9rem !important;
}

.campuscare-swal-cancel {
    background: transparent !important;
    color: #ffffff !important;
    border: 1.5px solid #ffffff !important;
    border-radius: 8px !important;
    padding: .65rem 1.25rem !important;
    font-weight: 700 !important;
    font-size: .9rem !important;
}
</style>
@endpush
