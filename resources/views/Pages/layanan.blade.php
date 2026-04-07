@extends('layouts.master')

@push('styles')
<style>
/* HERO */
.layanan-hero{
  background:linear-gradient(140deg,#071825 0%,#0d2d4a 45%,#0e5c3d 100%);
  padding:4rem 0 5rem;position:relative;overflow:hidden;
}
.layanan-hero::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(circle at 75% 40%,rgba(15,184,122,.15) 0%,transparent 55%),
             radial-gradient(circle at 20% 70%,rgba(46,134,193,.1) 0%,transparent 45%);
}
.layanan-hero-title{font-family:'Fraunces',serif;font-size:clamp(2rem,4vw,3rem);font-weight:700;color:white;line-height:1.1;}
.layanan-hero-title em{color:#52e8a6;font-style:italic;}

/* TAB PILLS */
.tab-pill-wrap{
  background:white;border-radius:20px;padding:1.5rem;
  box-shadow:0 12px 48px rgba(13,27,42,.12);
  margin-top:-2.5rem;position:relative;z-index:10;
}
.tab-pill{
  flex:1;border-radius:14px;padding:1.1rem;
  background:transparent;border:2px solid rgba(26,58,92,.1);
  cursor:pointer;transition:all .25s;text-align:center;
}
.tab-pill:hover{border-color:rgba(26,58,92,.2);background:rgba(26,58,92,.02);}
.tab-pill.active{border-color:transparent;box-shadow:0 4px 20px rgba(13,27,42,.12);}
.tab-pill.active.online-tab{background:linear-gradient(135deg,#1a3a5c,#2e86c1);}
.tab-pill.active.offline-tab{background:linear-gradient(135deg,#0b3d24,#0fb87a);}
.tab-pill .tp-icon{font-size:1.8rem;margin-bottom:.3rem;}
.tab-pill .tp-label{font-weight:700;font-size:.9rem;display:block;}
.tab-pill .tp-sub{font-size:.72rem;opacity:.65;}
.tab-pill.active .tp-label,.tab-pill.active .tp-sub{color:white;}
.tab-pill.active .tp-sub{opacity:.75;}

/* PANEL */
.panel{display:none;animation:fadeUp .4s ease both;}
.panel.active{display:block;}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}

/* SERVICE INFO CARD */
.service-info-card{
  background:white;border-radius:20px;overflow:hidden;
  box-shadow:var(--shadow-md);
}
.sic-head{padding:2rem 2rem 1.5rem;position:relative;overflow:hidden;}
.sic-head.online-head{background:linear-gradient(135deg,#1a3a5c,#2e86c1);}
.sic-head.offline-head{background:linear-gradient(135deg,#0b3d24,#0fb87a);}
.sic-head::before{content:'';position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.05);}
.sic-head h3{font-family:'Fraunces',serif;font-weight:700;color:white;margin-bottom:.4rem;font-size:1.6rem;}
.sic-head p{color:rgba(255,255,255,.75);font-size:.88rem;margin:0;}
.sic-badge{display:inline-flex;align-items:center;gap:.4rem;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);border-radius:50px;padding:.2rem .75rem;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.75rem;}
.sic-body{padding:1.8rem;}

/* INFO ROWS */
.info-row{display:flex;justify-content:space-between;align-items:center;padding:.55rem 0;font-size:.86rem;}
.info-row:not(:last-child){border-bottom:1px dashed rgba(26,58,92,.08);}
.info-row .label{color:var(--text-light);}
.info-row .val{font-weight:600;color:var(--text-dark);}

/* FEATURE PILLS */
.feat-pill{display:inline-flex;align-items:center;gap:.4rem;background:var(--surface);border-radius:50px;padding:.25rem .75rem;font-size:.78rem;font-weight:600;color:var(--text-mid);margin:.2rem;}
.feat-pill i{font-size:.85rem;}

/* BOOKING CARD */
.booking-card{
  background:white;border-radius:20px;padding:2rem;
  box-shadow:var(--shadow-md);position:sticky;top:85px;
}
.booking-card h5{font-family:'Fraunces',serif;font-weight:700;margin-bottom:.3rem;}

/* KONSELOR MINI */
.konselor-mini{
  display:flex;align-items:center;gap:.9rem;
  background:var(--surface);border-radius:12px;padding:.9rem;
  margin-bottom:1rem;
}
.km-avatar{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;}
.km-name{font-weight:700;font-size:.88rem;}
.km-role{font-size:.75rem;color:var(--text-light);}
.km-status{margin-left:auto;display:flex;align-items:center;gap:.3rem;font-size:.72rem;font-weight:600;}

/* DAY SELECTOR */
.day-selector{display:grid;grid-template-columns:repeat(5,1fr);gap:.4rem;margin-bottom:1rem;}
.day-btn{
  border:1.5px solid rgba(26,58,92,.12);border-radius:10px;
  padding:.5rem .3rem;text-align:center;cursor:pointer;transition:all .2s;
  background:white;
}
.day-btn:hover{border-color:rgba(26,58,92,.2);background:rgba(26,58,92,.03);}
.day-btn.selected{border-color:transparent;box-shadow:0 4px 20px rgba(13,27,42,.12);}
.day-btn.selected.online-color{background:linear-gradient(135deg,#1a3a5c,#2e86c1);color:white;}
.day-btn.selected.offline-color{background:linear-gradient(135deg,#0b3d24,#0fb87a);color:white;}
.day-btn .d-name{font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;display:block;}
.day-btn .d-date{font-size:.72rem;display:block;margin-top:.15rem;}
.day-btn .d-avail{font-size:.6rem;margin-top:.15rem;display:block;opacity:.6;}
.day-btn.past{opacity:.38;cursor:not-allowed;background:rgba(0,0,0,.02);}
.day-btn.today-highlight{border-color:#0fb87a;box-shadow:0 0 0 2px rgba(15,184,122,.2);}

/* TIME GRID */
.time-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.4rem;}
.time-slot{
  border:1.5px solid rgba(26,58,92,.12);border-radius:10px;
  padding:.5rem;text-align:center;font-size:.8rem;font-weight:600;
  color:var(--text-mid);cursor:pointer;transition:all .2s;
}
.time-slot:hover{border-color:var(--primary-light);color:var(--primary);background:rgba(26,58,92,.04);}
.time-slot.selected.online-color{background:var(--primary);border-color:var(--primary);color:white;}
.time-slot.selected.offline-color{background:var(--accent);border-color:var(--accent);color:white;}
.time-slot.unavail{opacity:.35;cursor:not-allowed;background:rgba(0,0,0,.03);}

/* FORM */
.form-label-c{font-size:.83rem;font-weight:600;color:var(--text-dark);margin-bottom:.35rem;display:block;}
.form-control,.form-select{
  border:1.5px solid rgba(26,58,92,.13);border-radius:10px;
  padding:.6rem .85rem;font-size:.88rem;transition:all .2s;width:100%;
}
.form-control:focus,.form-select:focus{
  border-color:var(--primary-light);box-shadow:0 0 0 3px rgba(46,134,193,.12);outline:none;
}

/* MEDIA OPTION */
.media-opt{
  border:1.5px solid rgba(26,58,92,.15);border-radius:10px;
  padding:.6rem;text-align:center;font-size:.8rem;font-weight:600;
  transition:all .2s;cursor:pointer;
}
.media-opt:hover{border-color:var(--primary-light);}
.media-opt.selected-online{border-color:var(--primary);background:rgba(26,58,92,.07);color:var(--primary);}
.media-opt.selected-offline{border-color:var(--accent);background:rgba(15,184,122,.07);color:#0b3d24;}

/* STEP DOTS */
.step-dots{display:flex;gap:.4rem;margin-bottom:1.2rem;}
.sd{width:8px;height:8px;border-radius:50%;background:rgba(26,58,92,.15);transition:all .2s;}
.sd.active{width:24px;border-radius:4px;background:var(--primary);}
.sd.active.green{background:var(--accent);}
.sd.done{background:var(--primary);opacity:.4;}

/* SUCCESS */
.success-screen{display:none;text-align:center;padding:1.5rem 0;}
.success-icon{width:72px;height:72px;border-radius:50%;margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-size:2rem;animation:popIn .4s cubic-bezier(.175,.885,.32,1.275);}
@keyframes popIn{from{transform:scale(0)}to{transform:scale(1)}}
.booking-summary{background:var(--surface);border-radius:14px;padding:1.1rem;font-size:.84rem;text-align:left;margin-top:1rem;}
.bs-row{display:flex;justify-content:space-between;padding:.4rem 0;}
.bs-row:not(:last-child){border-bottom:1px dashed rgba(26,58,92,.08);}
.bs-label{color:var(--text-light);font-size:.78rem;}
.bs-val{font-weight:600;font-size:.82rem;}

/* DARURAT */
.darurat-box{background:linear-gradient(135deg,#7b1fa2,#c62828);border-radius:14px;padding:1.2rem;color:white;}
</style>
@endpush

@section('konten')

<!-- HERO -->
<section class="layanan-hero">
  <div class="container position-relative" style="z-index:1">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="layanan-hero-title mb-3">Pilih Layanan<br>Konseling <em>Untukmu</em></h1>
        <p style="color:rgba(255,255,255,.7);font-size:.97rem;line-height:1.75;max-width:500px">
          Dua jalur layanan tersedia — online maupun tatap muka. Semua gratis, profesional, dan terjamin kerahasiaannya untuk mahasiswa IT Del.
        </p>
      </div>
      <div class="col-lg-5 d-none d-lg-flex gap-3 justify-content-end">
        <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:1rem 1.2rem;backdrop-filter:blur(12px)">
          <div style="font-size:.68rem;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem">Lokasi Offline</div>
          <div style="color:white;font-weight:700;font-size:.88rem">Gedung 5 lantai 2</div>
          <div style="color:rgba(255,255,255,.55);font-size:.75rem">ruangan diantara GD525 & 526</div>
        </div>
        <div style="background:rgba(15,184,122,.12);border:1px solid rgba(15,184,122,.25);border-radius:14px;padding:1rem 1.2rem;backdrop-filter:blur(12px)">
          <div style="font-size:.68rem;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem">Jam Operasional</div>
          <div style="color:white;font-weight:700;font-size:.88rem">Sen - Jum</div>
          <div style="color:rgba(255,255,255,.55);font-size:.75rem">09.00 - 16.00 WIB</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TAB PILLS -->
<div class="container">
  <div class="tab-pill-wrap">
    <div class="d-flex gap-3">
      <button class="tab-pill online-tab active" id="tab-online" onclick="switchTab('online')">
        <div class="tp-icon">💻</div>
        <span class="tp-label">Konseling Online</span>
        <div class="tp-sub">Video Call / Chat</div>
      </button>
      <button class="tab-pill offline-tab" id="tab-offline" onclick="switchTab('offline')">
        <div class="tp-icon">🏛️</div>
        <span class="tp-label">Konseling Offline</span>
        <div class="tp-sub">Tatap Muka di Kampus</div>
      </button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════
     PANEL ONLINE
══════════════════════════════════ -->
<div id="panel-online" class="panel active container" style="margin-top:2rem">
  <div class="row g-4">

    <!-- LEFT: Info -->
    <div class="col-lg-7">
      <div class="service-info-card">
        <div class="sic-head online-head">
          <span class="sic-badge"><span style="width:7px;height:7px;border-radius:50%;background:#52e8a6;display:inline-block;animation:statusPulse 2s ease infinite"></span> Layanan Aktif</span>
          <h3>Konseling Online</h3>
          <p>Sesi konseling jarak jauh yang aman dan nyaman. Pilih video call, voice, atau chat sesuai preferensimu.</p>
        </div>
        <div class="sic-body">
          <div class="konselor-mini">
            <div class="km-avatar" style="background:linear-gradient(135deg,var(--primary),var(--primary-light))">👩‍⚕️</div>
            <div>
              <div class="km-name">Ibu laura, M.Psi</div>
              <div class="km-role">Psikologi Klinis · Konselor IT Del</div>
            </div>
            <div class="km-status" style="color:var(--accent)">
              <span style="width:7px;height:7px;border-radius:50%;background:var(--accent);display:inline-block;animation:statusPulse 2s ease infinite"></span>Online
            </div>
          </div>

          <h6 class="fw-bold mb-2" style="font-size:.85rem">Fitur Tersedia:</h6>
          <div class="mb-3">
            <span class="feat-pill"><i class="bi bi-camera-video-fill text-primary"></i>Video Call 1-on-1</span>
            <span class="feat-pill"><i class="bi bi-chat-dots-fill text-success"></i>Sesi Chat</span>
            <span class="feat-pill"><i class="bi bi-mic-fill" style="color:var(--warm)"></i>Voice Call</span>
            <span class="feat-pill"><i class="bi bi-lock-fill text-primary"></i>Enkripsi E2E</span>
            <span class="feat-pill"><i class="bi bi-file-earmark-text-fill text-secondary"></i>Laporan Sesi</span>
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <div class="p-3" style="background:var(--surface);border-radius:12px">
                <h6 style="font-size:.8rem;font-weight:700;margin-bottom:.6rem"><i class="bi bi-clock text-primary me-1"></i>Jadwal Konselor</h6>
                <div class="info-row"><span class="label">Senin – Jumat</span><span class="val">09.00 – 16.00</span></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3" style="background:var(--surface);border-radius:12px">
                <h6 style="font-size:.8rem;font-weight:700;margin-bottom:.6rem"><i class="bi bi-info-circle text-primary me-1"></i>Detail Sesi</h6>
                <div class="info-row"><span class="label">Durasi</span><span class="val">1-2 jam</span></div>
                <div class="info-row"><span class="label">Biaya</span><span class="val" style="color:var(--accent)">Gratis</span></div>
              </div>
            </div>
          </div>

          <div class="mt-3 p-3" style="background:rgba(26,58,92,.05);border-radius:12px;border-left:3px solid var(--primary-light)">
            <p style="font-size:.83rem;color:var(--text-mid);margin:0"><strong>💡 Tips:</strong> Gunakan koneksi internet stabil dan tempat yang tenang untuk sesi yang optimal.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT: Booking Form Online -->
    <div class="col-lg-5">
      <div class="booking-card" id="booking-form-online">
        <h5>Booking Konseling Online</h5>
        <p style="font-size:.82rem;color:var(--text-light);margin-bottom:1.2rem">Pilih jadwal, topik, dan media sesi</p>

        <!-- Step dots -->
        <div class="step-dots">
          <div class="sd active" id="dot-o-1"></div>
          <div class="sd" id="dot-o-2"></div>
        </div>

        <!-- Step 1: Jadwal + Topik + Media -->
        <div id="online-step-1">

          <label class="form-label-c">Pilih Hari</label>
          <div class="day-selector" id="day-selector-online"></div>
          <div id="online-day-info" style="font-size:.78rem;color:var(--text-mid);background:var(--surface);border-radius:8px;padding:.5rem .7rem;margin-bottom:.8rem;display:none"></div>

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

          <label class="form-label-c">Media Sesi *</label>
          <div class="d-flex gap-2 mb-3">
            <label style="flex:1;cursor:pointer">
              <input type="radio" name="o_media" value="Video Call" class="d-none" onchange="updateMediaUI('online')">
              <div class="media-opt" data-val="Video Call">📹 Video Call</div>
            </label>
            <label style="flex:1;cursor:pointer">
              <input type="radio" name="o_media" value="Chat" class="d-none" onchange="updateMediaUI('online')">
              <div class="media-opt" data-val="Chat">💬 Chat</div>
            </label>
          </div>

          <button type="button" class="btn btn-primary w-100 rounded-pill" onclick="nextOnline(2)" style="font-weight:600">
            Lanjut <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </div>

        <!-- Step 2: Konfirmasi -->
        <div id="online-step-2" style="display:none">
          <h6 class="fw-bold mb-2" style="font-size:.88rem">Ringkasan Booking</h6>
          <div class="booking-summary" id="online-summary"></div>

          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="o_setuju">
            <label class="form-check-label" style="font-size:.78rem;color:var(--text-mid)" for="o_setuju">
              Saya menyetujui <a href="#" style="color:var(--primary)">syarat & ketentuan</a> layanan
            </label>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3" onclick="showOnlineStep(1)">
              <i class="bi bi-arrow-left"></i>
            </button>
            <button type="button" class="btn btn-primary rounded-pill flex-fill" style="font-weight:600" onclick="submitBooking('online')">
              <i class="bi bi-calendar-check me-1"></i>Konfirmasi
            </button>
          </div>
        </div>
      </div>

      <!-- Success Online -->
      <div class="booking-card success-screen" id="success-online">
        <div class="success-icon" style="background:linear-gradient(135deg,var(--primary),var(--primary-light))">✅</div>
        <h5 style="font-family:'Fraunces',serif">Booking Berhasil! 🎉</h5>
        <p style="font-size:.85rem;color:var(--text-mid);margin-top:.3rem">Konfirmasi akan dikirim dalam 2 jam kerja.</p>
        <div class="booking-summary" id="online-success-detail"></div>
        <button class="btn btn-primary w-100 rounded-pill mt-3" onclick="resetBooking('online')">
          <i class="bi bi-plus-circle me-2"></i>Buat Booking Baru
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ══════════════════════════════════
     PANEL OFFLINE
══════════════════════════════════ -->
<div id="panel-offline" class="panel container" style="margin-top:2rem">
  <div class="row g-4">

    <!-- LEFT: Info -->
    <div class="col-lg-7">
      <div class="service-info-card">
        <div class="sic-head offline-head">
          <span class="sic-badge"><i class="bi bi-building"></i> Ruang BK IT Del</span>
          <h3>Konseling Offline</h3>
          <p>Sesi tatap muka langsung di ruang Bimbingan & Konseling kampus IT Del. Lebih personal dan mendalam.</p>
        </div>
        <div class="sic-body">
          <div class="konselor-mini">
            <div class="km-avatar" style="background:linear-gradient(135deg,#0b3d24,var(--accent))">👩‍⚕️</div>
            <div>
              <div class="km-name">Ibu Laura, M.Psi</div>
              <div class="km-role">Gd. Kemahasiswaan Lt. 2</div>
            </div>
            <div class="km-status" style="color:var(--accent)">
              <span style="width:7px;height:7px;border-radius:50%;background:var(--accent);display:inline-block"></span>Hadir
            </div>
          </div>

          <h6 class="fw-bold mb-2" style="font-size:.85rem">Fasilitas Ruang BK:</h6>
          <div class="mb-3">
            <span class="feat-pill"><i class="bi bi-door-closed-fill" style="color:var(--accent)"></i>Ruang Privat</span>
            <span class="feat-pill"><i class="bi bi-snow2 text-info"></i>AC & Nyaman</span>
            <span class="feat-pill"><i class="bi bi-cup-hot-fill text-warning"></i>Ruang Tunggu</span>
            <span class="feat-pill"><i class="bi bi-wifi text-success"></i>Wi-Fi</span>
            <span class="feat-pill"><i class="bi bi-journal-bookmark-fill text-secondary"></i>Buku & Modul</span>
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <div class="p-3" style="background:var(--surface);border-radius:12px">
                <h6 style="font-size:.8rem;font-weight:700;margin-bottom:.6rem"><i class="bi bi-geo-alt text-success me-1"></i>Lokasi</h6>
                <div style="font-size:.84rem;color:var(--text-mid);line-height:1.6">
                  <strong>Gedung Kemahasiswaan</strong><br>
                  Lantai 2, Ruang BK-01<br>
                  IT Del, Sitoluama
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3" style="background:var(--surface);border-radius:12px">
                <h6 style="font-size:.8rem;font-weight:700;margin-bottom:.6rem"><i class="bi bi-calendar-week text-success me-1"></i>Jadwal Konselor</h6>
                <div class="info-row"><span class="label">Senin – Jumat</span><span class="val">09.00 – 16.00</span></div>
              </div>
            </div>
          </div>

          <div class="mt-3 p-3" style="background:rgba(15,184,122,.06);border-radius:12px;border-left:3px solid var(--accent)">
            <p style="font-size:.83rem;color:var(--text-mid);margin:0"><strong>📍 Petunjuk:</strong> Datang 10 menit sebelum jadwal dan bawa kartu mahasiswa aktif.</p>
          </div>
        </div>
      </div>

      <div style="background:white;border-radius:16px;padding:1.2rem;box-shadow:var(--shadow-sm);margin-top:1rem;display:flex;align-items:center;gap:1rem;">
        <div style="width:56px;height:56px;border-radius:14px;background:rgba(15,184,122,.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0">📍</div>
        <div>
          <div style="font-weight:700;font-size:.9rem">Institut Teknologi Del</div>
          <div style="font-size:.8rem;color:var(--text-light)">Sitoluama, Laguboti, Kab. Toba, Sumatera Utara 22381</div>
        </div>
        <a href="https://maps.google.com" target="_blank" class="btn btn-sm btn-outline-success rounded-pill ms-auto" style="white-space:nowrap">
          <i class="bi bi-map me-1"></i>Maps
        </a>
      </div>
    </div>

    <!-- RIGHT: Booking Form Offline -->
    <div class="col-lg-5">
      <div class="booking-card" id="booking-form-offline">
        <h5>Booking Konseling Offline</h5>
        <p style="font-size:.82rem;color:var(--text-light);margin-bottom:1.2rem">Reservasi tatap muka di kampus</p>

        <div class="step-dots">
          <div class="sd active green" id="dot-f-1"></div>
          <div class="sd" id="dot-f-2"></div>
        </div>

        <!-- Step 1: Jadwal + Topik -->
        <div id="offline-step-1">

          <label class="form-label-c">Pilih Hari</label>
          <div class="day-selector" id="day-selector-offline"></div>
          <div id="offline-day-info" style="font-size:.78rem;color:var(--text-mid);background:var(--surface);border-radius:8px;padding:.5rem .7rem;margin-bottom:.8rem;display:none"></div>

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

          <label class="form-label-c">Media Sesi *</label>
          <div class="d-flex gap-2 mb-3">
            <label style="flex:1;cursor:pointer">
              <input type="radio" name="o_media" value="Video Call" class="d-none" onchange="updateMediaUI('online')">
              <div class="media-opt" data-val="Video Call">📹 Video Call</div>
            </label>
            <label style="flex:1;cursor:pointer">
              <input type="radio" name="o_media" value="Chat" class="d-none" onchange="updateMediaUI('online')">
              <div class="media-opt" data-val="Chat">💬 Chat</div>
            </label>
          </div>

          <button type="button" class="btn w-100 rounded-pill" style="background:var(--accent);color:white;font-weight:600" onclick="nextOffline(2)">
              Lanjut <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </div>

        <!-- Step 2: Konfirmasi -->
        <div id="offline-step-2" style="display:none">
          <h6 class="fw-bold mb-2" style="font-size:.88rem">Ringkasan Booking</h6>
          <div class="booking-summary" id="offline-summary"></div>

          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="f_setuju">
            <label class="form-check-label" style="font-size:.78rem;color:var(--text-mid)" for="f_setuju">
              Saya menyetujui <a href="#" style="color:var(--accent)">syarat & ketentuan</a> layanan
            </label>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3" onclick="showOfflineStep(1)">
              <i class="bi bi-arrow-left"></i>
            </button>
            <button type="button" class="btn rounded-pill flex-fill" style="background:var(--accent);color:white;font-weight:600" onclick="submitBooking('offline')">
              <i class="bi bi-calendar-check me-1"></i>Konfirmasi
            </button>
          </div>
        </div>
      </div>

      <!-- Success Offline -->
      <div class="booking-card success-screen" id="success-offline">
        <div class="success-icon" style="background:linear-gradient(135deg,#0b3d24,var(--accent))">✅</div>
        <h5 style="font-family:'Fraunces',serif">Booking Berhasil! 🎉</h5>
        <p style="font-size:.85rem;color:var(--text-mid);margin-top:.3rem">Datang tepat waktu dan bawa kartu mahasiswa aktif.</p>
        <div class="booking-summary" id="offline-success-detail"></div>
        <button class="btn w-100 rounded-pill mt-3" style="background:var(--accent);color:white;font-weight:600" onclick="resetBooking('offline')">
          <i class="bi bi-plus-circle me-2"></i>Buat Booking Baru
        </button>
      </div>
    </div>

  </div>
</div>

<!-- DARURAT -->
<!-- <div class="container" style="margin-top:2.5rem;margin-bottom:0">
  <div class="darurat-box d-flex align-items-center gap-3">
    <div style="font-size:2rem;flex-shrink:0">🚨</div>
    <div class="flex-grow-1">
      <h6 style="font-weight:700;margin:0 0 .2rem;font-size:.95rem">Kondisi Darurat Mental Health?</h6>
      <p style="margin:0;font-size:.82rem;opacity:.85">Hubungi <strong>119 ext 8</strong> (Into The Light) atau langsung ke UKS IT Del.</p>
    </div>
    <a href="tel:119" class="btn btn-sm" style="background:rgba(255,255,255,.2);color:white;border-radius:50px;white-space:nowrap;font-weight:600;border:1px solid rgba(255,255,255,.3)">
      <i class="bi bi-telephone-fill me-1"></i>119 ext 8
    </a>
  </div>
</div>

<div style="height:4rem"></div> -->

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
        const res = await fetch('{{ route("booking.booked") }}');
        bookedSlotsFromDB = await res.json();
    } catch(e) {
        bookedSlotsFromDB = [];
    }
}

function getWeekDays(){
    const today = new Date();
    today.setHours(0,0,0,0);
    const dow = today.getDay();
    let diff;
    if      (dow === 0) diff = 1;
    else if (dow === 6) diff = 2;
    else                diff = 1 - dow;
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

function buildDaySelector(type){
    const today = new Date(); today.setHours(0,0,0,0);
    const days  = getWeekDays();
    const sched = type === 'online' ? onlineSchedule : offlineSchedule;
    const el    = document.getElementById(`day-selector-${type}`);
    const accentColor = type === 'online' ? 'var(--primary)' : 'var(--accent)';
    el.innerHTML = '';
    const shortLabels = ['SEN','SEL','RAB','KAM','JUM'];

    days.forEach((date, i) => {
        const isPast  = date < today;
        const isToday = date.getTime() === today.getTime();
        const slots   = sched[i] || { times: [] };

        const d = document.createElement('div');
        d.className        = 'day-btn' + (isPast ? ' past' : '');
        d.dataset.dow      = i;
        d.dataset.ymd      = toYMD(date);
        d.dataset.fulldate = date.toLocaleDateString('id-ID', { day:'numeric', month:'short' });
        d.dataset.dayname  = dayNames[i];

        d.innerHTML = `
            <span class="d-name">${shortLabels[i]}</span>
            <span class="d-date">${date.getDate()}/${date.getMonth()+1}</span>
            <span class="d-avail">${isPast ? 'Lewat' : slots.times.length + ' slot'}</span>
        `;

        if (isToday) {
            d.style.borderColor = accentColor;
            d.style.boxShadow   = `0 0 0 2px ${accentColor}22`;
        }

        if (!isPast) d.onclick = () => selectDay(type, d, i);
        el.appendChild(d);
    });
}

function selectDay(type, el, dow){
    const sched      = type === 'online' ? onlineSchedule : offlineSchedule;
    const ymd        = el.dataset.ymd;
    const colorClass = type === 'online' ? 'online-color' : 'offline-color';

    document.querySelectorAll(`#day-selector-${type} .day-btn`).forEach(b => b.classList.remove('selected','online-color','offline-color'));
    el.classList.add('selected', colorClass);

    document.getElementById(type === 'online' ? 'o_selected_day' : 'f_selected_day').value = el.dataset.dayname + ', ' + el.dataset.fulldate;
    document.getElementById(type === 'online' ? 'o_selected_ymd' : 'f_selected_ymd').value = ymd;
    document.getElementById(type === 'online' ? 'o_selected_time' : 'f_selected_time').value = '';

    const info  = document.getElementById(`${type}-day-info`);
    const slots = sched[dow]?.times || [];
    info.style.display = 'block';
    info.innerHTML = `<i class="bi bi-clock me-1"></i><strong>${el.dataset.dayname}</strong> — ${slots.length} slot tersedia`;

    const grid = document.getElementById(`time-grid-${type}`);
    grid.innerHTML = '';

    if (slots.length === 0) {
        grid.innerHTML = '<div style="grid-column:span 4;text-align:center;font-size:.82rem;color:var(--text-light);padding:.8rem">Tidak ada jadwal</div>';
        return;
    }

    slots.forEach(t => {
    // Perbaikan: parse jam dan menit dengan benar
    const parts    = t.split(':');
    const hour     = parseInt(parts[0], 10);
    const minute   = parseInt(parts[1], 10) || 0;

    const slotDate = parseYMDLocal(ymd);
    slotDate.setHours(hour, minute, 0, 0);

    // Tambah toleransi 30 menit agar slot yang sedang berjalan masih bisa dipilih
    const now         = new Date();
    const isPastTime  = slotDate < new Date(now.getTime() - 30 * 60 * 1000);

    const key      = `${ymd}-${t}`;
    const isBooked = bookedSlotsFromDB.includes(key);

    const s = document.createElement('div');
    s.className   = 'time-slot' + ((isBooked || isPastTime) ? ' unavail' : '');
    s.textContent = t + (isBooked ? ' ✗' : '');

    if (!isBooked && !isPastTime) {
        s.onclick = () => {
            document.querySelectorAll(`#time-grid-${type} .time-slot`)
                .forEach(x => x.classList.remove('selected','online-color','offline-color'));
            s.classList.add('selected', colorClass);
            document.getElementById(type === 'online' ? 'o_selected_time' : 'f_selected_time').value = t;
        };
    }
    grid.appendChild(s);
});
}

function switchTab(tab){
    ['online','offline'].forEach(t => {
        document.getElementById(`tab-${t}`)?.classList.remove('active');
        document.getElementById(`panel-${t}`)?.classList.remove('active');
    });
    document.getElementById(`tab-${tab}`).classList.add('active');
    document.getElementById(`panel-${tab}`).classList.add('active');
    document.getElementById(`panel-${tab}`).scrollIntoView({behavior:'smooth', block:'start'});
}

// ── ONLINE STEPS ──
function nextOnline(step){
    // Cek login
    if (!isLoggedIn) {
        if (confirm('Anda harus login terlebih dahulu untuk booking. Login sekarang?')) {
            window.location.href = '/login';
        }
        return;
    }

    if (step === 2) {
        if (!document.getElementById('o_selected_day').value)  { alert('Pilih hari terlebih dahulu!'); return; }
        if (!document.getElementById('o_selected_time').value) { alert('Pilih waktu terlebih dahulu!'); return; }
        if (!document.getElementById('o_topik').value)         { alert('Pilih topik konseling!'); return; }
        if (!document.querySelector('input[name="o_media"]:checked')) { alert('Pilih media sesi!'); return; }
        buildSummary('online');
    }
    showOnlineStep(step);
}

function showOnlineStep(s){
    [1,2].forEach(i => {
        const el = document.getElementById(`online-step-${i}`);
        if (el) el.style.display = i === s ? 'block' : 'none';
    });
    updateDots('online', s);
}

// ── OFFLINE STEPS ──
function nextOffline(step){
    // Cek login
    if (!isLoggedIn) {
        if (confirm('Anda harus login terlebih dahulu untuk booking. Login sekarang?')) {
            window.location.href = '/login';
        }
        return;
    }

    if (step === 2) {
        if (!document.getElementById('f_selected_day').value)  { alert('Pilih hari terlebih dahulu!'); return; }
        if (!document.getElementById('f_selected_time').value) { alert('Pilih waktu terlebih dahulu!'); return; }
        if (!document.getElementById('f_topik').value)         { alert('Pilih topik konseling!'); return; }
        if (!document.querySelector('input[name="o_media"]:checked')) { alert('Pilih media sesi!'); return; }
        buildSummary('offline');
    }
    showOfflineStep(step);
}

function showOfflineStep(s){
    [1,2].forEach(i => {
        const el = document.getElementById(`offline-step-${i}`);
        if (el) el.style.display = i === s ? 'block' : 'none';
    });
    updateDots('offline', s);
}

function updateDots(type, step){
    const prefix = type === 'online' ? 'dot-o-' : 'dot-f-';
    const gClass = type === 'offline' ? 'green' : '';
    [1,2].forEach(i => {
        const d = document.getElementById(prefix + i);
        if (!d) return;
        d.classList.remove('active','done', gClass);
        if      (i < step)  { d.classList.add('done'); }
        else if (i === step) { d.classList.add('active'); if(gClass) d.classList.add(gClass); }
    });
}

function buildSummary(type){
    const isOnline = type === 'online';
    const day   = document.getElementById(isOnline ? 'o_selected_day'  : 'f_selected_day').value;
    const time  = document.getElementById(isOnline ? 'o_selected_time' : 'f_selected_time').value;
    const topik = document.getElementById(isOnline ? 'o_topik'         : 'f_topik').value;
    const media = isOnline ? (document.querySelector('input[name="o_media"]:checked')?.value || '-') : '-';


     // Sesuaikan dengan mode anonim
    const isAnonim   = {{ Auth::check() ? (Auth::user()->isAnonim() ? 'true' : 'false') : 'false' }};
    const namaDisplay = isAnonim ? 'Mahasiswa Anonim' : '{{ Auth::check() ? Auth::user()->nama : "-" }}';
    const nimDisplay  = isAnonim ? '••••••••' : '{{ Auth::check() ? (optional(Auth::user()->mahasiswa)->nim ?? "-") : "-" }}';
    const prodiDisplay = '{{ Auth::check() ? (optional(Auth::user()->mahasiswa)->jurusan ?? "-") : "-" }}';
    const angkatanDisplay = '{{ Auth::check() ? (optional(Auth::user()->mahasiswa)->angkatan ?? "-") : "-" }}';

    const rows = [
        ['Nama',         namaDisplay + (isAnonim ? ' 🎭' : '')],
        ['NIM',          nimDisplay],
        ['Prodi',        prodiDisplay],
        ['Angkatan',     angkatanDisplay],
        ['Hari & Waktu', `${day} · ${time} WIB`],
        ['Jenis',         isOnline ? `💻 Online (${media})` : '🏛️ Offline'],
        ['Topik',         topik],
        ['Konselor',     'Ibu Laura, M.Psi'],
    ];

    const anonimBanner = isAnonim ? `
        <div style="background:rgba(15,184,122,.1);border:1px solid rgba(15,184,122,.3);border-radius:10px;padding:.6rem .8rem;margin-bottom:.8rem;font-size:.78rem;color:#0b6e4a;display:flex;align-items:center;gap:.5rem">
            <i class="bi bi-incognito"></i>
            <span>Mode anonim aktif — identitas kamu tersembunyi dari konselor</span>
        </div>` : '';

    document.getElementById(`${type}-summary`).innerHTML =
        rows.map(([l,v]) => `<div class="bs-row"><span class="bs-label">${l}</span><span class="bs-val">${v}</span></div>`).join('');
}

async function submitBooking(type){
    const setuju = document.getElementById(type === 'online' ? 'o_setuju' : 'f_setuju');
    if (!setuju.checked) { alert('Centang persetujuan terlebih dahulu'); return; }

    const isOnline = type === 'online';
    const ymd      = document.getElementById(isOnline ? 'o_selected_ymd'  : 'f_selected_ymd').value;
    const time     = document.getElementById(isOnline ? 'o_selected_time' : 'f_selected_time').value;
    const topik    = document.getElementById(isOnline ? 'o_topik'         : 'f_topik').value;

    if (!ymd || !time) { alert('Pilih hari dan waktu terlebih dahulu'); return; }

    const btn = event.target;
    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memproses...';

    try {
        const res  = await fetch('{{ route("booking.store") }}', {
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
                `<div class="bs-row"><span class="bs-label">Kode Booking</span><span class="bs-val" style="color:var(--accent)">${data.kode_booking}</span></div>`;

            document.getElementById(`booking-form-${type}`).style.display = 'none';
            const sc = document.getElementById(`success-${type}`);
            sc.style.display = 'block';
            sc.scrollIntoView({ behavior:'smooth', block:'center' });

            await fetchBookedSlots();
        } else {
            alert(data.message);
            if (data.redirect) window.location.href = data.redirect;
        }
    } catch(err) {
        alert('Terjadi kesalahan. Coba lagi.');
        console.error(err);
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-calendar-check me-1"></i>Konfirmasi';
    }
}

function resetBooking(type){
    document.getElementById(`booking-form-${type}`).style.display = 'block';
    document.getElementById(`success-${type}`).style.display      = 'none';
    type === 'online' ? showOnlineStep(1) : showOfflineStep(1);
    buildDaySelector(type);
}

function updateMediaUI(type){
    document.querySelectorAll('.media-opt').forEach(el => {
        el.style.borderColor = 'rgba(26,58,92,.15)';
        el.style.background  = '';
        el.style.color       = '';
    });
    const checked = document.querySelector(`input[name="${type === 'online' ? 'o_media' : 'f_media'}"]:checked`);
    if (checked) {
        const opt = checked.closest('label').querySelector('.media-opt');
        opt.style.borderColor = 'var(--primary)';
        opt.style.background  = 'rgba(26,58,92,.07)';
    }
}

// INIT
fetchBookedSlots().then(() => {
    buildDaySelector('online');
    buildDaySelector('offline');
});
</script>
@endpush