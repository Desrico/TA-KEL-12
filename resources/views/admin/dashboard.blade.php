@extends('layouts.admin')

@section('page-title', 'Dashboard Konselor')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg-base:    #f8fafc;
            --bg-sidebar: #eefdf5;
            --bg-card:    #ffffff;
            --border:     #e2e8f0;
            --accent:     #059669;
            --accent-light: #d1fae5;
            --text-1:     #1e293b;
            --text-2:     #475569;
            --text-3:     #94a3b8;
            --green:      #059669;
            --green-light: #10b981;
            --red:        #dc2626;
            --red-dim:    #fef2f2;
            --red-border: #fca5a5;
            --amber:      #d97706;
            --blue:       #2563eb;
            --radius-lg:  16px;
            --radius-md:  10px;
            --radius-sm:  6px;
            --shadow-sm:  0 1px 3px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.05);
        }

        /* ── Layout Override for dashboard ── */
        .pc-container {
            background: var(--bg-base);
        }

        /* ── Alert Banner ── */
        .alert-banner {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px; margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }
        .alert-header {
            display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px;
        }
        .alert-title-wrap { display: flex; gap: 12px; }
        .alert-icon { color: var(--red); margin-top: 2px; }
        .alert-title { font-size: 1.15rem; font-weight: 700; color: var(--red); margin-bottom: 6px; }
        .alert-desc { font-size: 0.95rem; color: var(--text-2); line-height: 1.5; }
        .alert-badge {
            background: var(--red); color: white;
            padding: 8px 16px; border-radius: 999px;
            font-size: 0.85rem; font-weight: 700;
            white-space: nowrap;
        }

        /* ── Priority Cards ── */
        .priority-cards {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px; margin-bottom: 24px;
        }
        .p-card {
            background: var(--bg-card);
            border: 1px solid var(--red-border);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            text-decoration: none; color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .p-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .p-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .p-card-name { font-weight: 700; font-size: 1.1rem; color: var(--text-1); }
        .p-card-badge {
            background: var(--red-dim); color: var(--red);
            font-size: 0.75rem; font-weight: 800; padding: 4px 10px;
            border-radius: 6px; text-transform: uppercase;
        }
        .p-card-meta { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-3); margin-bottom: 8px; }
        .p-card-meta svg { width: 16px; height: 16px; }

        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 24px; border-radius: var(--radius-md);
            font-size: 0.95rem; font-weight: 700; font-family: inherit;
            border: none; background: var(--accent); color: white;
            cursor: pointer; transition: background 0.2s; text-decoration: none;
        }
        .btn-primary:hover { background: #047857; color: white; }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

        /* ── Charts & Stats Layout ── */
        .charts-stats-grid {
            display: grid; grid-template-columns: 2fr 1fr;
            gap: 24px; margin-bottom: 32px;
        }

        .chart-column { display: flex; flex-direction: column; gap: 24px; }
        
        .card-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px;
        }
        .card-title { font-size: 1.1rem; font-weight: 700; color: var(--text-1); }
        .card-subtitle { font-size: 0.9rem; color: var(--text-3); margin-top: 4px; }

        .filter-dropdown {
            padding: 8px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm);
            font-size: 0.9rem; color: var(--text-2); background: var(--bg-card); cursor: pointer;
            outline: none;
        }
        .btn-icon {
            padding: 6px; border: 1px solid var(--border); border-radius: var(--radius-sm);
            background: var(--bg-card); color: var(--text-2); cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
        }

        /* ── Dashboard Tabs ── */
       .dashboard-heading-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .dashboard-page-title {
            margin: 0;
            font-size: 3rem;
            font-weight: 800;
            color: #0f5132;
            line-height: 1.1;
        }

        .dashboard-tabs {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 8px;
            width: fit-content;
            box-shadow: var(--shadow-sm);
        }

        .dashboard-tab-btn {
            border: none;
            background: transparent;
            color: var(--text-2);
            font-size: 0.95rem;
            font-weight: 700;
            padding: 12px 22px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dashboard-tab-btn:hover {
            background: #ecfdf5;
            color: var(--accent);
        }

        .dashboard-tab-btn.active {
            background: var(--accent);
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2);
        }

        .dashboard-tab-content {
            display: none;
        }

        .dashboard-tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .dashboard-heading-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .dashboard-page-title {
                font-size: 2.2rem;
            }

            .dashboard-tabs {
                width: 100%;
                justify-content: space-between;
            }
        }

        .topik-wrapper {
            display: flex;
            align-items: center;
            gap: 18px;
            height: 220px;
        }

        .topik-chart-container {
            width: 45%;
            height: 100%;
        }

        .topik-legend-container {
            width: 55%;
        }

        .topik-legend-container ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .topik-legend-container li {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 9px;
            font-size: 12px;
            color: #475569;
        }

        .topik-legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── Stats Right Column ── */
        .stats-section-title { font-size: 0.85rem; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 18px; margin-top: 8px;}
        
        .progress-item { margin-bottom: 24px; }
        .progress-header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; }
        .progress-header span { font-size: 1.05rem; font-weight: 500; color: var(--text-1); }
        .progress-header strong { font-size: 1.15rem; font-weight: 800; color: var(--text-1); }
        .progress-bar-bg { width: 100%; height: 5px; background: #eef2f6; border-radius: 999px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 999px; transition: width 0.5s ease; }
        
        .feelings-list { display: flex; flex-direction: column; gap: 8px; }
        .feeling-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px; background: #f8fafc; border-radius: var(--radius-md);
        }
        .feeling-item.danger { background: var(--red-dim); }
        .feeling-info { display: flex; align-items: center; gap: 12px; font-size: 0.95rem; font-weight: 600; }
        .feeling-percent { font-size: 1rem; font-weight: 800; color: var(--text-1); }
        .feeling-item.danger .feeling-percent { color: var(--red); }

        /* ── Table Area ── */
        .table-area {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 40px;
        }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .btn-link { color: var(--accent); font-size: 0.95rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px; }
        .btn-link:hover { color: var(--accent); }
        
        .premium-table { width: 100%; border-collapse: collapse; text-align: left; }
        .premium-table th {
            padding: 14px 16px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;
            color: var(--text-3); border-bottom: 1px solid var(--border);
        }
        .premium-table td { padding: 18px 16px; font-size: 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .premium-table tr:last-child td { border-bottom: none; }
        .action-link { 
            display: inline-flex; align-items: center; justify-content: center;
            padding: 8px 20px; border-radius: var(--radius-sm);
            background: var(--accent-light); color: #064e3b;
            font-weight: 700; text-decoration: none; font-size: 0.9rem;
            transition: all 0.2s;
        }
        .action-link:hover { background: var(--accent); color: white; transform: translateY(-1px); }

        .spin {
            width: 14px; height: 14px; border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff;
            animation: spin 0.7s linear infinite; flex-shrink: 0;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Toast ── */
        #toast {
            position: fixed; bottom: 28px; right: 28px; z-index: 9999;
            display: none; align-items: center; gap: 10px;
            padding: 14px 24px; border-radius: var(--radius-md);
            background: var(--text-1); color: white;
            box-shadow: var(--shadow-md); font-size: 0.95rem;
            animation: slideInUp 0.3s ease;
        }
        #toast.show { display: flex; }
        @keyframes slideInUp { from { transform:translateY(16px);opacity:0 } to { transform:none;opacity:1 } }

        @media (max-width: 1024px) {
            .charts-stats-grid { grid-template-columns: 1fr; }
        }

        /* ── Print Styles ── */
        @media print {
            .pc-sidebar, .pc-header, .top-nav, .btn-outline, .btn-primary, .btn-icon, select, .alert-banner, .table-area, .filter-dropdown { display: none !important; }
            .pc-container { margin-left: 0 !important; padding: 0 !important; }
            body { background: white !important; font-size: 10pt; color: #000; }
            .card-box { box-shadow: none !important; border: 1px solid #eee !important; margin-bottom: 20px !important; }
            .charts-stats-grid { display: block !important; }
            .charts-stats-grid > div { width: 100% !important; page-break-inside: avoid; }
            .progress-bar-bg { background: #e2e8f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .progress-fill { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* ── Tab 2 Statistik Konseling ── */
        .konseling-analytics-wrap {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 24px;
        }

        .konseling-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 30px;
        }

        .konseling-stat-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 16px 18px;
            min-height: 75px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        /* today list placeholder removed from CSS */
        .today-card h3 {
            font-size: 32px;
            color: #0d6efd;
            font-weight: 800;
        }

        .konseling-chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .konseling-chart-card {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 18px;
            min-height: 300px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .konseling-chart-card h4 {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 700;
            color: #212529;
        }

        .konseling-chart-box {
            height: 220px;
            position: relative;
        }

        @media (max-width: 1200px) {
            .konseling-stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .today-stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .konseling-chart-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .konseling-stat-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin-bottom: 20px;
            }

            .today-stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .konseling-chart-card {
                min-height: 280px;
                padding: 16px;
            }

            .konseling-chart-box {
                height: 200px;
            }
        }

        @media (max-width: 576px) {
            .konseling-stat-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .today-stat-grid {
                grid-template-columns: 1fr;
            }

            .konseling-stat-card {
                padding: 14px 12px;
                min-height: 65px;
            }

            .konseling-stat-card h3 {
                font-size: 24px;
            }

            .konseling-stat-card p {
                font-size: 11px;
                margin-top: 4px;
            }

            .konseling-chart-card {
                min-height: 250px;
                padding: 14px;
            }

            .konseling-chart-box {
                height: 180px;
            }

            .konseling-chart-card h4 {
                font-size: 13px;
                margin-bottom: 10px;
            }
        }
        .btn-notification {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
        }
        .btn-notification:hover {
            background: #f8fafc;
            color: #1e293b;
        }
        .btn-notification.enabled {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .btn-report {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 12px 28px; border-radius: var(--radius-md);
            font-size: 0.95rem; font-weight: 700;
            background: linear-gradient(135deg, #059669, #047857);
            color: white; border: none; cursor: pointer;
            text-decoration: none; transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }
        .btn-report:hover {
            background: linear-gradient(135deg, #047857, #065f46);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
            color: white;
        }
        .btn-report i { font-size: 1.1rem; }

        .btn-risk-list {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 12px 28px; border-radius: var(--radius-md);
            font-size: 0.95rem; font-weight: 700;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white; border: none; cursor: pointer;
            text-decoration: none; transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }
        .btn-risk-list:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3);
            color: white;
        }
        .btn-risk-list i { font-size: 1.1rem; }
    </style>
@endpush

@section('konten')
    @php
        $scanned  = isset($students) ? $students->whereNotNull('mental_level') : collect();
        $countL3  = $scanned->where('mental_level', 3)->count();
        $topL3ForCards = $scanned->where('mental_level', 3)->sortBy('mental_scanned_at')->take(3);
    @endphp

    <div class="container-fluid p-0">
        <!-- Alert Banner -->
         <div class="dashboard-heading-row">

            <div class="dashboard-tabs">
                <button type="button"
                    class="dashboard-tab-btn active"
                    data-tab="tab-mobile"
                    onclick="activateDashboardTab(this, event)">
                    Data Mobile
                </button>

                <button type="button"
                    class="dashboard-tab-btn"
                    data-tab="tab-web"
                    onclick="activateDashboardTab(this, event)">
                    Statistik Konseling
                </button>
            </div>

        </div>
        <div id="tab-mobile" class="dashboard-tab-content active">
        <div class="alert-banner">
            <div class="alert-header">
                <div class="alert-title-wrap">
                    <div class="alert-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div>
                        <div class="alert-title">Status Risiko Tinggi Mahasiswa</div>
                        <div class="alert-desc">Mahasiswa yang memerlukan perhatian segera berdasarkan indikator emosional.</div>
                    </div>
                </div>
                <div class="alert-badge">{{ $countL3 }} Kasus Urgent</div>
            </div>

            @if($countL3 > 0)
            <div class="priority-cards">
                @foreach($topL3ForCards as $s)
                <a href="{{ route('counselor.detail', $s->nim) }}" class="p-card">
                    <div class="p-card-top">
                        <div class="p-card-name">{{ $s->name }}</div>
                        <div class="p-card-badge">PERLU PENANGANAN</div>
                    </div>
                    <div class="p-card-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        {{ $s->nim }}
                    </div>
                    <div class="p-card-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        +62 812-3456-7890
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div style="padding: 20px; text-align: center; color: var(--text-3);">
                ✅ Tidak ada kasus urgent saat ini.
            </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-top: 16px; border-top: 1px solid var(--border); padding-top: 20px;">
                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <button class="btn-primary" style="background: transparent; color: var(--text-2); border: 1px solid var(--border);" id="btnRefresh" onclick="runScan()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
                        Pindai Ulang
                    </button>
                </div>

                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <a href="{{ route('counselor.prioritas') }}" class="btn-risk-list">
                        <i class="ti ti-alert-triangle"></i>
                        Lihat Semua Risiko Tinggi
                    </a>

                    @if($countL3 > 0)
                    <a href="{{ route('counselor.prioritas') ?? '#' }}" class="btn-report">
                        <i class="ti ti-file-analytics"></i>
                        Buat Laporan Prioritas
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Charts & Stats Layout -->
        <div class="charts-stats-grid">
            <!-- Left: Charts -->
            <div class="chart-column" id="printChartArea">
                <!-- Mood Chart -->
                <div class="card-box">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Grafik Tren Mood Mahasiswa</div>
                            <div class="card-subtitle">Perkembangan psikologis kolektif mingguan</div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <select class="filter-dropdown" onchange="loadChartData(this.value)">
                                <option value="14d">14 Hari terakhir</option>
                                <option value="1m">1 Bulan terakhir</option>
                                <option value="4m">4 Bulan terakhir</option>
                            </select>
                            <button class="btn-icon" title="Ekspor PDF" onclick="window.print()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </button>
                        </div>
                    </div>
                    <div style="height: 250px; position: relative;">
                        <canvas id="moodTrendChart"></canvas>
                    </div>
                </div>

                <!-- Feelings Chart -->
                <div class="card-box">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Grafik Tren Suasana Perasaan</div>
                            <div class="card-subtitle">Distribusi emosi dari waktu ke waktu</div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <select class="filter-dropdown" onchange="loadChartData(this.value)">
                                <option value="14d">14 Hari terakhir</option>
                                <option value="1m">1 Bulan terakhir</option>
                                <option value="4m">4 Bulan terakhir</option>
                            </select>
                            <button class="btn-icon" title="Ekspor PDF" onclick="window.print()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </button>
                        </div>
                    </div>
                    <div style="height: 200px; position: relative;">
                        <canvas id="feelingsTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right: Stats -->
            <div class="card-box" id="emotionDistributionArea">
                <div class="card-header" style="margin-bottom: 20px;">
                    <div class="card-title">Rincian Statistik</div>
                </div>
                
                <div class="stats-section-title" style="margin-top: 24px;">DISTRIBUSI MOOD</div>
                
                <div id="moodDistContainer">
                    <!-- Progress items will be loaded here -->
                    <div class="py-4 text-center"><div class="spin mx-auto" style="border-top-color:var(--accent);"></div></div>
                </div>

                <div class="card-header" style="margin-top: 32px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
                    <div class="stats-section-title" style="margin: 0; white-space: nowrap; flex-shrink: 0;">PERASAAN UMUM</div>
                    <select class="filter-dropdown" id="feelingFilter" style="width: auto; max-width: 55%; text-overflow: ellipsis;" onchange="loadFeelingDistribution(this.value)">
                        <option value="all">Semua Perasaan</option>
                        <option value="CAT:Positif" style="font-weight: 700; color: #059669; background: #ecfdf5;">📊 SEMUA POSITIF</option>
                        <optgroup label="Positif">
                            <option value="Gembira">Gembira</option>
                            <option value="Bangga">Bangga</option>
                            <option value="Bersyukur">Bersyukur</option>
                            <option value="Ceria">Ceria</option>
                            <option value="Semangat">Semangat</option>
                            <option value="Energik">Energik</option>
                            <option value="Kagum">Kagum</option>
                            <option value="Bergairah">Bergairah</option>
                        </optgroup>
                        <option value="CAT:Netral" style="font-weight: 700; color: #64748b; background: #f8fafc;">📊 SEMUA NETRAL / STABIL</option>
                        <optgroup label="Netral / Stabil">
                            <option value="Biasa Saja">Biasa Saja</option>
                            <option value="Stabil">Stabil</option>
                            <option value="Tenang">Tenang</option>
                            <option value="Santai">Santai</option>
                        </optgroup>
                        <option value="CAT:Penasaran" style="font-weight: 700; color: #f59e0b; background: #fffbeb;">📊 SEMUA PENASARAN / TERKEJUT</option>
                        <optgroup label="Penasaran / Terkejut">
                            <option value="Tercengang">Tercengang</option>
                            <option value="Penasaran">Penasaran</option>
                            <option value="Tertarik">Tertarik</option>
                            <option value="Gelagapan">Gelagapan</option>
                        </optgroup>
                        <option value="CAT:Sedih" style="font-weight: 700; color: #3b82f6; background: #eff6ff;">📊 SEMUA SEDIH / PUTUS ASA</option>
                        <optgroup label="Sedih / Putus Asa">
                            <option value="Pilu">Pilu</option>
                            <option value="Depresi">Depresi</option>
                            <option value="Kesepian">Kesepian</option>
                            <option value="Putus Asa">Putus Asa</option>
                        </optgroup>
                        <option value="CAT:Cemas" style="font-weight: 700; color: #8b5cf6; background: #f5f3ff;">📊 SEMUA CEMAS / PANIK</option>
                        <optgroup label="Cemas / Panik">
                            <option value="Cemas">Cemas</option>
                            <option value="Khawatir">Khawatir</option>
                            <option value="Panik">Panik</option>
                            <option value="Gelisah">Gelisah</option>
                        </optgroup>
                        <option value="CAT:Kesal" style="font-weight: 700; color: #ef4444; background: #fef2f2;">📊 SEMUA KESAL / MARAH</option>
                        <optgroup label="Kesal / Marah">
                            <option value="Kesal">Kesal</option>
                            <option value="Jengkel">Jengkel</option>
                            <option value="Benci">Benci</option>
                            <option value="Kecewa">Kecewa</option>
                        </optgroup>
                    </select>
                </div>
                <div class="feelings-list" id="distList" style="max-height: 500px; overflow-y: auto; padding-right: 8px;">
                    <div class="py-4 text-center"><div class="spin mx-auto" style="border-top-color:var(--accent);"></div></div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-area">
            <div class="table-header">
                <div>
                    <div class="card-title">Pratinjau Direktori Mahasiswa</div>
                    <div class="card-subtitle">Profil mahasiswa aktif terbaru dan informasi akademik.</div>
                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    {{-- Dropdown Angkatan (dari MongoDB) --}}
                    <select id="filterAngkatan" onchange="loadTopStudents()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 500; outline: none; background: white; cursor: pointer; color: #475569; min-width: 130px;">
                        <option value="Semua">🎓 Semua Angkatan</option>
                        @foreach($angkatanList as $thn)
                            <option value="{{ $thn }}">Angkatan {{ $thn }}</option>
                        @endforeach
                    </select>

                    {{-- Dropdown Level 1: Fakultas --}}
                    <div style="position: relative;">
                        <select id="filterFakultas" onchange="onFakultasChange()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 500; outline: none; background: white; cursor: pointer; color: #475569; min-width: 160px;">
                            <option value="Semua">🏛️ Semua Fakultas</option>
                            <option value="FAK:Vokasi">Vokasi</option>
                            <option value="FAK:Informatika & Elektro">Informatika &amp; Elektro</option>
                            <option value="FAK:Bioteknologi">Bioteknologi</option>
                            <option value="FAK:Teknik Industri">Teknik Industri</option>
                        </select>
                    </div>

                    {{-- Dropdown Level 2: Prodi (muncul setelah fakultas dipilih) --}}
                    <div id="wrapProdiFilter" style="overflow: hidden; max-width: 0; opacity: 0; transition: max-width 0.35s ease, opacity 0.3s ease; white-space: nowrap;">
                        <select id="filterProdi" onchange="loadTopStudents()" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #059669; font-size: 0.85rem; font-weight: 500; outline: none; background: #f0fdf4; cursor: pointer; color: #065f46; min-width: 200px;">
                        </select>
                    </div>

                    <a href="{{ route('counselor.semua-mahasiswa') }}" class="btn-link">Lihat Selengkapnya <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
                </div>
            </div>
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PROGRAM STUDI</th>
                        <th>TINGKATAN</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody id="topStudentsBody">
                    <tr><td colspan="4" style="text-align:center; padding: 32px;"><div class="spin" style="margin:0 auto; border-top-color:var(--accent);"></div></td></tr>
                </tbody>
            </table>
        </div>

    </div>

        <div id="tab-web" class="dashboard-tab-content">
    <div class="konseling-analytics-wrap">

        <!-- Total Statistik Konseling - Paling Atas -->
        <div class="today-section-header">
            <h5>📊 Total Statistik Konseling</h5>
        </div>

        <div class="konseling-stat-grid">
            <div class="konseling-stat-card">
                <div class="stat-row">
                    <i class="ti ti-calendar-event"></i>
                    <h3 id="totalPenjadwalan">0</h3>
                </div>
                <p>Total Penjadwalan</p>
            </div>

            <div class="konseling-stat-card">
                <div class="stat-row">
                    <i class="ti ti-circle-check"></i>
                    <h3 id="totalSesiSelesai">0</h3>
                </div>
                <p>Total Sesi Selesai</p>
            </div>

            <div class="konseling-stat-card">
                <div class="stat-row">
                    <i class="ti ti-circle-check"></i>
                    <h3 id="totalDiterima">0</h3>
                </div>
                <p>Penjadwalan Diterima</p>
            </div>

            <div class="konseling-stat-card">
                <div class="stat-row">
                    <i class="ti ti-square-x"></i>
                    <h3 id="totalDitolak">0</h3>
                </div>
                <p>Penjadwalan Dibatalkan</p>
            </div>
        </div>

        <!-- Daftar Penjadwalan Hari Ini - Di Tengah Atas Grafik -->
        <div class="today-list" style="margin-bottom:24px;">
            @if(isset($todayJadwals) && $todayJadwals->count())
                @foreach($todayJadwals as $jadwal)
                @php
                    $namaMahasiswa = optional(optional($jadwal->mahasiswa)->user)->nama
                        ?? optional($jadwal->mahasiswa)->nama
                        ?? 'Mahasiswa';
                @endphp
                <div class="today-row" style="display:flex; align-items:center; justify-content:space-between; padding:12px; background:#fff; border:1px solid #e9ecef; border-radius:8px; margin-bottom:10px;">
                    <div style="display:flex; align-items:center; gap:12px; min-width:120px;">
                        <div class="avatar-circle" style="width:44px;height:44px;border-radius:50%;background:#e9f7ff;color:#0d6efd;display:flex;align-items:center;justify-content:center;font-weight:700;">
                            {{ $loop->iteration }}
                        </div>
                    </div>
                    <div style="flex:1; text-align:center;">
                        <div style="font-weight:700;color:#1f2937;font-size:15px;">{{ $namaMahasiswa }}</div>
                        <div style="font-size:12px;color:#6c757d;">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('j F Y') }} · {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }} WIB</div>
                    </div>
                    <div style="min-width:120px; display:flex; justify-content:flex-end;">
                        <a href="{{ route('admin.sesi') }}" style="background:#065F46;color:#fff;border:none;border-radius:10px;padding:.5rem 1rem;font-size:.78rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;min-width:66px;">Lihat</a>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center" style="padding:18px; color:#6c757d;">Tidak ada penjadwalan hari ini.</div>
            @endif
        </div>

        <!-- Charts - Di Bawah -->
        <div class="konseling-chart-grid">
            <div class="konseling-chart-card">
                <h4>Grafik Jumlah Konseling</h4>
                <div class="konseling-chart-box">
                    <canvas id="konselingChart"></canvas>
                </div>
            </div>

            <div class="konseling-chart-card">
                <h4>Topik Masalah</h4>

                <div class="topik-wrapper">
                    <div class="topik-chart-container">
                        <canvas id="topikChart"></canvas>
                    </div>

                    <div class="topik-legend-container">
                        <ul id="topikLegend"></ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

    <div id="toast"></div>
    
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function showToast(msg, isError = false) {
        const t = document.getElementById('toast');
        if(!t) return;
        t.innerHTML = msg;
        t.style.background = isError ? 'var(--red)' : 'var(--text-1)';
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function runScan() {
        const btn = document.getElementById('btnRefresh');
        btn.disabled = true;
        btn.innerHTML = '<div class="spin"></div> Memindai…';

        fetch('{{ route("counselor.scan") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            showToast('✅ ' + (data.message ?? 'Scan selesai!'));
            setTimeout(() => location.reload(), 1200);
        })
        .catch(err => {
            showToast('⚠️ Scan gagal: ' + err.message, true);
            btn.disabled = false;
            btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg> Pindai Ulang`;
        });
    }

    let moodChartInstance = null;
    function renderChart(labels, data) {
        const ctx = document.getElementById('moodTrendChart');
        if (!ctx) return;

        if (moodChartInstance) {
            moodChartInstance.data.labels = labels;
            moodChartInstance.data.datasets[0].data = data;
            moodChartInstance.update();
            return;
        }

        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(5, 150, 105, 0.2)');
        gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

        moodChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rata-rata Skor Mood',
                    data: data,
                    borderColor: '#059669',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: function(context) {
                        const val = context.raw;
                        const colors = {1: '#ef4444', 2: '#8b5cf6', 3: '#3b82f6', 4: '#f59e0b', 5: '#64748b', 6: '#10b981', 7: '#059669'};
                        return colors[val] || '#059669';
                    },
                    pointBorderColor: function(context) {
                        const val = context.raw;
                        const colors = {1: '#ef4444', 2: '#8b5cf6', 3: '#3b82f6', 4: '#f59e0b', 5: '#64748b', 6: '#10b981', 7: '#059669'};
                        return colors[val] || '#059669';
                    },
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        display: true,
                        min: 1,
                        max: 7,
                        ticks: {
                            callback: function(value) {
                                const labels = ['', 'Marah', 'Takut', 'Sedih', 'Terkejut', 'Netral', 'Antusias', 'Senang'];
                                return labels[value] || '';
                            },
                            stepSize: 1,
                            font: { size: 11, family: 'Inter' },
                            color: 'var(--text-2)'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        border: {
                            display: false
                        }
                    },
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11, family: 'Inter' },
                            color: 'var(--text-2)'
                        }
                    }
                }
            }
        });
    }
    
    let feelingsChartInstance = null;
    function renderFeelingsTrendChart(labels, seriesData) {
        const ctx = document.getElementById('feelingsTrendChart');
        if(!ctx) return;

        if (feelingsChartInstance) {
            feelingsChartInstance.data.labels = labels;
            feelingsChartInstance.data.datasets[0].data = seriesData;
            feelingsChartInstance.update();
            return;
        }
        
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        feelingsChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Trend Dominan',
                    data: seriesData,
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: function(context) {
                        const val = context.raw;
                        const colors = {1: '#ef4444', 2: '#f59e0b', 3: '#64748b', 4: '#10b981', 5: '#059669'};
                        return colors[val] || '#10b981';
                    },
                    pointBorderColor: function(context) {
                        const val = context.raw;
                        const colors = {1: '#ef4444', 2: '#f59e0b', 3: '#64748b', 4: '#10b981', 5: '#059669'};
                        return colors[val] || '#10b981';
                    },
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        display: true, 
                        suggestedMin: 1, 
                        suggestedMax: 5,
                        ticks: {
                            callback: function(value) {
                                if(value === 5) return 'Semangat';
                                if(value === 4) return 'Tenang';
                                if(value === 3) return 'Netral';
                                if(value === 2) return 'Lelah';
                                if(value === 1) return 'Cemas';
                                return '';
                            },
                            stepSize: 1,
                            font: { size: 10, family: 'Inter' },
                            color: 'var(--text-2)'
                        },
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        border: { display: false }
                    },
                    x: { 
                        display: true,
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11, family: 'Inter' }, color: 'var(--text-2)' }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    function loadChartData(range) {
        fetch(`{{ route('counselor.chart-data') }}?range=${range}`)
            .then(res => res.json())
            .then(data => {
                renderChart(data.labels, data.data);
                renderFeelingsTrendChart(data.labels, data.feelingsTrend);
                
                // Update mood distribution based on this fetched data
                if (data.mood_distribution) {
                    updateMoodDistribution(data.mood_distribution);
                }
                // Trigger initial feeling distribution load
                loadFeelingDistribution('all');
            })
            .catch(err => console.error('Gagal memuat data grafik:', err));
    }

    function updateMoodDistribution(dist) {
        const container = document.getElementById('moodDistContainer');
        if(!container) return;
        
        let html = '';
        const labels = ['Senang', 'Antusias', 'Netral', 'Terkejut', 'Sedih', 'Takut', 'Marah'];
        const colors = ['#059669', '#10b981', '#64748b', '#f59e0b', '#3b82f6', '#8b5cf6', '#ef4444'];
        
        labels.forEach((lbl, idx) => {
            const pct = dist[lbl] || 0;
            html += `
                <div class="progress-item">
                    <div class="progress-header">
                        <span>${lbl}</span>
                        <strong>${pct}%</strong>
                    </div>
                    <div class="progress-bar-bg"><div class="progress-fill" style="width: ${pct}%; background: ${colors[idx]};"></div></div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function loadFeelingDistribution(feelingName) {
        const list = document.getElementById('distList');
        if(!list) return;

        fetch(`{{ route('counselor.feeling-distribution') }}?name=${feelingName}`)
            .then(res => res.json())
            .then(data => {
                if(!data.items || data.items.length === 0) {
                    list.innerHTML = '<div class="py-4 text-center text-muted">Tidak ada data untuk perasaan ini.</div>';
                    return;
                }
                
                let html = '';
                const colorMap = {
                    'Cemas': '#ef4444',
                    'Lelah': '#f59e0b',
                    'Netral': '#64748b',
                    'Tenang': '#10b981',
                    'Semangat': '#059669'
                };
                data.items.forEach(item => {
                    const barColor = colorMap[item.name] || '#10b981';
                    html += `
                        <div class="progress-item">
                            <div class="progress-header">
                                <span>${item.name}</span>
                                <strong>${item.percentage}%</strong>
                            </div>
                            <div class="progress-bar-bg"><div class="progress-fill" style="width: ${item.percentage}%; background: ${barColor};"></div></div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            })
            .catch(err => {
                list.innerHTML = '<div class="py-4 text-center text-danger">Gagal memuat data distribusi.</div>';
            });
    }

    // Peta prodi per-Fakultas untuk cascading dropdown
    const fakultasProdiMap = {
        'FAK:Vokasi': [
            { value: 'Semua Vokasi', label: '📋 Semua Prodi Vokasi' },
            { value: 'Teknologi Rekayasa Perangkat Lunak', label: 'Teknologi Rekayasa Perangkat Lunak' },
            { value: 'Teknologi Informasi',               label: 'Teknologi Informasi' },
            { value: 'Teknologi Komputer',                label: 'Teknologi Komputer' },
        ],
        'FAK:Informatika & Elektro': [
            { value: 'Semua Informatika & Elektro', label: '📋 Semua Prodi Informatika & Elektro' },
            { value: 'Informatika',   label: 'Informatika' },
            { value: 'Teknik Elektro', label: 'Teknik Elektro' },
        ],
        'FAK:Bioteknologi': [
            { value: 'Semua Bioteknologi', label: '📋 Semua Prodi Bioteknologi' },
            { value: 'Bioproses',    label: 'Bioproses' },
            { value: 'Bioteknologi', label: 'Bioteknologi' },
        ],
        'FAK:Teknik Industri': [
            { value: 'Semua Teknik Industri', label: '📋 Semua Prodi Teknik Industri' },
            { value: 'Managemen Rekayasa', label: 'Managemen Rekayasa' },
            { value: 'Metalurgi',          label: 'Metalurgi' },
        ],
    };

    function onFakultasChange() {
        const fakSel   = document.getElementById('filterFakultas');
        const prodiSel = document.getElementById('filterProdi');
        const wrap     = document.getElementById('wrapProdiFilter');
        const fak      = fakSel ? fakSel.value : 'Semua';

        if (fak === 'Semua') {
            // Sembunyikan dropdown prodi
            wrap.style.maxWidth = '0';
            wrap.style.opacity  = '0';
            loadTopStudents();
            return;
        }

        // Isi opsi prodi sesuai fakultas yang dipilih
        const options = fakultasProdiMap[fak] || [];
        prodiSel.innerHTML = options.map(o =>
            `<option value="${o.value}">${o.label}</option>`
        ).join('');

        // Tampilkan dropdown prodi dengan animasi
        wrap.style.maxWidth = '300px';
        wrap.style.opacity  = '1';

        loadTopStudents();
    }

    function loadTopStudents() {
        const body      = document.getElementById('topStudentsBody');
        const fakSel    = document.getElementById('filterFakultas');
        const prodiSel  = document.getElementById('filterProdi');
        const angktSel  = document.getElementById('filterAngkatan');
        if(!body) return;

        body.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 32px;"><div class="spin" style="margin:0 auto; border-top-color:var(--accent);"></div></td></tr>';

        const fak     = fakSel    ? fakSel.value    : 'Semua';
        const prodi   = prodiSel  ? prodiSel.value  : '';
        const angkatan = angktSel ? angktSel.value  : 'Semua';

        // Tentukan parameter prodi yang dikirim ke backend
        let prodiParam;
        if (fak === 'Semua') {
            prodiParam = 'Semua';
        } else if (!prodi || prodi.startsWith('Semua ')) {
            // Pilih "Semua Prodi [Fakultas]" → kirim kode FAK:
            prodiParam = fak;
        } else {
            // Pilih prodi spesifik
            prodiParam = prodi;
        }

        const url = `{{ route("counselor.top-students") }}?prodi=${encodeURIComponent(prodiParam)}&angkatan=${encodeURIComponent(angkatan)}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if(!data.students || data.students.length === 0) {
                    body.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 32px;">Belum ada data mahasiswa aktif.</td></tr>';
                    return;
                }

                let html = '';
                data.students.forEach(s => {
                    html += `
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-1);">${s.name}</div>
                                <div style="font-size: 0.85rem; color: var(--text-3);">${s.nim}</div>
                            </td>
                            <td><div style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${s.prodi || '-'}">${s.prodi || '-'}</div></td>
                            <td>${s.angkatan || '-'}</td>
                            <td>
                                <a href="/konselor/detail/${s.nim}" class="action-link">Detail Profil</a>
                            </td>
                        </tr>
                    `;
                });
                body.innerHTML = html;
            })
            .catch(err => {
                body.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 32px; color: var(--red);">Gagal memuat data pratinjau mahasiswa.</td></tr>';
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        initDashboardTabs();
        loadChartData('14d');
        loadTopStudents();
    });

    let konselingChartInstance = null;
    let topikChartInstance = null;

    function activateDashboardTab(button, event) {
        if (event) {
            event.preventDefault();
        }

        const buttons = document.querySelectorAll('.dashboard-tab-btn');
        const contents = document.querySelectorAll('.dashboard-tab-content');

        buttons.forEach(btn => btn.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));

        button.classList.add('active');

        const target = document.getElementById(button.dataset.tab);
        if (target) {
            target.classList.add('active');
        }

       if (button.dataset.tab === 'tab-web') {
            setTimeout(() => {
                loadKonselingStatistics();
            }, 100);
        }
    }

    function initDashboardTabs() {
        const buttons = document.querySelectorAll('.dashboard-tab-btn');
        const contents = document.querySelectorAll('.dashboard-tab-content');

        console.log('initDashboardTabs called, buttons found:', buttons.length, 'contents found:', contents.length);

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                console.log('Tab clicked:', button.dataset.tab);
                activateDashboardTab(button);
            });
        });
    }

    function renderKonselingChart() {
        const ctx = document.getElementById('konselingChart');
        if (!ctx) return;

        const labels = @json($monthlyLabels ?? []);
        const data = @json($monthlyCounts ?? []);

        // Fallback empty data handling
        if (!labels || !labels.length || !data || !data.length) {
            ctx.parentElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #94a3b8; font-size: 14px;">Belum ada data penjadwalan konseling</div>';
            return;
        }

        if (konselingChartInstance) {
            konselingChartInstance.destroy();
        }

        const maxValue = Math.max(...data, 1);
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(5, 150, 105, 0.3)');
        gradient.addColorStop(1, 'rgba(5, 150, 105, 0.01)');

        konselingChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Konseling',
                    data: data,
                    borderColor: '#059669',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#059669',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBorderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    segment: {
                        borderColor: ctx => '#059669',
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: { 
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                        displayColors: true,
                        borderColor: '#059669',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Konseling: ' + context.parsed.y + ' sesi';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: Math.ceil(maxValue * 1.2),
                        ticks: {
                            precision: 0,
                            font: { size: 11, family: 'Inter' },
                            color: '#475569',
                            padding: 12
                        },
                        grid: { 
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: { 
                            font: { size: 11, family: 'Inter' },
                            color: '#475569',
                            padding: 8
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

   function renderTopikChart() {
        const ctx = document.getElementById('topikChart');
        if (!ctx) return;

        const labels = @json($topikLabels ?? []);
        const data = @json($topikCounts ?? []);
        const total = data.reduce((a, b) => a + b, 0);

        if (!labels || !labels.length || !data || !data.length) {
            ctx.parentElement.innerHTML =
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#6c757d;font-size:13px;">Belum ada data topik penjadwalan</div>';
            return;
        }

        if (topikChartInstance) {
            topikChartInstance.destroy();
        }

        const colors = [
            '#0d6efd',
            '#6f42c1',
            '#20c997',
            '#fd7e14',
            '#dc3545',
            '#198754',
            '#0dcaf0',
            '#6c757d',
            '#212529'
        ];

        topikChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                layout: {
                    padding: 0
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#212529',
                            padding: 12,
                            boxWidth: 14,
                            boxHeight: 14,
                            generateLabels: function(chart) {
                                const data = chart.data;

                                return data.labels.map((label, i) => ({
                                    text: label + ' (' + data.datasets[0].data[i] + ')',
                                    fillStyle: colors[i % colors.length],
                                    hidden: false,
                                    index: i
                                }));
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(33, 37, 41, 0.9)',
                        padding: 10,
                        titleFont: {
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 11
                        },
                        cornerRadius: 4,
                        borderColor: '#dee2e6',
                        borderWidth: 1,
                        callbacks: {
                            title: function(tooltipItems) {
                                return tooltipItems[0].label;
                            },
                            label: function(context) {
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(1);

                                return 'Total: ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    async function loadKonselingStatistics() {
        try {
            const response = await fetch('{{ route("counselor.web.jadwal-data") }}');
            const data = await response.json();

            // CARD STATISTIK
            document.getElementById('totalPenjadwalan').innerText =
                data.total_count ?? 0;

            document.getElementById('totalSesiSelesai').innerText =
                data.status_counts?.selesai ?? 0;

            document.getElementById('totalDiterima').innerText =
                data.status_counts?.diterima ?? 0;

            document.getElementById('totalDitolak').innerText =
                data.status_counts?.ditolak ?? 0;

            // CHART KONSELING
            renderKonselingChartFromApi(
                data.trend_data?.labels ?? [],
                data.trend_data?.data ?? []
            );

            // DONUT TOPIK
            renderTopikChartFromApi(
                Object.keys(data.problem_distribution ?? {}),
                Object.values(data.problem_distribution ?? {})
            );

        } catch (error) {
            console.error('Gagal memuat statistik konseling:', error);
        }
    }

    function renderKonselingChartFromApi(labels, data) {
        const ctx = document.getElementById('konselingChart');
        if (!ctx) return;

        if (konselingChartInstance) {
            konselingChartInstance.destroy();
        }

        konselingChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Konseling',
                    data: data,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.15)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#059669',
                    pointBorderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
   function renderTopikChartFromApi(labels, data) {
        const ctx = document.getElementById('topikChart');
        if (!ctx) return;

        const colors = [
            '#3498db', '#ff6384', '#ff9f40', '#f4c542',
            '#4bc0c0', '#9966ff', '#2ecc71', '#95a5a6'
        ];

        const legend = document.getElementById('topikLegend');
        if (legend) {
            legend.innerHTML = labels.map((label, index) => `
                <li>
                    <span class="topik-legend-color" style="background:${colors[index % colors.length]}"></span>
                    <span>${label} (${data[index]})</span>
                </li>
            `).join('');
        }

        if (topikChartInstance) {
            topikChartInstance.destroy();
        }

        topikChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: labels.map((_, i) => colors[i % colors.length]),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

</script>
@endpush
