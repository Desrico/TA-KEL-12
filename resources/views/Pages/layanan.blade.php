@extends('layouts.master')

@push('styles')
<style>
  .layanan-page-hero {
    background: linear-gradient(180deg, var(--navbar-bg) 0%, #ffffff 82%);
    padding: 4.5rem 0 3.5rem;
  }

  .layanan-hero-wrap {
    max-width: 760px;
  }

  .layanan-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 999px;
    padding: .42rem .9rem;
    font-size: .76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 1rem;
  }

  .layanan-title {
    font-family: 'Fraunces', serif;
    font-size: clamp(2rem, 4vw, 3.2rem);
    line-height: 1.12;
    color: var(--primary);
    margin-bottom: 1rem;
  }

  .layanan-desc {
    max-width: 620px;
    color: var(--text-mid);
    font-size: .97rem;
    line-height: 1.85;
    margin: 0;
  }

  .service-switch-wrap {
    margin-top: -1.3rem;
    position: relative;
    z-index: 4;
  }

  .service-switch {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: var(--shadow-sm);
    padding: .7rem;
    display: flex;
    gap: .7rem;
    flex-wrap: wrap;
  }

  .service-tab {
    flex: 1;
    min-width: 220px;
    border: 1px solid var(--border);
    border-radius: 14px;
    background: #fff;
    color: var(--text-mid);
    padding: 1rem 1.1rem;
    text-align: left;
    transition: all .2s ease;
  }

  .service-tab:hover {
    border-color: #cfe7db;
    background: #f8fffb;
  }

  .service-tab.active {
    background: var(--primary-soft);
    border-color: transparent;
    color: var(--primary);
  }

  .service-tab-title {
    display: block;
    font-weight: 700;
    font-size: .95rem;
    margin-bottom: .2rem;
  }

  .service-tab-sub {
    display: block;
    font-size: .82rem;
    color: var(--text-light);
  }

  .service-tab.active .service-tab-sub {
    color: var(--primary);
    opacity: .85;
  }

  .layanan-panel {
    display: none;
    padding-top: 2rem;
  }

  .layanan-panel.active {
    display: block;
  }

  .service-main-card,
  .jadwal-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 22px;
    box-shadow: var(--shadow-sm);
  }

  .service-main-card {
    overflow: hidden;
  }

  .service-main-head {
    padding: 1.6rem 1.6rem 1.2rem;
    border-bottom: 1px solid #eef5f1;
    background: #fcfefd;
  }

  .service-main-head h3 {
    font-family: 'Fraunces', serif;
    font-size: 1.7rem;
    color: var(--text-dark);
    margin-bottom: .45rem;
  }

  .service-main-head p {
    margin: 0;
    color: var(--text-mid);
    font-size: .92rem;
    line-height: 1.75;
    max-width: 650px;
  }

  .service-main-body {
    padding: 1.5rem;
  }

  .service-summary-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
  }

  .service-summary-box {
    background: #f8fffb;
    border: 1px solid #e6f2ec;
    border-radius: 16px;
    padding: 1rem;
  }

  .service-summary-box h6 {
    font-weight: 700;
    font-size: .88rem;
    margin-bottom: .55rem;
    color: var(--text-dark);
  }

  .service-summary-box p,
  .service-summary-box li {
    font-size: .86rem;
    color: var(--text-mid);
    line-height: 1.7;
    margin: 0;
  }

  .service-summary-box ul {
    margin: 0;
    padding-left: 1rem;
  }

  .service-note {
    margin-top: 1rem;
    background: #f7fcf9;
    border-left: 3px solid var(--primary);
    border-radius: 12px;
    padding: .95rem 1rem;
    color: var(--text-mid);
    font-size: .85rem;
    line-height: 1.75;
  }

  .jadwal-card {
    padding: 1.5rem;
    position: sticky;
    top: 88px;
  }

  .jadwal-card h4 {
    font-family: 'Fraunces', serif;
    font-size: 1.35rem;
    color: var(--text-dark);
    margin-bottom: .3rem;
  }

  .jadwal-sub {
    color: var(--text-light);
    font-size: .84rem;
    margin-bottom: 1rem;
  }

  .step-dots {
    display: flex;
    gap: .4rem;
    margin-bottom: 1rem;
  }

  .sd {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #d9e6df;
    transition: all .2s ease;
  }

  .sd.active {
    width: 24px;
    background: var(--primary);
  }

  .sd.done {
    background: #93c5b0;
  }

  .form-label-c {
    font-size: .84rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: .4rem;
    display: block;
  }

  .form-select,
  .form-control {
    border: 1px solid #d8e8df;
    border-radius: 12px;
    padding: .72rem .9rem;
    font-size: .9rem;
    width: 100%;
    transition: all .2s ease;
  }

  .form-select:focus,
  .form-control:focus {
    border-color: #9ccdb5;
    box-shadow: 0 0 0 3px rgba(6, 78, 59, 0.08);
    outline: none;
  }

  .day-selector {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: .45rem;
    margin-bottom: 1rem;
  }

  .day-btn {
    border: 1px solid #d8e8df;
    border-radius: 12px;
    background: #fff;
    padding: .6rem .35rem;
    text-align: center;
    transition: all .2s ease;
    cursor: pointer;
  }

  .day-btn:hover {
    background: #f8fffb;
    border-color: #b9dac8;
  }

  .day-btn.selected {
    background: var(--primary-soft);
    border-color: transparent;
    color: var(--primary);
  }

  .day-btn .d-name {
    display: block;
    font-size: .66rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
  }

  .day-btn .d-date {
    display: block;
    font-size: .78rem;
    margin-top: .15rem;
  }

  .day-btn .d-avail {
    display: block;
    font-size: .62rem;
    opacity: .7;
    margin-top: .15rem;
  }

  .day-btn.past {
    opacity: .4;
    cursor: not-allowed;
  }

  .time-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .45rem;
  }

  .time-slot {
    border: 1px solid #d8e8df;
    border-radius: 12px;
    background: #fff;
    padding: .65rem .4rem;
    text-align: center;
    font-size: .84rem;
    font-weight: 600;
    color: var(--text-mid);
    cursor: pointer;
    transition: all .2s ease;
  }

  .time-slot:hover {
    background: #f8fffb;
    border-color: #b9dac8;
    color: var(--primary);
  }

  .time-slot.selected.online-color,
  .time-slot.selected.offline-color {
    background: var(--primary);
    border-color: var(--primary);
    color: #fff;
  }

  .time-slot.unavail {
    opacity: .38;
    cursor: not-allowed;
    background: #f5f7f6;
  }

  .jadwal-helper {
    font-size: .78rem;
    color: var(--text-light);
    background: #f8fffb;
    border: 1px solid #e6f2ec;
    border-radius: 10px;
    padding: .55rem .75rem;
    margin-bottom: .9rem;
    display: none;
  }

  .jadwal-summary {
    background: #f8fffb;
    border: 1px solid #e6f2ec;
    border-radius: 14px;
    padding: 1rem;
    margin-top: 1rem;
  }

  .bs-row {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding: .42rem 0;
  }

  .bs-row:not(:last-child) {
    border-bottom: 1px dashed #dfece5;
  }

  .bs-label {
    color: var(--text-light);
    font-size: .8rem;
  }

  .bs-val {
    color: var(--text-dark);
    font-size: .84rem;
    font-weight: 600;
    text-align: right;
  }

  .success-screen {
    display: none;
    text-align: center;
    padding: 1.5rem;
  }

  .success-icon {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: var(--primary-soft);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
    margin: 0 auto 1rem;
  }

  .layanan-bottom-note {
    margin-top: 2rem;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 1.2rem 1.3rem;
    color: var(--text-mid);
    font-size: .9rem;
    line-height: 1.75;
  }

  @media (max-width: 991.98px) {
    .jadwal-card {
      position: static;
    }
  }

  @media (max-width: 767.98px) {
    .service-summary-grid {
      grid-template-columns: 1fr;
    }

    .day-selector {
      grid-template-columns: repeat(3, 1fr);
    }

    .time-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 575.98px) {
    .time-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>
@endpush

@section('konten')

<section class="layanan-page-hero">
  <div class="container">
    <div class="layanan-hero-wrap">
      <div class="layanan-kicker">
        <i class="bi bi-heart-pulse"></i>
        Layanan Konseling
      </div>

      <h1 class="layanan-title">Pilih layanan yang sesuai dengan kebutuhanmu</h1>

      <p class="layanan-desc">
        Campus Care menyediakan dua bentuk layanan konseling, yaitu online dan tatap muka di kampus. Halaman ini dirancang agar mahasiswa dapat memahami perbedaan layanan dengan jelas dan mengajukan jadwal sesi secara lebih mudah.
      </p>
    </div>
  </div>
</section>

<div class="container service-switch-wrap">
  <div class="service-switch">
    <button class="service-tab active" id="tab-online" onclick="switchTab('online')">
      <span class="service-tab-title">Konseling Online</span>
      <span class="service-tab-sub">Untuk akses yang lebih fleksibel melalui alur digital</span>
    </button>

    <button class="service-tab" id="tab-offline" onclick="switchTab('offline')">
      <span class="service-tab-title">Konseling Offline</span>
      <span class="service-tab-sub">Untuk sesi tatap muka langsung di lingkungan kampus</span>
    </button>
  </div>
</div>

<div id="panel-online" class="layanan-panel active container">
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="service-main-card">
        <div class="service-main-head">
          <h3>Konseling Online</h3>
          <p>
            Layanan ini membantu mahasiswa memulai proses konseling tanpa harus datang langsung ke kampus. Setelah jadwal dikonfirmasi, sesi dilanjutkan melalui ruang percakapan pada sistem dan dapat berlanjut ke video call sesuai alur layanan.
          </p>
        </div>

        <div class="service-main-body">
          <div class="service-summary-grid">
            <div class="service-summary-box">
              <h6>Cocok digunakan ketika</h6>
              <ul>
                <li>Mahasiswa membutuhkan akses yang lebih fleksibel.</li>
                <li>Mahasiswa belum siap datang langsung ke ruang konseling.</li>
                <li>Mahasiswa berada di luar kampus atau memiliki jadwal padat.</li>
              </ul>
            </div>

            <div class="service-summary-box">
              <h6>Informasi layanan</h6>
              <ul>
                <li>Hari layanan: Senin–Jumat</li>
                <li>Jam layanan: 09.00–16.00 WIB</li>
                <li>Durasi sesi: 1–2 jam</li>
                <li>Biaya: Gratis untuk mahasiswa</li>
              </ul>
            </div>
          </div>

          <div class="service-note">
            Layanan online dirancang untuk memberi akses awal yang lebih mudah. Setelah pengajuan dibuat, mahasiswa dapat melanjutkan proses pada ruang chat dan video call yang tersedia di sistem.
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="jadwal-card" id="jadwal-form-online">
        <h4>Pengajuan Jadwal Konseling Online</h4>
        <div class="jadwal-sub">Pilih hari, waktu, dan topik konseling.</div>

        <div class="step-dots">
          <div class="sd active" id="dot-o-1"></div>
          <div class="sd" id="dot-o-2"></div>
        </div>

        <div id="online-step-1">
          <label class="form-label-c">Pilih Hari</label>
          <div class="day-selector" id="day-selector-online"></div>
          <div id="online-day-info" class="jadwal-helper"></div>

          <label class="form-label-c">Pilih Waktu</label>
          <div class="time-grid" id="time-grid-online"></div>

          <input type="hidden" id="o_selected_time">
          <input type="hidden" id="o_selected_day">
          <input type="hidden" id="o_selected_ymd">
          <input type="hidden" id="o_selected_date">

          <label class="form-label-c mt-3">Topik Konseling *</label>
          <select class="form-select mb-3" id="o_topik">
            <option value="">Pilih Topik</option>
            <option>Stres Akademik</option>
            <option>Kecemasan & Kekhawatiran</option>
            <option>Motivasi Belajar</option>
            <option>Hubungan Sosial</option>
            <option>Bimbingan Karir</option>
            <option>Lainnya</option>
          </select>

          <button type="button" class="btn btn-register-custom w-100" onclick="nextOnline(2)">
            Lanjut
          </button>
        </div>

        <div id="online-step-2" style="display:none">
          <h6 class="fw-bold mb-2" style="font-size:.9rem">Ringkasan Jadwal</h6>
          <div class="jadwal-summary" id="online-summary"></div>

          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="o_setuju">
            <label class="form-check-label" style="font-size:.8rem;color:var(--text-mid)" for="o_setuju">
              Saya menyetujui syarat dan ketentuan layanan
            </label>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary" onclick="showOnlineStep(1)">Kembali</button>
            <button type="button" class="btn btn-register-custom flex-fill" onclick="submitJadwal('online')">
              Ajukan Jadwal
            </button>
          </div>
        </div>
      </div>

      <div class="jadwal-card success-screen" id="success-online">
        <div class="success-icon">
          <i class="bi bi-check-lg"></i>
        </div>
        <h4>Pengajuan jadwal berhasil</h4>
        <p style="font-size:.85rem;color:var(--text-mid);margin-top:.35rem">
          Pengajuan jadwal akan diproses oleh sistem.
        </p>
        <div class="jadwal-summary" id="online-success-detail"></div>
        <button class="btn btn-register-custom w-100 mt-3" onclick="resetJadwal('online')">
          Ajukan Jadwal Baru
        </button>
      </div>
    </div>
  </div>
</div>

<div id="panel-offline" class="layanan-panel container">
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="service-main-card">
        <div class="service-main-head">
          <h3>Konseling Offline</h3>
          <p>
            Layanan ini ditujukan bagi mahasiswa yang lebih nyaman melakukan sesi tatap muka secara langsung. Proses pengajuan tetap dilakukan melalui sistem agar jadwal layanan lebih teratur dan mudah dipantau.
          </p>
        </div>

        <div class="service-main-body">
          <div class="service-summary-grid">
            <div class="service-summary-box">
              <h6>Cocok digunakan ketika</h6>
              <ul>
                <li>Mahasiswa ingin sesi tatap muka secara langsung.</li>
                <li>Mahasiswa membutuhkan suasana konseling yang lebih personal.</li>
                <li>Mahasiswa berada di area kampus dan tersedia hadir sesuai jadwal.</li>
              </ul>
            </div>

            <div class="service-summary-box">
              <h6>Informasi layanan</h6>
              <ul>
                <li>Lokasi: Gedung Kemahasiswaan Lt. 2</li>
                <li>Hari layanan: Senin–Jumat</li>
                <li>Jam layanan: 09.00–16.00 WIB</li>
                <li>Biaya: Gratis untuk mahasiswa</li>
              </ul>
            </div>
          </div>

          <div class="service-note">
            Mahasiswa disarankan datang beberapa menit sebelum sesi dimulai agar proses layanan berjalan lebih tertib.
          </div>
        </div>
      </div>

      <div class="layanan-bottom-note">
        <div><strong>Lokasi:</strong> Gedung 5 (GD5) Lantai 2, antara GD525 & GD526</div>
        <div><strong>Hari:</strong> Senin – Jumat</div>
        <div><strong>Waktu:</strong> 09.00 – 16.00 WIB</div>
    </div>
    </div>

    <div class="col-lg-5">
      <div class="jadwal-card" id="jadwal-form-offline">
        <h4>Pengajuan Jadwal Konseling Offline</h4>
        <div class="jadwal-sub">Pilih hari, waktu, dan topik konseling.</div>

        <div class="step-dots">
          <div class="sd active" id="dot-f-1"></div>
          <div class="sd" id="dot-f-2"></div>
        </div>

        <div id="offline-step-1">
          <label class="form-label-c">Pilih Hari</label>
          <div class="day-selector" id="day-selector-offline"></div>
          <div id="offline-day-info" class="jadwal-helper"></div>

          <label class="form-label-c">Pilih Waktu</label>
          <div class="time-grid" id="time-grid-offline"></div>

          <input type="hidden" id="f_selected_time">
          <input type="hidden" id="f_selected_day">
          <input type="hidden" id="f_selected_ymd">
          <input type="hidden" id="f_selected_date">

          <label class="form-label-c mt-3">Topik Konseling *</label>
          <select class="form-select mb-3" id="f_topik">
            <option value="">Pilih Topik</option>
            <option>Stres Akademik</option>
            <option>Kecemasan & Kekhawatiran</option>
            <option>Motivasi Belajar</option>
            <option>Hubungan Sosial</option>
            <option>Bimbingan Karir</option>
            <option>Lainnya</option>
          </select>

          <button type="button" class="btn btn-register-custom w-100" onclick="nextOffline(2)">
            Lanjut
          </button>
        </div>

        <div id="offline-step-2" style="display:none">
          <h6 class="fw-bold mb-2" style="font-size:.9rem">Ringkasan Jadwal</h6>
          <div class="jadwal-summary" id="offline-summary"></div>

          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="f_setuju">
            <label class="form-check-label" style="font-size:.8rem;color:var(--text-mid)" for="f_setuju">
              Saya menyetujui syarat dan ketentuan layanan
            </label>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary" onclick="showOfflineStep(1)">Kembali</button>
            <button type="button" class="btn btn-register-custom flex-fill" onclick="submitJadwal('offline')">
              Ajukan Jadwal
            </button>
          </div>
        </div>
      </div>

      <div class="jadwal-card success-screen" id="success-offline">
        <div class="success-icon">
          <i class="bi bi-check-lg"></i>
        </div>
        <h4>Pengajuan jadwal berhasil</h4>
        <p style="font-size:.85rem;color:var(--text-mid);margin-top:.35rem">
          Silakan hadir sesuai jadwal yang telah dipilih.
        </p>
        <div class="jadwal-summary" id="offline-success-detail"></div>
        <button class="btn btn-register-custom w-100 mt-3" onclick="resetJadwal('offline')">
          Ajukan Jadwal Baru
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

const onlineSchedule = {
    0: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    1: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    2: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    3: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    4: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
};

const offlineSchedule = {
    0: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    1: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    2: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    3: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
    4: { times:['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] },
};

const dayNames = ['Senin','Selasa','Rabu','Kamis','Jumat'];
let bookedSlotsFromDB = [];

async function fetchBookedSlots() {
    try {
        const res = await fetch('{{ route("jadwal.terisi") }}');
        bookedSlotsFromDB = await res.json();
    } catch(e) {
        bookedSlotsFromDB = [];
    }
}

function getWeekDays() {
    const today = new Date();
    today.setHours(0,0,0,0);

    const dow = today.getDay();
    let diff;

    if (dow === 0) diff = 1;
    else if (dow === 6) diff = 2;
    else diff = 1 - dow;

    const monday = new Date(today);
    monday.setDate(today.getDate() + diff);

    const days = [];
    for (let i = 0; i < 5; i++) {
        const d = new Date(monday);
        d.setDate(monday.getDate() + i);
        days.push(d);
    }

    return days;
}

function toYMD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function parseYMDLocal(ymd) {
    const [year, month, day] = ymd.split('-').map(Number);
    return new Date(year, month - 1, day);
}

function buildDaySelector(type) {
    const today = new Date();
    today.setHours(0,0,0,0);

    const days = getWeekDays();
    const sched = type === 'online' ? onlineSchedule : offlineSchedule;
    const el = document.getElementById(`day-selector-${type}`);
    const shortLabels = ['SEN','SEL','RAB','KAM','JUM'];

    if (!el) return;

    el.innerHTML = '';

    days.forEach((date, i) => {
        const isPast = date < today;
        const slots = sched[i] || { times: [] };

        const d = document.createElement('div');
        d.className = 'day-btn' + (isPast ? ' past' : '');
        d.dataset.dow = i;
        d.dataset.ymd = toYMD(date);
        d.dataset.fulldate = date.toLocaleDateString('id-ID', { day:'numeric', month:'short' });
        d.dataset.dayname = dayNames[i];

        d.innerHTML = `
            <span class="d-name">${shortLabels[i]}</span>
            <span class="d-date">${date.getDate()}/${date.getMonth() + 1}</span>
            <span class="d-avail">${isPast ? 'Lewat' : slots.times.length + ' slot'}</span>
        `;

        if (!isPast) {
            d.onclick = () => selectDay(type, d, i);
        }

        el.appendChild(d);
    });
}

function selectDay(type, el, dow) {
    const sched = type === 'online' ? onlineSchedule : offlineSchedule;
    const ymd = el.dataset.ymd;
    const colorClass = type === 'online' ? 'online-color' : 'offline-color';

    document.querySelectorAll(`#day-selector-${type} .day-btn`).forEach(btn => {
        btn.classList.remove('selected', 'online-color', 'offline-color');
    });

    el.classList.add('selected', colorClass);

    document.getElementById(type === 'online' ? 'o_selected_day' : 'f_selected_day').value = `${el.dataset.dayname}, ${el.dataset.fulldate}`;
    document.getElementById(type === 'online' ? 'o_selected_ymd' : 'f_selected_ymd').value = ymd;
    document.getElementById(type === 'online' ? 'o_selected_time' : 'f_selected_time').value = '';

    const info = document.getElementById(`${type}-day-info`);
    const slots = sched[dow]?.times || [];

    if (info) {
        info.style.display = 'block';
        info.innerHTML = `<strong>${el.dataset.dayname}</strong> — ${slots.length} slot tersedia`;
    }

    const grid = document.getElementById(`time-grid-${type}`);
    grid.innerHTML = '';

    if (slots.length === 0) {
        grid.innerHTML = '<div style="grid-column:span 4;text-align:center;font-size:.82rem;color:var(--text-light);padding:.8rem">Tidak ada jadwal</div>';
        return;
    }

    slots.forEach(t => {
        const parts = t.split(':');
        const hour = parseInt(parts[0], 10);
        const minute = parseInt(parts[1], 10) || 0;

        const slotDate = parseYMDLocal(ymd);
        slotDate.setHours(hour, minute, 0, 0);

        const now = new Date();
        const isPastTime = slotDate < new Date(now.getTime() - 30 * 60 * 1000);

        const key = `${ymd}-${t}`;
        const isBooked = bookedSlotsFromDB.includes(key);

        const slot = document.createElement('div');
        slot.className = 'time-slot' + ((isBooked || isPastTime) ? ' unavail' : '');
        slot.textContent = t + (isBooked ? ' ✗' : '');

        if (!isBooked && !isPastTime) {
            slot.onclick = () => {
                document.querySelectorAll(`#time-grid-${type} .time-slot`).forEach(x => {
                    x.classList.remove('selected', 'online-color', 'offline-color');
                });
                slot.classList.add('selected', colorClass);
                document.getElementById(type === 'online' ? 'o_selected_time' : 'f_selected_time').value = t;
            };
        }

        grid.appendChild(slot);
    });
}

function switchTab(tab) {
    ['online', 'offline'].forEach(t => {
        document.getElementById(`tab-${t}`)?.classList.remove('active');
        document.getElementById(`panel-${t}`)?.classList.remove('active');
    });

    document.getElementById(`tab-${tab}`)?.classList.add('active');
    document.getElementById(`panel-${tab}`)?.classList.add('active');
}

function applyTabFromHash() {
    const hash = (window.location.hash || '').replace('#', '').toLowerCase();
    if (hash === 'online' || hash === 'offline') {
        switchTab(hash);
    }
}

function nextOnline(step) {
    if (!isLoggedIn) {
        if (confirm('Anda harus login terlebih dahulu untuk membuat jadwal. Login sekarang?')) {
            window.location.href = '/login';
        }
        return;
    }

    if (step === 2) {
        if (!document.getElementById('o_selected_day').value) { alert('Pilih hari terlebih dahulu!'); return; }
        if (!document.getElementById('o_selected_time').value) { alert('Pilih waktu terlebih dahulu!'); return; }
        if (!document.getElementById('o_topik').value) { alert('Pilih topik konseling!'); return; }
        buildSummary('online');
    }

    showOnlineStep(step);
}

function showOnlineStep(step) {
    [1, 2].forEach(i => {
        const el = document.getElementById(`online-step-${i}`);
        if (el) el.style.display = i === step ? 'block' : 'none';
    });
    updateDots('online', step);
}

function nextOffline(step) {
    if (!isLoggedIn) {
        if (confirm('Anda harus login terlebih dahulu untuk membuat jadwal. Login sekarang?')) {
            window.location.href = '/login';
        }
        return;
    }

    if (step === 2) {
        if (!document.getElementById('f_selected_day').value) { alert('Pilih hari terlebih dahulu!'); return; }
        if (!document.getElementById('f_selected_time').value) { alert('Pilih waktu terlebih dahulu!'); return; }
        if (!document.getElementById('f_topik').value) { alert('Pilih topik konseling!'); return; }
        buildSummary('offline');
    }

    showOfflineStep(step);
}

function showOfflineStep(step) {
    [1, 2].forEach(i => {
        const el = document.getElementById(`offline-step-${i}`);
        if (el) el.style.display = i === step ? 'block' : 'none';
    });
    updateDots('offline', step);
}

function updateDots(type, step) {
    const prefix = type === 'online' ? 'dot-o-' : 'dot-f-';

    [1, 2].forEach(i => {
        const dot = document.getElementById(prefix + i);
        if (!dot) return;

        dot.classList.remove('active', 'done');

        if (i < step) dot.classList.add('done');
        else if (i === step) dot.classList.add('active');
    });
}

function buildSummary(type) {
    const isOnline = type === 'online';
    const day = document.getElementById(isOnline ? 'o_selected_day' : 'f_selected_day').value;
    const time = document.getElementById(isOnline ? 'o_selected_time' : 'f_selected_time').value;
    const topik = document.getElementById(isOnline ? 'o_topik' : 'f_topik').value;
    const jenisLayanan = isOnline ? 'Online' : 'Offline';

    const isAnonim = {{ Auth::check() ? (Auth::user()->isAnonim() ? 'true' : 'false') : 'false' }};
    const namaDisplay = isAnonim ? 'Mahasiswa Anonim' : '{{ Auth::check() ? Auth::user()->nama : "-" }}';
    const nimDisplay = isAnonim ? '••••••••' : '{{ Auth::check() ? (optional(Auth::user()->mahasiswa)->nim ?? "-") : "-" }}';
    const prodiDisplay = '{{ Auth::check() ? (optional(Auth::user()->mahasiswa)->jurusan ?? "-") : "-" }}';
    const angkatanDisplay = '{{ Auth::check() ? (optional(Auth::user()->mahasiswa)->angkatan ?? "-") : "-" }}';

    const rows = isAnonim ? [
        ['Prodi', prodiDisplay],
        ['Angkatan', angkatanDisplay],
        ['Hari & Waktu', `${day} · ${time} WIB`],
        ['Jenis', jenisLayanan],
        ['Topik', topik],
        ['Konselor', 'Ibu Laura, M.Psi'],
    ] : [
        ['Nama', namaDisplay],
        ['NIM', nimDisplay],
        ['Prodi', prodiDisplay],
        ['Angkatan', angkatanDisplay],
        ['Hari & Waktu', `${day} · ${time} WIB`],
        ['Jenis', jenisLayanan],
        ['Topik', topik],
        ['Konselor', 'Ibu Laura, M.Psi'],
    ];

    const anonimBanner = isAnonim ? `
        <div style="background:rgba(15,184,122,.1);border:1px solid rgba(15,184,122,.3);border-radius:10px;padding:.6rem .8rem;margin-bottom:.8rem;font-size:.78rem;color:#0b6e4a;display:flex;align-items:center;gap:.5rem">
            <i class="bi bi-incognito"></i>
            <span>Mode anonim aktif — identitas kamu tersembunyi dari konselor</span>
        </div>` : '';

    document.getElementById(`${type}-summary`).innerHTML =
        anonimBanner + rows.map(([label, value]) => `
            <div class="bs-row">
                <span class="bs-label">${label}</span>
                <span class="bs-val">${value}</span>
            </div>
        `).join('');
}

async function submitJadwal(type) {
    const setuju = document.getElementById(type === 'online' ? 'o_setuju' : 'f_setuju');
    if (!setuju.checked) {
        alert('Centang persetujuan terlebih dahulu');
        return;
    }

    const isOnline = type === 'online';
    const ymd = document.getElementById(isOnline ? 'o_selected_ymd' : 'f_selected_ymd').value;
    const time = document.getElementById(isOnline ? 'o_selected_time' : 'f_selected_time').value;
    const topik = document.getElementById(isOnline ? 'o_topik' : 'f_topik').value;

    if (!ymd || !time) {
        alert('Pilih hari dan waktu terlebih dahulu');
        return;
    }

    const btn = (window.event && window.event.target)
        ? window.event.target
        : document.querySelector(`#panel-${type} button[onclick*="submitJadwal"]`);

    if (!btn) {
        alert('Tombol konfirmasi tidak ditemukan. Muat ulang halaman lalu coba lagi.');
        return;
    }

    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Memproses...';

    try {
        const checkRes = await fetch('{{ route("jadwal.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ tanggal: ymd, waktu: time, jenis: type, topik: topik })
        });

        const checkData = await checkRes.json();

        if (!checkData.success || !checkData.is_available) {
            alert(checkData.message || 'Jadwal ini sudah tidak tersedia. Silakan pilih slot lain.');
            if (checkData.redirect) window.location.href = checkData.redirect;
            return;
        }

        const res = await fetch('{{ route("jadwal.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ tanggal: ymd, waktu: time, jenis: type, topik: topik })
        });

        const data = await res.json();

        if (data.success) {
            const summaryEl = document.getElementById(`${type}-summary`);
            document.getElementById(`${type}-success-detail`).innerHTML =
                summaryEl.innerHTML +
                `<div class="bs-row"><span class="bs-label">Kode Jadwal</span><span class="bs-val" style="color:var(--accent)">${data.kode_jadwal}</span></div>`;

            document.getElementById(`jadwal-form-${type}`).style.display = 'none';

            const successCard = document.getElementById(`success-${type}`);
            successCard.style.display = 'block';
            successCard.scrollIntoView({ behavior:'smooth', block:'center' });

            await fetchBookedSlots();
        } else {
            alert(data.message);
            if (data.redirect) window.location.href = data.redirect;
        }
    } catch (err) {
        alert('Terjadi kesalahan. Coba lagi.');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function resetJadwal(type) {
    document.getElementById(`jadwal-form-${type}`).style.display = 'block';
    document.getElementById(`success-${type}`).style.display = 'none';

    if (type === 'online') showOnlineStep(1);
    else showOfflineStep(1);

    buildDaySelector(type);
}

fetchBookedSlots().then(() => {
    buildDaySelector('online');
    buildDaySelector('offline');
    applyTabFromHash();
});

window.addEventListener('hashchange', applyTabFromHash);
</script>
@endpush