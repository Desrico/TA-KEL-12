# Campus Care - UI Design System & Style Guide (Dashboard & Edukasi)

Dokumen ini berisi spesifikasi visual terfokus untuk **Sidebar**, **Dasbor Admin/Konselor**, dan **Modul & Manajemen Edukasi** aplikasi **Campus Care (Mental Health IT Del)**. Panduan ini dirancang khusus untuk mempermudah pembuatan komponen dan *library styles* yang presisi di **Figma**.

---

## 1. Skema Warna (Color Palette)

Gunakan variabel warna asli dari stylesheet proyek ini untuk menetapkan *Color Styles* di Figma.

### A. Tema Dasbor & Sidebar (Dashboard & Sidebar Tokens)
Digunakan pada tata letak dasbor konselor dan menu sidebar utama.

* **Primary (Deep Emerald - `--admin-primary`):** `#064E3B`
  * *Penggunaan:* Latar belakang header sidebar yang sedang aktif, header navigasi utama, serta warna dasar tombol aksi utama.
* **Primary Hover (Forest Green - `--admin-primary-700`):** `#065F46`
  * *Penggunaan:* Efek hover pada tombol aksi utama dan teks judul-judul penting yang ditebalkan.
* **Green Accent (Primary 600 - `--admin-primary-600`):** `#047857`
  * *Penggunaan:* Warna aksen hijau penekanan, tautan menu navigasi aktif, dan ikon utama.
* **Emerald Soft (Primary 500 - `--admin-soft`):** `#D1FAE5`
  * *Penggunaan:* Latar belakang menu sidebar sebelah kiri dan lencana status sukses.
* **Mint White (Soft 2 - `--admin-soft-2`):** `#EFFCF5`
  * *Penggunaan:* Efek warna sorot (hover) pada baris menu samping dan navigasi sekunder.
* **Warm Cream (Admin BG - `--admin-bg`):** `#FFFAF4`
  * *Penggunaan:* Latar belakang utama (background canvas) seluruh halaman dasbor konselor.
* **Emerald Border (Border - `--admin-border`):** `#DDEFE7`
  * *Penggunaan:* Garis tepi (border) kartu penampung data, garis tabel sesi, dan input field form.
* **Slate 900 (Dark Text - `--admin-text`):** `#0F172A`
  * *Penggunaan:* Warna teks utama untuk judul halaman, sub-judul, header kartu, dan header kolom tabel.
* **Slate 600 (Mid Text - `--admin-text-mid`):** `#475569`
  * *Penggunaan:* Warna teks sekunder untuk paragraf biasa, deskripsi menu, dan label form input.
* **Slate 500 (Light Text - `--admin-text-light`):** `#64748B`
  * *Penggunaan:* Warna teks bantuan (helper text), penunjuk waktu (timestamp) notifikasi, serta placeholder input.
* **Red (Danger - `--admin-danger`):** `#DC2626`
  * *Penggunaan:* Warna teks status ditolak/batal, teks alert kegagalan, dan tombol aksi destruktif (hapus).

### B. Tema Modul & Manajemen Edukasi (Education Tokens)
Digunakan secara khusus pada halaman portal edukasi dan manajemen modul.

* **Off-White (Edu BG - `--edu-bg`):** `#F9FAFB`
  * *Penggunaan:* Latar belakang utama kanvas portal edukasi.
* **Pure White (Edu Card - `--edu-card`):** `#FFFFFF`
  * *Penggunaan:* Latar belakang kartu (card box) modul edukasi.
* **Edu Border (Border - `--edu-border`):** `#E5E7EB`
  * *Penggunaan:* Garis pembatas kartu modul dan tabel manajemen edukasi.
* **Emerald Green (Edu Accent - `--edu-green`):** `#059669`
  * *Penggunaan:* Tombol "Tambah Modul", aksen warna hijau portal edukasi, dan link Call-to-Action modul.
* **Green Soft (Light Tint - `--edu-green-light`):** `#D1FAE5`
  * *Penggunaan:* Latar belakang badge status jumlah modul terdaftar, dan latar belakang ikon hijau.
* **Blue (Edu Blue - `--edu-blue`):** `#0284C7`
  * *Penggunaan:* Latar belakang lencana kategori konten web dan aksen warna biru edukasi.
* **Blue Light (Light Tint - `--edu-blue-light`):** `#E0F2FE`
  * *Penggunaan:* Latar belakang badge "Konten Edukasi Web" terdaftar, dan latar belakang ikon biru.


### C. Teks (Typography Colors)
* **Dark Slate (Primary Text):** `#0F172A` (atau `#1E293B` untuk teks dasbor)
  * *Penggunaan:* Teks utama untuk judul halaman, sub-judul, header kartu, dan header kolom tabel.
* **Medium Slate (Secondary Text):** `#475569` (atau `#6B7280` untuk deskripsi edukasi)
  * *Penggunaan:* Teks sekunder untuk penjelasan ringkas, deskripsi modul, dan label form input.
* **Light Slate (Disabled/Helper Text):** `#64748B` (atau `#94A3B8` untuk metadata)
  * *Penggunaan:* Teks bantuan (helper text), penunjuk waktu (timestamp), placeholder input, dan teks metadata.

### D. Warna Semantik & Status (Semantic Colors)
* **Danger (Red):** `#DC2626` (Latar lencana / alert: `#FEE2E2` / `#FEF2F2`)
  * *Penggunaan:* Status pembatalan/penolakan sesi, pesan kesalahan (error alerts), penanda kasus urgent risiko tinggi mahasiswa, tombol hapus data, serta pop-up konfirmasi hapus.
* **Warning (Amber):** `#B45309` (Latar lencana: `#FEF3C7`)
  * *Penggunaan:* Status penundaan (menunggu persetujuan), penanda tingkat emosional sedang mahasiswa, pesan peringatan, serta pop-up konfirmasi peringatan.
* **Info (Blue):** `#1D4ED8` (Latar lencana: `#DBEAFE` / `#E0F2FE`, Ikon Edukasi: `#0284C7`)
  * *Penggunaan:* Status penyelesaian sesi konseling, grafik tren emosional mahasiswa, ikon kategori modul, serta lencana informasi portal edukasi.

---

## 2. Tipografi (Typography)

Seluruh dasbor admin dan manajemen edukasi menggunakan satu keluarga font utama: **`Plus Jakarta Sans`**.

### Aturan Tipografi Figma (Dashboard & Edukasi)

* **Dashboard Title:**
  * *Font Family:* Plus Jakarta Sans
  * *Size (px):* `48px` (3rem)
  * *Weight:* Bold (`800`)
  * *Line Height:* `110%`
  * *Penggunaan:* Judul besar dashboard konselor.
* **Section Title:**
  * *Font Family:* Plus Jakarta Sans
  * *Size (px):* `32px` (2rem)
  * *Weight:* Bold (`800`)
  * *Line Height:* `120%`
  * *Penggunaan:* Judul halaman portofolio/edukasi.
* **Card Title:**
  * *Font Family:* Plus Jakarta Sans
  * *Size (px):* `20px` (1.25rem)
  * *Weight:* Bold (`800`)
  * *Line Height:* `130%`
  * *Penggunaan:* Nama kartu modul edukasi (`.edu-card-title`).
* **Table Header:**
  * *Font Family:* Plus Jakarta Sans
  * *Size (px):* `12px` (0.75rem)
  * *Weight:* Bold (`700`)
  * *Line Height:* `120%`
  * *Penggunaan:* Kolom header tabel manajemen modul (`<th>`).
* **Body Text:**
  * *Font Family:* Plus Jakarta Sans
  * *Size (px):* `15px` (0.95rem)
  * *Weight:* Regular (`400`)
  * *Line Height:* `165%`
  * *Penggunaan:* Deskripsi modul, petunjuk halaman (`.edu-card-desc`).
* **Badge Text:**
  * *Font Family:* Plus Jakarta Sans
  * *Size (px):* `11px` (0.72rem)
  * *Weight:* Bold (`700`)
  * *Line Height:* `120%`
  * *Penggunaan:* Label status aktif/draft, badge jumlah modul.

---

### 3. Sudut & Efek Visual (Radius & Shadows)

* **Corner Radius & Dimensi Figma (Radius & Box Sizes):**
  * *Catatan Acuan Figma:* Ukuran di bawah ini didasarkan pada **Frame Canvas Desktop `1440px` x `900px`** dengan lebar area kerja konten sebesar **`1076px`** (setelah dikurangi sidebar `300px` dan padding `32px` di kiri & kanan).
  * **Kartu Modul Edukasi (`.edu-card`):**
    * *Corner Radius:* `20px`
    * *Lebar (Width - W):* `526px` (untuk layout 2-kolom di portal edukasi, fleksibel)
    * *Tinggi (Height - H):* `345px` (tinggi minimum tetap)
  * **Kartu Ringkasan Statistik (`.card-box`):**
    * *Corner Radius:* `16px`
    * *Lebar (Width - W):* `342px` (acuan standard layout 3-kolom di dasbor)
    * *Tinggi (Height - H):* `124px` (tinggi tetap di dasbor)
  * **Kartu Alert Risiko Tinggi (`.alert-banner`):**
    * *Corner Radius:* `16px`
    * *Lebar (Width - W):* `1076px` (lebar penuh halaman kontainer)
    * *Tinggi (Height - H):* `120px` (tinggi standard representatif)
  * **Kartu Mahasiswa Prioritas (`.p-card`):**
    * *Corner Radius:* `16px`
    * *Lebar (Width - W):* `257px` (acuan standard layout 4-kolom)
    * *Tinggi (Height - H):* `160px` (tinggi representatif)
  * **Kontainer Seksi Formulir (`.f-section`):**
    * *Corner Radius:* `16px`
    * *Lebar (Width - W):* `900px` (lebar kontainer formulir tengah)
    * *Tinggi (Height - H):* `520px` (tinggi representatif di Figma)
  * **Input Form & Dropdown Setengah Lebar (`.f-input` / `.f-select`):**
    * *Corner Radius:* `10px`
    * *Lebar (Width - W):* `414px` (untuk layout form 2-kolom dalam seksi `900px`)
    * *Tinggi (Height - H):* `46px`
  * **Input Form & Textarea Lebar Penuh (`.f-input` / `.f-textarea`):**
    * *Corner Radius:* `10px`
    * *Lebar (Width - W):* `844px` (lebar penuh dalam seksi `900px` setelah dikurangi padding `28px` kiri-kanan)
    * *Tinggi (Height - H):* `46px` (input) atau `140px` (textarea)
  * **Area Drag & Drop File (`.f-upload-area`):**
    * *Corner Radius:* `12px`
    * *Lebar (Width - W):* `844px` (lebar penuh form)
    * *Tinggi (Height - H):* `180px` (tinggi representatif di Figma)
  * **Tombol Kirim Formulir (`.f-submit`):**
    * *Corner Radius:* `12px`
    * *Lebar (Width - W):* `194px` (menyesuaikan isi teks tombol)
    * *Tinggi (Height - H):* `48px`
  * **Tabs Kontrol Dasbor (`.dashboard-tabs`):**
    * *Corner Radius:* `18px`
    * *Lebar (Width - W):* `360px` (representatif)
    * *Tinggi (Height - H):* `62px`
  * **Tombol Tab Dasbor (`.dashboard-tab-btn`):**
    * *Corner Radius:* `12px`
    * *Lebar (Width - W):* `168px` (representatif)
    * *Tinggi (Height - H):* `46px`
  * **Tombol Utama Dasbor (`.btn-primary`):**
    * *Corner Radius:* `10px`
    * *Lebar (Width - W):* `180px` (representatif)
    * *Tinggi (Height - H):* `42px`
  * **Filter Dropdown Dasbor (`.filter-dropdown`):**
    * *Corner Radius:* `6px`
    * *Lebar (Width - W):* `160px` (representatif)
    * *Tinggi (Height - H):* `38px`
  * **Tabel Premium (`.premium-table`):**
    * *Corner Radius:* `16px`
    * *Lebar (Width - W):* `100%` (lebar penuh halaman kontainer)
    * *Tinggi (Height - H):* Menyesuaikan jumlah baris data
  * **Wadah Ikon Modul (`.edu-card-icon`):**
    * *Corner Radius:* `14px`
    * *Lebar (Width - W):* `52px`
    * *Tinggi (Height - H):* `52px`
  * **Sidebar Menu Active Link (`.pc-link`):**
    * *Corner Radius:* `12px`
    * *Lebar (Width - W):* `260px`
    * *Tinggi (Height - H):* `42px`
  * **Dropdown Menu Profil:**
    * *Corner Radius:* `12px`
    * *Lebar (Width - W):* `100%` (mengikuti kontainer dropdown)
    * *Tinggi (Height - H):* Auto (menyesuaikan jumlah item menu)
  * **Thumbnail Modul (`.mi-thumb`):**
    * *Corner Radius:* `10px`
    * *Lebar (Width - W):* `52px`
    * *Tinggi (Height - H):* `52px`
  * **Tombol Tambah Modul (`.mi-btn-add`):**
    * *Corner Radius:* `8px`
    * *Lebar (Width - W):* Auto (menyesuaikan teks + padding kiri-kanan `16px`)
    * *Tinggi (Height - H):* `38px`
  * **Dropdown Sort (`.mi-sort-select`):**
    * *Corner Radius:* `8px`
    * *Lebar (Width - W):* Auto (menyesuaikan pilihan teks)
    * *Tinggi (Height - H):* `34px`
  * **Tombol Aksi Ikon (`.mi-btn-icon`):**
    * *Corner Radius:* `8px`
    * *Lebar (Width - W):* `34px`
    * *Tinggi (Height - H):* `34px`
  * **Lencana Status Kapsul (`.edu-badge` / `.mi-badge`):**
    * *Corner Radius:* `999px` (bulat sempurna di kedua sisi)
    * *Lebar (Width - W):* Auto (menyesuaikan label teks)
    * *Tinggi (Height - H):* `26px`
* **Efek Bayangan (Box Shadows):**
  * **Shadow Small (Dropdown menu & Notifikasi):** `Y: 2px`, `Blur: 12px`, `Color: #064e3b0f`
  * **Shadow Medium (Main Cards & Modal):** `Y: 10px`, `Blur: 30px`, `Color: #064e3b1a`
  * **Shadow Hover Edu Card (Glow intensif saat kursor di atas kartu):** `X: 0`, `Y: 8px`, `Blur: 32px`, `Color: #00000017`
  * **Shadow Stats Card (Dasbor):** `X: 0`, `Y: 8px`, `Blur: 24px`, `Color: #064e3b0d`

---

### 4. Komponen Dasbor & Edukasi (UI Components Specs)

### A. Sidebar Navigasi & Profil (Sidebar & Profile)
* **Lebar Utama Sidebar (`.pc-sidebar`):**
  * *Lebar (Width - W):* `300px` (tetap)
* **Header Area Logo (`.m-header`):**
  * *Lebar (Width - W):* `300px`
  * *Tinggi (Height - H):* `260px`
* **Tautan Menu Sidebar (`.pc-link`):**
  * *Lebar (Width - W):* `260px` (mengikuti area dalam sidebar)
  * *Tinggi (Height - H):* `42px`
  * *Padding:* `10px` atas-bawah, `14px` kiri-kanan
  * *Margin Bawah:* `4px`
  * *Area Ikon (`.pc-micon`):* `W = 22px`, `H = 22px`
  * *Ukuran Teks Menu:* `14px` (`0.88rem`)
* **Widget Profil Bawah (`.admin-sidebar-profile`):**
  * *Lebar (Width - W):* `300px`
  * *Tinggi (Height - H):* `80px`
  * *Padding:* `16px` atas, `20px` kiri-kanan, `18px` bawah
  * *Avatar (`.admin-sidebar-avatar`):* `W = 46px`, `H = 46px` (Radius: `50%` bulat penuh)
* **Menu Dropdown Profil (`.admin-sidebar-menu`):**
  * *Ketebalan Border:* `2px`
  * *Padding Item:* `11px` atas-bawah, `16px` kiri-kanan

### B. Header Halaman Dasbor (Dasbor Header)
* **Ketebalan Border Bawah Header (`.pc-header`):** `H = 2px` (solid `#DDEFE7`)
* **Tombol Notifikasi (`.admin-notif-btn`):** `W = 42px`, `H = 42px` (Border: `2px`)
* **Jarak Judul Halaman (`.page-header`):** Margin Bawah `16px` (`1rem`), Padding Kiri-Kanan `24px` (`1.5rem`)

### C. Kartu Portal Edukasi (`.edu-card`)
* **Dimensi Utama Kotak Kartu:**
  * *Lebar (Width - W):* `540px` (fleksibel mengikuti sistem grid responsif)
  * *Tinggi (Height - H):* `345px` (tinggi minimum tetap untuk keselarasan visual)
* **Radius Kelengkungan Sudut:** `20px`
* **Padding Dalam Kartu:** `32px` atas, kiri, kanan; `24px` bawah
* **Ketebalan Garis Tepi (Border):** `1.5px` (solid warna `#E5E7EB`)
* **Wadah Ikon Modul (`.edu-card-icon`):**
  * *Lebar (Width - W):* `52px`
  * *Tinggi (Height - H):* `52px`
  * *Radius:* `14px`
* **Emoji Latar Transparan (`.edu-card-bg-icon`):**
  * *Lebar (Width - W):* `112px`
  * *Tinggi (Height - H):* `112px` (Size `7rem`)
  * *Posisi:* `bottom: -14px`, `right: -10px`, Opacity `0.07`
* **Lencana Status Modul (`.edu-badge`):**
  * *Lebar (Width - W):* Auto
  * *Tinggi (Height - H):* Auto (Padding: `4px` atas-bawah, `10px` kiri-kanan, Font Size `11px`)
  * *Radius:* `999px` (kapsul bulat penuh)
* **Garis Pembatas Footer Kartu (`.edu-card-footer`):** Border Atas `H = 1px` (solid `#F3F4F6`), Padding Atas `18px`
* **Teks Call-to-Action (`.edu-cta`):** Font Size `13px` (`0.82rem`), Jarak gap teks-panah `6px` (hover: `10px`)

### D. Tabel Premium Manajemen Modul (`.premium-table`)
* **Sel Header (`<th>`):** Padding `16px` atas-bawah, `20px` kiri-kanan (Tinggi baris `H = 50px`)
* **Sel Konten (`<td>`):** Padding `16px` atas-bawah, `20px` kiri-kanan (Tinggi baris `H = 84px`)
* **Ketebalan Garis Pembatas Baris:** `H = 1px` (solid `#E2E8F0`)
* **Thumbnail Modul (`.mi-thumb`):** `W = 52px`, `H = 52px` (Radius: `10px`)
* **Tombol Aksi Ikon (`.mi-btn-icon`):** `W = 34px`, `H = 34px` (Radius: `8px`, Border: `1px`)
* **Tombol Tambah Modul (`.mi-btn-add`):** `W = Auto`, `H = 38px`, Padding `10px` atas-bawah, `16px` kiri-kanan
* **Tabs Navigasi Kategori (`.mi-tabs`):** `W = Auto`, `H = 36px`, Padding `4px`
  * *Tab item (`.mi-tab`):* Padding `6px` atas-bawah, `18px` kiri-kanan

### E. Lencana Status Modul & Titik Indikator (`.mi-badge`)
* **Ukuran Lencana Status:** 
  * *Lebar (Width - W):* Auto
  * *Tinggi (Height - H):* `26px`
  * *Radius:* `999px` (kapsul bulat penuh)
  * *Padding:* `5px` atas-bawah, `12px` kiri-kanan
* **Titik Bulat Indikator Status (Dot):** 
  * *Lebar (Width - W):* `6px`
  * *Tinggi (Height - H):* `6px`
  * *Radius:* `50%` (bulat penuh)
  * *Jarak ke Teks (Gap):* `5px`

### F. Paginasi Halaman (`.custom-pagination`)
* **Tombol Nomor Halaman (`.page-link`):** `W = 32px`, `H = 32px` (Radius: `8px`)

### G. Komponen Utama Halaman Dasbor (Dashboard Components)
* **Kartu Ringkasan Statistik (`.card-box`):**
  * *Lebar (Width - W):* Fleksibel (biasanya dibagi dalam grid 3 atau 4 kolom)
  * *Tinggi (Height - H):* Auto (menyesuaikan isi, tinggi minimum `124px`)
  * *Radius:* `16px`
  * *Padding:* `24px`
  * *Shadow:* `0 1px 3px rgba(0,0,0,0.05)` (Shadow Small)
* **Kartu Alert Risiko Tinggi (`.alert-banner`):**
  * *Lebar (Width - W):* `100%` (lebar penuh halaman kontainer)
  * *Tinggi (Height - H):* Auto (menyesuaikan isi)
  * *Radius:* `16px`
  * *Padding:* `24px`
  * *Shadow:* `0 1px 3px rgba(0,0,0,0.05)` (Shadow Small)
  * *Lencana Angka Kasus (`.alert-badge`):* `W = Auto`, `H = Auto` (Padding: `8px 16px`), Radius: `999px`
* **Kartu Mahasiswa Prioritas Risiko Tinggi (`.p-card`):**
  * *Lebar (Width - W):* Fleksibel (dalam grid minmax `250px` per kolom)
  * *Tinggi (Height - H):* Auto (menyesuaikan isi)
  * *Radius:* `16px`
  * *Border:* `1px` (solid warna `#fca5a5`)
  * *Padding:* `16px 20px`
* **Wadah Grafik Tren Mood (Chart Container Card Box):**
  * *Lebar (Width - W):* Fleksibel (mengikuti layout grid `charts-stats-grid` kolom kiri `2fr` atau `100%` di mobile)
  * *Tinggi (Height - H):* `320px` - `380px` (tinggi area grafik)
  * *Radius:* `16px`
  * *Padding:* `24px`
* **Tabs Navigasi Dasbor (`.dashboard-tabs`):**
  * *Lebar (Width - W):* Auto (menyesuaikan isi tombol tab)
  * *Tinggi (Height - H):* Auto (mengikuti tombol tab)
  * *Radius:* `18px`
  * *Padding:* `8px`
  * *Tab item (`.dashboard-tab-btn`):* Padding `12px 22px`, Radius `12px`
* **Tombol Utama Dasbor (`.btn-primary`):**
  * *Lebar (Width - W):* Auto (menyesuaikan teks label)
  * *Tinggi (Height - H):* `42px`
  * *Radius:* `10px`
  * *Padding:* `12px 24px`
* **Filter Dropdown Form Dasbor (`.filter-dropdown`):**
  * *Lebar (Width - W):* Auto (menyesuaikan teks opsi terpanjang)
  * *Tinggi (Height - H):* `38px`
  * *Radius:* `6px`
  * *Padding:* `8px 14px`

### H. Formulir Pembuatan & Penyuntingan Modul (Module Form Components)
* **Kontainer Formulir / Seksi Input (`.f-section`):**
  * *Lebar (Width - W):* Maksimum `900px` (terpusat di tengah halaman)
  * *Tinggi (Height - H):* Auto (menyesuaikan jumlah input)
  * *Radius:* `16px`
  * *Padding:* `28px` atas-kiri-kanan, `24px` bawah
* **Input Teks, Dropdown Pilihan, & Textarea (`.f-input`, `.f-select`, `.f-textarea`):**
  * *Lebar (Width - W):* `100%` (mengisi penuh lebar seksi form)
  * *Tinggi (Height - H):*
    * Teks Input & Dropdown: `46px` (Padding: `12px 16px`)
    * Area Teks (Textarea): Min-height `120px` (Padding: `12px 16px`, resize vertical)
  * *Radius:* `10px`
* **Area Unggah File Drag & Drop (`.f-upload-area`):**
  * *Lebar (Width - W):* `100%`
  * *Tinggi (Height - H):* Auto (Padding: `40px 24px` di dalam area putus-putus)
  * *Radius:* `12px`
  * *Border:* `2px` (dashed/putus-putus warna `#cbd5e1`)
* **Preview File Unggahan (`.f-file-preview` / `.f-thumb-preview`):**
  * *Lebar (Width - W):* `100%` (untuk list preview file PDF/MP4) atau `Auto` (untuk preview box thumbnail gambar)
  * *Tinggi (Height - H):* Auto (Tinggi box `H = 64px`, ukuran thumbnail gambar di dalamnya `W = 44px`, `H = 44px` dengan Radius `8px`)
  * *Radius:* `10px`
* **Tombol Kirim Formulir (`.f-submit`):**
  * *Lebar (Width - W):* Auto (menyesuaikan teks label + ikon)
  * *Tinggi (Height - H):* `48px`
  * *Radius:* `12px`
  * *Padding:* `14px 28px`
  * *Shadow:* `0 4px 14px rgba(5,150,105,0.3)`

---

## 5. Daftar Ikon Terpakai & Ukurannya (Icons & Sizes)

Dasbor dan modul edukasi menggunakan ikon dari **Tabler Icons** (`ti ti-[name]`) dan **Bootstrap Icons** (`bi bi-[name]`).

* **Ikon Sidebar Menu (`.pc-micon i`):** `W = 18px`, `H = 18px` (Size `1.08rem`)
* **Ikon Tombol Notifikasi & Header Halaman:** `W = 20px`, `H = 20px` (`1.15rem` - `1.2rem`)
* **Ikon Tombol Aksi Tabel (`.mi-btn-icon svg`):** `W = 15px`, `H = 15px`
* **Ikon Tombol Kembali (`.btn-back-link i`):** `W = 19px`, `H = 19px` (`1.2rem`)
* **Ikon Tambah Modul (`.mi-btn-add i`):** `W = 17px`, `H = 17px` (`1.1rem`)
* **Ikon Tautan CTA (`.edu-cta svg`):** `W = 16px`, `H = 16px`
* **Ikon Titik SVG Poin Rewards (`.mi-point svg`):** `W = 12px`, `H = 12px`

