@extends('layouts.admin')

@section('page-title', 'Penjadwalan Konseling')
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">

<style>
  .jadwal-page-wrap {
    display: grid;
  }

  .jadwal-calendar-card,
  .jadwal-detail-card {
    background: #ffffff;
    border: 2px solid var(--admin-border);
    border-radius: 18px;
    box-shadow: var(--admin-shadow-sm);
    max-width: 1100px;
    margin: 0 auto;
    width: calc(100% - 48px);
  }

  .jadwal-calendar-card {
    padding: 0;
    overflow: hidden;
  }

  .jadwal-detail-card {
    padding: 1.2rem;
    height: fit-content;
    position: sticky;
    top: 90px;
  }

  .jadwal-head {
    padding: 1.5rem 1.7rem 1rem;
    border-bottom: 1px solid #edf2ef;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .jadwal-head h6 {
    margin: 0 0 .3rem 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--admin-primary);
    letter-spacing: -0.3px;
  }

  .jadwal-head p {
    margin: 0;
    font-size: .85rem;
    color: var(--admin-text-light);
  }

  .jadwal-calendar-content {
    padding: 1.2rem 1.4rem;
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
    border: 2px solid var(--admin-border) !important;
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
    margin-top: 0;
    padding: 1rem 1.2rem 0;
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

  .btn-ketidaktersediaan {
    border: 2px solid var(--admin-primary);
    background: #ffffff;
    color: var(--admin-primary);
    padding: 10px 18px;
    border-radius: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s ease;
  }

  .btn-ketidaktersediaan:hover {
    background: var(--admin-primary);
    color: #ffffff;
  }

  .modal-unavailable {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
  }

  .modal-unavailable.show {
    display: flex;
  }

  .modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(2px);
  }

  .modal-box {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 760px;
    background: #fff;
    border-radius: 28px;
    padding: 28px 28px 24px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
    animation: modalFadeIn 0.22s ease;
  }

@keyframes detailModalIn {
  from {
    opacity: 0;
    transform: translateY(12px) scale(.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

  .modal-top {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 22px;
  }

  .modal-icon {
    width: 58px;
    height: 58px;
    min-width: 58px;
    border-radius: 50%;
    background: #E8F6EE;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--admin-primary);
    font-size: 28px;
  }

  .modal-title-wrap h3 {
    margin: 0 0 4px;
    font-size: 24px;
    font-weight: 800;
    color: #111827;
  }

  .modal-title-wrap p {
    margin: 0;
    color: #4B5563;
    font-size: 15px;
  }

  .modal-form {
    width: 100%;
  }

  .modal-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr 1fr;
    gap: 16px;
    margin-bottom: 18px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-group-full {
    margin-bottom: 18px;
  }

  .form-group label {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
  }

  .form-group label span {
    font-weight: 500;
    color: #6B7280;
  }

  .form-group input[type="date"],
  .form-group input[type="time"] {
    cursor: pointer;
  }

  .form-group input[type="date"],
  .form-group input[type="time"],
  .form-group textarea {
    width: 100%;
    border: 1.5px solid #E5E7EB;
    border-radius: 14px;
    background: #fff;
    padding: 13px 14px;
    font-size: 14px;
    color: #111827;
    outline: none;
    transition: 0.2s ease;
  }

  .form-group input[type="date"]:focus,
  .form-group input[type="time"]:focus,
  .form-group textarea:focus {
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px rgba(11, 107, 80, 0.08);
  }

  .form-group textarea {
    resize: none;
    min-height: 120px;
  }

  #charCount {
    margin-top: 6px;
    text-align: right;
    color: #9CA3AF;
    font-size: 12px;
  }

  .repeat-box {
    margin-top: 4px;
  }

  .checkbox-inline {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #374151;
    cursor: pointer;
  }

  .checkbox-inline input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--admin-primary);
  }

  .modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 14px;
    margin-top: 22px;
  }

  .btn-batal,
  .btn-simpan {
    min-width: 138px;
    height: 50px;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s ease;
  }

  .btn-batal {
    border: 1.5px solid #D1D5DB;
    background: #fff;
    color: #6B7280;
  }

  .btn-batal:hover {
    background: #F9FAFB;
  }

  .btn-simpan {
    border: none;
    background: var(--admin-primary);
    color: #fff;
  }

  .btn-simpan:hover {
    background: #09553F;
  }

  body.modal-open {
    overflow: hidden;
  }

.modal-detail {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: rgba(0, 0, 0, 0.35);
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.modal-detail.show {
  display: flex;
}

.modal-detail-box {
  position: relative;
  width: 100%;
  max-width: 450px;
  background: #ffffff;
  border-radius: 18px;
  padding: 28px 30px;
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
}

.modal-detail-box h3 {
  margin: 0 45px 18px 0;
  font-size: 1.45rem;
  font-weight: 800;
  color: #1F2937;
  line-height: 1.3;
}
.modal-detail-box p {
  margin: 0 0 14px;
  font-size: .98rem;
  color: #374151;
  line-height: 1.6;
}


.modal-detail-box p b {
  font-weight: 700;
  color: #111827;
}

.modal-detail-box p span {
  color: #374151;
}

.campus-alert-popup {
    width: 420px !important;
    border-radius: 18px !important;
    background: #065f46 !important;
    color: #ffffff !important;
    padding: 30px 28px !important;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.25) !important;
}

.campus-alert-title {
    color: #ffffff !important;
    font-size: 24px !important;
    font-weight: 800 !important;
    margin-top: 10px !important;
}

.campus-alert-content {
    color: #ecfdf5 !important;
    font-size: 15px !important;
    line-height: 1.5 !important;
    margin-top: 8px !important;
}

.campus-alert-confirm {
    border: none !important;
    border-radius: 8px !important;
    background: #fde68a !important;
    color: #064e3b !important;
    padding: 10px 28px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
}

.campus-alert-confirm:hover {
    background: #fcd34d !important;
}

.campus-alert-container {
    z-index: 999999 !important;
}

.campus-alert-cancel {
    border: 1px solid rgba(255, 255, 255, 0.45) !important;
    border-radius: 8px !important;
    background: transparent !important;
    color: #ffffff !important;
    padding: 10px 28px !important;
    font-weight: 800 !important;
    cursor: pointer !important;
}

.campus-alert-cancel:hover {
    background: rgba(255, 255, 255, 0.12) !important;
}

.campus-alert-popup .swal2-actions {
    display: flex !important;
    justify-content: center !important;
    gap: 18px !important;
    margin-top: 28px !important;
}

.campus-alert-confirm,
.campus-alert-cancel {
    min-width: 150px !important;
}
  
.modal-close {
  position: absolute;
  top: 18px;
  right: 20px;
  width: 34px;
  height: 34px;
  border: none;
  border-radius: 50%;
  background: #F3F4F6;
  color: #374151;
  font-size: 24px;
  line-height: 1;
  cursor: pointer;

  display: flex;
  align-items: center;
  justify-content: center;

  transition: .2s ease;
}

.modal-close:hover {
  background: #E5E7EB;
  color: #111827;
}

.modal-action {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 14px;
  margin-top: 24px;
}

.modal-action form {
  margin: 0;
}

.btn-edit,
.btn-hapus {
  min-width: 92px;
  height: 44px;
  padding: 0 20px;
  border-radius: 10px;
  border: none;
  font-size: .95rem;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  transition: .2s ease;
}

.btn-edit {
  background: var(--admin-primary);
  color: #ffffff !important;
}

.btn-edit:hover {
  background: #09553F;
  color: #ffffff;
}

.btn-hapus {
  background: #DC2626;
  color: #ffffff;
}

.btn-hapus:hover {
  background: #B91C1C;
  color: #ffffff;
}

.btn-edit:hover,
.btn-edit:focus,
.btn-edit:visited {
  color: #ffffff;
}

  /* ==============================
     TOOLBAR KALENDER CUSTOM FIXED
  ============================== */

  .calendar-toolbar-custom {
    display: grid;
    grid-template-columns: 56px minmax(0, 1fr) 56px;
    align-items: center;
    width: 100%;
    min-height: 56px;
    margin: 0 0 20px 0;
  }

  .calendar-nav-btn {
    width: 56px;
    height: 56px;
    border: 1.5px solid #d8eee6 !important;
    background: #ffffff;
    color: #005b4a;
    border-radius: 14px !important;
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;

    transition: 0.2s ease;
  }

  .calendar-nav-btn:hover {
    background: #eef8f4;
    color: var(--admin-primary);
    border-color: var(--admin-primary) !important;
  }

  #prevMonth {
    justify-self: start;
  }

  #nextMonth {
    justify-self: end;
  }

  .calendar-month-title {
    justify-self: center;
    text-align: center;
    font-size: 1.25rem;
    font-weight: 800;
    color: #111827;
    line-height: 1;
  }

  .required-mark {
      color: #dc2626;
      font-weight: 800;
  }

  @media (max-width: 768px) {
    .modal-box {
      padding: 22px 18px;
      border-radius: 22px;
    }

    .modal-grid {
      grid-template-columns: 1fr;
    }

    .modal-actions {
      flex-direction: column;
    }

    .btn-batal,
    .btn-simpan {
      width: 100%;
    }

    .calendar-toolbar-custom {
      grid-template-columns: 46px minmax(0, 1fr) 46px;
      margin-bottom: 16px;
    }

    .calendar-nav-btn {
      width: 46px;
      height: 46px;
      font-size: 24px;
      border-radius: 12px !important;
    }

    .calendar-month-title {
      font-size: 1rem;
    }
  }

  @media (max-width: 576px) {
  .modal-detail-box {
    max-width: 100%;
    padding: 24px 22px;
  }

  .modal-detail-box h3 {
    font-size: 1.25rem;
  }

  .modal-action {
    justify-content: flex-start;
  }

  .btn-edit,
  .btn-hapus {
    min-width: 88px;
  }
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
  <button type="button" class="btn-ketidaktersediaan" onclick="openUnavailableModal()">
        + Atur Ketidaktersediaan
    </button>
</div>
<div id="unavailableDetailModal" class="modal-detail" onclick="handleDetailModalClick(event)">
    <div class="modal-detail-box">
        <button type="button" class="modal-close" onclick="closeUnavailableDetail()">×</button>

        <h3>Detail Ketidaktersediaan</h3>

        <p><b>Tanggal:</b> <span id="detailTanggal"></span></p>
        <p><b>Waktu:</b> <span id="detailWaktu"></span></p>
        <p><b>Alasan:</b> <span id="detailAlasan"></span></p>

        <div class="modal-action">
            <a id="btnEditUnavailable" class="btn-edit">Edit</a>

            <form id="formDeleteUnavailable" method="POST" onsubmit="confirmDeleteUnavailable(event)">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-hapus">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

    <div class="jadwal-calendar-content">

    <div class="calendar-toolbar-custom">
    <button type="button" class="calendar-nav-btn" id="prevMonth" aria-label="Bulan sebelumnya">
        ‹
    </button>

    <div class="calendar-month-title" id="calendarTitle"></div>

    <button type="button" class="calendar-nav-btn" id="nextMonth" aria-label="Bulan berikutnya">
        ›
    </button>
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
        <div class="legend-item">
          <span class="legend-dot" style="background:#D9D9D9;"></span>
          <span>Tidak Tersedia</span>
      </div>
      </div>
    </div>
  </div>
</div>

<div id="unavailableModal" class="modal-unavailable">
    <div class="modal-overlay" onclick="closeUnavailableModal()"></div>

    <div class="modal-box">
        <div class="modal-top">
            <div class="modal-icon">
                <i class="ti ti-calendar-event"></i>
            </div>

            <div class="modal-title-wrap">
                <h3>Atur Ketidaktersediaan</h3>
                <p>Tandai waktu di mana Anda tidak tersedia melakukan konseling.</p>
            </div>
        </div>

       <form id="unavailableForm" action="{{ route('konselor.ketidaktersediaan.store') }}" method="POST" onsubmit="submitUnavailableForm(event)">
            @csrf

            <div class="modal-grid">
                <div class="form-group">
                    <label for="tanggal_mulai">Tanggal</label>
                    <input
                        type="date"
                        name="tanggal_mulai"
                        id="tanggal_mulai"
                        min="{{ now('Asia/Jakarta')->toDateString() }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="jam_mulai">Dari</label>
                    <input type="time" name="jam_mulai" id="jam_mulai" required>
                </div>

                <div class="form-group">
                    <label for="jam_selesai">Sampai</label>
                    <input type="time" name="jam_selesai" id="jam_selesai" required>
                </div>
            </div>

            {{-- disamakan dengan tanggal_mulai supaya backend tetap jalan --}}
            <input type="hidden" name="tanggal_selesai" id="tanggal_selesai">

            <div class="form-group form-group-full">
                <label for="alasan">Alasan <span class="required-mark">*</span></label>
                  <textarea
                      name="alasan"
                      id="alasan"
                      rows="5"
                      maxlength="200"
                      required
                      placeholder="Contoh: rapat, izin, kegiatan kampus"
                  ></textarea>
                <small id="charCount">0/200</small>
            </div>

            <div class="form-group form-group-full repeat-box">
                <label class="checkbox-inline">
                    <input type="checkbox" name="ulang_mingguan" id="ulang_mingguan" value="1">
                    <span>Ulangi setiap minggu pada hari yang sama.</span>
                </label>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-batal" onclick="closeUnavailableModal()">Batal</button>
                <button type="button" class="btn-simpan" onclick="submitUnavailableForm(event)">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

      function getTodayYmd() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function isPastDate(dateValue) {
        if (!dateValue) {
            return false;
        }

        return dateValue < getTodayYmd();
    }
    function openUnavailableModal(reset = true) {
        const modal = document.getElementById('unavailableModal');
        const form = document.getElementById('unavailableForm');

        if (!modal) return;

        if (reset && form) {
            form.action = '{{ route('konselor.ketidaktersediaan.store') }}';
            const methodInput = document.getElementById('methodPut');
            if (methodInput) {
                methodInput.remove();
            }
            form.reset();
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            if (tanggalSelesai) {
                tanggalSelesai.value = '';
            }
        }

        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function handleDetailModalClick(event) {
    if (event.target.id === 'unavailableDetailModal') {
        closeUnavailableDetail();
    }
}

    function closeUnavailableModal() {
        const modal = document.getElementById('unavailableModal');

        if (modal) {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const detailEl = document.getElementById('jadwal-detail');

        const alasanInput = document.getElementById('alasan');
        const charCount = document.getElementById('charCount');

        const tanggalMulai = document.getElementById('tanggal_mulai');
        const tanggalSelesai = document.getElementById('tanggal_selesai');

        const jamMulai = document.getElementById('jam_mulai');
        const jamSelesai = document.getElementById('jam_selesai');

        const modalOverlay = document.querySelector('.modal-overlay');

        function showNativePicker(input) {
            if (!input) return;

            input.style.cursor = 'pointer';

            input.addEventListener('click', function () {
                try {
                    if (typeof input.showPicker === 'function') {
                        input.showPicker();
                    }
                } catch (error) {
                    input.focus();
                }
            });

            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();

                    try {
                        if (typeof input.showPicker === 'function') {
                            input.showPicker();
                        }
                    } catch (error) {
                        input.focus();
                    }
                }
            });
        }

        showNativePicker(tanggalMulai);
        showNativePicker(jamMulai);
        showNativePicker(jamSelesai);

        if (tanggalMulai) {
            tanggalMulai.setAttribute('min', getTodayYmd());

            tanggalMulai.addEventListener('change', function () {
                if (isPastDate(this.value)) {
                    this.value = '';

                    if (tanggalSelesai) {
                        tanggalSelesai.value = '';
                    }

                    showKetidaktersediaanWarning(
                        'Tanggal Tidak Valid',
                        'Tanggal yang sudah lewat tidak dapat dipilih.'
                    );

                    return;
                }

                if (tanggalSelesai) {
                    tanggalSelesai.value = this.value;
                }
            });
        }

        if (alasanInput && charCount) {
            alasanInput.addEventListener('input', function () {
                charCount.textContent = `${this.value.length}/200`;
            });
        }

        if (modalOverlay) {
            modalOverlay.addEventListener('click', function () {
                closeUnavailableModal();
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeUnavailableModal();
            }
        });

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
          },
          'Tidak Tersedia': {
              bg: '#D9D9D9',
              color: '#374151'
          }
      };

      const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'id',
    height: 'auto',
    firstDay: 0,
    headerToolbar: false,

    buttonText: {
        today: 'Hari ini'
    },

    events: '{{ route("admin.jadwal.events") }}',

    datesSet: function (info) {
        const titleEl = document.getElementById('calendarTitle');
        if (titleEl) {
            titleEl.innerText = info.view.title;
        }
    },

   eventClick: function(info) {
    info.jsEvent.preventDefault();

    const data = info.event.extendedProps;

    if (data.status === 'Tidak Tersedia') {
        document.getElementById('detailTanggal').textContent =
            info.event.start.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

        document.getElementById('detailWaktu').textContent = data.waktu ?? '-';
        document.getElementById('detailAlasan').textContent = data.alasan ?? '-';

        document.getElementById('btnEditUnavailable').onclick = function () {
            editUnavailable(data);
        };

        document.getElementById('formDeleteUnavailable').action =
            `/konselor/ketidaktersediaan/${data.id}`;

        document.getElementById('unavailableDetailModal').classList.add('show');
        return;
    }

    if (data.detail_url) {
    window.location.href = '{{ route("admin.riwayat") }}';
    return;
}

window.location.href = '{{ route("admin.riwayat") }}';
}
});

calendar.render();

document.getElementById('prevMonth').addEventListener('click', function () {
    calendar.prev();
});

document.getElementById('nextMonth').addEventListener('click', function () {
    calendar.next();
});
});
   function submitUnavailableForm(event) {
    event.preventDefault();

    const form = event.target;

    const tanggal = document.getElementById('tanggal_mulai')?.value;
    const jamMulai = document.getElementById('jam_mulai')?.value;
    const jamSelesai = document.getElementById('jam_selesai')?.value;
    const alasan = document.getElementById('alasan')?.value.trim();

    if (!tanggal || !jamMulai || !jamSelesai || !alasan) {
        showKetidaktersediaanWarning(
            'Data Belum Lengkap',
            'Tanggal, jam mulai, jam selesai, dan alasan wajib diisi.'
        );
        return;
    }

    if (typeof isPastDate === 'function' && isPastDate(tanggal)) {
        showKetidaktersediaanWarning(
            'Tanggal Tidak Valid',
            'Tanggal ketidaktersediaan tidak boleh menggunakan tanggal yang sudah lewat.'
        );
        return;
    }

    if (jamSelesai <= jamMulai) {
        showKetidaktersediaanWarning(
            'Waktu Tidak Valid',
            'Jam selesai harus lebih besar dari jam mulai.'
        );
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Simpan',
        html: `
            <div class="ketidaktersediaan-popup-text">
                Apakah Anda yakin ingin menyimpan data ketidaktersediaan konselor?
            </div>
        `,
        icon: 'question',
        iconColor: '#fde68a',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        buttonsStyling: false,
        target: document.body,
        backdrop: true,
        allowOutsideClick: false,
        customClass: {
            container: 'campus-alert-container',
            popup: 'campus-alert-popup',
            title: 'campus-alert-title',
            htmlContainer: 'campus-alert-content',
            confirmButton: 'campus-alert-confirm',
            cancelButton: 'campus-alert-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

function closeUnavailableDetail() {
    document.getElementById('unavailableDetailModal').classList.remove('show');
}

function editUnavailable(data) {

    closeUnavailableDetail();
    openUnavailableModal(false);

    const tanggalInput = document.getElementById('tanggal_mulai');
    const tanggalSelesai = document.getElementById('tanggal_selesai');
    const jamMulaiInput = document.getElementById('jam_mulai');
    const jamSelesaiInput = document.getElementById('jam_selesai');
    const alasanInput = document.getElementById('alasan');

    if (tanggalInput) {
        tanggalInput.value = data.tanggal ?? '';
    }

    if (jamMulaiInput) {
        jamMulaiInput.value = data.jam_mulai ?? '';
    }

    if (jamSelesaiInput) {
        jamSelesaiInput.value = data.jam_selesai ?? '';
    }

    if (alasanInput) {
        alasanInput.value = data.alasan ?? '';
    }

    if (tanggalSelesai) {
        tanggalSelesai.value = data.tanggal ?? '';
    }

    const form = document.getElementById('unavailableForm');

    if (form) {

        form.action = `/admin/ketidaktersediaan/${data.id}`;

        let methodInput = document.getElementById('methodPut');

        if (!methodInput) {
            methodInput = document.createElement('input');

            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.id = 'methodPut';

            form.appendChild(methodInput);
        }

        methodInput.value = 'PUT';
    }
}

function showKetidaktersediaanWarning(title, message) {
    Swal.fire({
        title: title,
        html: `
            <div class="ketidaktersediaan-popup-text">
                ${message}
            </div>
        `,
        icon: 'warning',
        iconColor: '#fde68a',
        confirmButtonText: 'OK',
        buttonsStyling: false,
        target: document.body,
        backdrop: true,
        allowOutsideClick: false,
        customClass: {
            container: 'campus-alert-container',
            popup: 'campus-alert-popup',
            title: 'campus-alert-title',
            htmlContainer: 'campus-alert-content',
            confirmButton: 'campus-alert-confirm'
        }
    });
}

function confirmDeleteUnavailable(event) {
    event.preventDefault();

    const form = event.target;

    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `
            <div class="ketidaktersediaan-popup-text">
                Apakah Anda yakin ingin menghapus data ketidaktersediaan ini?
            </div>
        `,
        icon: 'warning',
        iconColor: '#fde68a',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        buttonsStyling: false,
        target: document.body,
        backdrop: true,
        allowOutsideClick: false,
        customClass: {
            container: 'campus-alert-container',
            popup: 'campus-alert-popup',
            title: 'campus-alert-title',
            htmlContainer: 'campus-alert-content',
            confirmButton: 'campus-alert-confirm',
            cancelButton: 'campus-alert-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

</script>
@endpush