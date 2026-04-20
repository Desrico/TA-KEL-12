{{-- riwayat.blade.php --}}
@extends('layouts.mantis')
@section('page-title', 'Riwayat Konseling')
@section('konten')

<div class="kons-card">
    <div class="kons-card-header">
        <h6 style="font-weight:700;margin:0">Riwayat Konseling</h6>
    </div>
    <div class="kons-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.85rem">
                <thead style="background:#f8fafb">
                    <tr style="color:#8898aa;font-size:.73rem;text-transform:uppercase">
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Waktu</th>
                        <th class="py-3">Catatan</th>
                        <th class="py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $r)
                    <tr>
                        <td class="px-4 py-3">{{ optional($r->mahasiswa)->nama ?? 'Anonim' }}</td>
                        <td class="py-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                        <td class="py-3">{{ $r->waktu }} WIB</td>
                        <td class="py-3">
                            @if($r->catatan)
                                {{ $r->catatan }}
                            @else
                                <span style="color:#ffab00">Belum ada catatan</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if(!$r->catatan)
                                <a href="{{ route('admin.riwayat.laporan', $r->id) }}" class="btn btn-primary btn-sm">Buat Laporan</a>
                            @else
                                <span class="badge bg-success">Laporan Tersimpan</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection