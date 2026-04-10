@extends('layouts.mantis')
@section('page-title', 'Riwayat Mahasiswa')
@section('konten')
<div class="kons-card">
    <div class="kons-card-header">
        <h6 style="font-weight:700;margin:0">Data Mahasiswa</h6>
    </div>
    <div class="kons-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.85rem">
                <thead style="background:#f8fafb">
                    <tr style="color:#8898aa;font-size:.73rem;text-transform:uppercase">
                        <th class="px-4 py-3">Nama</th>
                        <th class="py-3">NIM</th>
                        <th class="py-3">Jurusan</th>
                        <th class="py-3">Angkatan</th>
                        <th class="py-3">Total Sesi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mahasiswas as $m)
                    <tr>
                        <td class="px-4 py-3" style="font-weight:600">{{ optional($m->user)->nama ?? '-' }}</td>
                        <td class="py-3">{{ $m->nim }}</td>
                        <td class="py-3">{{ $m->jurusan }}</td>
                        <td class="py-3">{{ $m->angkatan }}</td>
                        <td class="py-3">{{ $m->jadwalKonseling->count() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5" style="color:#8898aa">Belum ada data mahasiswa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection