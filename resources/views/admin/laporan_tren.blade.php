<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Statistik & Tren Emosional Mahasiswa – Campus Care</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-screen: #f1f5f9;
            --bg-paper: #ffffff;
            --border-color: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent-success: #16a34a;
            --accent-success-soft: #f0fdf4;
            --accent-warning: #d97706;
            --accent-warning-soft: #fffbeb;
            --accent-danger: #dc2626;
            --accent-danger-soft: #fef2f2;
            
            --shadow-premium: 0 20px 40px rgba(15, 23, 42, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
            --radius-md: 12px;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-screen); 
            color: var(--text-primary); 
            min-height: 100vh;
            line-height: 1.5;
            padding-bottom: 60px;
        }

        /* ── Topbar (Screen Only) ── */
        .topbar {
            position: sticky; 
            top: 0; 
            z-index: 100;
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            padding: 0 40px; 
            height: 72px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.25s ease;
        }
        .btn-back:hover {
            background: #f8fafc;
            color: var(--text-primary);
            border-color: #cbd5e1;
            transform: translateX(-2px);
        }
        .topbar-logo { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            font-weight: 800; 
            font-size: 1.15rem; 
            text-decoration: none; 
            color: var(--text-primary); 
        }
        .logo-icon {
            width: 32px; 
            height: 32px; 
            border-radius: 8px;
            background: linear-gradient(135deg, #059669, #047857);
            display: flex; 
            align-items: center; 
            justify-content: center;
            color: #fff; 
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2);
            font-size: 0.95rem;
        }
        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            background: #0f172a;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }
        .btn-print:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
        }

        /* ── Container Laporan ── */
        .report-container {
            max-width: 900px;
            margin: 40px auto 0 auto;
            padding: 0 20px;
        }

        /* ── Kertas Laporan (A4 preview) ── */
        .report-paper {
            background: var(--bg-paper);
            padding: 50px 60px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-premium);
            border: 1px solid #e2e8f0;
            color: #000000;
        }

        /* ── Kop Surat ── */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 24px;
            border-bottom: 4px double #000000;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .kop-logo {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .kop-info {
            flex: 1;
            text-align: center;
        }
        .kop-info h4 {
            font-size: 0.8rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .kop-info h2 {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .kop-info h3 {
            font-size: 1rem;
            font-weight: 700;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .kop-info p {
            font-size: 0.72rem;
            margin: 0;
            color: #334155;
            line-height: 1.4;
        }

        /* ── Judul Laporan ── */
        .report-title-container {
            text-align: center;
            margin-bottom: 28px;
        }
        .report-title {
            font-size: 1.15rem;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 4px;
            letter-spacing: 0.02em;
        }
        .report-subtitle {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
        }

        /* ── Meta Table ── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
            font-size: 0.8rem;
        }
        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        /* ── Laporan Sections ── */
        .report-section {
            margin-bottom: 28px;
        }
        .section-title {
            font-size: 0.95rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-left: 4px solid #000;
            padding-left: 10px;
            letter-spacing: 0.02em;
        }
        .section-desc {
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 16px;
            text-align: justify;
            line-height: 1.5;
        }
        .sub-section-title {
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #1e293b;
        }

        /* ── Stats Summary Grid ── */
        .stats-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-summary-box {
            border: 1px solid #cbd5e1;
            padding: 14px 10px;
            border-radius: 8px;
            text-align: center;
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .stat-val {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 2px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        .stat-lbl {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-summary-box.accent-box {
            border-color: #a7f3d0;
            background-color: var(--accent-success-soft);
        }
        .stat-summary-box.accent-box .stat-val {
            color: var(--accent-success);
        }

        /* ── Chart Area in Paper ── */
        .report-chart-container {
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            background: #ffffff;
            margin-bottom: 20px;
            height: 280px;
            position: relative;
        }

        /* ── Tables ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.82rem;
        }
        .report-table th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            text-align: left;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.02em;
        }
        .report-table td {
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            vertical-align: middle;
            color: #1e293b;
        }
        .report-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .distribution-row {
            display: flex;
            gap: 20px;
            margin-top: 12px;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 6px;
            text-transform: uppercase;
            text-align: center;
        }
        .badge-danger {
            background-color: var(--accent-danger-soft);
            color: var(--accent-danger);
            border: 1px solid #fecaca;
        }
        .badge-warning {
            background-color: var(--accent-warning-soft);
            color: var(--accent-warning);
            border: 1px solid #fde047;
        }
        .badge-success {
            background-color: var(--accent-success-soft);
            color: var(--accent-success);
            border: 1px solid #bbf7d0;
        }

        /* ── Signature Section ── */
        .signature-section {
            margin-top: 48px;
        }
        .signature-container {
            display: flex;
            justify-content: space-between;
            gap: 40px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 0.85rem;
        }
        .signature-title {
            margin-bottom: 60px;
            color: #1e293b;
        }

        /* ── Toast ── */
        #toast {
            position: fixed; 
            bottom: 32px; 
            right: 32px; 
            z-index: 1000;
            display: none; 
            align-items: center; 
            gap: 12px;
            padding: 16px 24px; 
            border-radius: 12px;
            background: #1e293b; 
            color: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            font-size: 0.88rem; 
            font-weight: 600;
            animation: slideUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        #toast.show { display: flex; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* ── Empty State ── */
        .empty-state {
            text-align: center; 
            padding: 80px 40px;
            background: #fff; 
            border: 2px dashed #cbd5e1; 
            border-radius: var(--radius-md);
        }
        .empty-icon { font-size: 3.5rem; margin-bottom: 20px; display: block; }
        .empty-state h2 { font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin-bottom: 8px; }
        .empty-state p { color: var(--text-secondary); font-size: 0.95rem; }

        /* ── CSS khusus Cetak ── */
        @media print {
            body { 
                background: #ffffff !important; 
                padding: 0 !important; 
                margin: 0 !important;
            }
            .no-print { 
                display: none !important; 
            }
            .report-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .report-paper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                border-radius: 0 !important;
            }
            .stats-summary-grid {
                gap: 10px;
            }
            .stat-summary-box {
                border: 1px solid #000000 !important;
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .stat-summary-box.accent-box .stat-val {
                color: #000000 !important;
            }
            .report-chart-container {
                border: 1px solid #000000 !important;
            }
            .report-table th {
                border: 1px solid #000000 !important;
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .report-table td {
                border: 1px solid #000000 !important;
            }
            .badge {
                border: 1px solid #000000 !important;
                background: none !important;
                color: #000000 !important;
            }
            .report-section {
                page-break-inside: avoid;
            }
            .signature-section {
                page-break-inside: avoid;
            }
            table tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<!-- Topbar (Hanya Tampil di Layar) -->
<header class="topbar no-print">
    <div class="topbar-left">
        <a href="{{ route('admin.dashboard') }}" class="btn-back" title="Kembali ke Dashboard">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <div class="topbar-logo">
            <div class="logo-icon">🎓</div>
            <span>Campus Care</span>
        </div>
    </div>
    <div class="topbar-actions">
        <button class="btn-print" onclick="printElementToPDF('printTrendArea', 'Laporan_Statistik_Tren_Kesehatan_Mental_{{ $range }}_{{ now()->format('d_M_Y') }}.pdf')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Ekspor Laporan PDF
        </button>
    </div>
</header>

<!-- Main Wrapper -->
<main class="report-container">

    @if($totalCheckins === 0)
        <div class="empty-state" style="margin-top: 60px;">
            <span class="empty-icon">📊</span>
            <h2>Belum Ada Data Tren!</h2>
            <p>Tidak ditemukan rekaman data check-in mood mahasiswa untuk rentang waktu {{ $rangeName }}.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn-print" style="margin-top: 20px; display: inline-flex; text-decoration: none;">Kembali ke Dashboard</a>
        </div>
    @else
        <!-- Area Kertas Cetak -->
        <div id="printTrendArea" class="report-paper">
            

            <!-- Judul Dokumen -->
            <div class="report-title-container">
                <h1 class="report-title">Laporan Statistik & Tren Kesehatan Mental Mahasiswa</h1>
                <p class="report-subtitle">Analisis Data Check-in Suasana Hati dan Emosional Kolektif – Periode {{ $rangeName }}</p>
            </div>

            <!-- Meta Laporan -->
            <table class="meta-table">
                <tr>
                    <td style="width: 16%; font-weight: 700;">Nomor Laporan</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 32%;"></td>
                    <td style="width: 16%; font-weight: 700;">Tanggal Cetak</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 32%;">{{ now()->isoFormat('DD MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Unit Kerja</td>
                    <td>:</td>
                    <td>Bimbingan & Konseling Mahasiswa</td>
                    <td style="font-weight: 700;">Konselor Pencetak</td>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Rentang Analisis</td>
                    <td>:</td>
                    <td>{{ $rangeName }} (Data Historis Harian/Bulanan)</td>
                    <td style="font-weight: 700;">Sumber Data</td>
                    <td>:</td>
                    <td>Aplikasi Mobile (Check-in Harian Kolektif)</td>
                </tr>
            </table>

            <!-- Section I: Ringkasan Eksekutif -->
            <div class="report-section">
                <h3 class="section-title">I. Ringkasan Statistik</h3>
                <p class="section-desc">
                    Laporan statistik ini menyajikan ringkasan eksekutif dan analisis grafik terkait suasana hati (mood) kolektif mahasiswa yang terekam melalui fitur check-in harian pada aplikasi mobile. Data ini digunakan untuk mendeteksi fluktuasi kondisi psikologis kelompok mahasiswa pada rentang waktu tertentu, membantu konselor mengidentifikasi puncak tingkat kecemasan atau kelelahan mental kolektif (misalnya selama periode ujian), dan mengevaluasi efektivitas program dukungan emosional kampus.
                </p>
                
                @php
                    $avgMoodKolektif = count($chartData) > 0 ? round(array_sum($chartData) / count($chartData), 2) : 0;
                    $topEmotion = count($distribution) > 0 ? $distribution[0]['name'] : '-';
                    
                    if ($avgMoodKolektif < 3.5) {
                        $statusEvaluasi = "Rendah (Stres Kolektif)";
                        $evalClass = "accent-box"; // We can reuse styles
                    } elseif ($avgMoodKolektif < 5.0) {
                        $statusEvaluasi = "Cukup / Stabil";
                        $evalClass = "accent-box";
                    } else {
                        $statusEvaluasi = "Sangat Baik (Positif)";
                        $evalClass = "accent-box";
                    }
                @endphp

                <div class="stats-summary-grid">
                    <div class="stat-summary-box">
                        <div class="stat-val">{{ $totalCheckins }}</div>
                        <div class="stat-lbl">Total Input Check-in</div>
                    </div>
                    <div class="stat-summary-box accent-box">
                        <div class="stat-val">{{ $avgMoodKolektif }} / 7.0</div>
                        <div class="stat-lbl">Rata-rata Mood</div>
                    </div>
                    <div class="stat-summary-box">
                        <div class="stat-val">{{ $topEmotion }}</div>
                        <div class="stat-lbl">Emosi Terbanyak</div>
                    </div>
                    <div class="stat-summary-box">
                        <div class="stat-val">{{ $statusEvaluasi }}</div>
                        <div class="stat-lbl">Status Evaluasi</div>
                    </div>
                </div>
            </div>

            <!-- Section II: Visualisasi Grafik Tren Mood -->
            <div class="report-section">
                <h3 class="section-title">II. Visualisasi Grafik Tren Suasana Hati</h3>
                <p class="section-desc">
                    Kurva di bawah ini menggambarkan fluktuasi rata-rata mood kolektif mahasiswa dari hari ke hari atau bulan ke bulan. Nilai tinggi (mendekati 7.0) mengindikasikan emosi positif/bahagia secara kolektif, sedangkan penurunan garis kurva (mendekati 1.0) menunjukkan adanya indikator stres, kelelahan fisik/mental, atau emosi negatif.
                </p>
                
                <div class="report-chart-container">
                    <canvas id="laporanMoodTrendChart"></canvas>
                </div>
            </div>

            <!-- Section III: Tabel Rincian Data Tren -->
            <div class="report-section" style="page-break-before: auto;">
                <h3 class="section-title">III. Rincian Poin Data Tren Mood</h3>
                <p class="section-desc" style="margin-bottom: 10px;">
                    Daftar angka rata-rata suasana hati per titik waktu pengelompokan yang direpresentasikan oleh kurva grafik di atas:
                </p>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 10%; text-align: center;">No</th>
                            <th>Tanggal / Periode</th>
                            <th style="width: 30%; text-align: center;">Jumlah Check-in Suasana Hati</th>
                            <th style="width: 30%; text-align: center;">Rata-rata Skor Mood Kolektif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trendTable as $index => $row)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td><strong>{{ $row['label'] }}</strong></td>
                                <td style="text-align: center;">{{ $row['count'] }} kali input</td>
                                <td style="text-align: center; font-weight: 700;">{{ $row['score'] }} / 7.0</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Section IV: Analisis Distribusi Emosi & Tingkatan Mood (Page Break jika penuh) -->
            <div class="report-section" style="page-break-before: auto;">
                <h3 class="section-title">IV. Analisis Distribusi Emosi & Tingkat Mood</h3>
                <p class="section-desc">
                    Berikut adalah pemetaan persentase sebaran emosi spesifik dan tingkatan mood yang dirasakan mahasiswa selama periode {{ $rangeName }}:
                </p>
                
                <div class="distribution-row">
                    <!-- Tabel Top 5 Feelings -->
                    <div style="flex: 1.2;">
                        <h4 class="sub-section-title">A. Top 5 Emosi Teratas (Feelings)</h4>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Nama Emosi</th>
                                    <th style="width: 25%; text-align: center;">Persentase</th>
                                    <th>Keterangan Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distribution as $feel)
                                    <tr>
                                        <td><strong>{{ $feel['name'] }}</strong></td>
                                        <td style="text-align: center; font-weight: 700; color: var(--accent-success);">{{ $feel['percentage'] }}%</td>
                                        <td style="font-size: 0.75rem; line-height: 1.3;">{{ $feel['desc'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: var(--text-muted);">Tidak ada data emosi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Distribusi Mood -->
                    <div style="flex: 0.8;">
                        <h4 class="sub-section-title">B. Distribusi Tingkat Mood</h4>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Tingkat Mood</th>
                                    <th style="width: 35%; text-align: center;">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($moodDist as $m)
                                    @php
                                        // Pilih warna/klasifikasi badge berdasarkan nama mood
                                        $mName = strtolower($m['name']);
                                        if (str_contains($mName, 'sedih') || str_contains($mName, 'buruk') || str_contains($mName, 'sangat buruk')) {
                                            $badgeClass = "badge-danger";
                                        } elseif (str_contains($mName, 'cemas') || str_contains($mName, 'biasa')) {
                                            $badgeClass = "badge-warning";
                                        } else {
                                            $badgeClass = "badge-success";
                                        }
                                    @endphp
                                    <tr>
                                        <td><span class="badge {{ $badgeClass }}" style="font-size: 0.7rem;">{{ $m['name'] }}</span></td>
                                        <td style="text-align: center; font-weight: 700;">{{ $m['percentage'] }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; color: var(--text-muted);">Tidak ada data mood</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Section V: Lembar Pengesahan -->
            <div class="report-section signature-section" style="page-break-inside: avoid;">
                <div class="signature-container">
                    <div class="signature-box">
                        <p>Dibuat Oleh:</p>
                        <p style="font-weight: 700; margin-top: 4px;">Konselor Unit Layanan,</p>
                        <div style="height: 64px;"></div>
                        <p style="text-decoration: underline; font-weight: 700; margin-bottom: 2px;">_______________________________</p>
                        <p style="font-size: 0.72rem; color: #475569;">NIP. __________________________</p>
                    </div>
                    <div class="signature-box">
                        <p>Mengetahui / Menyetujui:</p>
                        <div style="height: 64px; margin-top: 25px;"></div>
                        <p style="text-decoration: underline; font-weight: 700; margin-bottom: 2px;">_______________________________</p>
                        <p style="font-size: 0.72rem; color: #475569;">NIP. __________________________</p>
                    </div>
                </div>
            </div>

        </div>
    @endif

</main>

<div id="toast" class="no-print"></div>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js" class="no-print"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" class="no-print"></script>
<script class="no-print">
    // Render grafik tren di laporan
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('laporanMoodTrendChart');
        if (!ctx) return;

        const labels = @json($labels);
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Rata-rata Skor Mood',
                    data: chartData,
                    borderColor: '#059669', // Campus Care Green
                    backgroundColor: 'rgba(5, 150, 105, 0.04)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#047857',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    pointRadius: 4,
                    tension: 0.25,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false, // MATIKAN ANIMASI: Agar canvas langsung tergambar utuh saat diekspor ke PDF
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 10
                            },
                            color: '#64748b'
                        }
                    },
                    y: {
                        min: 1,
                        max: 7,
                        grid: {
                            color: '#e2e8f0'
                        },
                        ticks: {
                            stepSize: 1,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 10
                            },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });

    function showToast(msg, isSuccess = true) {
        const t = document.getElementById('toast');
        t.innerHTML = isSuccess ? `<span>✅</span> ${msg}` : `<span>⚠️</span> ${msg}`;
        t.style.background = isSuccess ? '#1e293b' : '#dc2626';
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function printElementToPDF(elementId, filename) {
        showToast('⏳ Sedang memproses dokumen PDF...');
        const element = document.getElementById(elementId);

        const opt = {
            margin:       [12, 12, 12, 12], // Margin ideal cetak formal
            filename:     filename,
            image:        { type: 'jpeg', quality: 1.0 },
            html2canvas:  { 
                scale: 2.2, // Teks tajam
                useCORS: true, 
                backgroundColor: '#ffffff',
                letterRendering: true
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            showToast('Laporan berhasil diunduh!');
        }).catch(err => {
            console.error(err);
            showToast('Gagal mengunduh laporan.', false);
        });
    }
</script>

</body>
</html>
