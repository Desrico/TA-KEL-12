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
            background: var(--accent-light); color: var(--accent);
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
        <div class="alert-banner">
            <div class="alert-header">
                <div class="alert-title-wrap">
                    <div class="alert-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <div>
                        <div class="alert-title">Label Merah Mahasiswa</div>
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
                        <div class="p-card-badge">SIAGA TINGGI</div>
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

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button class="btn-primary" style="background: white; color: var(--text-1); border: 1px solid var(--border);" id="btnRefresh" onclick="runScan()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
                    Pindai Ulang
                </button>
                @if($countL3 > 0)
                <a href="{{ route('counselor.prioritas') ?? '#' }}" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Buat Laporan
                </a>
                @endif
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
                            <div class="card-title">Grafik Tren Suasana Hati</div>
                            <div class="card-subtitle">Perkembangan psikologis mingguan</div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <select class="filter-dropdown" onchange="loadChartData(this.value)">
                                <option value="14d">14 Hari terakhir</option>
                                <option value="1m">1 Bulan terakhir</option>
                                <option value="4m">4 Bulan terakhir</option>
                            </select>
                            <button class="btn-icon" onclick="printElementToPDF('printChartArea', 'Laporan_Mood_Trend.pdf')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            </button>
                        </div>
                    </div>
                    <div style="height: 200px; position: relative;">
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
                            <button class="btn-icon" onclick="printElementToPDF('feelingsTrendChart', 'Laporan_Feelings_Trend.pdf')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
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

                <div class="card-header" style="margin-top: 32px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                    <div class="stats-section-title" style="margin: 0;">PERASAAN UMUM</div>
                    <select class="filter-dropdown" id="feelingFilter" style="width: auto;" onchange="loadFeelingDistribution(this.value)">
                        <option value="all">Semua Perasaan</option>
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
                        <optgroup label="Netral / Stabil">
                            <option value="Biasa Saja">Biasa Saja</option>
                            <option value="Stabil">Stabil</option>
                            <option value="Tenang">Tenang</option>
                            <option value="Santai">Santai</option>
                        </optgroup>
                        <optgroup label="Penasaran / Terkejut">
                            <option value="Tercengang">Tercengang</option>
                            <option value="Penasaran">Penasaran</option>
                            <option value="Tertarik">Tertarik</option>
                            <option value="Gelagapan">Gelagapan</option>
                        </optgroup>
                        <optgroup label="Sedih / Putus Asa">
                            <option value="Pilu">Pilu</option>
                            <option value="Depresi">Depresi</option>
                            <option value="Kesepian">Kesepian</option>
                            <option value="Putus Asa">Putus Asa</option>
                        </optgroup>
                        <optgroup label="Cemas / Panik">
                            <option value="Cemas">Cemas</option>
                            <option value="Khawatir">Khawatir</option>
                            <option value="Panik">Panik</option>
                            <option value="Gelisah">Gelisah</option>
                        </optgroup>
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
                <a href="{{ route('counselor.semua-mahasiswa') }}" class="btn-link">Lihat Selengkapnya <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
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

    <div id="toast"></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function showToast(msg, isError = false) {
        const t = document.getElementById('toast');
        if(!t) return;
        t.innerHTML = msg;
        t.style.background = isError ? 'var(--red)' : 'var(--text-1)';
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function printElementToPDF(elementId, filename) {
        showToast('⏳ Menyusun dokumen PDF...');
        const element = document.getElementById(elementId);

        const opt = {
            margin:       10,
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            showToast('✅ Berhasil mengunduh PDF');
        }).catch(err => {
            console.error(err);
            showToast('⚠️ Gagal menyusun PDF', true);
        });
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
        if(!ctx) return;

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
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#059669',
                    pointRadius: 4,
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
                        min: 1, 
                        max: 5,
                        ticks: {
                            callback: function(value) {
                                if(value === 5) return '🤩 Senang (5)';
                                if(value === 4) return '🤫 Biasa (4)';
                                if(value === 3) return '😐 Netral (3)';
                                if(value === 2) return '😨 Takut (2)';
                                if(value === 1) return '🤬 Marah/Sedih (1)';
                                return null;
                            },
                            stepSize: 1,
                            font: { size: 11, family: 'Inter' },
                            color: 'var(--text-2)'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' },
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
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointRadius: 4,
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
                        min: 1, 
                        max: 5,
                        ticks: {
                            callback: function(value) {
                                if(value === 5) return 'Gembira';
                                if(value === 4) return 'Biasa';
                                if(value === 3) return 'Stabil';
                                if(value === 2) return 'Cemas';
                                if(value === 1) return 'Sedih';
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
                data.items.forEach(item => {
                    html += `
                        <div class="progress-item">
                            <div class="progress-header">
                                <span>${item.name}</span>
                                <strong>${item.percentage}%</strong>
                            </div>
                            <div class="progress-bar-bg"><div class="progress-fill" style="width: ${item.percentage}%; background: #10b981;"></div></div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            })
            .catch(err => {
                list.innerHTML = '<div class="py-4 text-center text-danger">Gagal memuat data distribusi.</div>';
            });
    }

    function loadTopStudents() {
        const body = document.getElementById('topStudentsBody');
        if(!body) return;

        fetch('{{ route("counselor.top-students") }}')
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
                            <td>${s.prodi || '-'}</td>
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
        loadChartData('14d');
        loadTopStudents();
    });
</script>
@endpush
