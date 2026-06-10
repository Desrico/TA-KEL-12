@extends('layouts.admin')

@section('page-title', 'Manajemen Modul')

@push('styles')
    <style>
        .pc-container {
            background: #f8fafc !important;
        }
        .container-fluid { padding: 32px; max-width: 100%; box-sizing: border-box; }

        .btn-back-link { display: inline-flex; align-items: center; gap: 6px; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; padding: 8px 12px; border-radius: 8px; margin-bottom: 0;}
        .btn-back-link:hover { background: #ffffff; color: #059669; }

        .premium-table {
            width: 100%; border-collapse: collapse; text-align: left;
            background: #ffffff; border-radius: 16px; overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .premium-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        .premium-table th {
            padding: 16px 20px; font-size: 0.75rem; font-weight: 700; color: #059669;
            text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;
        }
        .premium-table td {
            padding: 16px 20px; border-bottom: 1px solid #e2e8f0;
            vertical-align: middle; font-size: 0.85rem; color: #1e293b;
        }
        .premium-table tr:last-child td { border-bottom: none; }
        .premium-table tbody tr { transition: background 0.2s; }
        .premium-table tbody tr:hover { background: #f8fafc; }

        .mi-btn-add {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; background: #059669; color: #fff; border: none; cursor: pointer; text-decoration: none; transition: 0.2s;
        }
        .mi-btn-add:hover { background: #047857; color: #fff; box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.2); transform: translateY(-1px); }

        .mi-tabs {
            display: inline-flex; background: #f1f5f9; padding: 4px; border-radius: 999px; gap: 4px; border: 1px solid #e2e8f0;
        }
        .mi-tab {
            display: inline-flex; align-items: center; padding: 6px 18px; border-radius: 999px; font-size: 0.85rem; font-weight: 600; text-decoration: none; color: #64748b; transition: all 0.2s;
        }
        .mi-tab:hover:not(.active) { color: #1e293b; background: #e2e8f0; }
        .mi-tab.active { background: #059669; color: #fff; box-shadow: 0 2px 4px rgba(5,150,105,0.2); }
        
        .mi-sort-select {
            border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 30px 6px 10px; font-size: 0.82rem; font-weight: 600; color: #1e293b; background: #fff; outline: none; appearance: none; cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 9px center;
        }
        .mi-sort-select:focus { border-color: #059669; }

        .mi-thumb { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0; flex-shrink: 0; background: #f3f4f6; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; overflow: hidden; }
        .mi-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .mi-module-cell { display: flex; align-items: center; gap: 14px; }
        .mi-title { font-weight: 700; color: #1e293b; font-size: 0.92rem; margin-bottom: 3px; }
        .mi-desc { font-size: 0.78rem; color: #64748b; line-height: 1.4; }

        .mi-point { display: inline-flex; align-items: center; gap: 5px; background: #d1fae5; color: #059669; border-radius: 999px; padding: 5px 12px; font-size: 0.8rem; font-weight: 700; white-space: nowrap; border: 1px solid #a7f3d0;}
        .mi-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 999px; padding: 5px 12px; font-size: 0.78rem; font-weight: 700; white-space: nowrap; }
        .mi-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .mi-badge.aktif { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;}
        .mi-badge.aktif::before { background: #16a34a; }
        .mi-badge.draft { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;}
        .mi-badge.draft::before { background: #94a3b8; }
        
        .mi-actions { display: flex; gap: 6px; }
        .mi-btn-icon { width: 34px; height: 34px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; display: flex; align-items: center; justify-content: center; color: #64748b; text-decoration: none; cursor: pointer; transition: all 0.18s; }
        .mi-btn-icon:hover { border-color: #059669; color: #059669; background: #f0fdf4; }
        .mi-btn-icon.del:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

        .mi-empty { text-align: center; padding: 72px 24px; color: #94a3b8; }
        .mi-empty .emoji { font-size: 2.8rem; margin-bottom: 14px; opacity: 0.5; }
        .mi-empty p { font-size: 0.9rem; font-weight: 600; margin: 0; }

        .search-results-info { padding: 8px 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; font-size: 0.8rem; color: #059669; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        
        .custom-pagination .page-link { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 8px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; color: #475569; text-decoration: none; transition: 0.2s; cursor: pointer; border: none; background: transparent; }
        .custom-pagination .page-link:hover:not(.disabled):not(.active) { background: #e2e8f0; color: #1e293b; }
        .custom-pagination .page-link.active { background: #047857; color: #ffffff; }
        .custom-pagination .page-link.disabled { color: #cbd5e1; cursor: not-allowed; }
    </style>
@endpush

@section('konten')
    <div class="container-fluid">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('counselor.education.index') }}" class="btn-back-link">
                    <i class="ti ti-arrow-left" style="font-size: 1.2rem;"></i>
                    Edukasi & Intervensi
                </a>
                <span style="color: #94a3b8; font-size: 1.2rem; font-weight: 300;">/</span>
                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 999px; padding: 3px 12px; font-size: 0.8rem; font-weight: 700;">
                    Manajemen Modul
                </span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('counselor.education.modules.create') }}" class="mi-btn-add">
                    <i class="ti ti-plus" style="font-size: 1.1rem;"></i>
                    Tambah Modul
                </a>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <p style="color: #475569; font-size: 0.95rem; margin: 0;">Kelola dan terbitkan konten edukasi psikologi untuk mahasiswa.</p>
        </div>

        @if(session('success'))
            <div style="padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 8px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div class="mi-tabs">
                <a href="{{ route('counselor.education.modules.index', ['filter' => 'semua', 'sort' => $sort]) }}"
                   class="mi-tab {{ $filter === 'semua' ? 'active' : '' }}">Semua Modul</a>
                <a href="{{ route('counselor.education.modules.index', ['filter' => 'aktif', 'sort' => $sort]) }}"
                   class="mi-tab {{ $filter === 'aktif' ? 'active' : '' }}">Aktif</a>
                <a href="{{ route('counselor.education.modules.index', ['filter' => 'draft', 'sort' => $sort]) }}"
                   class="mi-tab {{ $filter === 'draft' ? 'active' : '' }}">Draft</a>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.82rem; color: #475569; font-weight: 500;">
                <span>Urutkan:</span>
                <form method="GET" action="{{ route('counselor.education.modules.index') }}" id="sortForm">
                    <input type="hidden" name="filter" value="{{ $filter }}">
                    <select name="sort" class="mi-sort-select" onchange="document.getElementById('sortForm').submit()">
                        <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="terlama" {{ $sort === 'terlama' ? 'selected' : '' }}>Terlama</option>
                        <option value="az"      {{ $sort === 'az'      ? 'selected' : '' }}>A–Z</option>
                        <option value="za"      {{ $sort === 'za'      ? 'selected' : '' }}>Z–A</option>
                    </select>
                </form>
            </div>
        </div>

        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
            @if($modules->isEmpty())
                <div class="mi-empty">
                    <div class="emoji">📭</div>
                    <p>Belum ada modul edukasi yang ditemukan.</p>
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="premium-table" style="min-width: 800px; border: none; border-radius: 0; box-shadow: none;">
                        <thead>
                            <tr>
                                <th>Modul Edukasi</th>
                                <th style="width:130px;">Poin</th>
                                <th style="width:110px;">Status</th>
                                <th style="width:100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $m)
                            <tr>
                                <td>
                                    <div class="mi-module-cell">
                                        <div class="mi-thumb">
                                            @if($m->thumbnail)
                                                <img src="{{ Str::startsWith($m->thumbnail, 'modules/') ? Storage::url($m->thumbnail) : $m->thumbnail }}"
                                                     alt="{{ $m->title }}"
                                                     onerror="this.parentElement.innerHTML='📖'">
                                            @else
                                                📖
                                            @endif
                                        </div>
                                        <div>
                                            <div class="mi-title">{{ $m->title }}</div>
                                            <div class="mi-desc">{{ Str::limit($m->description, 70) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="mi-point">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#059669" stroke="#059669" stroke-width="0"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                        {{ $m->reward_point }} Poin
                                    </span>
                                </td>
                                <td>
                                    <span class="mi-badge {{ $m->status ? 'aktif' : 'draft' }}">
                                        {{ $m->status ? 'Aktif' : 'Draft' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="mi-actions">
                                        <a href="{{ route('counselor.education.modules.edit', $m->id) }}" class="mi-btn-icon" title="Edit">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form action="{{ route('counselor.education.modules.destroy', $m->id) }}" method="POST"
                                              class="delete-form" style="display:contents;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="mi-btn-icon del" title="Hapus">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($modules->hasPages())
                <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <div style="font-size: 0.85rem; color: #64748b; font-weight: 500;">
                        Menampilkan {{ $modules->firstItem() }}&ndash;{{ $modules->lastItem() }} dari {{ $modules->total() }} modul
                    </div>
                    <div class="custom-pagination" style="display: flex; gap: 4px; align-items: center;">
                        {{ $modules->appends(['filter' => $filter, 'sort' => $sort])->links('vendor.pagination.custom') }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Konfirmasi Hapus Modul
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus modul ini?',
                    text: "Data modul yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'swal2-custom-popup',
                        title: 'swal2-custom-title',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Tampilkan success alert jika ada session success
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                toast: true,
                position: 'top-end'
            });
        @endif
    });
</script>
@endpush
