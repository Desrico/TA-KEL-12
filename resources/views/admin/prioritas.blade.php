<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mahasiswa Prioritas – WebKonselor</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-base:    #f8fafc;
            --bg-card:    #ffffff;
            --border:     #e2e8f0;
            --accent:     #dc2626;
            --accent-dim: #fef2f2;
            --green:      #059669;
            --amber:      #d97706;
            --red:        #dc2626;
            --text-1:     #1e293b;
            --text-2:     #475569;
            --text-3:     #94a3b8;
            --radius-lg:  16px;
            --radius-md:  10px;
            --radius-sm:  6px;
            --shadow-red: 0 4px 12px rgba(220, 38, 38, 0.15);
            --shadow-sm:  0 1px 3px rgba(0,0,0,0.05);
        }

        body { font-family: 'Inter', sans-serif; background: var(--bg-base); color: var(--text-1); min-height: 100vh; }

        .topbar {
            position: sticky; top: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; height: 64px;
            background: rgba(255,255,255,0.9); backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
        }
        .topbar-logo { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1.1rem; text-decoration: none; color: var(--text-1); }
        .topbar-logo .dot {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #10b981, #059669);
            display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: #fff;
        }
        
        .container { max-width: 900px; margin: 0 auto; padding: 40px 32px; }

        .page-header { margin-bottom: 32px; display: flex; align-items: center; gap: 16px; }
        .page-header h1 {
            font-size: 1.5rem; font-weight: 700; letter-spacing: -0.03em;
            color: var(--text-1); margin: 0;
        }

        .btn-back {
            display: inline-flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; border-radius: var(--radius-md);
            background: #fff; border: 1px solid var(--border);
            color: var(--text-2); text-decoration: none; transition: 0.2s;
        }
        .btn-back:hover { background: var(--bg-base); color: var(--text-1); border-color: #cbd5e1; }

        .btn-print {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;
            border-radius: var(--radius-md); font-size: 0.85rem; font-weight: 600;
            background: #fff; color: var(--text-1); border: 1px solid var(--border); cursor: pointer; text-decoration: none;
            transition: 0.2s;
        }
        .btn-print:hover { background: var(--bg-base); border-color: #cbd5e1; }
        .btn-print svg { color: var(--text-2); }

        .student-grid { display: flex; flex-direction: column; gap: 24px; margin-top: 24px; }
        .student-card {
            display: flex; flex-direction: column;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 28px;
            text-decoration: none; color: inherit;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }
        .student-card:hover { 
            border-color: #fca5a5; 
            box-shadow: 0 20px 25px -5px rgba(220, 38, 38, 0.1), 0 8px 10px -6px rgba(220, 38, 38, 0.1); 
            transform: translateY(-4px); 
        }

        .student-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .card-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; flex-wrap: wrap; }
        
        .card-user { display: flex; gap: 20px; align-items: flex-start; }
        .avatar-lg {
            width: 64px; height: 64px; border-radius: 16px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.8rem;
            background: #fee2e2; color: #dc2626;
            border: 1px solid #fecaca;
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.5);
        }
        
        .user-details h3 { font-size: 1.3rem; font-weight: 700; color: #0f172a; margin: 0 0 6px 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .user-details p { font-size: 0.9rem; color: #64748b; margin: 0 0 16px 0; font-weight: 500; }
        
        .pill-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .pill { 
            background: #f1f5f9; color: #475569; padding: 6px 12px; 
            border-radius: 8px; font-size: 0.8rem; font-weight: 600; 
            display: flex; align-items: center; gap: 6px;
            border: 1px solid #e2e8f0;
        }
        .pill-red { background: #fef2f2; color: #dc2626; border-color: #fecaca; padding: 4px 12px; }
        
        .ldot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; animation: pulse 1.8s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.35} }

        .card-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 16px; }
        .confidence-area { background: #f8fafc; padding: 12px 16px; border-radius: 12px; border: 1px solid #f1f5f9; text-align: right; }
        
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px;
            border-radius: 10px; font-size: 0.85rem; font-weight: 600;
            background: #dc2626; color: #ffffff;
            transition: 0.2s;
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }
        .btn-primary:hover { background: #b91c1c; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3); }

        .red-flag-box {
            margin-top: 24px; padding: 16px 20px;
            background: #fef2f2; border-left: 4px solid #dc2626; border-radius: 8px 16px 16px 8px;
            display: flex; gap: 16px; align-items: flex-start;
        }
        .rf-icon { font-size: 1.5rem; line-height: 1; }
        .rf-content h4 { color: #991b1b; font-size: 0.85rem; font-weight: 700; margin: 0 0 6px 0; text-transform: uppercase; letter-spacing: 0.05em; }
        .rf-content p { color: #b91c1c; font-size: 0.95rem; margin: 0; line-height: 1.6; font-weight: 500; }

        #toast {
            position: fixed; bottom: 28px; right: 28px; z-index: 999;
            display: none; align-items: center; gap: 10px;
            padding: 12px 18px; border-radius: var(--radius-md);
            background: #fff; border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            font-size: 0.84rem; color: var(--text-1); font-weight: 500;
            animation: slideInUp 0.3s ease;
        }
        #toast.show { display: flex; }
        @keyframes slideInUp { from { transform:translateY(16px);opacity:0 } to { transform:none;opacity:1 } }
    </style>
</head>
<body>

<header class="topbar">
    <a href="{{ route('counselor.dashboard') }}" class="topbar-logo">
        <div class="dot">🎓</div>
        WebKonselor
    </a>
</header>

<main class="container">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div class="page-header" style="margin: 0;">
            <a href="{{ route('counselor.dashboard') }}" class="btn-back" title="Kembali">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </a>
            <h1>🚨 Daftar Seluruh Mahasiswa Prioritas (Level 3)</h1>
        </div>
        
        <button class="btn-print" onclick="printElementToPDF('printLevel3Area', 'Daftar_Seluruh_Mahasiswa_Prioritas.pdf')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak seluruh mahasiswa
        </button>
    </div>

    @if($students->isEmpty())
        <div style="text-align: center; padding: 60px 20px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); color: var(--text-3);">
            <div style="font-size: 2.5rem; margin-bottom: 16px;">✅</div>
            <p>Tidak ada mahasiswa dengan status prioritas (Level 3) saat ini.</p>
        </div>
    @else
        <div id="printLevel3Area" style="background: var(--bg-card); padding: 32px; border-radius: var(--radius-lg); border: 1px solid var(--border);">
            <h2 style="font-size: 1.1rem; margin-bottom: 8px;" data-html2canvas-ignore="true">Total: {{ $students->count() }} Mahasiswa</h2>
            <p style="color: var(--text-3); font-size: 0.85rem; margin-bottom: 24px;" data-html2canvas-ignore="true">
                Berikut adalah daftar mahasiswa yang terdeteksi membutuhkan penanganan cepat berdasarkan AI.
            </p>

            <div class="student-grid">
                @foreach($students as $s)
                <a class="student-card" href="{{ route('counselor.detail', $s->nim) }}">
                    <div class="card-top">
                        <div class="card-user">
                            <div class="avatar-lg">{{ substr($s->name, 0, 1) }}</div>
                            <div class="user-details">
                                <h3>
                                    {{ $s->name }}
                                    <span class="pill pill-red"><span class="ldot"></span>{{ $s->mental_label }}</span>
                                </h3>
                                <p>{{ $s->nim }} • {{ $s->gender }}</p>
                                <div class="pill-group">
                                    <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg> {{ $s->journal_texts_count }} Jurnal</span>
                                    @if($s->mental_scanned_at)
                                    <span class="pill"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> {{ $s->mental_scanned_at->isoFormat('DD MMM, HH:mm') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-actions">
                            <div class="confidence-area">
                                <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">Keyakinan AI</div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 80px; height: 6px; background: #e2e8f0; border-radius: 999px; overflow: hidden;">
                                        <div style="width: {{ round($s->mental_confidence) }}%; height: 100%; background: #dc2626; border-radius: 999px;"></div>
                                    </div>
                                    <span style="font-weight: 800; font-size: 0.9rem; color: #dc2626;">{{ round($s->mental_confidence) }}%</span>
                                </div>
                            </div>
                            <span class="btn-primary" data-html2canvas-ignore="true">Lihat Profil & Jurnal →</span>
                        </div>
                    </div>

                    @if($s->mental_red_flag)
                    <div class="red-flag-box">
                        <div class="rf-icon">🚨</div>
                        <div class="rf-content">
                            <h4>Peringatan Temuan Krisis (AI)</h4>
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
    function showToast(msg, color = 'var(--green)') {
        const t = document.getElementById('toast');
        t.innerHTML = `<span style="width:8px;height:8px;border-radius:50%;background:${color};display:inline-block;flex-shrink:0"></span> ${msg}`;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function printElementToPDF(elementId, filename) {
        showToast('⏳ Menyusun dokumen PDF...', 'var(--amber)');
        const element = document.getElementById(elementId);

        const opt = {
            margin:       10,
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                backgroundColor: '#ffffff',
                scrollY: 0,
                scrollX: 0
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            showToast('✅ Berhasil mengunduh PDF', 'var(--green)');
        }).catch(err => {
            console.error(err);
            showToast('⚠️ Gagal menyusun PDF', 'var(--red)');
        });
    }
</script>

</body>
</html>
