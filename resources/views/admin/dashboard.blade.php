@extends('layouts.admin')

@section('page-title','Dashboard')

@push('styles')
<style>
  /* ── Page Header ── */
  .an-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    margin-bottom: 1.4rem;
  }
  .an-page-header h4 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0d1b2a;
    margin: 0 0 .2rem;
    line-height: 1.2;
  }
  .an-page-header p {
    font-size: .82rem;
    color: #8898aa;
    margin: 0;
  }
  .filter-select {
    border: 1px solid #dde3ea;
    border-radius: 10px;
    padding: .4rem 2rem .4rem .85rem;
    font-size: .82rem;
    color: #5a6a72;
    background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24'%3E%3Cpath fill='%238898aa' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") no-repeat right .6rem center;
    appearance: none;
    cursor: pointer;
    outline: none;
    font-family: inherit;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
  }

  /* ── Stat Cards ── */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.1rem;
  }
  @media(max-width:960px){ .stat-grid { grid-template-columns: repeat(2,1fr); } }
  @media(max-width:520px){ .stat-grid { grid-template-columns: 1fr; } }

  .stat-card {
    background: white;
    border-radius: 16px;
    border: 1px solid #eef0f4;
    padding: 1.2rem 1.3rem 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
    transition: transform .2s, box-shadow .2s;
  }
  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.09);
  }
  .s-emoji { font-size: 1.5rem; margin-bottom: .55rem; display: block; }
  .s-num   { font-size: 2rem; font-weight: 800; color: #0d1b2a; line-height: 1; margin-bottom: .18rem; }
  .s-lbl   { font-size: .78rem; color: #8898aa; margin-bottom: .3rem; }
  .s-change { font-size: .73rem; font-weight: 600; color: #0fb87a; }
  .s-change.down { color: #e74c3c; }

  /* ── Donut Legend ── */
  .donut-wrap { display: flex; justify-content: center; margin-bottom: .75rem; }
  .donut-legend li:nth-child(1) .dot { background:#0fb87a; }
  .donut-legend li:nth-child(2) .dot { background:#1a5c3a; }
  .donut-legend li:nth-child(3) .dot { background:#e74c3c; }
  .donut-legend li:nth-child(4) .dot { background:#f5a623; }
  .donut-legend li:nth-child(5) .dot { background:#2f80ed; }
  .leg-left { display: flex; align-items: center; gap: 8px; }
  .dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
  .leg-pct { font-weight: 700; color: #0d1b2a; font-size: .79rem; }

  /* ── Bottom Row ── */
  .bottom-row { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1rem; }
  @media(max-width:840px){ .bottom-row { grid-template-columns: 1fr; } }

  /* ── Topic Table ── */
  .topic-table { width: 100%; border-collapse: collapse; }
  .topic-table thead th {
    font-size: .71rem; font-weight: 700; color: #aab5bc;
    text-transform: uppercase; letter-spacing: .05em;
    padding: .45rem .8rem; border-bottom: 1px solid #eef0f4; text-align: left;
  }
  .topic-table tbody td {
    font-size: .82rem; color: #5a6a72;
    padding: .62rem .8rem; border-bottom: 1px solid #f4f6f8;
  }
  .topic-table tbody tr:last-child td { border-bottom: none; }
  .topic-table tbody tr:hover td { background: #fafbfc; }
  .t-name { font-weight: 600; color: #0d1b2a; }
  .t-up   { color: #0fb87a; font-weight: 700; }
  .t-down { color: #e74c3c; font-weight: 700; }
  .t-flat { color: #f5a623; font-weight: 700; }

  /* ── Insight Card ── */
  .insight-card {
    background: #fffcf0; border: 1px solid #fce8a0;
    border-radius: 16px; padding: 1.4rem;
    display: flex; flex-direction: column; gap: .6rem;
    box-shadow: 0 2px 10px rgba(0,0,0,.04); height: 100%;
  }
  .insight-emoji { font-size: 2rem; margin-bottom: .1rem; }
  .insight-card h6 { font-size: .95rem; font-weight: 800; color: #0d1b2a; margin: 0; }
  .insight-card p  { font-size: .8rem; color: #7a6930; margin: 0; line-height: 1.65; }
</style>
@endpush

@section('konten')

{{-- ── Page Header ── --}}
<div class="an-page-header">
  <div>
    <p>Lihat statistik dan insight dari layanan konseling Anda</p>
  </div>
  <select class="filter-select">
    <option>Bulan Ini</option>
    <option>3 Bulan Terakhir</option>
    <option>6 Bulan Terakhir</option>
    <option>Tahun Ini</option>
  </select>
</div>

{{-- ── Stat Cards ── --}}
<div class="stat-grid">
  <div class="stat-card">
    <span class="s-emoji">🗂️</span>
    <div class="s-num">{{ $totalPenjadwalan }}</div>
    <div class="s-lbl">Total Penjadwalan</div>
    <div class="s-change">Semua status penjadwalan</div>
  </div>
  <div class="stat-card">
    <span class="s-emoji">👤</span>
    <div class="s-num">{{ $mahasiswaAktif }}</div>
    <div class="s-lbl">Mahasiswa Aktif</div>
    <div class="s-change">Sudah pernah konseling</div>
  </div>
  <div class="stat-card">
    <span class="s-emoji">✅</span>
    <div class="s-num">{{ $disetujui }}</div>
    <div class="s-lbl">Penjadwalan Disetujui</div>
    <div class="s-change">{{ $menunggu }} persetujuan</div>
  </div>
  <div class="stat-card">
    <span class="s-emoji">📉</span>
    <div class="s-num">{{ $approvalRate }}%</div>
    <div class="s-lbl">Approval Rate</div>
    <div class="s-change {{ $ditolak > 0 ? 'down' : '' }}">{{ $ditolak }} jadwal ditolak</div>
  </div>
</div>

{{-- ── Chart Row ── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="kons-card h-100">
            <div class="kons-card-header">
                <h6>Tren Konseling</h6>
            </div>
            <div class="kons-card-body">
                <div class="chart-box">
                    <div id="trenHasilKonseling"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="kons-card h-100">
            <div class="kons-card-header">
                <h6>Distribusi Masalah</h6>
            </div>
            <div class="kons-card-body d-flex flex-column">
                <div class="chart-box chart-box-small">
                    <div id="distribusiMasalah"></div>
                </div>

                <ul class="donut-legend mt-3">
                    @forelse(($topikLabels ?? collect()) as $index => $label)
                        @php
                            $count = $topikCounts[$index] ?? 0;
                            $total = $totalTopik ?? 0;
                            $percent = $total > 0 ? round(($count / $total) * 100) : 0;
                        @endphp

                        <li>
                            <span class="leg-left">
                                <span class="dot"></span>
                                {{ $label }}
                            </span>
                            <span class="leg-pct">{{ $percent }}%</span>
                        </li>
                    @empty
                        <li>
                            <span class="leg-left">Belum ada topik</span>
                            <span class="leg-pct">0%</span>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('template/dist')}}/assets/js/plugins/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

  const trenEl = document.querySelector('#trenHasilKonseling');
  const donutEl = document.querySelector('#distribusiMasalah');

  if (!trenEl) {
    console.error('Element #trenHasilKonseling tidak ditemukan');
    return;
  }

  if (!donutEl) {
    console.error('Element #distribusiMasalah tidak ditemukan');
    return;
  }

  // ── Line Chart ──
  var optionsLine = {
    series: [{
      name: 'Jumlah Sesi',
      data: @json($monthlyCounts)
    }],
    chart: {
      type: 'line',
      height: 220,
      toolbar: { show: false },
      zoom: { enabled: false },
      fontFamily: 'Plus Jakarta Sans, Public Sans, sans-serif'
    },
    colors: ['#0fb87a'],
    stroke: {
      curve: 'smooth',
      width: 2.5
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.18,
        opacityTo: 0,
        stops: [0, 100],
        colorStops: [
          {
            offset: 0,
            color: '#0fb87a',
            opacity: 0.18
          },
          {
            offset: 100,
            color: '#0fb87a',
            opacity: 0
          }
        ]
      }
    },
    markers: {
      size: 5,
      colors: ['#0fb87a'],
      strokeColors: '#ffffff',
      strokeWidth: 2,
      hover: { size: 7 }
    },
    xaxis: {
      categories: @json($monthlyLabels),
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: {
        style: { colors: '#aab5bc', fontSize: '12px' }
      }
    },
    yaxis: {
      min: 0,
      max: 80,
      tickAmount: 4,
      labels: {
        style: { colors: '#aab5bc', fontSize: '11px' }
      }
    },
    grid: {
      borderColor: '#f0f2f5',
      strokeDashArray: 0,
      xaxis: { lines: { show: false } }
    },
    tooltip: {
      theme: 'dark',
      y: {
        formatter: function(val) {
          return val + ' sesi';
        }
      }
    },
    legend: { show: false },
    dataLabels: { enabled: false }
  };

  var chartLine = new ApexCharts(trenEl, optionsLine);
  chartLine.render();

  // ── Donut Chart ──
    var optionsDonut = {
      series: @json($topikCounts ?? []),
      chart: {
        type: 'donut',
        height: 200,
        fontFamily: 'Plus Jakarta Sans, Public Sans, sans-serif',
        toolbar: { show: false }
      },
      labels: @json($topikLabels ?? []),
      colors: ['#0fb87a', '#1a5c3a', '#e74c3c', '#f5a623', '#2f80ed'],
      plotOptions: {
        pie: {
          donut: {
            size: '65%',
            labels: {
              show: true,
              total: {
                show: true,
                label: 'Total',
                fontSize: '12px',
                color: '#8898aa',
                formatter: function() {
                  return '{{ $totalTopik ?? 0 }} topik';
                }
              },
              value: {
                fontSize: '18px',
                fontWeight: 700,
                color: '#0d1b2a'
              }
            }
          }
        }
      },
      stroke: {
        width: 3,
        colors: ['#ffffff']
      },
      dataLabels: { enabled: false },
      legend: { show: false },
      tooltip: {
        theme: 'dark',
        y: {
          formatter: function(val) {
            return val + ' topik';
          }
        }
      }
    };

    var chartDonut = new ApexCharts(donutEl, optionsDonut);
    chartDonut.render();
    });
</script>
@endpush