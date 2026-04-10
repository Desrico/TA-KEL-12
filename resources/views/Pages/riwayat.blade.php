@extends('layouts.master')

@section('konten')
<section class="container" style="margin-top:2rem;">
  <div style="background:white;border-radius:16px;padding:1.2rem 1.3rem;border:1px solid rgba(26,58,92,.08);box-shadow:0 8px 24px rgba(13,27,42,.06);">
    <h4 style="margin:0 0 .3rem;font-weight:800;color:var(--text-dark)">Riwayat Konseling</h4>
    <p style="margin:0 0 1rem;color:var(--text-light);font-size:.88rem;">Daftar booking dan status konseling kamu.</p>

    <div class="table-responsive">
      <table class="table table-hover mb-0" style="font-size:.86rem;">
        <thead>
          <tr style="color:#8898aa;font-size:.74rem;text-transform:uppercase;">
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($riwayat as $item)
          <tr>
            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
            <td>{{ $item->waktu }} WIB</td>
            <td>
              @if($item->status === 'disetujui')
                <span class="badge" style="background:#d4f7ea;color:#0fb87a;">Disetujui</span>
              @elseif($item->status === 'ditolak')
                <span class="badge" style="background:#fdf2f2;color:#e74c3c;">Ditolak</span>
              @else
                <span class="badge" style="background:#fff8e6;color:#f5a623;">Menunggu</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3" class="text-center py-4" style="color:#8898aa;">Belum ada riwayat booking.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>
@endsection
