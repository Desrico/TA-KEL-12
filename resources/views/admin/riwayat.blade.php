@extends('layouts.admin')

@section('page-title', 'Riwayat Konseling')

@push('styles')
<style>
    .sesi-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
        overflow: hidden;
        max-width: 1100px;
        margin: 0 auto;
        width: calc(100% - 48px);
    }

    .sesi-head {
        padding: 1.5rem 1.7rem 1rem;
        border-bottom: 1px solid #edf2ef;
    }

    .sesi-head h6 {
        margin: 0 0 .3rem 0;
        font-weight: 700;
        color: var(--admin-primary);
        font-size: 1.25rem;
        letter-spacing: -0.3px;
    }

    .sesi-head p {
        margin: 0;
        color: var(--admin-text-light);
        font-size: .85rem;
    }

    .sesi-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1.2rem 1.5rem 1.2rem;
        flex-wrap: wrap;
        border-bottom: none;
    }

    .sesi-search-wrap {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .sesi-search {
        position: relative;
    }

    .sesi-search input {
        width: 240px;
        border: 1px solid #d9e8df;
        border-radius: 12px;
        padding: .7rem 1rem .7rem 2.5rem;
        font-size: .86rem;
        outline: none;
    }

    .sesi-search i {
        position: absolute;
        left: .85rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9aa8b5;
        font-size: 1rem;
    }

    .sesi-filter-btn {
        border: 1px solid #d9e8df;
        background: #fff;
        color: #64748b;
        border-radius: 12px;
        padding: .7rem 1rem;
        font-size: .84rem;
        font-weight: 600;
    }

    .sesi-table-wrap {
        padding: 0 1.5rem 1.5rem;
    }

    .sesi-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .86rem;
    }

    .sesi-table thead th {
        background: #dff1e5;
        color: #0f172a;
        font-weight: 700;
        padding: 1rem .95rem;
        text-align: left;
        border-bottom: 1px solid #d6e9de;
    }

    .sesi-table thead th:first-child {
        border-top-left-radius: 18px;
    }

    .sesi-table thead th:last-child {
        border-top-right-radius: 18px;
        text-align: center;
    }

    .sesi-table tbody td {
        padding: .95rem .95rem;
        border-bottom: 1px solid #edf2ef;
        vertical-align: middle;
        color: #334155;
    }

    .sesi-table tbody tr:hover {
        background: #fbfefc;
    }

    .mahasiswa-cell {
        display: flex;
        align-items: center;
        gap: .85rem;
        min-width: 210px;
    }

    .mahasiswa-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        overflow: hidden;
        background: #d9e6df;
        flex-shrink: 0;
    }

    .mahasiswa-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .mahasiswa-avatar-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #cfe9da;
        color: #064E3B;
        font-weight: 700;
        font-size: .9rem;
    }

    .mahasiswa-name {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .mahasiswa-sub {
        font-size: .74rem;
        color: #7b8a97;
        margin-top: 2px;
    }

    .waktu-text {
        line-height: 1.45;
        min-width: 120px;
    }

    .layanan-text {
    white-space: nowrap;
}

    .status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .42rem .95rem;
        border-radius: 999px;
        font-size: .74rem;
        font-weight: 600;
        min-width: 118px;
        line-height: 1.1;
        text-align: center;
    }

    .status-menunggu {
        background: #f3efb0;
        color: #8a7b1f;
    }

    .status-diterima {
        background: #bfeec9;
        color: #228b52;
    }

    .status-berlangsung {
        background: #cfc3fa;
        color: #6d4ee6;
    }

    .status-selesai {
        background: #a9d6ff;
        color: #1f78d1;
    }

    .status-ditolak,
    .status-dibatalkan {
        background: #ffb1b1;
        color: #d93030;
    }

    .btn-lihat {
        background: #065F46;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: .5rem 1rem;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 66px;
    }

    .btn-lihat:hover {
        background: #064E3B;
        color: #fff;
    }

    .action-stack {
        display: inline-flex;
        gap: .5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-buat-laporan {
        background: #0f766e;
    }

    .btn-buat-laporan:hover {
        background: #115e59;
    }

    .empty-row {
        text-align: center;
        color: #94a3b8;
        padding: 2.5rem 1rem !important;
    }

    .sesi-pagination {
        display: flex;
        justify-content: center;
        padding: 0 1.5rem 1.5rem;
    }

    .sesi-pagination-shell {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .sesi-pagination-inline {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        flex-wrap: nowrap;
        margin: 0 auto;
        width: fit-content;
    }

    .sesi-pagination-link,
    .sesi-pagination-page,
    .sesi-pagination-ellipsis {
        height: 34px;
        min-width: 34px;
        padding: 0 .85rem;
        border: 1px solid #dbe7df;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: .82rem;
        font-weight: 700;
        color: #065F46;
        background: #fff;
        white-space: nowrap;
        box-sizing: border-box;
    }

    .sesi-pagination-pages {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }

    .sesi-pagination-page.is-active {
        background: #065F46;
        border-color: #065F46;
        color: #fff;
    }

    .sesi-pagination-link.is-disabled,
    .sesi-pagination-ellipsis {
        color: #94a3b8;
        background: #f8fafc;
    }

    .sesi-pagination-link:hover,
    .sesi-pagination-page:hover {
        background: #f0f9f7;
        border-color: #bfe3d2;
        color: #047857;
    }

    .sesi-pagination-link.is-disabled:hover,
    .sesi-pagination-ellipsis:hover {
        background: #f8fafc;
        border-color: #dbe7df;
        color: #94a3b8;
    }
    @media (max-width: 991.98px) {
        .sesi-search input {
            width: 100%;
            min-width: 220px;
        }

        .sesi-table-wrap {
            overflow-x: auto;
        }

        .sesi-table {
            min-width: 880px;
        }
    }
</style>
@endpush

@section('konten')
@php
    $search = request('search', '');
@endphp
<div class="sesi-card">
    <div class="sesi-head">
        <h6>Daftar Riwayat Konseling</h6>
            <p>Kelola dan lihat detail riwayat konseling mahasiswa yang telah dijadwalkan</p>
    </div>

    <div class="sesi-toolbar">
        <div></div>

        <form class="sesi-search-wrap" id="sesiSearchForm" method="GET" action="{{ route('admin.riwayat') }}">
            <div class="sesi-search">
                <i class="ti ti-search"></i>
                <input
                    id="sesiSearchInput"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari mahasiswa..."
                    autocomplete="off"
                >
            </div>
            <button type="submit" class="sesi-filter-btn">Cari</button>
            @if($search !== '')
                <a href="{{ route('admin.riwayat') }}" class="sesi-filter-btn text-decoration-none">Reset</a>
            @endif
        </form>
    </div>

    <div class="sesi-table-wrap">
        <table class="sesi-table">
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Waktu</th>
                    <th>Layanan</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwal as $item)
                    @php
                        $mahasiswa = optional($item)->mahasiswa;
                        $user = optional($mahasiswa)->user;

                        $isAnonim = filter_var($item->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

                        $namaAnonim = 'Anonim';

                        if ($user && method_exists($user, 'getAnonimDisplayName')) {
                            $namaAnonim = trim($user->getAnonimDisplayName()) ?: 'Anonim';
                        }

                        $nama = $isAnonim
                            ? $namaAnonim
                            : (optional($user)->nama ?? 'Mahasiswa');

                        $nim = $isAnonim
                            ? '-'
                            : (optional($mahasiswa)->nim ?? '-');

                        $foto = $isAnonim
                            ? null
                            : (optional(optional($user)->profil)->foto ?? null);

                        $tanggal = optional($item)->tanggal
                            ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j F Y')
                            : '-';

                        $waktu = optional($item)->waktu
                            ? substr($item->waktu, 0, 5) . ' WIB'
                            : '-';

                        $layanan = 'Sesi ' . ucfirst($item->jenis ?? 'Online');

                        $statusRaw = strtolower($item->status ?? 'menunggu');

                        $statusLabel = match ($statusRaw) {
                            'menunggu' => 'Menunggu Konfirmasi',
                            'disetujui', 'diterima' => 'Diterima',
                            'berlangsung' => 'Sedang Berlangsung',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Dibatalkan',
                            'dibatalkan' => 'Dibatalkan',
                            default => ucfirst($statusRaw),
                        };

                        $statusClass = match ($statusRaw) {
                            'menunggu' => 'status-menunggu',
                            'disetujui', 'diterima' => 'status-diterima',
                            'berlangsung' => 'status-berlangsung',
                            'selesai' => 'status-selesai',
                            'ditolak', 'dibatalkan' => 'status-dibatalkan',
                            default => 'status-menunggu',
                        };

                        // Sesi selesai tanpa isi laporan bisa langsung dibuatkan laporan.
                        $sudahAdaLaporan = trim((string) ($item->laporan ?? '')) !== ''
                            || trim((string) ($item->ringkasan_masalah ?? '')) !== ''
                            || trim((string) ($item->observasi_konselor ?? '')) !== ''
                            || trim((string) optional($item->sesiKonseling?->laporan)->isi_laporan) !== '';

                        $bisaBuatLaporan = $statusRaw === 'selesai' && !$sudahAdaLaporan;
                    @endphp

                    <tr>
                        <td>
                            <div class="mahasiswa-cell">
                                <div class="mahasiswa-avatar">
                                    @if($foto)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($foto) }}" alt="{{ $nama }}">
                                    @else
                                        <div class="mahasiswa-avatar-fallback">
                                            {{ strtoupper(substr($nama, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <div class="mahasiswa-name">{{ $nama }}</div>
                                    <div class="mahasiswa-sub">{{ $nim }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="waktu-text">
                                <div>{{ $tanggal }}</div>
                                <div>{{ $waktu }}</div>
                            </div>
                        </td>

                        <td>
                            <div class="layanan-text">{{ $layanan }}</div>
                        </td>

                        <td>
                            <span class="status-pill {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td style="text-align:center;">
                            <div class="action-stack">
                                <a href="{{ route('admin.riwayat.detail', $item->id) }}" class="btn-lihat">Lihat</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">
                            Belum ada data riwayat konseling
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($jadwal, 'links'))
        <div class="sesi-pagination">
            {{ $jadwal->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('sesiSearchForm');
    const input = document.getElementById('sesiSearchInput');

    if (!form || !input) {
        return;
    }

    let debounceTimer = null;

    input.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);

        debounceTimer = window.setTimeout(() => {
            form.submit();
        }, 300);
    });
})();
</script>
@endpush
