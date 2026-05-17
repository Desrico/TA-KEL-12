@extends('layouts.admin')

@section('page-title', 'Laporan Hasil Konseling')

@push('styles')
<style>
    .laporan-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(6, 78, 59, 0.08);
        max-width: 1100px;
        margin: 0 auto;
        overflow: hidden;
        width: calc(100% - 48px);
    }

    .laporan-head {
        padding: 24px 28px 18px;
        border-bottom: 1px solid #edf2ef;
        display: flex;
        gap: 18px;
        justify-content: space-between;
        align-items: flex-start;
    }

    .laporan-head h5 {
        margin: 0 0 6px;
        font-weight: 800;
        color: #064e3b;
        font-size: 1.2rem;
    }

    .laporan-head p {
        margin: 0;
        color: #64748b;
        font-size: .88rem;
    }

    .laporan-search {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .laporan-search input {
        width: 280px;
        height: 42px;
        border: 1px solid #dceee4;
        border-radius: 12px;
        padding: 0 14px;
        font-size: .88rem;
        outline: none;
    }

    .laporan-search input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .btn-laporan {
        border: 0;
        border-radius: 10px;
        background: #065f46;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 38px;
        padding: 0 14px;
        font-size: .78rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-laporan:hover {
        background: #064e3b;
        color: #fff;
    }

    .btn-secondary-link {
        background: #fff;
        color: #065f46;
        border: 1px solid #dceee4;
    }

    .btn-secondary-link:hover {
        background: #f0fdf4;
        color: #065f46;
    }

    .laporan-table-wrap {
        padding: 20px;
        overflow-x: auto;
    }

    .laporan-table {
        width: 100%;
        min-width: 760px;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #d7e7de;
        border-radius: 14px;
        overflow: hidden;
        font-size: .86rem;
    }

    .laporan-table th {
        background: #f5f8f7;
        color: #111827;
        font-weight: 800;
        padding: 13px 14px;
        border-bottom: 1px solid #d6e9de;
        text-align: left;
    }

    .laporan-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #edf2ef;
        color: #1f2937;
        vertical-align: middle;
    }

    .laporan-table tbody tr:hover {
        background: #fbfefc;
    }

    .student-name {
        font-weight: 800;
        color: #111827;
    }

    .student-sub {
        color: #64748b;
        font-size: .78rem;
        margin-top: 2px;
    }

    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 34px 14px !important;
    }

    .sesi-pagination {
        padding: 0 20px 22px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .laporan-card {
            width: calc(100% - 24px);
        }

        .laporan-head {
            flex-direction: column;
        }

        .laporan-search {
            width: 100%;
            flex-wrap: wrap;
        }

        .laporan-search input {
            flex: 1;
            min-width: 180px;
            width: auto;
        }
    }
</style>
@endpush

@section('konten')
<div class="laporan-card">
    <div class="laporan-head">
        <div>
            <h5>Laporan Hasil Konseling</h5>
            <p>Daftar mahasiswa yang memiliki laporan sesi konseling.</p>
        </div>

        <form class="laporan-search" method="GET" action="{{ route('admin.laporan') }}">
            <input type="search" name="q" value="{{ $q ?? request('q') }}" placeholder="Cari nama, NIM, prodi, angkatan">
            <button type="submit" class="btn-laporan">Cari</button>
            @if(request()->filled('q'))
                <a href="{{ route('admin.laporan') }}" class="btn-laporan btn-secondary-link">Reset</a>
            @endif
        </form>
    </div>

    <div class="laporan-table-wrap">
        <table class="laporan-table">
            <thead>
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Prodi</th>
                    <th>Angkatan</th>
                    <th>Jumlah Laporan</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswa as $item)
                    <tr>
                        <td>
                            <div class="student-name">{{ optional($item->user)->nama ?? 'Anonim' }}</div>
                        </td>
                        <td>{{ $item->nim ?? '-' }}</td>
                        <td>{{ $item->jurusan ?? '-' }}</td>
                        <td>{{ $item->angkatan ?? '-' }}</td>
                        <td>{{ $item->total_laporan ?? 0 }}</td>
                        <td style="text-align:center;">
                            <a href="{{ route('admin.laporan.mahasiswa', $item->id) }}" class="btn-laporan">
                                <i class="ti ti-file-text"></i>
                                Lihat Laporan
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            Belum ada laporan sesi konseling.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($mahasiswa, 'links'))
        <div class="sesi-pagination">
            {{ $mahasiswa->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@endsection
