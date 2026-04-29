@extends('layouts.admin')

@section('page-title', 'Manajemen Challenge')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        .pc-container {
            background: #f8fafc;
        }

        .container { max-width: 1200px; margin: 0 auto; width: 100%; padding: 32px; }

        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; }
        .page-header h1 { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 700; color: #1e293b; }
        .page-header p { color: #94a3b8; font-size: 0.9rem; margin-top: 4px; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; border-radius: 10px;
            background: #059669; color: white;
            font-weight: 600; font-size: 0.9rem; text-decoration: none;
            transition: all 0.2s; border: none; cursor: pointer;
        }
        .btn-primary:hover { background: #047857; transform: translateY(-1px); color: white;}

        /* ── Table Styling ── */
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
        .premium-table { width: 100%; border-collapse: collapse; text-align: left; }
        .premium-table th {
            padding: 16px 24px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
            color: #94a3b8; border-bottom: 1px solid #e2e8f0; background: #fafafa;
        }
        .premium-table td { padding: 20px 24px; font-size: 0.88rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .premium-table tr:last-child td { border-bottom: none; }
        
        .badge-status { padding: 4px 12px; border-radius: 99px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .badge-active { background: #d1fae5; color: #059669; }
        .badge-inactive { background: #f1f5f9; color: #94a3b8; }

        .actions { display: flex; gap: 8px; }
        .btn-icon {
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid #e2e8f0; background: #fff;
            color: #475569; transition: all 0.2s; text-decoration: none;
        }
        .btn-icon:hover { color: #059669; border-color: #059669; background: #d1fae5; }
        .btn-icon.delete:hover { color: #dc2626; border-color: #dc2626; background: #fef2f2; }

        .alert { padding: 16px 24px; border-radius: 10px; margin-bottom: 24px; font-size: 0.9rem; font-weight: 600; }
        .alert-success { background: #d1fae5; border: 1px solid rgba(5, 150, 105, 0.2); color: #059669; }
        
        .btn-back-link { display: inline-flex; align-items: center; gap: 6px; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; padding: 8px 12px; border-radius: 8px; margin-bottom: 16px;}
        .btn-back-link:hover { background: #ffffff; color: #059669; }
    </style>
@endpush

@section('konten')
    <div class="container-fluid">
        <a href="{{ route('counselor.education.index') }}" class="btn-back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali ke Menu Utama Edukasi
        </a>

        <div class="page-header">
            <div>
                <h1>🎮 Manajemen Challenge</h1>
                <p>Daftar kuis dan tantangan interaktif untuk mahasiswa.</p>
            </div>
            <a href="{{ route('counselor.education.challenges.create') }}" class="btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Tambah Challenge
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Challenge</th>
                        <th style="width: 140px;">Status</th>
                        <th style="width: 100px;">Point</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($challenges as $c)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="width: 54px; height: 54px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 1px solid #e2e8f0;">🏆</div>
                                <div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 1rem;">{{ $c->title }}</div>
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 2px;">{{ Str::limit($c->description, 60) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-status {{ $c->status ? 'badge-active' : 'badge-inactive' }}">
                                {{ $c->status ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td style="font-family: 'Outfit', sans-serif; font-weight: 700; color: #059669;">{{ $c->reward_point }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('counselor.education.challenges.edit', $c->id) }}" class="btn-icon" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </a>
                                <form action="{{ route('counselor.education.challenges.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus challenge ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon delete" title="Hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 64px; color: #94a3b8;">
                            <div style="font-size: 2.5rem; margin-bottom: 16px; opacity: 0.5;">📭</div>
                            <div style="font-size: 0.95rem; font-weight: 500;">Belum ada challenge yang dibuat.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
