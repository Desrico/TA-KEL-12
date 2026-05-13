<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mahasiswa Prioritas (Level 3) – Campus Care</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-base:    #fdfdfd;
            --bg-gradient: linear-gradient(180deg, #fff5f5 0%, #fdfdfd 300px);
            --bg-card:    #ffffff;
            --border:     #f1f5f9;
            --accent:     #dc2626;
            --accent-soft: #fef2f2;
            --accent-border: #fca5a5;
            --text-1:     #0f172a;
            --text-2:     #475569;
            --text-3:     #94a3b8;
            --radius-xl:  24px;
            --radius-lg:  16px;
            --radius-md:  12px;
            --shadow-premium: 0 10px 40px rgba(220, 38, 38, 0.08);
            --shadow-hover: 0 20px 50px rgba(220, 38, 38, 0.12);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-base); 
            background-image: var(--bg-gradient);
            color: var(--text-1); 
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ── Topbar ── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px; height: 72px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
        }
        .topbar-logo { 
            display: flex; align-items: center; gap: 12px; 
            font-weight: 800; font-size: 1.2rem; 
            text-decoration: none; color: var(--text-1); 
        }
        .logo-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            display: flex; align-items: center; justify-content: center;
            color: #fff; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        /* ── Container ── */
        .container { max-width: 1000px; margin: 0 auto; padding: 48px 32px; }

        /* ── Page Header ── */
        .header-section {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 40px; flex-wrap: wrap; gap: 20px;
        }
        .title-area { display: flex; align-items: center; gap: 20px; }
        .btn-back {
            display: flex; align-items: center; justify-content: center;
            width: 48px; height: 48px; border-radius: 14px;
            background: #fff; border: 1px solid var(--border);
            color: var(--text-2); text-decoration: none; transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .btn-back:hover { 
            transform: translateX(-4px);
            background: var(--accent-soft); color: var(--accent); border-color: var(--accent-border);
        }
        .header-text h1 {
            font-size: 1.75rem; font-weight: 800; color: #1e293b;
            letter-spacing: -0.03em; margin: 0;
        }
        .header-text p { color: var(--text-3); font-size: 0.95rem; font-weight: 500; margin-top: 4px; }

        .btn-print {
            display: inline-flex; align-items: center; gap: 10px; padding: 12px 24px;
            border-radius: 14px; font-size: 0.9rem; font-weight: 700;
            background: #fff; color: var(--text-1); border: 1px solid var(--border);
            cursor: pointer; text-decoration: none; transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .btn-print:hover { 
            background: #f8fafc; border-color: #cbd5e1; transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        /* ── Student Grid ── */
        .student-grid { display: flex; flex-direction: column; gap: 28px; }

        .student-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 32px;
            text-decoration: none; color: inherit;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow-premium);
            position: relative;
            overflow: hidden;
        }
        .student-card:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent-border);
        }
        .student-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 6px; height: 100%;
            background: linear-gradient(180deg, #ef4444, #991b1b);
        }

        .card-main { display: flex; justify-content: space-between; gap: 32px; align-items: flex-start; flex-wrap: wrap; }
        
        .profile-section { display: flex; gap: 24px; align-items: flex-start; flex: 1; }
        .avatar-box {
            width: 72px; height: 72px; border-radius: 20px; flex-shrink: 0;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 2rem; color: #dc2626;
            border: 2px solid #fff; box-shadow: 0 8px 20px rgba(220, 38, 38, 0.15);
        }

        .info-box h3 {
            font-size: 1.4rem; font-weight: 800; color: #0f172a;
            margin-bottom: 6px; display: flex; align-items: center; gap: 12px;
        }
        .nim-text { font-size: 0.95rem; color: var(--text-3); font-weight: 600; margin-bottom: 16px; }

        .tag-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .tag {
            padding: 6px 14px; border-radius: 10px; font-size: 0.8rem; font-weight: 700;
            display: flex; align-items: center; gap: 8px; border: 1px solid var(--border);
            background: #f8fafc; color: var(--text-2);
        }
        .tag-krisis { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
        .pulse-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.2); } 100% { opacity: 1; transform: scale(1); } }

        .metrics-section { display: flex; flex-direction: column; align-items: flex-end; gap: 20px; min-width: 200px; }
        .ai-confidence {
            background: #f8fafc; padding: 16px; border-radius: 16px;
            border: 1px solid #f1f5f9; width: 100%; text-align: right;
        }
        .confidence-label { font-size: 0.7rem; font-weight: 800; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
        .progress-container { display: flex; align-items: center; gap: 12px; justify-content: flex-end; }
        .progress-bg { width: 100px; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #ef4444, #dc2626); border-radius: 999px; }
        .confidence-value { font-weight: 800; font-size: 1rem; color: #dc2626; }

        .btn-view {
            padding: 10px 24px; background: #0f172a; color: #fff;
            border-radius: 12px; font-size: 0.85rem; font-weight: 700;
            transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-view:hover { background: #1e293b; transform: scale(1.05); }

        /* ── Red Flag Alert ── */
        .red-flag-alert {
            margin-top: 28px; padding: 20px 24px;
            background: #fff5f5; border: 1px solid #fee2e2; border-radius: 18px;
            display: flex; gap: 20px; align-items: flex-start;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .rf-icon-circle {
            width: 44px; height: 44px; border-radius: 12px;
            background: #dc2626; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);
        }
        .rf-text h4 { color: #991b1b; font-size: 0.85rem; font-weight: 800; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
        .rf-text p { color: #b91c1c; font-size: 1rem; font-weight: 600; line-height: 1.5; font-style: italic; }

        /* ── Toast ── */
        #toast {
            position: fixed; bottom: 32px; right: 32px; z-index: 1000;
            display: none; align-items: center; gap: 12px;
            padding: 16px 24px; border-radius: 16px;
            background: #1e293b; color: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            font-size: 0.9rem; font-weight: 600;
            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        #toast.show { display: flex; }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* ── Empty State ── */
        .empty-state {
            text-align: center; padding: 100px 40px;
            background: #fff; border: 2px dashed #e2e8f0; border-radius: 32px;
        }
        .empty-icon { font-size: 4rem; margin-bottom: 24px; display: block; }
        .empty-state h2 { font-size: 1.5rem; font-weight: 800; color: var(--text-1); margin-bottom: 12px; }
        .empty-state p { color: var(--text-3); font-size: 1.05rem; }

        @media (max-width: 768px) {
            .header-section { flex-direction: column; align-items: flex-start; }
            .metrics-section { align-items: flex-start; width: 100%; }
            .ai-confidence { text-align: left; }
            .progress-container { justify-content: flex-start; }
            .btn-view { width: 100%; text-align: center; }
            .topbar { padding: 0 20px; }
        }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('counselor.dashboard') }}" class="topbar-logo">
        <div class="logo-icon">🎓</div>
        <span>Campus Care</span>
    </a>
    <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-3);">Panel Konselor</div>
</header>

<main class="container">

    <div class="header-section">
        <div class="title-area">
            <a href="{{ route('counselor.dashboard') }}" class="btn-back">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <div class="header-text">
                <h1>Daftar Mahasiswa Prioritas</h1>
                <p>Menampilkan kasus Level 3 yang membutuhkan penanganan segera</p>
            </div>
        </div>
        
        <button class="btn-print" onclick="printElementToPDF('printLevel3Area', 'Laporan_Mahasiswa_Prioritas.pdf')">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Ekspor Laporan PDF
        </button>
    </div>

    @if($students->isEmpty())
        <div class="empty-state">
            <span class="empty-icon">✨</span>
            <h2>Semua Aman!</h2>
            <p>Tidak ada mahasiswa dengan status krisis (Level 3) saat ini.</p>
        </div>
    @else
        <div id="printLevel3Area">
            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;" data-html2canvas-ignore="true">
                <span style="background: var(--accent); color: #fff; padding: 4px 12px; border-radius: 8px; font-weight: 800; font-size: 0.9rem;">
                    {{ $students->count() }} Mahasiswa
                </span>
                <span style="color: var(--text-3); font-size: 0.85rem; font-weight: 600;">Terdeteksi risiko tinggi oleh sistem AI</span>
            </div>

            <div class="student-grid">
                @foreach($students as $s)
                <a class="student-card" href="{{ route('counselor.detail', $s->nim) }}">
                    <div class="card-main">
                        <div class="profile-section">
                            <div class="avatar-box">{{ substr($s->name, 0, 1) }}</div>
                            <div class="info-box">
                                <h3>{{ $s->name }}</h3>
                                <div class="nim-text">{{ $s->nim }} • {{ $s->jenis_kelamin }} • Tingkat {{ $s->angkatan }}</div>
                                <div class="tag-group">
                                    <span class="tag tag-krisis"><div class="pulse-dot"></div> {{ $s->mental_label }}</span>
                                    <span class="tag">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        {{ $s->journal_texts_count }} Jurnal
                                    </span>
                                    @if($s->mental_scanned_at)
                                    <span class="tag">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        {{ $s->mental_scanned_at->isoFormat('DD MMM, HH:mm') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="metrics-section">
                            <div class="ai-confidence">
                                <div class="confidence-label">Tingkat Keyakinan AI</div>
                                <div class="progress-container">
                                    <div class="progress-bg">
                                        <div class="progress-fill" style="width: {{ round($s->mental_confidence) }}%;"></div>
                                    </div>
                                    <span class="confidence-value">{{ round($s->mental_confidence) }}%</span>
                                </div>
                            </div>
                            <span class="btn-view" data-html2canvas-ignore="true">Tinjau Kasus →</span>
                        </div>
                    </div>

                    @if($s->mental_red_flag)
                    <div class="red-flag-alert">
                        <div class="rf-text">
                            <h4>Analisis Temuan Krisis</h4>
                            <p>"{{ $s->mental_red_flag }}"</p>
                        </div>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    @endif

</main>

<div id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function showToast(msg, isSuccess = true) {
        const t = document.getElementById('toast');
        t.innerHTML = isSuccess ? `<span>✅</span> ${msg}` : `<span>⚠️</span> ${msg}`;
        t.style.background = isSuccess ? '#0f172a' : '#dc2626';
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function printElementToPDF(elementId, filename) {
        showToast('⏳ Sedang memproses laporan PDF...');
        const element = document.getElementById(elementId);

        const opt = {
            margin:       15,
            filename:     filename,
            image:        { type: 'jpeg', quality: 1 },
            html2canvas:  { 
                scale: 3, 
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
