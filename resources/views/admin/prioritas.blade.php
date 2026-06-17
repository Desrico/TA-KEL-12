<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Prioritas Kesehatan Mental Mahasiswa – Campus Care</title>
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
            --accent-danger: #dc2626;
            --accent-danger-soft: #fef2f2;
            --accent-warning: #d97706;
            --accent-warning-soft: #fffbeb;
            --accent-success: #16a34a;
            --accent-success-soft: #f0fdf4;
            
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
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 2px;
        }
        .stat-lbl {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-summary-box.danger {
            border-color: #fecaca;
            background-color: var(--accent-danger-soft);
        }
        .stat-summary-box.danger .stat-val {
            color: var(--accent-danger);
        }
        .stat-summary-box.warning {
            border-color: #fde047;
            background-color: var(--accent-warning-soft);
        }
        .stat-summary-box.warning .stat-val {
            color: var(--accent-warning);
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
            vertical-align: top;
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
            .stat-summary-box.danger .stat-val,
            .stat-summary-box.warning .stat-val {
                color: #000000 !important;
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
        <button class="btn-print" onclick="printElementToPDF('printLevel3Area', 'Laporan_Prioritas_Kesehatan_Mental_{{ now()->format('d_M_Y') }}.pdf')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Ekspor Laporan PDF
        </button>
    </div>
</header>

<!-- Main Wrapper -->
<main class="report-container">

    @if($students->isEmpty())
        <div class="empty-state" style="margin-top: 60px;">
            <span class="empty-icon">✨</span>
            <h2>Semua Mahasiswa Aman!</h2>
            <p>Tidak ada mahasiswa dengan status krisis (Level 3) saat ini.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn-print" style="margin-top: 20px; display: inline-flex; text-decoration: none;">Kembali ke Dashboard</a>
        </div>
    @else
        <!-- Area Kertas Cetak -->
        <div id="printLevel3Area" class="report-paper">
            

            <!-- Judul Dokumen -->
            <div class="report-title-container">
                <h1 class="report-title">Laporan Prioritas Klasifikasi Kesehatan Mental Mahasiswa</h1>
                <p class="report-subtitle">Kategori Level 3 (Krisis/Urgent) - Periode Layanan {{ now()->isoFormat('MMMM YYYY') }}</p>
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
                    <td>{{ auth()->user()->nama ?? 'Staf Konselor IT Del' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 700;">Status Kasus</td>
                    <td>:</td>
                    <td><span style="color: var(--accent-danger); font-weight: bold; text-transform: uppercase;">Prioritas Level 3 (Krisis)</span></td>
                    <td style="font-weight: 700;">Sistem Deteksi</td>
                    <td>:</td>
                    <td>Predictive AI Classifier + Manual Review</td>
                </tr>
            </table>

            <!-- Section I: Ringkasan Eksekutif -->
            <div class="report-section">
                <h3 class="section-title">I. Ringkasan Eksekutif</h3>
                <p class="section-desc">
                    Laporan ini menyajikan data analitis kondisi kesehatan emosional mahasiswa berdasarkan hasil klasifikasi otomatis sistem kecerdasan buatan (AI) yang mengukur sentimen jurnal ekspresi dan check-in suasana hati mahasiswa harian, dikombinasikan dengan validasi data oleh konselor. Kategori Level 3 menandakan mahasiswa berada pada status krisis emosional yang membutuhkan perhatian, komunikasi, serta intervensi langsung secara luring oleh Unit Bimbingan & Konseling secepatnya guna mengantisipasi risiko klinis yang lebih berat.
                </p>
                
                <div class="stats-summary-grid">
                    <div class="stat-summary-box">
                        <div class="stat-val">{{ $totalStudents }}</div>
                        <div class="stat-lbl">Mahasiswa Terdaftar</div>
                    </div>
                    <div class="stat-summary-box">
                        <div class="stat-val">{{ $totalScanned }}</div>
                        <div class="stat-lbl">Mahasiswa Dipindai</div>
                    </div>
                    <div class="stat-summary-box danger">
                        <div class="stat-val">{{ $l3Count }}</div>
                        <div class="stat-lbl">Kasus L3 (Krisis)</div>
                    </div>
                    <div class="stat-summary-box warning">
                        <div class="stat-val">{{ $ratio }}%</div>
                        <div class="stat-lbl">Rasio Krisis</div>
                    </div>
                </div>
            </div>

            <!-- Section II: Analisis Distribusi Kasus -->
            <div class="report-section">
                <h3 class="section-title">II. Analisis Distribusi Kasus</h3>
                <p class="section-desc" style="margin-bottom: 8px;">
                    Di bawah ini merupakan pemetaan kontribusi kasus krisis (Level 3) berdasarkan data akademik Program Studi dan tahun Angkatan mahasiswa aktif:
                </p>
                <div class="distribution-row">
                    <!-- Tabel Prodi -->
                    <div style="flex: 1;">
                        <h4 class="sub-section-title">A. Berdasarkan Program Studi</h4>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Program Studi</th>
                                    <th style="width: 25%; text-align: center;">Kasus</th>
                                    <th style="width: 30%; text-align: center;">Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prodiBreakdown as $prodi => $count)
                                    <tr>
                                        <td>{{ $prodi }}</td>
                                        <td style="text-align: center; font-weight: 700;">{{ $count }}</td>
                                        <td style="text-align: center;">{{ $l3Count > 0 ? round(($count / $l3Count) * 100, 1) : 0 }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: var(--text-muted);">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Angkatan -->
                    <div style="flex: 1;">
                        <h4 class="sub-section-title">B. Berdasarkan Angkatan</h4>
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Tahun Angkatan</th>
                                    <th style="width: 25%; text-align: center;">Kasus</th>
                                    <th style="width: 30%; text-align: center;">Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($angkatanBreakdown as $angkatan => $count)
                                    <tr>
                                        <td>Angkatan {{ $angkatan }}</td>
                                        <td style="text-align: center; font-weight: 700;">{{ $count }}</td>
                                        <td style="text-align: center;">{{ $l3Count > 0 ? round(($count / $l3Count) * 100, 1) : 0 }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: var(--text-muted);">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Section III: Tabel Rinci Mahasiswa Prioritas (Mulai di Halaman Baru jika panjang) -->
            <div class="report-section" style="page-break-before: auto; margin-top: 10px;">
                <h3 class="section-title">III. Detail Daftar Kasus Prioritas (Level 3)</h3>
                <p class="section-desc" style="margin-bottom: 12px;">
                    Daftar mahasiswa yang diklasifikasikan ke dalam kasus Level 3 yang memerlukan penanganan klinis segera:
                </p>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 6%; text-align: center;">No</th>
                            <th style="width: 25%;">Nama & NIM</th>
                            <th style="width: 22%;">Prodi & Angkatan</th>
                            <th style="width: 12%; text-align: center;">Keyakinan AI</th>
                            <th>Keterangan / Red Flag Masalah</th>
                            <th style="width: 13%; text-align: center;">Tindak Lanjut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $s)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
                                <td style="vertical-align: middle;">
                                    <strong>{{ $s->name }}</strong><br>
                                    <span style="font-size: 0.72rem; color: var(--text-secondary);">NIM: {{ $s->nim }}</span>
                                </td>
                                <td style="vertical-align: middle;">
                                    {{ $s->prodi }}<br>
                                    <span style="font-size: 0.72rem; color: var(--text-secondary);">Angkatan {{ $s->angkatan }}</span>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--accent-danger); vertical-align: middle;">
                                    {{ round($s->mental_confidence) }}%
                                </td>
                                <td style="font-size: 0.78rem; line-height: 1.45;">
                                    @if($s->mental_red_flag)
                                        <span style="color: #991b1b; font-weight: 500;">"{{ $s->mental_red_flag }}"</span>
                                    @else
                                        <span style="color: #64748b;">- Tidak ada catatan red flag spesifik -</span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @php
                                        // Deteksi apakah ada notifikasi yang pernah dikirim untuk NIM ini di database MongoDB
                                        $notifExists = \App\Models\NotifikasiMahasiswa::where('nim', (string)$s->nim)->exists();
                                    @endphp
                                    @if($notifExists)
                                        <span class="badge badge-success">Notifikasi OK</span>
                                    @else
                                        <span class="badge badge-danger">Perlu Notif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                    Tidak ada data mahasiswa dengan status Level 3 saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Section IV: Tren Kesehatan Mental Kolektif -->
            <div class="report-section" style="page-break-inside: avoid;">
                <h3 class="section-title">IV. Analisis Tren Mood Bulanan Kolektif</h3>
                <p class="section-desc">
                    Nilai di bawah merupakan statistik suasana hati (mood) rata-rata kelompok mahasiswa prioritas di atas yang diperoleh dari check-in harian dalam kurun waktu 4 bulan terakhir. Rentang skor penilaian berkisar antara 1.0 (Sangat Murung/Krisis) hingga 7.0 (Sangat Bahagia/Stabil). Penurunan tren skor mood bulanan yang signifikan mengindikasikan perlunya program pencegahan stres terstruktur di tingkat institusi.
                </p>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Bulan Aktivitas</th>
                            <th style="width: 25%; text-align: center;">Frekuensi Check-in</th>
                            <th style="width: 25%; text-align: center;">Rata-rata Skor Mood</th>
                            <th style="width: 32%;">Kondisi Mental Kolektif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyMoodTrend as $month => $data)
                            @php
                                $carbonMonth = \Carbon\Carbon::createFromFormat('Y-m', $month);
                                $monthName = $carbonMonth->isoFormat('MMMM YYYY');
                                $avgScore = $data['avg_score'];
                                $checkinCount = $data['count'];
                                
                                if ($avgScore < 3.0) {
                                    $evalStatus = "Krisis/Risiko Tinggi (Perlu Intervensi)";
                                    $label = "badge-danger";
                                } elseif ($avgScore < 4.5) {
                                    $evalStatus = "Stres Tinggi/Kelelahan Mental";
                                    $label = "badge-warning";
                                } else {
                                    $evalStatus = "Stabil/Sesuai Batas Wajar";
                                    $label = "badge-success";
                                }
                            @endphp
                            <tr>
                                <td><strong>{{ $monthName }}</strong></td>
                                <td style="text-align: center;">{{ $checkinCount }} kali</td>
                                <td style="text-align: center; font-weight: 700; font-size: 0.92rem;">{{ $avgScore }} / 7.0</td>
                                <td><span class="badge {{ $label }}">{{ $evalStatus }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 16px; color: var(--text-muted);">
                                    Tidak ada riwayat check-in yang terekam pada kelompok ini dalam 4 bulan terakhir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Section V: Lembar Pengesahan -->
            <div class="report-section signature-section">
                <div class="signature-container">
                    <div class="signature-box">
                        <p>Dibuat Oleh:</p>
                        <p style="font-weight: 700; margin-top: 4px;">Konselor Unit Layanan,</p>
                        <div style="height: 64px;"></div>
                        <p style="text-decoration: underline; font-weight: 700; margin-bottom: 2px;">{{ auth()->user()->nama ?? 'Staf Konselor IT Del' }}</p>
                        <p style="font-size: 0.72rem; color: #475569;">Staf Konseling Mahasiswa</p>
                    </div>
                    <div class="signature-box">
                        <p>Mengetahui / Menyetujui:</p>
                        <p style="font-weight: 700; margin-top: 4px;">Kepala Unit Layanan BK IT Del,</p>
                        <div style="height: 64px;"></div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" class="no-print"></script>
<script class="no-print">
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
            margin:       [12, 12, 12, 12], // Margin ideal untuk dokumen A4 formal
            filename:     filename,
            image:        { type: 'jpeg', quality: 1.0 },
            html2canvas:  { 
                scale: 2.2, // Ketajaman teks optimal saat dicetak
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
