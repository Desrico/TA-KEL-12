@extends('layouts.admin')

@section('page-title', 'Laporan Konseling')

@push('styles')
<style>
    .laporan-card {
        background: #fff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
        overflow: hidden;
    }

    .laporan-head {
        padding: 1.4rem 1.6rem;
        border-bottom: 1px solid #edf2ef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .laporan-head h5 {
        margin: 0;
        font-weight: 800;
        color: #064E3B;
    }

    .laporan-head p {
        margin: .25rem 0 0;
        color: #718096;
        font-size: .86rem;
    }

    .laporan-stack {
        display: grid;
        gap: 1.25rem;
    }

    .laporan-detail {
        background: linear-gradient(180deg, #ffffff 0%, #f7fcf9 100%);
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 8px 22px rgba(6, 95, 70, .06);
        padding: 1.5rem 1.6rem;
    }

    .laporan-detail-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .laporan-detail-head h5 {
        margin: 0;
        color: #064E3B;
        font-weight: 800;
    }

    .laporan-detail-head p {
        margin: .3rem 0 0;
        color: #64748b;
        font-size: .88rem;
    }

    .laporan-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: .9rem;
        margin-bottom: 1.2rem;
    }

    .laporan-meta-item {
        background: #ffffff;
        border: 1px solid #e6f1eb;
        border-radius: 16px;
        padding: .9rem 1rem;
    }

    .laporan-meta-label {
        display: block;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .02em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: .32rem;
    }

    .laporan-meta-value {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.5;
    }

    .laporan-form-label {
        display: block;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: .55rem;
    }

    .laporan-textarea {
        width: 100%;
        min-height: 240px;
        border: 1px solid #cfe6d9;
        border-radius: 16px;
        padding: 1rem 1.05rem;
        resize: vertical;
        font-size: .92rem;
        line-height: 1.7;
        color: #334155;
        background: #fff;
    }

    .laporan-textarea:focus {
        outline: none;
        border-color: #34d399;
        box-shadow: 0 0 0 4px rgba(52, 211, 153, .14);
    }

    .laporan-actions {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn-secondary-soft {
        background: #effaf4;
        color: #065F46;
        border: 1px solid #cceedd;
    }

    .btn-secondary-soft:hover {
        background: #def7e8;
        color: #064E3B;
    }

    .laporan-content {
        background: #fff;
        border: 1px solid #e6f1eb;
        border-radius: 18px;
        padding: 1.1rem 1.15rem;
        color: #334155;
        line-height: 1.8;
        white-space: pre-wrap;
    }

    .laporan-row-active {
        background: #f7fcf9;
    }

    .laporan-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .86rem;
    }

    .laporan-table thead th {
        background: #e6f5ec;
        color: #0f172a;
        font-weight: 700;
        padding: 1rem;
        border-bottom: 1px solid #d6e9de;
    }

    .laporan-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #edf2ef;
        vertical-align: middle;
        color: #334155;
    }

    .laporan-table tbody tr:hover {
        background: #fbfefc;
    }

    .student-name {
        font-weight: 700;
        color: #0f172a;
    }

    .student-sub {
        font-size: .76rem;
        color: #8191a3;
        margin-top: 2px;
    }

    .topic-text {
        max-width: 230px;
        color: #475569;
        font-size: .82rem;
    }

    .status-pill {
        display: inline-flex;
        border-radius: 999px;
        padding: .42rem .85rem;
        font-size: .74rem;
        font-weight: 700;
    }

    .status-selesai {
        background: #d1fae5;
        color: #047857;
    }

    .status-belum {
        background: #fef3c7;
        color: #b45309;
    }

    .btn-laporan {
        border: none;
        border-radius: 10px;
        padding: .55rem .9rem;
        font-size: .78rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        white-space: nowrap;
    }

    .btn-buat {
        background: #065F46;
        color: #fff;
    }

    .btn-buat:hover {
        background: #064E3B;
        color: #fff;
    }

    .btn-lihat {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #bbf7d0;
    }

    .btn-lihat:hover {
        background: #d1fae5;
        color: #065F46;
    }

    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 2.5rem 1rem !important;
    }

    @media (max-width: 991.98px) {
        .laporan-table-wrap {
            overflow-x: auto;
        }

        .laporan-table {
            min-width: 900px;
        }
    }
</style>
@endpush

@section('konten')
<div class="laporan-stack">
    @if(isset($jadwal))
        @php
            $selectedNama = optional(optional($jadwal->mahasiswa)->user)->nama ?? 'Anonim';
            $selectedNim = optional($jadwal->mahasiswa)->nim ?? '-';
            $selectedTopik = $jadwal->topik ?? null;

            if (!$selectedTopik && !empty($jadwal->catatan) && preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
                $selectedTopik = trim($match[1]);
            }

            $isExistingReport = !empty($laporan);
        @endphp

        <div class="laporan-detail">
            <div class="laporan-detail-head">
                <div>
                    <h5>{{ $isExistingReport ? 'Detail Laporan Konseling' : 'Buat Laporan Konseling' }}</h5>
                    <p>{{ $isExistingReport ? 'Laporan sesi yang sudah tersimpan untuk mahasiswa terpilih.' : 'Isi hasil sesi konseling untuk mahasiswa terpilih.' }}</p>
                </div>
                <a href="{{ route('admin.laporan') }}" class="btn-laporan btn-secondary-soft">
                    <i class="ti ti-arrow-left"></i>
                    Kembali ke Daftar
                </a>
            </div>

            <div class="laporan-meta">
                <div class="laporan-meta-item">
                    <span class="laporan-meta-label">Mahasiswa</span>
                    <div class="laporan-meta-value">{{ $selectedNama }}<br>{{ $selectedNim }}</div>
                </div>
                <div class="laporan-meta-item">
                    <span class="laporan-meta-label">Jadwal</span>
                    <div class="laporan-meta-value">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}<br>{{ substr($jadwal->waktu, 0, 5) }} WIB</div>
                </div>
                <div class="laporan-meta-item">
                    <span class="laporan-meta-label">Layanan</span>
                    <div class="laporan-meta-value">{{ ucfirst($jadwal->jenis ?? 'Online') }}</div>
                </div>
                <div class="laporan-meta-item">
                    <span class="laporan-meta-label">Topik</span>
                    <div class="laporan-meta-value">{{ $selectedTopik ?: '-' }}</div>
                </div>
            </div>

            @if($isExistingReport)
                <div>
                    <label class="laporan-form-label">Isi Laporan</label>
                    <div class="laporan-content">{{ $laporan->isi_laporan }}</div>
                </div>
            @else
                <form method="POST" action="{{ route('admin.laporan.laporan.store', $jadwal->id) }}">
                    @csrf
                    <div>
                        <label for="isi_laporan" class="laporan-form-label">Isi Laporan</label>
                        <textarea
                            id="isi_laporan"
                            name="isi_laporan"
                            class="laporan-textarea @error('isi_laporan') is-invalid @enderror"
                            placeholder="Tulis ringkasan sesi, kondisi mahasiswa, intervensi yang diberikan, dan tindak lanjut yang disarankan."
                        >{{ old('isi_laporan') }}</textarea>
                        @error('isi_laporan')
                            <div class="text-danger mt-2 small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="laporan-actions">
                        <button type="submit" class="btn-laporan btn-buat">
                            <i class="ti ti-device-floppy"></i>
                            Simpan Laporan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @else
        <div class="laporan-card">
            <div class="laporan-head">
                <div>
                    <h5>Laporan Hasil Konseling</h5>
                    <p>Dokumentasikan hasil sesi konseling dan perkembangan kondisi mahasiswa.</p>
                </div>
            </div>

            <div class="laporan-table-wrap">
                <table class="laporan-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Layanan</th>
                            <th>Topik</th>
                            <th>Status</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $l)
                            @php
                                $nama = optional(optional($l->mahasiswa)->user)->nama ?? 'Anonim';
                                $nim = optional($l->mahasiswa)->nim ?? '-';

                                $topik = $l->topik ?? null;
                                if (!$topik && !empty($l->catatan) && preg_match('/Topik:\s*([^|]+)/i', $l->catatan, $match)) {
                                    $topik = trim($match[1]);
                                }

                                $sudahAdaLaporan = !empty(optional($l->sesiKonseling)->laporan);
                            @endphp

                            <tr>
                                <td>
                                    <div class="student-name">{{ $nama }}</div>
                                    <div class="student-sub">{{ $nim }}</div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ substr($l->waktu, 0, 5) }} WIB</td>
                                <td>{{ ucfirst($l->jenis ?? 'Online') }}</td>
                                <td>
                                    <div class="topic-text">{{ $topik ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($sudahAdaLaporan)
                                        <span class="status-pill status-selesai">Laporan Tersedia</span>
                                    @else
                                        <span class="status-pill status-belum">Belum Dilaporkan</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <a href="{{ route('admin.laporan.laporan', $l->id) }}"
                                       class="btn-laporan {{ $sudahAdaLaporan ? 'btn-lihat' : 'btn-buat' }}">
                                        <i class="ti {{ $sudahAdaLaporan ? 'ti-eye' : 'ti-file-plus' }}"></i>
                                        {{ $sudahAdaLaporan ? 'Lihat Laporan' : 'Buat Laporan' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    Belum ada data konseling yang dapat dibuatkan laporan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
