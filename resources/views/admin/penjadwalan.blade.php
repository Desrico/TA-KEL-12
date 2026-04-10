@extends('layouts.mantis')
@section('page-title', 'Penjadwalan')
@section('konten')
<div class="kons-card">
    <div class="kons-card-header">
        <h6 style="font-weight:700;margin:0">Semua Booking</h6>
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
                        <th class="py-3">Status</th>
                        <th class="py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td class="px-4 py-3">
                            @php
                                $isAnonimBooking = str_starts_with((string) ($b->catatan ?? ''), '[ANONIM]');
                            @endphp
                            @if($isAnonimBooking)
                                <div style="font-weight:600">{{ optional($b->mahasiswa)->jurusan ?? '-' }} · {{ optional($b->mahasiswa)->angkatan ?? '-' }}</div>
                                <div style="font-size:.75rem;color:#8898aa">Mode anonim</div>
                            @else
                                <div style="font-weight:600">{{ optional(optional($b->mahasiswa)->user)->nama ?? 'Anonim' }}</div>
                                <div style="font-size:.75rem;color:#8898aa">{{ optional($b->mahasiswa)->nim ?? '-' }} · {{ optional($b->mahasiswa)->jurusan ?? '-' }}</div>
                            @endif
                        </td>
                        <td class="py-3">{{ \Carbon\Carbon::parse($b->tanggal)->format('d M Y') }}</td>
                        <td class="py-3">{{ $b->waktu }} WIB</td>
                        <td class="py-3" style="max-width:200px;font-size:.78rem">{{ $b->catatan ?? '-' }}</td>
                        <td class="py-3"><span class="badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                        <td class="py-3">
                            @if($b->status === 'menunggu')
                                <form action="{{ route('admin.booking.setujui', $b->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill px-3" style="background:#d4f7ea;color:#0fb87a;font-size:.75rem;font-weight:600;border:none">
                                        <i class="ti ti-check"></i> Setujui
                                    </button>
                                </form>
                                <form action="{{ route('admin.booking.tolak', $b->id) }}" method="POST" class="d-inline ms-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill px-3" style="background:#fdf2f2;color:#e74c3c;font-size:.75rem;font-weight:600;border:none">
                                        <i class="ti ti-x"></i> Tolak
                                    </button>
                                </form>
                            @else
                                <span style="font-size:.75rem;color:#8898aa">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5" style="color:#8898aa">Belum ada booking</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection