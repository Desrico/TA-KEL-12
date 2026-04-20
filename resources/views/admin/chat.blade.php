@extends('layouts.mantis')
@section('page-title', 'Laporan')
@section('konten')
<div class="kons-card">
    <div class="kons-card-header">
        <h6 style="font-weight:700;margin:0">Laporan Konseling</h6>
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $l)
                    <tr>
                        <td class="px-4 py-3">
                            @php
                                $isAnonimBooking = str_starts_with((string) ($l->catatan ?? ''), '[ANONIM]');
                            @endphp
                            @if($isAnonimBooking)
                                <div style="font-weight:600">{{ optional($l->mahasiswa)->jurusan ?? '-' }} · {{ optional($l->mahasiswa)->angkatan ?? '-' }}</div>
                                <div style="font-size:.75rem;color:#8898aa">Mode anonim</div>
                            @else
                                <div style="font-weight:600">{{ optional(optional($l->mahasiswa)->user)->nama ?? 'Anonim' }}</div>
                                <div style="font-size:.75rem;color:#8898aa">{{ optional($l->mahasiswa)->nim ?? '-' }}</div>
                            @endif
                        </td>
                        <td class="py-3">{{ \Carbon\Carbon::parse($l->tanggal)->format('d M Y') }}</td>
                        <td class="py-3">{{ $l->waktu }} WIB</td>
                        <td class="py-3" style="font-size:.78rem">{{ $l->catatan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5" style="color:#8898aa">Belum ada laporan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection