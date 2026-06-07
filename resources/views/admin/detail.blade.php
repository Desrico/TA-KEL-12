@extends('layouts.admin')

@section('page-title', $student->name . ' – Detail Jurnal')

@push('styles')
    <style>
        * { box-sizing: border-box; }
        .pc-container {
            background: #f8fafc;
            max-width: 100%;
            overflow-x: hidden;
        }
        .container-fluid { padding: 32px; max-width: 100%; box-sizing: border-box; }

        .grid-layout { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 24px; margin-bottom: 24px; width: 100%; }
        @media (max-width: 1024px) { .grid-layout { grid-template-columns: 1fr; } }

        /* ── Profile Card ── */
        .profile-card {
            display: flex; align-items: flex-start; justify-content: space-between;
            background: #ffffff; border: 1px solid #e2e8f0;
            border-radius: 16px; padding: 24px 28px;
            margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .profile-left h1 { font-size: 1.8rem; font-weight: 800; color: #1e293b; margin-bottom: 16px; line-height: 1; }
        .profile-info-row { display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 24px; }
        .info-group { display: flex; align-items: center; gap: 12px; }
        .info-item { display: flex; flex-direction: column; gap: 2px; }
        .info-label { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .info-value { font-size: 0.9rem; font-weight: 700; color: #475569; }
        .v-divider { width: 1px; height: 28px; background: #e2e8f0; }

        .pill { font-size: 0.85rem; font-weight: 700; padding: 6px 12px; border-radius: 999px; white-space: nowrap; }
        .pill-L { background: #dbeafe; color: #2563eb; }
        .pill-P { background: #d1fae5; color: #059669; }

        /* ── Cards ── */
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .card-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 24px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 4px; }
        .stat-card .label { font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card .value { font-size: 1.5rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 6px; }

        /* ── Insight Card ── */
        .insight-card { background: #0f766e; color: #fff; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; height: 95%; justify-content: space-between; }
        .insight-card .header { display: flex; align-items: center; gap: 10px; font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; }
        .insight-card p { font-size: 1rem; line-height: 1.6; color: #ccfbf1; margin-bottom: 16px; flex-grow: 1; overflow-y: auto; max-height: 220px; padding-right: 4px; }
        /* Custom Scrollbar for Insight Text */
        .insight-card p::-webkit-scrollbar { width: 4px; }
        .insight-card p::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .insight-card p::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 10px; }
        .score-bar { background: rgba(255,255,255,0.2); height: 6px; border-radius: 999px; width: 100%; margin-top: 8px; overflow: hidden; }
        .score-fill { background: #fff; height: 100%; border-radius: 999px; transition: width 0.5s ease-out; }
        
        .btn-outline { background: #ffffff; border: 1px solid #e2e8f0; color: #475569; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
        .btn-outline:hover { background: #f8fafc; color: #0f766e; border-color: #059669; }
        .btn-white { background: #ffffff; color: #0f766e; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; cursor: pointer; transition: all 0.2s; }
        .btn-white:hover { background: #f8fafc; color: #0f766e;}

        /* ── Print Styles ── */
        @media print {
            .sidebar, .top-nav, .btn-outline, .btn-white, .action-link, #statusSelect, button, .modal-backdrop, .back-link, .risk-alert div:last-child { display: none !important; }
            .container-fluid { padding: 0 !important; margin: 0 !important; }
            body { background: white !important; font-size: 10pt; color: #000; }
            .card, .table-area, .profile-card, .risk-alert { box-shadow: none !important; border: 1px solid #eee !important; margin-bottom: 20px !important; width: 100% !important; }
            .insight-card { background: #f0fdf4 !important; color: #166534 !important; border: 1px solid #bbf7d0 !important; height: auto !important; }
            .insight-card p { color: #166534 !important; max-height: none !important; overflow: visible !important; }
            .premium-table th { background: #f8fafc !important; color: #1e293b !important; border-bottom: 1px solid #eee !important; }
            .grid-layout { display: block !important; }
            .grid-layout > div { width: 100% !important; margin-bottom: 20px; }
            .print-only { display: block !important; }
            .no-print { display: none !important; }
        }
        .print-only { display: none; }

        @media (max-width: 1024px) {
            .profile-info-row { flex-direction: column; align-items: flex-start; gap: 16px; }
            .profile-info-row > div { flex-wrap: wrap; gap: 12px !important; }
            .v-divider { display: none; }
            .stats-grid { grid-template-columns: 1fr; }
            .table-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .card-title { flex-wrap: wrap; }
            .card-title > div { margin-left: 0 !important; width: 100%; justify-content: flex-start; }
            .custom-modal-body { padding: 20px 10px; }
            .profile-card { padding: 20px 16px !important; }
            .container-fluid { padding: 16px; }
            .btn-outline, .btn-white { padding: 8px 12px; font-size: 0.8rem; }
        }

        /* ── Table Area ── */
        .table-area { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px;margin-top: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); max-width: 100%; box-sizing: border-box; }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        
        .premium-table { width: 100%; border-collapse: collapse; text-align: left; }
        .premium-table th {
            padding: 14px 16px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase;
            color: #94a3b8; border-bottom: 1px solid #e2e8f0;
        }
        .premium-table td { padding: 18px 16px; font-size: 1rem; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .premium-table tr:last-child td { border-bottom: none; }
        
        .btn-outline {
            padding: 10px 18px; border: 1px solid #e2e8f0; border-radius: 6px;
            background: #fff; color: #475569; font-size: 0.95rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s;
        }
        .btn-outline:hover { border-color: #059669; color: #059669; }

        .action-link { 
            display: inline-flex; align-items: center; justify-content: center;
            padding: 8px 16px; border-radius: 6px;
            background: #d1fae5; color: #059669;
            font-weight: 700; text-decoration: none; font-size: 0.9rem;
            transition: all 0.2s;
        }
        .action-link:hover { background: #059669; color: white; transform: translateY(-1px); }

        .no-journals {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 80px 40px; text-align: center; color: #94a3b8;
        }
        .no-journals svg { width: 120px; height: 120px; margin-bottom: 24px; opacity: 0.05; color: #1e293b; }
        .no-journals p { font-size: 1.1rem; font-weight: 500; }

        /* ── Badges & Alerts ── */
        .risk-alert { background: #fef2f2; border: 1px solid #fca5a5; border-radius: 16px; padding: 24px 28px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 16px; }
        .risk-alert .title { color: #dc2626; font-weight: 800; font-size: 1.1rem; margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }
        
        .btn-back-link { display: inline-flex; align-items: center; gap: 6px; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; padding: 8px 12px; border-radius: 8px; margin-bottom: 16px;}
        .btn-back-link:hover { background: #ffffff; color: #059669; }
        
        /* ── Journal Timeline UI ── */
        .journal-item {
            background: #fdfdfd;
            border: 1px solid #edf2f7;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }
        .journal-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            background: #fff;
        }
        .journal-time {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .journal-content {
            color: #334155;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .ai-insight-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 14px 18px;
            margin-top: 12px;
            position: relative;
            overflow: hidden;
        }
        .ai-insight-box::before {
            content: 'AI';
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 3rem;
            font-weight: 900;
            color: rgba(5, 150, 105, 0.05);
            font-style: italic;
        }
        .mood-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
        }
        .feeling-tag {
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 600;
            margin-top: 4px;
            margin-left: 4px;
        }
        
        .premium-table thead th {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 16px;
        }
        .premium-table tbody td {
            padding: 24px 16px;
        }

        .spin { width: 18px; height: 18px; border: 2px solid rgba(255, 255, 255, 0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Detail Preview Modal (PDF Export) ── */
        .custom-modal-backdrop { display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.65); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 20px; }
        .custom-modal-backdrop.show { display: flex; }
        .custom-modal { background: #fff; border-radius: 16px; width: 100%; max-width: 1200px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .custom-modal-header { padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc; }
        .custom-modal-header h2 { font-size: 1.1rem; font-weight: 800; color: #1e293b; margin: 0; }
        .custom-modal-close-btn { background: none; border: none; font-size: 1.8rem; color: #94a3b8; cursor: pointer; line-height: 1; padding: 0; }
        .custom-modal-close-btn:hover { color: #dc2626; }
        .custom-modal-body { overflow: auto; background: #cbd5e1; display: flex; align-items: flex-start; justify-content: flex-start; padding: 40px 20px; }
        .custom-modal-footer { padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; background: #f8fafc; }
        .paper-preview-box { background: #fff; width: 1050px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); flex-shrink: 0; }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
@endpush

@section('konten')
    <div class="container-fluid" style="padding: 24px; width: 100%; max-width: 100%; box-sizing: border-box;">
        <a href="{{ route('counselor.dashboard') }}" class="btn-back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali ke Dashboard Mahasiswa
        </a>

        <!-- Profile Card -->
        <div class="profile-card" style="flex-direction: column; align-items: stretch; padding: 28px 32px;">
            <div class="profile-left">
                <h1>{{ $student->name }}</h1>
                <div class="profile-info-row">
                    <div style="display: flex; align-items: center; gap: 24px;">
                        <!-- Gender & NIM -->
                        <div class="info-group">
                            <span class="pill {{ $student->jenis_kelamin === 'Laki-laki' ? 'pill-L' : 'pill-P' }}" style="font-size: 0.7rem; padding: 4px 10px;">
                                {{ $student->jenis_kelamin }}
                            </span>
                            <div class="info-item">
                                <span class="info-label">Identitas</span>
                                <span class="info-value">NIM: {{ $student->nim }}</span>
                            </div>
                        </div>

                        <div class="v-divider"></div>

                        <!-- Program Studi -->
                        <div class="info-item">
                            <span class="info-label">Program Studi</span>
                            <span class="info-value">{{ $student->prodi ?? 'Teknologi Rekayasa Perangkat Lunak' }}</span>
                        </div>

                        <div class="v-divider"></div>

                        <!-- Angkatan -->
                        <div class="info-item">
                            <span class="info-label">Tingkatan / Angkatan</span>
                            <span class="info-value">Tingkat {{ $student->angkatan }}</span>
                        </div>
                    </div>

                    <!-- Action Controls (Sejajar dengan Info) -->
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 16px; background: #f8fafc; padding: 8px 16px; border-radius: 12px; border: 1px solid #e2e8f0; flex-wrap: wrap;">
                            <div style="display: flex; flex-direction: column; gap: 2px;">
                                <span class="info-label" style="text-align: right;">Koreksi Status</span>
                                <select id="statusSelect" style="border: none; background: transparent; font-size: 0.85rem; font-weight: 700; color: #1e293b; outline: none; cursor: pointer; padding: 0;">
                                    <option value="0" {{ $student->mental_level == 0 ? 'selected' : '' }}>Level 0 (Positif)</option>
                                    <option value="1" {{ $student->mental_level == 1 ? 'selected' : '' }}>Level 1 (Ringan)</option>
                                    <option value="2" {{ $student->mental_level == 2 ? 'selected' : '' }}>Level 2 (Pantau)</option>
                                    <option value="3" {{ $student->mental_level == 3 ? 'selected' : '' }}>Level 3 (Krisis)</option>
                                </select>
                            </div>
                            <div class="v-divider" style="height: 20px;"></div>
                            <button onclick="updateStatus('{{ $student->nim }}', event)" style="background: #059669; color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px;">
                                Simpan
                            </button>
                        </div>

                        <!-- Tombol Kirim Notifikasi -->
                        <button onclick="openSendNotifModal()" style="background: #0f766e; color: white; border: none; padding: 12px 18px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                            Kirim Notifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if($student->mental_red_flag && $student->mental_level == 3)
        <div class="risk-alert">
            <div>
                <div class="title">
                    Temuan Risiko Kesehatan Mental
                    <span class="pill" style="background: #dc2626; color: white; font-size: 0.8rem;">Perlu penanganan</span>
                </div>
                <div style="color: #1e293b; font-size: 0.9rem; line-height: 1.5;">
                    {{ $student->mental_red_flag }}
                </div>
                <div style="margin-top: 10px; font-size: 0.85rem; color: #94a3b8; font-weight: 500;">
                    *Alasan ini dideteksi otomatis berdasarkan pola Jurnal dan Tren Mood mahasiswa selama 14 hari terakhir.
                </div>
            </div>
        </div>
        @endif

        <div class="grid-layout">
            <!-- Left Column -->
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div class="card" style="padding: 12px 20px;">
                    <div class="card-title">
                        Grafik Tren Mood Mahasiswa
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <select id="chartRange" onchange="updateCharts(this.value)" style="background: #f8fafc; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: 1px solid #e2e8f0; color: #475569; outline: none; cursor: pointer;">
                                <option value="14">14 Hari Terakhir</option>
                                <option value="30">30 Hari Terakhir</option>
                                <option value="90">3 Bulan Terakhir</option>
                                <option value="all">Semua Riwayat</option>
                            </select>
                            <button onclick="window.print()" class="btn-outline" style="padding: 6px 12px; font-size: 0.75rem; display: flex; align-items: center; gap: 5px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                Ekspor PDF
                            </button>
                        </div>
                    </div>
                    <div style="height: 200px; position: relative; width: 100%;">
                        <canvas id="detailMoodChart"></canvas>
                    </div>
                </div>

                <div class="card" style="padding: 12px 20px;">
                    <div class="card-title">
                        Tren Perasaan
                            <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;" id="feelingRangeText">14 Hari Terakhir</span>
                    </div>
                    <div style="height: 200px; position: relative; width: 100%;">
                        <canvas id="detailFeelingChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Right Column (Insight Analisis Jurnal & Stats) -->
            <div>
                <div class="insight-card">
                    <div>
                        <div class="header">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">✨</div>
                            Insight Analisis Jurnal
                        </div>
                        <p id="insightText">
                            @if($student->mental_insight)
                                {{ $student->mental_insight }}
                            @else
                                Insight otomatis dari jurnal mahasiswa belum digenerate. Klik tombol Ringkas Jurnal di bawah untuk menggunakan AI merangkum kondisi psikologis mahasiswa ini berdasarkan seluruh riwayat jurnalnya.
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">
                            <span>Tingkat Kepastian AI</span>
                            <span>{{ $student->mental_confidence ?? 0 }}%</span>
                        </div>
                        <div class="score-bar">
                            <div class="score-fill" style="width: {{ $student->mental_confidence ?? 0 }}%;"></div>
                        </div>
                        <div style="font-size: 0.75rem; color: #ccfbf1; margin-top: 12px; line-height: 1.4; opacity: 0.9;">
                            <span style="font-weight: 700;">Apa maksud angka ini?</span><br>
                            Semakin tinggi persentase, semakin kuat pola emosi yang ditemukan AI. Jika di bawah 70%, disarankan konselor melakukan validasi lebih mendalam karena data mungkin bersifat ambigu.
                        </div>

                        @if($student->journalTexts->count() > 0)
                        <button class="btn-white" style="margin-top: 20px;" id="btnSummary" onclick="openSummary('{{ $student->nim }}')">
                            {{ $student->mental_insight ? '🔄 Perbarui Analisis' : '📄 Ringkas Jurnal' }}
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid (Horizontal at Bottom) -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Jurnal</div>
                <div class="value">📄 {{ $student->journalTexts->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Cek-in Harian</div>
                <div class="value" style="color: #059669;">📅 {{ $student->dailyCheckins->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Pembaruan Terakhir</div>
                <div class="value" style="font-size: 1.1rem; color: #475569;">
                    ⏱️ 
                    @if($student->journalTexts->first())
                        {{ $student->journalTexts->first()->created_at->isoFormat('DD MMM YYYY') }}
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        <!-- Journal Table -->
        <div class="table-area">
            <div class="print-only" style="margin-bottom: 20px; border-bottom: 2px solid #1e293b; padding-bottom: 10px;">
                <h1 style="font-size: 1.5rem; margin-bottom: 4px;">Laporan Monitoring Kesehatan Mental Mahasiswa</h1>
                <p style="font-size: 0.9rem; color: #64748b;">Dicetak pada: {{ now()->isoFormat('DD MMMM YYYY, HH:mm') }}</p>
            </div>

            <div class="table-header">
                <span style="font-weight: 600; font-size: 1rem; color: #1e293b;">Riwayat Log Jurnal</span>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;" class="no-print">
                    <!-- Tombol Urutkan (Ikon Saja) -->
                    <button onclick="toggleSort()" class="btn-outline" style="padding: 8px 10px; transition: transform 0.3s ease;" title="Urutkan Waktu" id="sortButton">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="6" x2="3" y2="6"></line><line x1="17" y1="12" x2="7" y2="12"></line><line x1="14" y1="18" x2="10" y2="18"></line></svg>
                    </button>
                    
                    <!-- Tombol Ekspor PDF (Fungsional via Browser Print) -->
                    <button onclick="window.print()" class="btn-outline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Ekspor PDF
                    </button>
                </div>
            </div>

            @if($sortedLogs->isEmpty())
                <div class="no-journals">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <p>Mahasiswa ini belum memiliki riwayat aktivitas.</p>
                </div>
            @else
                <div style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; max-width: 100%;">
                    <table class="premium-table" style="min-width: 800px;">
                        <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th style="width: 160px;">Tanggal & Waktu</th>
                            <th style="width: 180px;">Mood dan Perasaan</th>
                            <th>Isi Jurnal & Analisis AI</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sortedLogs as $log)
                        @php
                            $journals = $log['journals'];
                            $checkin = $log['checkin'];
                            $createdAt = $log['created_at'];
                            
                            // Helper Mood Styling
                            $moodName = $checkin?->mood?->mood_name ?? 'Tidak ada';
                            $moodStyle = match($moodName) {
                                'Bahagia', 'Senang', 'Gembira' => 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;',
                                'Sedih', 'Pilu', 'Depresi' => 'background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;',
                                'Marah', 'Kesal' => 'background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;',
                                'Cemas', 'Khawatir', 'Gelisah' => 'background: #fffbeb; color: #92400e; border: 1px solid #fef3c7;',
                                default => 'background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;'
                            };
                        @endphp
                        <tr>
                            <td style="text-align: center; vertical-align: top; color: #94a3b8; font-weight: 700;">{{ $loop->iteration }}</td>
                            <td data-timestamp="{{ $createdAt->timestamp }}" style="vertical-align: top;">
                                <div style="font-weight: 800; color: #1e293b; font-size: 1.05rem; letter-spacing: -0.02em;">{{ $createdAt->isoFormat('DD MMM YYYY') }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600; margin-top: 4px;">{{ $createdAt->format('l') }}</div>
                            </td>
                            <td style="vertical-align: top;">
                                <!-- Label Mood -->
                                <div style="font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Mood:</div>
                                <div class="mood-badge" style="{{ $moodStyle }}; margin-bottom: 12px; width: fit-content;">
                                    {{ $moodName }}
                                </div>

                                <!-- Label Perasaan -->
                                @if($checkin?->feeling)
                                    <div style="font-size: 0.65rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Perasaan:</div>
                                    <div class="mood-badge" style="background: #ffffff; color: #64748b; border: 1px solid #e2e8f0; width: fit-content;">
                                        {{ $checkin->feeling->feeling_name }}
                                    </div>
                                @endif
                            </td>
                            <td style="vertical-align: top; padding-right: 32px;">
                                @if($journals && $journals->count() > 0)
                                    @foreach($journals->sortByDesc('created_at') as $j)
                                        <div class="journal-item">
                                            <div class="journal-time">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                {{ $j->created_at->format('H:i') }} WIB
                                            </div>
                                            <div class="journal-content">
                                                {{ $j->description }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div style="padding: 16px; background: #f8fafc; border-radius: 12px; border: 1px dashed #e2e8f0; color: #94a3b8; font-size: 0.85rem; font-style: italic;">
                                        Tidak ada catatan jurnal untuk hari ini
                                    </div>
                                @endif

                                @if($checkin && $checkin->aiAnalysis)
                                    <div class="ai-insight-box">
                                        <div style="font-size: 0.65rem; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                            Analisis AI Emora
                                        </div>
                                        <div style="font-size: 0.85rem; color: #065f46; line-height: 1.6; font-weight: 500;">
                                            "{{ $checkin->aiAnalysis->text_analysis }}"
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td style="vertical-align: top;">
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    @if($checkin && $checkin->aiAnalysis)
                                        <span style="display: inline-flex; align-items: center; gap: 4px; color: #059669; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            Analysed
                                        </span>
                                    @endif
                                    <a href="#" class="action-link" style="font-size: 0.8rem; width: 100%; justify-content: center;">Tinjau Detail</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Chart Preview Modal (Untuk Ekspor PDF) -->
    <div class="custom-modal-backdrop" id="detailPreviewModal" onclick="if(event.target.id==='detailPreviewModal') closeDetailPreview()">
        <div class="custom-modal">
            <div class="custom-modal-header">
                <h2 id="detailPreviewTitle">Pratinjau Laporan</h2>
                <button class="custom-modal-close-btn" onclick="closeDetailPreview()">&times;</button>
            </div>
            <div class="custom-modal-body">
                <div id="detailPreviewContent" class="paper-preview-box"></div>
            </div>
            <div class="custom-modal-footer">
                <button class="btn-outline" onclick="closeDetailPreview()">Batal</button>
                <button class="btn-white" style="background: #059669; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;" onclick="confirmDetailDownload()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Unduh PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Kirim Notifikasi ke Mahasiswa -->
    <div class="custom-modal-backdrop" id="sendNotifModal" onclick="if(event.target.id==='sendNotifModal') closeSendNotifModal()">
        <div class="custom-modal" style="max-width: 550px;">
            <div class="custom-modal-header" style="background: #0f766e; color: white;">
                <h2 style="color: white; margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    Kirim Notifikasi ke Mahasiswa
                </h2>
                <button class="custom-modal-close-btn" onclick="closeSendNotifModal()" style="color: white;">&times;</button>
            </div>
            <form id="sendNotifForm" onsubmit="submitNotification(event)">
                <div style="padding: 24px;">
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 700; font-size: 0.85rem; color: #475569; margin-bottom: 8px;">
                            Pesan Notifikasi
                        </label>
                        <textarea id="notifMessage" required rows="4" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-size: 0.9rem; outline: none; resize: vertical; box-sizing: border-box;" placeholder="Tulis pesan notifikasi untuk mahasiswa di sini..."></textarea>
                    </div>
                    <div style="font-size: 0.75rem; color: #0f766e; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px; line-height: 1.5; font-weight: 500;">
                        <strong>Informasi:</strong> Notifikasi ini akan langsung tersimpan di database MongoDB dan dapat diakses secara real-time oleh aplikasi mobile mahasiswa.
                    </div>
                </div>
                <div class="custom-modal-footer" style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px; background: #f8fafc;">
                    <button type="button" class="btn-outline" onclick="closeSendNotifModal()">Batal</button>
                    <button type="submit" class="btn-white" id="btnSubmitNotif" style="background: #0f766e; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; font-size: 0.9rem;">
                        Kirim Notifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const csrfToken = '{{ csrf_token() }}';

    function updateStatus(nim, event) {
        const level = document.getElementById('statusSelect').value;
        const btn = event.currentTarget;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        fetch(`/konselor/update-status/${nim}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ mental_level: level })
        })
        .then(res => res.json())
        .then(data => {
            alert('✅ ' + data.message);
            location.reload();
        })
        .catch(err => {
            alert('⚠️ Gagal memperbarui status.');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    function openSummary(nim) {
        const insightEl = document.getElementById('insightText');
        const btn = document.getElementById('btnSummary');
        if (!insightEl) return;

        const originalHtml = btn ? btn.innerHTML : 'Ringkas Jurnal';
        insightEl.innerHTML = '<div style="display:flex; align-items:center; gap:8px; color:rgba(255,255,255,0.8);"><div class="spin"></div> Menghubungkan ke AI...</div>';
        
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Memproses...';
        }

        fetch('{{ route("counselor.summary") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ nim })
        })
        .then(res => res.json())
        .then(data => {
            const text = data.summary ?? data.message ?? JSON.stringify(data);
            insightEl.style.opacity = '0';
            setTimeout(() => {
                insightEl.textContent = text;
                insightEl.style.transition = 'opacity 0.5s';
                insightEl.style.opacity = '1';
            }, 200);
        })
        .catch(() => {
            insightEl.innerHTML = '<span style="color:#fca5a5;">⚠️ Gagal mendapatkan ringkasan AI. Pastikan server Python berjalan.</span>';
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    }

    // Modal helpers (Preview PDF)
    function closeDetailPreview() {
        document.getElementById('detailPreviewModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    // Modal helpers (Kirim Notifikasi)
    function openSendNotifModal() {
        document.getElementById('sendNotifModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSendNotifModal() {
        document.getElementById('sendNotifModal').classList.remove('show');
        document.getElementById('notifMessage').value = '';
        document.body.style.overflow = '';
    }

    function submitNotification(event) {
        event.preventDefault();
        const msg = document.getElementById('notifMessage').value;
        const btn = document.getElementById('btnSubmitNotif');
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Mengirim...';

        fetch('/konselor/kirim-notifikasi/{{ $student->nim }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ pesan: msg })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('✅ ' + data.message);
                closeSendNotifModal();
            } else {
                alert('⚠️ ' + (data.message || 'Gagal mengirim notifikasi.'));
            }
        })
        .catch(err => {
            alert('⚠️ Gagal menghubungi server.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }

    // ── Jurnal Sorting ──
    let currentSortOrder = 'desc'; // Default newest first
    function toggleSort() {
        const tbody = document.querySelector('.premium-table tbody');
        if (!tbody) return;
        
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const button = document.getElementById('sortButton');
        
        currentSortOrder = currentSortOrder === 'desc' ? 'asc' : 'desc';
        
        rows.sort((a, b) => {
            const cellA = a.querySelector('[data-timestamp]');
            const cellB = b.querySelector('[data-timestamp]');
            if (!cellA || !cellB) return 0;
            const timeA = parseInt(cellA.dataset.timestamp);
            const timeB = parseInt(cellB.dataset.timestamp);
            return currentSortOrder === 'desc' ? timeB - timeA : timeA - timeB;
        });
        if (button) button.style.transform = currentSortOrder === 'asc' ? 'rotate(180deg)' : 'rotate(0deg)';
        rows.forEach(row => tbody.appendChild(row));
    }

    // ── Chart.js Setup ──
    const moodLevels = { 'Marah': 1, 'Takut': 2, 'Sedih': 3, 'Terkejut': 4, 'Netral': 5, 'Antusias': 6, 'Senang': 7 };
    const moodLblMap = ['', 'Marah', 'Takut', 'Sedih', 'Terkejut', 'Netral', 'Antusias', 'Senang'];
    const checkinsRaw = @json($student->dailyCheckins);
    
    let moodChart, feelingChart;

    function initCharts(days = 14) {
        const checkins = [...checkinsRaw].reverse();
        let filtered = checkins;
        
        const parseDate = (d) => {
            if (!d) return new Date();
            if (typeof d === 'string') return new Date(d);
            if (d.$date) return new Date(parseInt(d.$date.$numberLong || d.$date));
            return new Date(d);
        };

        if (days !== 'all') {
            const cutoffDate = new Date();
            cutoffDate.setDate(cutoffDate.getDate() - parseInt(days));
            filtered = checkins.filter(c => parseDate(c.created_at) >= cutoffDate);
        }
        
        // Mood Data
        const moodLabels = filtered.map(c => parseDate(c.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
        const moodData = filtered.map(c => moodLevels[c.mood?.mood_name] || 5);

        const moodCtx = document.getElementById('detailMoodChart');
        if (moodChart) moodChart.destroy();
        if (moodCtx && moodData.length > 0) {
            moodChart = new Chart(moodCtx, {
                type: 'line',
                data: { labels: moodLabels, datasets: [{ label: 'Mood', data: moodData, borderColor: '#059669', backgroundColor: 'rgba(5,150,105,0.1)', borderWidth: 2.5, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#059669', fill: true }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { min: 1, max: 7, ticks: { stepSize: 1, callback: v => moodLblMap[v] || '', font: { size: 10 } }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
            });
        } else if (moodCtx) {
            // Render empty chart
            moodChart = new Chart(moodCtx, {
                type: 'line',
                data: { labels: ['Tidak ada data'], datasets: [{ data: [] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { display: false }, x: { display: false } } }
            });
        }

        // Feeling Data
        const feelingFiltered = filtered.filter(c => c.feeling);
        const feelLabels = feelingFiltered.map(c => parseDate(c.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
        const feelingNames = feelingFiltered.map(c => c.feeling.feeling_name);

        const uniqueF = feelingNames.filter((v, i, s) => s.indexOf(v) === i);
        const feelData = feelingNames.map(label => uniqueF.indexOf(label) + 1);

        const feelCtx = document.getElementById('detailFeelingChart');
        if (feelingChart) feelingChart.destroy();
        if (feelCtx && feelData.length > 0) {
            feelingChart = new Chart(feelCtx, {
                type: 'line',
                data: { labels: feelLabels, datasets: [{ label: 'Perasaan', data: feelData, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', borderWidth: 2.5, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#10b981', fill: true }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => uniqueF[ctx.parsed.y - 1] || '' } } }, scales: { y: { min: 1, max: Math.max(uniqueF.length, 2), ticks: { stepSize: 1, callback: v => uniqueF[v - 1] || '', font: { size: 9 } }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
            });
        } else if (feelCtx) {
            // Render empty chart
            feelingChart = new Chart(feelCtx, {
                type: 'line',
                data: { labels: ['Tidak ada data'], datasets: [{ data: [] }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { display: false }, x: { display: false } } }
            });
        }
    }

    function updateCharts(range) {
        initCharts(range === 'all' ? 'all' : parseInt(range));
        
        // Update label text for feeling chart
        const rangeText = document.getElementById('chartRange').options[document.getElementById('chartRange').selectedIndex].text;
        document.getElementById('feelingRangeText').textContent = rangeText;
    }

    // Initial load
    initCharts(14);

    // ── Download Chart with Preview ──
    const getImgFromCanvas = (canvas) => {
        const tmp = document.createElement('canvas');
        tmp.width = canvas.width; tmp.height = canvas.height;
        const c = tmp.getContext('2d');
        c.fillStyle='#fff'; c.fillRect(0,0,tmp.width,tmp.height); c.drawImage(canvas,0,0);
        return tmp.toDataURL('image/jpeg',1.0);
    };

    let detailReportEl = null;

    async function downloadDetailChart(type) {
        const moodCanvas = document.getElementById('detailMoodChart');
        const feelCanvas = document.getElementById('detailFeelingChart');
        const moodImg    = moodCanvas ? getImgFromCanvas(moodCanvas) : null;
        const feelImg    = feelCanvas ? getImgFromCanvas(feelCanvas) : null;

        const totalJurnal  = '{{ $student->journalTexts->count() }}';
        const totalCheckin = '{{ $student->dailyCheckins->count() }}';
        const lastUpdate   = '{{ $student->journalTexts->first() ? $student->journalTexts->first()->created_at->isoFormat("DD MMM YYYY") : "-" }}';
        const studentName  = '{{ $student->name }}';
        const studentNim   = '{{ $student->nim }}';
        const mentalLabel  = '{{ $student->mental_label ?? "Belum dianalisis" }}';

        let chartSection='', title='', fileName='';
        if (type==='mood' && moodImg) {
            title='Laporan Tren Mood \u2013 '+studentName; fileName='Laporan_Mood_{{ $student->nim }}.pdf';
            chartSection=`<h3 style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:10px;">Grafik Tren Mood Mahasiswa</h3><div style="border:1px solid #e2e8f0;border-radius:12px;padding:14px;"><img src="${moodImg}" style="max-width:100%;display:block;"></div>`;
        } else if (type==='feeling' && feelImg) {
            title='Laporan Tren Perasaan \u2013 '+studentName; fileName='Laporan_Perasaan_{{ $student->nim }}.pdf';
            chartSection=`<h3 style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:10px;">Grafik Tren Perasaan</h3><div style="border:1px solid #e2e8f0;border-radius:12px;padding:14px;"><img src="${feelImg}" style="max-width:100%;display:block;"></div>`;
        }

        const wrapper = document.createElement('div');
        wrapper.style.cssText='padding:40px;background:#fff;width:1050px;font-family:Plus Jakarta Sans,sans-serif;';
        wrapper.innerHTML=`
            <div style="border-bottom:3px solid #059669;padding-bottom:18px;margin-bottom:28px;display:flex;justify-content:space-between;align-items:center;">
                <div><h1 style="font-size:22px;font-weight:800;color:#064E3B;margin:0;">${title}</h1><p style="font-size:13px;color:#64748b;margin:5px 0 0;">Campus Care \u2013 Monitoring Kesehatan Mental</p></div>
                <div style="text-align:right;font-size:12px;color:#64748b;"><strong style="color:#1e293b;">Dicetak:</strong><br>${new Date().toLocaleString('id-ID',{dateStyle:'long',timeStyle:'short'})}</div>
            </div>
            <div style="display:flex;gap:32px;align-items:flex-start;">
                <div style="flex:1.6;">${chartSection}</div>
                <div style="flex:1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:24px;">
                    <h3 style="font-size:15px;font-weight:700;color:#1e293b;border-bottom:1px solid #e2e8f0;padding-bottom:10px;margin-bottom:18px;">Profil Mahasiswa</h3>
                    <p style="font-size:14px;font-weight:700;color:#1e293b;margin:0 0 4px;">${studentName}</p>
                    <p style="font-size:12px;color:#64748b;margin:0 0 16px;">${studentNim}</p>
                    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:10px;">
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Status Klasifikasi</div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b;">${mentalLabel}</div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Total Jurnal</div><div style="font-size:22px;font-weight:800;color:#1e293b;">${totalJurnal}</div></div>
                        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Cek-in</div><div style="font-size:22px;font-weight:800;color:#059669;">${totalCheckin}</div></div>
                    </div>
                    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-top:10px;"><div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px;">Pembaruan Terakhir</div><div style="font-size:13px;font-weight:700;color:#475569;">${lastUpdate}</div></div>
                </div>
            </div>
            <div style="margin-top:36px;border-top:1px solid #eee;padding-top:12px;text-align:center;color:#94a3b8;font-size:10px;">Dokumen ini dihasilkan secara otomatis oleh Sistem Monitoring Campus Care.</div>`;

        wrapper.dataset.filename = fileName;
        detailReportEl = wrapper;
        document.getElementById('detailPreviewTitle').textContent = title;
        document.getElementById('detailPreviewContent').innerHTML = wrapper.innerHTML;
        document.getElementById('detailPreviewModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function confirmDetailDownload() {
        if (!detailReportEl) return;
        const opt = { margin:[10,10], filename:detailReportEl.dataset.filename||'Laporan.pdf', image:{type:'jpeg',quality:0.98}, html2canvas:{scale:2,useCORS:true,backgroundColor:'#fff'}, jsPDF:{unit:'mm',format:'a4',orientation:'landscape'} };
        html2pdf().set(opt).from(detailReportEl).save().then(()=>{ closeDetailPreview(); });
    }

    function closeDetailPreview() {
        document.getElementById('detailPreviewModal').classList.remove('show');
        document.body.style.overflow='';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeSummaryModal(); closeDetailPreview(); }
    });
</script>
@endpush
