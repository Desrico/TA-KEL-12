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
                        <th class="py-3">Jenis</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Waktu</th>
                        <th class="py-3">Catatan</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $l)
                    @php
                        $status = strtolower($l->status ?? 'menunggu');
                        $jenis = strtolower($l->jenis ?? 'offline');
                        $scheduledAt = $jenis === 'online'
                            ? \Carbon\Carbon::parse(trim($l->tanggal . ' ' . ($l->waktu ?? '00:00:00')))
                            : null;
                        $isBeforeSchedule = $scheduledAt && now()->lt($scheduledAt);
                        $canStartChat = $jenis === 'online'
                            && in_array($status, ['disetujui', 'berlangsung'], true)
                            && ! $isBeforeSchedule;
                    @endphp
                    <tr>
                        <td class="px-4 py-3">
                            <div style="font-weight:600">{{ optional(optional($l->mahasiswa)->user)->nama ?? 'Anonim' }}</div>
                            <div style="font-size:.75rem;color:#8898aa">{{ optional($l->mahasiswa)->nim ?? '-' }}</div>
                        </td>
                        <td class="py-3">{{ ucfirst($l->jenis ?? '-') }}</td>
                        <td class="py-3">{{ ucfirst($l->status ?? '-') }}</td>
                        <td class="py-3">{{ \Carbon\Carbon::parse($l->tanggal)->format('d M Y') }}</td>
                        <td class="py-3">{{ $l->waktu }} WIB</td>
                        <td class="py-3" style="font-size:.78rem">{{ $l->catatan ?? '-' }}</td>
                        <td class="py-3 text-center">
                            @if($canStartChat)
                                <a href="{{ route('mahasiswa.chat') }}" class="btn btn-sm" style="background:#065F46;color:#fff;border-radius:10px;padding:.45rem .85rem;font-weight:600;">
                                    {{ $status === 'berlangsung' ? 'Lanjutkan Chat' : 'Mulai Sesi' }}
                                </a>
                            @elseif($jenis === 'online' && in_array($status, ['disetujui', 'berlangsung'], true) && $isBeforeSchedule)
                                <button
                                    type="button"
                                    class="btn btn-sm"
                                    style="background:#cbd5e1;color:#475569;border-radius:10px;padding:.45rem .85rem;font-weight:600;border:none;"
                                    title="Sesi dimulai {{ $scheduledAt->translatedFormat('j F Y') }} pukul {{ $scheduledAt->format('H:i') }} WIB"
                                    disabled
                                >
                                    Mulai {{ $scheduledAt->format('H:i') }} WIB
                                </button>
                            @else
                                <span style="font-size:.75rem;color:#94a3b8;">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5" style="color:#8898aa">Belum ada laporan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
