@extends('layouts.admin')

@section('page-title', 'Daftar Seluruh Mahasiswa')

@push('styles')
    <style>
        .pc-container {
            background: #f8fafc;
        }
        .container-fluid { padding: 32px; }

        .btn-back-link { display: inline-flex; align-items: center; gap: 6px; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; padding: 8px 12px; border-radius: 8px; margin-bottom: 16px;}
        .btn-back-link:hover { background: #ffffff; color: #059669; }

        .btn-print {
            display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;
            border-radius: 8px; font-size: 0.85rem; font-weight: 600;
            background: #fff; color: #1e293b; border: 1px solid #e2e8f0; cursor: pointer; text-decoration: none;
            transition: 0.2s;
        }
        .btn-print:hover { background: #f8fafc; border-color: #cbd5e1; }
        .btn-print i { color: #475569; }

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
            vertical-align: middle;
        }
        .premium-table tr:last-child td { border-bottom: none; }
        .premium-table tbody tr { transition: background 0.2s; }
        .premium-table tbody tr:hover { background: #f8fafc; }

        .student-cell { display: flex; align-items: center; gap: 14px; }
        .avatar {
            width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
            background: linear-gradient(135deg, #f87171, #ef4444); color: #fff;
        }
        .avatar.l2 { background: linear-gradient(135deg, #fbbf24, #d97706); }
        .avatar.l1 { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .avatar.l0 { background: linear-gradient(135deg, #10b981, #059669); }

        .name-wrapper { display: flex; flex-direction: column; }
        .name-wrapper .name { font-weight: 600; font-size: 0.9rem; color: #1e293b; }
        .name-wrapper .red-flag { font-size: 0.7rem; color: #dc2626; font-weight: 600; margin-top: 4px; display: flex; align-items: center; gap: 4px; text-transform: uppercase; letter-spacing: 0.05em; }

        .pill-status {
            display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px;
            border-radius: 999px; font-size: 0.7rem; font-weight: 700; white-space: nowrap;
            text-transform: uppercase; letter-spacing: 0.02em;
        }
        .pill-status.l3 { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .pill-status.l2 { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .pill-status.l1 { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .pill-status.l0 { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .ldot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .ldot.anim { animation: pulse 1.8s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.35} }

        .conf-wrap { display: flex; flex-direction: column; gap: 4px; }
        .conf-bar { width: 100px; height: 6px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
        .conf-fill { height: 100%; border-radius: 999px; }
        .conf-fill.l3 { background: #dc2626; }
        .conf-fill.l2 { background: #d97706; }
        .conf-fill.l1 { background: #2563eb; }
        .conf-fill.l0 { background: #059669; }
        .conf-val { font-size: 0.75rem; color: #475569; font-weight: 600; }

        .btn-detail {
            display: inline-flex; align-items: center; padding: 6px 12px;
            border-radius: 6px; font-size: 0.75rem; font-weight: 600;
            text-decoration: none; background: #fff;
            border: 1px solid #e2e8f0; color: #475569;
            transition: 0.2s; white-space: nowrap;
        }
        .btn-detail:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }

        #toast {
            position: fixed; bottom: 28px; right: 28px; z-index: 9999;
            display: none; align-items: center; gap: 10px;
            padding: 12px 18px; border-radius: 8px;
            background: #fff; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            font-size: 0.84rem; color: #1e293b; font-weight: 500;
            animation: slideInUp 0.3s ease;
        }
        #toast.show { display: flex; }
        @keyframes slideInUp { from { transform:translateY(16px);opacity:0 } to { transform:none;opacity:1 } }
    </style>
@endpush

@section('konten')
    <div class="container-fluid">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('counselor.dashboard') }}" class="btn-back-link" style="margin-bottom: 0;">
                    <i class="ti ti-arrow-left" style="font-size: 1.2rem;"></i>
                    Dashboard
                </a>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">📑 Daftar Seluruh Mahasiswa</h2>
            </div>
            
            <button class="btn-print" onclick="printElementToPDF('printLevel3Area', 'Daftar_Seluruh_Mahasiswa.pdf')">
                <i class="ti ti-printer" style="font-size: 1.1rem;"></i>
                Cetak Laporan PDF
            </button>
        </div>

        @if($students->isEmpty())
            <div style="text-align: center; padding: 60px 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; color: #94a3b8;">
                <div style="font-size: 2.5rem; margin-bottom: 16px;">✅</div>
                <p>Belum ada mahasiswa yang masuk dalam rekam data AI.</p>
            </div>
        @else
            <div id="printLevel3Area" style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: auto;">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>MAHASISWA</th>
                            <th>NIM</th>
                            <th>GENDER & JURNAL</th>
                            <th>STATUS</th>
                            <th>PREDIKSI AI</th>
                            <th style="text-align: right;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                        @php
                            $lvlClass = $s->mental_level === 3 ? 'l3' : ($s->mental_level === 2 ? 'l2' : ($s->mental_level === 1 ? 'l1' : 'l0'));
                        @endphp
                        <tr>
                            <td>
                                <div class="student-cell">
                                    <div class="avatar {{ $lvlClass }}">{{ substr($s->name, 0, 1) }}</div>
                                    <div class="name-wrapper">
                                        <span class="name">{{ $s->name }}</span>
                                        @if($s->mental_red_flag)
                                            <span class="red-flag">🚨 Red Flag Terdeteksi</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color: #475569; font-size: 0.85rem; font-weight: 500;">
                                {{ $s->nim }}
                            </td>
                            <td style="color: #475569; font-size: 0.85rem;">
                                <div style="font-weight: 500;">{{ $s->gender }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">{{ $s->journal_texts_count }} jurnal</div>
                            </td>
                            <td>
                                <span class="pill-status {{ $lvlClass }}">
                                    <span class="ldot {{ $s->mental_level === 3 ? 'anim' : '' }}"></span>
                                    {{ $s->mental_label }}
                                </span>
                            </td>
                            <td>
                                <div class="conf-wrap">
                                    <span class="conf-val">{{ round($s->mental_confidence) }}%</span>
                                    <div class="conf-bar"><div class="conf-fill {{ $lvlClass }}" style="width:{{ round($s->mental_confidence) }}%"></div></div>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('counselor.detail', $s->nim) }}" class="btn-detail" data-html2canvas-ignore="true">Lihat Riwayat</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div id="toast"></div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function showToast(msg, color = '#059669') {
        const t = document.getElementById('toast');
        t.innerHTML = `<span style="width:8px;height:8px;border-radius:50%;background:${color};display:inline-block;flex-shrink:0"></span> ${msg}`;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function printElementToPDF(elementId, filename) {
        showToast('⏳ Menyusun dokumen PDF...', '#d97706');
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
            showToast('✅ Berhasil mengunduh PDF', '#059669');
        }).catch(err => {
            console.error(err);
            showToast('⚠️ Gagal menyusun PDF', '#dc2626');
        });
    }
</script>
@endpush
