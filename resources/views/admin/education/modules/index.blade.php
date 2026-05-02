@extends('layouts.admin')

@section('page-title', 'Manajemen Modul')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<style>
    :root {
        --mi-bg: #f9fafb;
        --mi-card: #ffffff;
        --mi-border: #e5e7eb;
        --mi-green: #059669;
        --mi-green-light: #d1fae5;
        --mi-text-1: #111827;
        --mi-text-2: #6b7280;
        --mi-text-3: #9ca3af;
    }

    .pc-container { background: var(--mi-bg) !important; }

    .mi-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 24px 60px;
    }

    /* ---- Page header ---- */
    .mi-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 28px 0 24px;
    }
    .mi-header-left h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--mi-text-1);
        margin: 0 0 4px;
        letter-spacing: -0.02em;
    }
    .mi-header-left p {
        font-size: 0.82rem;
        color: var(--mi-text-2);
        margin: 0;
    }

    .mi-btn-add {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: var(--mi-green);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.18s, box-shadow 0.18s, transform 0.15s;
        box-shadow: 0 3px 10px rgba(5,150,105,0.25);
    }
    .mi-btn-add:hover { background: #047857; color: #fff; transform: translateY(-1px); box-shadow: 0 5px 14px rgba(5,150,105,0.3); }

    .mi-back {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: var(--mi-text-2);
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 0;
        transition: color 0.18s;
    }
    .mi-back:hover { color: var(--mi-green); }

    /* ---- Alert ---- */
    .mi-alert {
        padding: 12px 18px;
        border-radius: 10px;
        margin-bottom: 16px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .mi-alert.success { background: var(--mi-green-light); color: #065f46; border: 1px solid #a7f3d0; }

    /* ---- Table card ---- */
    .mi-card {
        background: var(--mi-card);
        border: 1.5px solid var(--mi-border);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }

    /* ---- Toolbar: tabs + sort ---- */
    .mi-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1.5px solid var(--mi-border);
        gap: 12px;
        flex-wrap: wrap;
    }

    .mi-tabs {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .mi-tab {
        display: inline-flex;
        align-items: center;
        padding: 7px 16px;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--mi-text-2);
        border: 1.5px solid transparent;
        transition: all 0.18s;
    }
    .mi-tab:hover { background: #f3f4f6; color: var(--mi-text-1); }
    .mi-tab.active {
        background: var(--mi-green);
        color: #fff;
        border-color: var(--mi-green);
    }

    .mi-sort {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--mi-text-2);
        font-weight: 500;
    }
    .mi-sort-select {
        border: 1.5px solid var(--mi-border);
        border-radius: 8px;
        padding: 6px 30px 6px 10px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--mi-text-1);
        background: #fff;
        outline: none;
        appearance: none;
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 9px center;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .mi-sort-select:focus { border-color: var(--mi-green); }

    /* ---- Table ---- */
    .mi-table { width: 100%; border-collapse: collapse; }

    .mi-table thead tr { background: #fafafa; }
    .mi-table th {
        padding: 11px 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--mi-text-3);
        text-align: left;
        border-bottom: 1.5px solid var(--mi-border);
    }
    .mi-table th:first-child { padding-left: 24px; }
    .mi-table th:last-child { padding-right: 24px; text-align: right; }

    .mi-table td {
        padding: 16px 20px;
        font-size: 0.875rem;
        color: var(--mi-text-1);
        border-bottom: 1.5px solid #f3f4f6;
        vertical-align: middle;
    }
    .mi-table td:first-child { padding-left: 24px; }
    .mi-table td:last-child { padding-right: 24px; }
    .mi-table tbody tr:last-child td { border-bottom: none; }
    .mi-table tbody tr { transition: background 0.15s; }
    .mi-table tbody tr:hover { background: #fafafa; }

    /* Module cell */
    .mi-module-cell { display: flex; align-items: center; gap: 14px; }
    .mi-thumb {
        width: 52px; height: 52px;
        border-radius: 10px;
        object-fit: cover;
        border: 1.5px solid var(--mi-border);
        flex-shrink: 0;
        background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        overflow: hidden;
    }
    .mi-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .mi-title { font-weight: 700; color: var(--mi-text-1); font-size: 0.92rem; margin-bottom: 3px; }
    .mi-desc { font-size: 0.78rem; color: var(--mi-text-3); line-height: 1.4; }

    /* Point badge */
    .mi-point {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--mi-green-light);
        color: var(--mi-green);
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
    }

    /* Status badge */
    .mi-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .mi-badge::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .mi-badge.aktif { background: #dcfce7; color: #15803d; }
    .mi-badge.aktif::before { background: #16a34a; }
    .mi-badge.draft { background: #f3f4f6; color: #6b7280; }
    .mi-badge.draft::before { background: #9ca3af; }

    /* Action buttons */
    .mi-actions { display: flex; gap: 6px; justify-content: flex-end; }
    .mi-btn-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        border: 1.5px solid var(--mi-border);
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        color: var(--mi-text-2);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.18s;
    }
    .mi-btn-icon:hover { border-color: var(--mi-green); color: var(--mi-green); background: #f0fdf4; }
    .mi-btn-icon.del:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* Empty state */
    .mi-empty { text-align: center; padding: 72px 24px; color: var(--mi-text-3); }
    .mi-empty .emoji { font-size: 2.8rem; margin-bottom: 14px; opacity: 0.5; }
    .mi-empty p { font-size: 0.9rem; font-weight: 600; margin: 0; }

    /* ---- Footer: count + pagination ---- */
    .mi-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 24px;
        border-top: 1.5px solid var(--mi-border);
        flex-wrap: wrap;
        gap: 10px;
    }
    .mi-count { font-size: 0.78rem; color: var(--mi-text-3); font-weight: 500; }

    .mi-pagination {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .mi-page-btn {
        width: 32px; height: 32px;
        border-radius: 8px;
        border: 1.5px solid var(--mi-border);
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--mi-text-2);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.18s;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .mi-page-btn:hover { border-color: var(--mi-green); color: var(--mi-green); }
    .mi-page-btn.active { background: var(--mi-green); color: #fff; border-color: var(--mi-green); }
    .mi-page-btn.disabled { opacity: 0.35; pointer-events: none; }
</style>
@endpush

@section('konten')
<div class="mi-wrap">

    {{-- Back + Header --}}
    <div style="padding-top:20px;">
        <a href="{{ route('counselor.education.index') }}" class="mi-back">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Edukasi & Intervensi
        </a>
    </div>

    <div class="mi-header">
        <div class="mi-header-left">
            <h1>Manajemen Modul</h1>
            <p>Kelola dan terbitkan konten edukasi psikologi untuk mahasiswa.</p>
        </div>
        <a href="{{ route('counselor.education.modules.create') }}" class="mi-btn-add">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Modul
        </a>
    </div>

    @if(session('success'))
        <div class="mi-alert success">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="mi-card">

        {{-- Toolbar --}}
        <div class="mi-toolbar">
            <div class="mi-tabs">
                <a href="{{ route('counselor.education.modules.index', ['filter' => 'semua', 'sort' => $sort]) }}"
                   class="mi-tab {{ $filter === 'semua' ? 'active' : '' }}">Semua Modul</a>
                <a href="{{ route('counselor.education.modules.index', ['filter' => 'aktif', 'sort' => $sort]) }}"
                   class="mi-tab {{ $filter === 'aktif' ? 'active' : '' }}">Aktif</a>
                <a href="{{ route('counselor.education.modules.index', ['filter' => 'draft', 'sort' => $sort]) }}"
                   class="mi-tab {{ $filter === 'draft' ? 'active' : '' }}">Draft</a>
            </div>

            <div class="mi-sort">
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

        {{-- Table --}}
        <table class="mi-table">
            <thead>
                <tr>
                    <th>Modul Edukasi</th>
                    <th style="width:130px;">Poin</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($modules as $m)
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
                                  onsubmit="return confirm('Hapus modul ini?')" style="display:contents;">
                                @csrf @method('DELETE')
                                <button type="submit" class="mi-btn-icon del" title="Hapus">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="mi-empty">
                            <div class="emoji">📭</div>
                            <p>Belum ada modul yang dibuat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer: count + pagination --}}
        <div class="mi-footer">
            <div class="mi-count">
                Menampilkan {{ $modules->firstItem() ?? 0 }}–{{ $modules->lastItem() ?? 0 }} dari {{ $modules->total() }} modul
            </div>

            @if($modules->hasPages())
            <div class="mi-pagination">
                {{-- Prev --}}
                @if($modules->onFirstPage())
                    <span class="mi-page-btn disabled">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                @else
                    <a href="{{ $modules->previousPageUrl() }}" class="mi-page-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif

                {{-- Page numbers --}}
                @foreach($modules->getUrlRange(1, $modules->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="mi-page-btn {{ $page == $modules->currentPage() ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Next --}}
                @if($modules->hasMorePages())
                    <a href="{{ $modules->nextPageUrl() }}" class="mi-page-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                @else
                    <span class="mi-page-btn disabled">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                @endif
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
