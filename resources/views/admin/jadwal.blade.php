@extends('layouts.admin')

@section('page-title', 'Penjadwalan')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">

<style>
  .jadwal-page-wrap {
    display: grid;
  }

  .jadwal-calendar-card,
  .jadwal-detail-card {
    background: #ffffff;
    border: 1px solid var(--admin-border);
    border-radius: 18px;
    box-shadow: var(--admin-shadow-sm);
  }

  .jadwal-calendar-card {
    padding: 1.2rem;
  }

  .jadwal-detail-card {
    padding: 1.2rem;
    height: fit-content;
    position: sticky;
    top: 90px;
  }

  .jadwal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
  }

  .jadwal-head h6 {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--admin-text);
  }

  .jadwal-head p {
    margin: .25rem 0 0;
    font-size: .84rem;
    color: var(--admin-text-light);
  }

  #calendar {
    min-height: 650px;
  }

  .fc .fc-toolbar-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--admin-text);
  }

  .fc .fc-button {
    background: #fff !important;
    border: 1px solid var(--admin-border) !important;
    color: var(--admin-text-mid) !important;
    box-shadow: none !important;
    border-radius: 10px !important;
    padding: .45rem .8rem !important;
    text-transform: capitalize !important;
  }

  .fc .fc-button:hover,
  .fc .fc-button.fc-button-active {
    background: var(--admin-soft-2) !important;
    color: var(--admin-primary) !important;
    border-color: var(--admin-border) !important;
  }

  .fc-theme-standard td,
  .fc-theme-standard th {
    border-color: #E9F1EC;
  }

  .fc .fc-col-header-cell-cushion {
    padding: .8rem 0;
    font-size: .82rem;
    font-weight: 600;
    color: var(--admin-text-mid);
    text-decoration: none;
  }

  .fc .fc-daygrid-day-number {
    font-size: .82rem;
    color: var(--admin-text-mid);
    text-decoration: none;
    padding: .5rem;
  }

  .fc .fc-daygrid-event {
    border-radius: 999px;
    padding: 2px 8px;
    font-size: .72rem;
    font-weight: 600;
    margin: 2px 4px;
  }

  .jadwal-legend {
    display: flex;
    gap: 1rem 1.5rem;
    flex-wrap: wrap;
    margin-top: 1.2rem;
    padding-top: 1rem;
    border-top: 1px solid #E9F1EC;
  }

  .legend-item {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    font-size: .83rem;
    color: var(--admin-text-mid);
  }

  .legend-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 2px 6px rgba(0,0,0,.08);
  }

  .detail-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--admin-text);
    margin-bottom: .9rem;
  }

  .detail-empty {
    font-size: .88rem;
    color: var(--admin-text-light);
    line-height: 1.7;
  }

  .detail-list {
    display: grid;
    gap: .8rem;
  }

  .detail-row {
    border: 1px solid #E9F1EC;
    border-radius: 14px;
    padding: .85rem .95rem;
    background: #FAFDFB;
  }

  .detail-label {
    font-size: .73rem;
    font-weight: 700;
    color: var(--admin-text-light);
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: .25rem;
  }

  .detail-value {
    font-size: .88rem;
    color: var(--admin-text);
    line-height: 1.55;
  }

  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    border-radius: 999px;
    padding: .28rem .75rem;
    font-size: .75rem;
    font-weight: 700;
  }

  @media (max-width: 991.98px) {
    .jadwal-page-wrap {
      grid-template-columns: 1fr;
    }

    .jadwal-detail-card {
      position: static;
    }
  }
</style>
@endpush

@section('konten')
<div class="jadwal-page-wrap">
  <div class="jadwal-calendar-card">
    <div class="jadwal-head">
      <div>
        <h6>Kalender Jadwal Konseling</h6>
        <p>Lihat tanggal yang memiliki jadwal konseling dan klik tanggal/event untuk melihat detail.</p>
      </div>
    </div>

    <div id="calendar"></div>

    <div class="jadwal-legend">
      <div class="legend-item">
        <span class="legend-dot" style="background:#E9D98B;"></span>
        <span>Menunggu Konfirmasi</span>
      </div>
      <div class="legend-item">
        <span class="legend-dot" style="background:#B8EEC0;"></span>
        <span>Diterima</span>
      </div>
      <div class="legend-item">
        <span class="legend-dot" style="background:#C9B8F5;"></span>
        <span>Sedang Berlangsung</span>
      </div>
      <div class="legend-item">
        <span class="legend-dot" style="background:#8EC9F5;"></span>
        <span>Selesai</span>
      </div>
      <div class="legend-item">
        <span class="legend-dot" style="background:#F4A6A6;"></span>
        <span>Ditolak</span>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const detailEl = document.getElementById('jadwal-detail');

    const statusMap = {
      'Menunggu': {
        bg: '#FFF8DC',
        color: '#8A6D1D'
      },
      'Disetujui': {
        bg: '#EAFBF0',
        color: '#166534'
      },
      'Berlangsung': {
        bg: '#F1EBFF',
        color: '#6D28D9'
      },
      'Selesai': {
        bg: '#E8F4FF',
        color: '#1D4ED8'
      },
      'Ditolak': {
        bg: '#FDECEC',
        color: '#B91C1C'
      }
    };

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'id',
      height: 'auto',
      firstDay: 0,
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: ''
      },
      buttonText: {
        today: 'Hari ini'
      },
      events: '{{ route("admin.jadwal.events") }}',
      eventClick: function(info) {
        const data = info.event.extendedProps;
        const statusStyle = statusMap[data.status] || {
          bg: '#F3F4F6',
          color: '#374151'
        };

        detailEl.innerHTML = `
          <div class="detail-list">
            <div class="detail-row">
              <div class="detail-label">Mahasiswa</div>
              <div class="detail-value">${data.nama}</div>
            </div>

            <div class="detail-row">
              <div class="detail-label">Tanggal</div>
              <div class="detail-value">${info.event.start.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
              })}</div>
            </div>

            <div class="detail-row">
              <div class="detail-label">Waktu</div>
              <div class="detail-value">${data.waktu} WIB</div>
            </div>

            <div class="detail-row">
              <div class="detail-label">Jenis Layanan</div>
              <div class="detail-value">${data.jenis}</div>
            </div>

            <div class="detail-row">
              <div class="detail-label">Topik</div>
              <div class="detail-value">${data.topik}</div>
            </div>

            <div class="detail-row">
              <div class="detail-label">Status</div>
              <div class="detail-value">
                <span class="status-pill" style="background:${statusStyle.bg}; color:${statusStyle.color};">
                  ${data.status}
                </span>
              </div>
            </div>
          </div>
        `;
      }
    });

    calendar.render();
  });
</script>
@endpush