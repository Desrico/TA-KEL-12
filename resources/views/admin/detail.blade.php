@extends('layouts.admin')

@section('page-title', $student->name . ' – Detail Jurnal')

@push('styles')
    <style>
        .pc-container {
            background: #f8fafc;
        }
        .container-fluid { padding: 32px; }

        .grid-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; }
        @media (max-width: 900px) { .grid-layout { grid-template-columns: 1fr; } }

        /* ── Profile Card ── */
        .profile-card {
            display: flex; align-items: flex-start; justify-content: space-between;
            background: #ffffff; border: 1px solid #e2e8f0;
            border-radius: 16px; padding: 24px 28px;
            margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .profile-left h1 { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 6px; display: flex; align-items: center; gap: 12px; }
        .profile-left p { color: #475569; font-size: 1rem; margin-bottom: 10px; }
        .profile-left .tags { display: flex; gap: 16px; font-size: 1rem; font-weight: 600; color: #059669; }

        .pill { font-size: 0.85rem; font-weight: 700; padding: 6px 12px; border-radius: 999px; white-space: nowrap; }
        .pill-L { background: #dbeafe; color: #2563eb; }
        .pill-P { background: #d1fae5; color: #059669; }

        /* ── Cards ── */
        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .card-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 24px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 4px; }
        .stat-card .label { font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card .value { font-size: 1.5rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 6px; }

        /* ── Insight Card ── */
        .insight-card { background: #0f766e; color: #fff; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; height: 100%; justify-content: space-between; }
        .insight-card .header { display: flex; align-items: center; gap: 10px; font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; }
        .insight-card p { font-size: 1rem; line-height: 1.6; color: #ccfbf1; margin-bottom: 24px; flex-grow: 1; }
        .score-bar { background: rgba(255,255,255,0.2); height: 6px; border-radius: 999px; width: 100%; margin-top: 8px; overflow: hidden; }
        .score-fill { background: #fff; height: 100%; border-radius: 999px; }
        
        .btn-white { background: #fff; color: #0f766e; border: none; padding: 12px 18px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; cursor: pointer; text-align: center; display: flex; justify-content: center; align-items: center; gap: 6px; width: 100%; }
        .btn-white:hover { background: #f8fafc; color: #0f766e;}

        /* ── Table Area ── */
        .table-area { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
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

        /* ── Modal ── */
        .modal-backdrop { display: none; position: fixed; inset: 0; z-index: 2000; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 24px; }
        .modal-backdrop.show { display: flex; }
        .modal { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); width: 100%; max-width: 560px; overflow: hidden; }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
        .modal-header h2 { font-size: 1.3rem; font-weight: 800; color: #1e293b; margin: 0; }
        .modal-close { background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; }
        .modal-body { padding: 24px; }
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; min-height: 120px; font-size: 1.05rem; line-height: 1.6; color: #475569; }
        .spin { width: 18px; height: 18px; border: 2px solid rgba(5, 150, 105, 0.2); border-top-color: #059669; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
@endpush

@section('konten')
    <div class="container-fluid">
        <a href="{{ route('counselor.dashboard') }}" class="btn-back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali ke Dashboard Mahasiswa
        </a>

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-left">
                <div>
                    <h1>
                        {{ $student->name }}
                        <span class="pill {{ $student->gender === 'Laki-laki' ? 'pill-L' : 'pill-P' }}">
                            {{ $student->gender }}
                        </span>
                    </h1>
                    <p>NIM: {{ $student->nim }} • {{ $student->prodi ?? 'Teknologi Rekayasa Perangkat Lunak' }}</p>
                    <div class="tags">
                        <span>🎓 Semester 6</span>
                        <span>📅 Angkatan 2023</span>
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="display: flex; flex-direction: column; align-items: flex-end;">
                    <span style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; color: #94a3b8; margin-bottom: 6px;">Koreksi Status</span>
                    <select id="statusSelect" style="border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; font-size: 0.85rem; outline: none; background: #fff; cursor: pointer; color: #1e293b;">
                        <option value="0" {{ $student->mental_level == 0 ? 'selected' : '' }}>Level 0 (Positif)</option>
                        <option value="1" {{ $student->mental_level == 1 ? 'selected' : '' }}>Level 1 (Ringan)</option>
                        <option value="2" {{ $student->mental_level == 2 ? 'selected' : '' }}>Level 2 (Pantau)</option>
                        <option value="3" {{ $student->mental_level == 3 ? 'selected' : '' }}>Level 3 (Krisis)</option>
                    </select>
                </div>
                <button onclick="updateStatus('{{ $student->nim }}', event)" style="background: #059669; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; align-self: flex-end; display: flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Simpan
                </button>
            </div>
        </div>

        @if($student->mental_red_flag && $student->mental_level == 3)
        <div class="risk-alert">
            <div style="font-size: 1.5rem;">🚨</div>
            <div>
                <div class="title">
                    TEMUAN KRISIS (AI RISK ANALYSIS)
                    <span class="pill" style="background: #dc2626; color: white; font-size: 0.8rem;">URGENT</span>
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
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="card" style="padding: 20px;">
                    <div class="card-title">
                        <span style="font-size: 1.2rem;">🙂</span> Tren Mood
                        <div style="margin-left: auto; background: #f8fafc; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: 1px solid #e2e8f0; color: #475569;">7 Hari Terakhir ⌄</div>
                    </div>
                    <div style="height: 180px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 0.85rem; border-bottom: 2px dashed #059669;">[Grafik Tren Mood akan tampil di sini]</div>
                </div>

                <div class="card" style="padding: 20px;">
                    <div class="card-title">
                        <span style="font-size: 1.2rem;">📍</span> Tren Perasaan
                    </div>
                    <div style="height: 180px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 0.85rem; border-bottom: 2px dashed #94a3b8;">[Grafik Tren Perasaan akan tampil di sini]</div>
                </div>

                <!-- Stats -->
                <div class="stats-grid" style="margin-top: 0;">
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
            </div>

            <!-- Right Column (Insight Analisis Jurnal) -->
            <div>
                <div class="insight-card">
                    <div>
                        <div class="header">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;">✨</div>
                            Insight Analisis Jurnal
                        </div>
                        <p id="insightText">
                            Insight otomatis dari jurnal mahasiswa belum digenerate. Klik tombol Ringkas Jurnal di bawah untuk menggunakan AI merangkum kondisi psikologis mahasiswa ini berdasarkan seluruh riwayat jurnalnya.
                        </p>
                    </div>
                    
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">
                            <span>82%</span>
                        </div>
                        <div class="score-bar">
                            <div class="score-fill" style="width: 82%;"></div>
                        </div>

                        @if($student->journalTexts->count() > 0)
                        <button class="btn-white" style="margin-top: 24px;" id="btnSummary" onclick="openSummary('{{ $student->nim }}', '{{ $student->name }}')">
                            📄 Ringkas Jurnal
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Journal Table -->
        <div class="table-area">
            <div class="table-header">
                <span style="font-weight: 600; font-size: 1rem; color: #1e293b;">Riwayat Log Jurnal</span>
                <div style="display: flex; gap: 8px;">
                    <button class="btn-outline">Filter</button>
                    <button class="btn-outline">Ekspor PDF</button>
                </div>
            </div>

            @if($student->journalTexts->count() === 0)
                <div class="no-journals">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <p>Mahasiswa ini belum mengisi jurnal.</p>
                </div>
            @else
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th style="width: 160px;">Tanggal & Waktu</th>
                            <th style="width: 180px;">Mood Utama</th>
                            <th>Isi Jurnal & Analisis AI</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->journalTexts as $i => $journal)
                        @php
                            $checkin = $student->dailyCheckins->filter(function($c) use ($journal) {
                                return $c->created_at->format('Y-m-d') === $journal->created_at->format('Y-m-d');
                            })->first();
                        @endphp
                        <tr>
                            <td style="color:#94a3b8; font-size:0.8rem; font-weight: 500; vertical-align: top; padding-top: 20px;">
                                {{ $i + 1 }}
                            </td>
                            <td style="color:#1e293b; font-size:0.85rem; vertical-align: top; padding-top: 20px;">
                                {{ $journal->created_at->isoFormat('DD MMM YYYY, HH:mm') }}
                            </td>
                            <td style="vertical-align: top; padding-top: 20px;">
                                @if($checkin)
                                    <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                                        Mood: {{ $checkin->mood->mood_name }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">
                                        Feeling: {{ $checkin->feeling->feeling_name }}
                                    </div>
                                @else
                                    <span style="color:#94a3b8; font-style:italic;">—</span>
                                @endif
                            </td>
                            <td style="vertical-align: top; padding-top: 20px; padding-right: 32px;">
                                <div style="line-height: 1.6; color: #475569; font-size: 0.85rem; margin-bottom: 12px;">
                                    {{ $journal->description }}
                                </div>
                                @if($checkin && $checkin->aiAnalysis)
                                    <div style="background: #f8fafc; border-left: 3px solid #059669; padding: 10px 14px; border-radius: 0 8px 8px 0; margin-top: 12px;">
                                        <div style="font-size: 0.7rem; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Analisis AI</div>
                                        <div style="font-size: 0.8rem; color: #475569; line-height: 1.5; font-style: italic;">
                                            "{{ $checkin->aiAnalysis->text_analysis }}"
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td style="vertical-align: top; padding-top: 20px;">
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    @if($checkin && $checkin->aiAnalysis)
                                        <span style="color: #059669; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Sudah dianalisis</span>
                                    @else
                                        <span style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Belum ada analisis</span>
                                    @endif
                                    <a href="#" class="action-link" style="font-size: 0.8rem; margin-top: 4px;">Lihat Detail</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- AI Summary Modal -->
    <div class="modal-backdrop" id="summaryModal" onclick="closeModal(event)">
        <div class="modal" role="dialog" aria-modal="true">
            <div class="modal-header">
                <h2>✨ Ringkasan Jurnal oleh AI</h2>
                <button class="modal-close" onclick="closeSummaryModal()" title="Tutup">&times;</button>
            </div>
            <div class="modal-body">
                <div class="summary-box loading" id="summaryBox">
                    <div class="spin"></div> Memuat ringkasan dari AI…
                </div>
                <div id="summaryMeta" style="display:none; margin-top:12px; font-size:0.8rem; color:#94a3b8;">
                    Dihasilkan oleh AI Summarizer
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

    function openSummary(nim, name) {
        const modal  = document.getElementById('summaryModal');
        const box    = document.getElementById('summaryBox');
        const metaEl = document.getElementById('summaryMeta');
        const btn    = document.getElementById('btnSummary');
        const originalHtml = btn.innerHTML;

        box.innerHTML  = '<div class="spin"></div> Memuat ringkasan dari AI…';
        metaEl.style.display = 'none';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        btn.disabled = true; 
        btn.innerHTML = '<div class="spin"></div> Memuat…';

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
            const text   = data.summary ?? data.message ?? JSON.stringify(data);
            box.textContent = text;
            metaEl.style.display = 'block';
        })
        .catch(() => {
            box.textContent = '⚠️ Gagal menghubungi AI. Pastikan server Python berjalan di port 8001.';
        })
        .finally(() => {
            btn.disabled = false; 
            btn.innerHTML = originalHtml;
        });
    }

    function closeSummaryModal() {
        document.getElementById('summaryModal').classList.remove('show');
        document.body.style.overflow = '';
    }

    function closeModal(e) {
        if (e.target.id === 'summaryModal') closeSummaryModal();
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSummaryModal();
    });
</script>
@endpush
