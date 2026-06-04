# Sequence Diagram - Sistem Monitoring Kesehatan Mental Mahasiswa

Dokumen ini berisi kumpulan lengkap sequence diagram yang menggambarkan seluruh alur proses utama dalam sistem (TA-KEL-12).

---

## 1. Login

Alur autentikasi pengguna. Sistem mendukung dua jalur login: lokal (konselor/admin yang sudah tersimpan di database) dan via API kampus (CIS) untuk mahasiswa baru.

**Controller**: [LoginController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/LoginController.php)

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna (Mahasiswa/Konselor)
    participant Browser as Browser
    participant LoginCtrl as LoginController
    participant DB as Database (MySQL)
    participant CIS as API CIS Kampus

    User->>Browser: Akses Halaman /login
    Browser->>LoginCtrl: GET /login
    LoginCtrl-->>Browser: Tampilkan Form Login

    User->>Browser: Isi Username & Password lalu Submit
    Browser->>LoginCtrl: POST /login (username, password)
    activate LoginCtrl

    LoginCtrl->>DB: Cari user berdasarkan username_cis / email (attemptLocalLogin)
    activate DB
    DB-->>LoginCtrl: Data User (atau null jika tidak ada)
    deactivate DB

    alt User Ditemukan di Database Lokal & Password Cocok
        LoginCtrl->>DB: Cek role (konselor/mahasiswa) dan sinkronisasi (syncResolvedRoleForUser)
        DB-->>LoginCtrl: Role yang valid
        LoginCtrl->>Browser: Login sukses, simpan session
        activate Browser
        alt Role = Konselor
            Browser-->>User: Redirect ke /admin/dashboard
        else Role = Mahasiswa
            Browser-->>User: Redirect ke /dashboard
        end
        deactivate Browser

    else User Tidak Ditemukan Lokal, Coba Login via CIS
        LoginCtrl->>CIS: POST loginWithCredentials (username, password)
        activate CIS
        CIS-->>LoginCtrl: Token CIS
        deactivate CIS

        LoginCtrl->>CIS: GET getMahasiswaByUsername (username, token)
        activate CIS
        CIS-->>LoginCtrl: Data Mahasiswa dari CIS
        deactivate CIS

        LoginCtrl->>DB: Buat/Update akun User & data Mahasiswa
        activate DB
        DB-->>LoginCtrl: User tersimpan
        deactivate DB

        LoginCtrl->>Browser: Login sukses, simpan session
        Browser-->>User: Redirect ke /dashboard (sebagai Mahasiswa)
    
    else Login Gagal (Password Salah / CIS Error)
        LoginCtrl-->>Browser: Kembalikan form dengan pesan error
        Browser-->>User: Tampilkan pesan "Username atau password salah"
    end

    deactivate LoginCtrl
```

---

## 2. Logout

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna
    participant Browser as Browser
    participant LoginCtrl as LoginController

    User->>Browser: Klik tombol Logout
    Browser->>LoginCtrl: POST /logout
    activate LoginCtrl
    LoginCtrl->>LoginCtrl: Auth::logout() - hapus sesi aktif
    LoginCtrl->>LoginCtrl: invalidate() & regenerateToken() - amankan CSRF
    LoginCtrl-->>Browser: Redirect ke halaman /login
    deactivate LoginCtrl
    Browser-->>User: Tampilkan Halaman Login
```

---

## 3. Dashboard Konselor (Halaman Utama)

Alur saat konselor membuka halaman dashboard utama yang menampilkan data statistik mahasiswa, jadwal hari ini, dan chart.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `index`) & [AdminController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/AdminController.php) (fungsi `dashboard`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant Browser as Browser
    participant DashCtrl as DashboardController
    participant DB as Database

    Konselor->>Browser: Akses /konselor/dashboard
    Browser->>DashCtrl: GET /konselor/dashboard
    activate DashCtrl

    DashCtrl->>DB: Ambil data Mahasiswa dengan jurnal (urut by mental_level)
    activate DB
    DB-->>DashCtrl: Daftar Mahasiswa + jumlah jurnal
    deactivate DB

    DashCtrl->>DB: Ambil JadwalKonseling hari ini (konselor_id, tanggal=today)
    activate DB
    DB-->>DashCtrl: Jadwal konseling hari ini
    deactivate DB

    DashCtrl->>DB: Ambil daftar angkatan unik untuk dropdown filter
    activate DB
    DB-->>DashCtrl: Daftar angkatan
    deactivate DB

    DashCtrl-->>Browser: Render view admin.dashboard (students, lastScan, todayJadwals)
    deactivate DashCtrl
    Browser-->>Konselor: Tampilkan Halaman Dashboard

    Note over Browser,DashCtrl: Chart data dimuat secara AJAX (asinkron)
    Browser->>DashCtrl: GET /konselor/chart-data?range=14d (AJAX)
    activate DashCtrl
    DashCtrl->>DB: Ambil DailyCheckin dalam rentang waktu
    activate DB
    DB-->>DashCtrl: Data check-in mahasiswa
    deactivate DB
    DashCtrl->>DB: Ambil data distribusi Feeling
    activate DB
    DB-->>DashCtrl: Data feeling
    deactivate DB
    DashCtrl-->>Browser: JSON (labels, moodTrend, feelingsTrend, distribution)
    deactivate DashCtrl
    Browser-->>Konselor: Chart dan statistik berhasil ditampilkan
```

---

## 4. Melihat Detail Mahasiswa (Jurnal & Checkin History)

Alur saat konselor mengklik nama seorang mahasiswa di dashboard untuk melihat riwayat jurnal, mood, dan status mentalnya secara lengkap.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `showDetail`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant Browser as Browser
    participant DashCtrl as DashboardController
    participant MongoDB as MongoDB (Data Mahasiswa)

    Konselor->>Browser: Klik nama mahasiswa di dashboard
    Browser->>DashCtrl: GET /konselor/detail/{nim}
    activate DashCtrl

    DashCtrl->>MongoDB: Ambil data Student berikut semua journalTexts (urut desc)
    activate MongoDB
    MongoDB-->>DashCtrl: Data Student + Jurnal
    deactivate MongoDB

    DashCtrl->>MongoDB: Ambil semua dailyCheckins (dengan relasi mood & feeling, urut desc)
    activate MongoDB
    MongoDB-->>DashCtrl: Data Checkin harian
    deactivate MongoDB

    DashCtrl->>DashCtrl: Gabungkan & urutkan Jurnal + Checkin berdasarkan tanggal (sortedLogs)

    DashCtrl-->>Browser: Render view admin.detail (student, sortedLogs)
    deactivate DashCtrl
    Browser-->>Konselor: Tampilkan Halaman Detail Mahasiswa (Timeline Aktivitas)
```

---

## 5. Halaman Semua Mahasiswa (Daftar dengan Filter)

Alur saat konselor membuka halaman daftar semua mahasiswa dan melakukan pencarian/filter berdasarkan prodi, angkatan, atau level mental.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `semuaMahasiswa`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant Browser as Browser
    participant DashCtrl as DashboardController
    participant MongoDB as MongoDB

    Konselor->>Browser: Akses Halaman Semua Mahasiswa (/konselor/semua-mahasiswa)
    Browser->>DashCtrl: GET /konselor/semua-mahasiswa?search=&angkatan=Semua&prodi=Semua&level=Semua
    activate DashCtrl

    DashCtrl->>MongoDB: Ambil daftar angkatan unik untuk filter dropdown
    activate MongoDB
    MongoDB-->>DashCtrl: Daftar angkatan
    deactivate MongoDB

    DashCtrl->>MongoDB: Query mahasiswa dengan filter (angkatan, prodi, level, search)
    activate MongoDB
    MongoDB-->>DashCtrl: Daftar mahasiswa yang sesuai filter
    deactivate MongoDB

    DashCtrl-->>Browser: Render view admin.semua_mahasiswa
    deactivate DashCtrl
    Browser-->>Konselor: Tampilkan Tabel Mahasiswa

    Konselor->>Browser: Ubah filter / ketik di kolom pencarian
    Browser->>DashCtrl: GET /konselor/semua-mahasiswa?search=X&level=3 (request baru)
    activate DashCtrl
    DashCtrl->>MongoDB: Query ulang dengan filter baru
    MongoDB-->>DashCtrl: Hasil yang difilter
    DashCtrl-->>Browser: Render ulang halaman dengan data baru
    deactivate DashCtrl
    Browser-->>Konselor: Tampilkan hasil filter
```

---

## 6. Halaman Mahasiswa Prioritas (Level 3 / Krisis)

Alur saat konselor mengakses halaman khusus yang menampilkan hanya mahasiswa dengan status krisis (Level 3) beserta analisis statistiknya.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `prioritas`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant Browser as Browser
    participant DashCtrl as DashboardController
    participant MongoDB as MongoDB

    Konselor->>Browser: Akses Halaman Prioritas (/konselor/prioritas)
    Browser->>DashCtrl: GET /konselor/prioritas
    activate DashCtrl

    DashCtrl->>MongoDB: Ambil mahasiswa dengan mental_level = 3, urut by name
    activate MongoDB
    MongoDB-->>DashCtrl: Daftar mahasiswa krisis + jumlah jurnal
    deactivate MongoDB

    DashCtrl->>MongoDB: Hitung totalStudents & totalScanned
    MongoDB-->>DashCtrl: Statistik jumlah mahasiswa
    
    DashCtrl->>DashCtrl: Hitung distribusi kasus per Prodi & per Angkatan
    
    DashCtrl->>MongoDB: Ambil DailyCheckin 4 bulan terakhir untuk NIMs mahasiswa Level 3
    activate MongoDB
    MongoDB-->>DashCtrl: Data check-in mahasiswa krisis
    deactivate MongoDB
    
    DashCtrl->>DashCtrl: Hitung tren mood bulanan (monthlyMoodTrend)

    DashCtrl-->>Browser: Render view admin.prioritas (students, stats, breakdown, trend)
    deactivate DashCtrl
    Browser-->>Konselor: Tampilkan Halaman Mahasiswa Prioritas
```

---

## 7. Halaman Laporan Konseling (Daftar & Detail)

Alur saat konselor membuka halaman laporan, melihat daftar mahasiswa yang punya riwayat sesi, dan melihat detail laporan per mahasiswa.

**Controller**: [LaporanController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/LaporanController.php)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant Browser as Browser
    participant LaporanCtrl as LaporanController
    participant DB as Database (MySQL)
    participant GroqAI as Groq AI Service

    %% Membuka Daftar Laporan
    Konselor->>Browser: Akses Halaman Laporan (/admin/laporan)
    Browser->>LaporanCtrl: GET /admin/laporan
    activate LaporanCtrl
    LaporanCtrl->>DB: Query Mahasiswa yang punya jadwal dengan laporan (paginate 10)
    activate DB
    DB-->>LaporanCtrl: Daftar Mahasiswa + total laporan
    deactivate DB
    LaporanCtrl-->>Browser: Render view admin.laporan.index
    deactivate LaporanCtrl
    Browser-->>Konselor: Tampilkan Daftar Mahasiswa yang Punya Laporan

    %% Melihat Detail Laporan Per Mahasiswa
    Konselor->>Browser: Klik nama mahasiswa
    Browser->>LaporanCtrl: GET /admin/laporan/mahasiswa/{id}
    activate LaporanCtrl
    LaporanCtrl->>DB: Ambil riwayat jadwal + sesi + laporan mahasiswa (paginate 10)
    activate DB
    DB-->>LaporanCtrl: Riwayat sesi konseling mahasiswa
    deactivate DB
    LaporanCtrl->>DB: Ambil ringkasan AI terakhir (AiLaporanSummary)
    activate DB
    DB-->>LaporanCtrl: Data AI Summary (jika ada)
    deactivate DB
    LaporanCtrl-->>Browser: Render view admin.laporan.show
    deactivate LaporanCtrl
    Browser-->>Konselor: Tampilkan Detail Riwayat Sesi + Ringkasan AI

    %% Generate Ringkasan AI
    Konselor->>Browser: Klik tombol "Buat Ringkasan AI"
    Browser->>LaporanCtrl: POST /admin/laporan/mahasiswa/{id}/ai-summary
    activate LaporanCtrl
    LaporanCtrl->>DB: Kumpulkan payload laporan (topik, ringkasan, observasi, progress)
    activate DB
    DB-->>LaporanCtrl: Data laporan semua sesi
    deactivate DB
    LaporanCtrl->>GroqAI: Kirim payload ke Groq API (summarize)
    activate GroqAI
    GroqAI-->>LaporanCtrl: Teks ringkasan AI
    deactivate GroqAI
    LaporanCtrl->>DB: Simpan ke tabel AiLaporanSummary
    activate DB
    DB-->>LaporanCtrl: Tersimpan
    deactivate DB
    LaporanCtrl-->>Browser: Redirect dengan flash message sukses
    deactivate LaporanCtrl
    Browser-->>Konselor: Halaman ter-refresh, ringkasan AI ditampilkan
```

---

## 8. Pengajuan & Persetujuan Jadwal Konseling

Alur lengkap dari Mahasiswa memesan slot hingga Konselor menyetujui atau menolak pengajuan tersebut.

**Controller**: [JadwalController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/JadwalController.php) & [AdminController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/AdminController.php)

```mermaid
sequenceDiagram
    autonumber
    actor Mahasiswa as Mahasiswa
    actor Konselor as Konselor (Admin)
    participant JController as JadwalController
    participant AController as AdminController
    participant DB as Database (MySQL)

    Mahasiswa->>JController: GET /konseling (buka form pengajuan)
    activate JController
    JController->>DB: Ambil slot waktu yang sudah terisi (getBookedSlots)
    DB-->>JController: Daftar slot terisi
    JController-->>Mahasiswa: Tampilkan Form + Slot Tersedia
    deactivate JController

    Mahasiswa->>JController: POST /jadwal (submit form pengajuan)
    activate JController
    JController->>DB: Validasi ketersediaan slot (checkAvailability)
    DB-->>JController: Status slot (tersedia)
    JController->>DB: Simpan jadwal dengan status 'menunggu'
    DB-->>JController: Berhasil disimpan
    JController-->>Mahasiswa: Redirect + notifikasi sukses
    deactivate JController

    Konselor->>AController: GET /admin/jadwal (lihat daftar pengajuan masuk)
    activate AController
    AController->>DB: Ambil semua jadwal (filter by konselor_id)
    DB-->>AController: Daftar jadwal
    AController-->>Konselor: Tampilkan Halaman Jadwal + Kalender
    deactivate AController

    alt Konselor Menyetujui
        Konselor->>AController: POST /admin/jadwal/{id}/setujui
        activate AController
        AController->>DB: Update status jadi 'disetujui'
        AController->>DB: Buat notifikasi untuk Mahasiswa (Notifikasi::create)
        DB-->>AController: Sukses
        AController-->>Konselor: Redirect back dengan pesan sukses
        deactivate AController
    else Konselor Menolak
        Konselor->>AController: POST /admin/jadwal/{id}/tolak
        activate AController
        AController->>DB: Update status jadi 'ditolak'
        DB-->>AController: Sukses
        AController-->>Konselor: Redirect back dengan pesan sukses
        deactivate AController
    end
```

---

## 9. Update Status Mental Mahasiswa Secara Manual

Alur saat konselor mengubah level klasifikasi mental seorang mahasiswa secara manual dari dashboard.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `updateStatus`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant Browser as Browser
    participant DashCtrl as DashboardController
    participant MongoDB as MongoDB

    Konselor->>Browser: Pilih level mental baru di halaman detail mahasiswa
    Browser->>DashCtrl: POST /konselor/update-status/{nim} (mental_level)
    activate DashCtrl

    DashCtrl->>DashCtrl: Validasi input (mental_level harus 0, 1, 2, atau 3)

    DashCtrl->>MongoDB: Cari Student berdasarkan NIM
    activate MongoDB
    MongoDB-->>DashCtrl: Data Student
    deactivate MongoDB

    DashCtrl->>MongoDB: Update mental_level, mental_label, mental_confidence=100, red_flag
    activate MongoDB
    MongoDB-->>DashCtrl: Update berhasil
    deactivate MongoDB

    DashCtrl-->>Browser: JSON response (status: success)
    deactivate DashCtrl
    Browser-->>Konselor: Tampilkan notifikasi sukses, label status diperbarui
```

---

## 10. Scan Level 3 (AI Mental Health Detection)

Alur saat konselor memicu pemindaian AI terhadap seluruh mahasiswa untuk mendeteksi level krisis.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `scanLevel3`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant DashCtrl as DashboardController
    participant MongoDB as MongoDB
    participant AIService as AI Service (Python - Port 8001)
    participant DB as Database (MySQL)
    participant NotifSystem as Sistem Notifikasi

    Konselor->>DashCtrl: POST /konselor/scan (Klik Scan AI)
    activate DashCtrl

    DashCtrl->>MongoDB: Ambil semua mahasiswa + journalTexts
    MongoDB-->>DashCtrl: Daftar mahasiswa

    loop Untuk Setiap Mahasiswa
        DashCtrl->>MongoDB: Cek apakah ada jurnal/checkin baru sejak scan terakhir
        MongoDB-->>DashCtrl: Status data terbaru

        alt Tidak ada data baru sejak scan terakhir
            DashCtrl->>DashCtrl: Lewati mahasiswa ini (skipped++)
        else Ada data baru
            DashCtrl->>MongoDB: Ambil histori mood & feeling 14 hari terakhir
            MongoDB-->>DashCtrl: Data histori
            DashCtrl->>AIService: POST /api/classify (teks jurnal, histori mood)
            activate AIService
            AIService-->>DashCtrl: Hasil klasifikasi (level, label, confidence, red_flag)
            deactivate AIService
            DashCtrl->>MongoDB: Update mental_level, label, confidence, scanned_at
            
            alt Level == 3 dan Sebelumnya Bukan Level 3
                DashCtrl->>DB: Kirim notifikasi ke semua konselor (Notification::send)
                DashCtrl->>DB: Simpan ke tabel Notifikasi (status: belum)
                DB-->>DashCtrl: Notifikasi tersimpan
            end
        end
    end

    DashCtrl-->>Konselor: JSON (saved: X mahasiswa diproses, skipped: Y dilewati)
    deactivate DashCtrl
```

---

## 11. Ringkasan Jurnal Mahasiswa dengan AI

Alur saat konselor meminta AI meringkas seluruh jurnal seorang mahasiswa.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `getSummary`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant DashCtrl as DashboardController
    participant MongoDB as MongoDB
    participant AIService as AI Service (Python - Port 8001)

    Konselor->>DashCtrl: POST /konselor/summary (nim=XXX)
    activate DashCtrl

    DashCtrl->>MongoDB: Ambil semua deskripsi jurnal mahasiswa berdasarkan NIM
    activate MongoDB
    MongoDB-->>DashCtrl: Daftar deskripsi jurnal
    deactivate MongoDB

    alt Jurnal Kosong
        DashCtrl-->>Konselor: JSON ("belum memiliki jurnal")
    else Ada Jurnal
        DashCtrl->>AIService: POST /api/summarize (nim, journal_texts[])
        activate AIService
        AIService-->>DashCtrl: Ringkasan (summary text)
        deactivate AIService

        DashCtrl->>MongoDB: Update mental_insight & mental_scanned_at pada Student
        DashCtrl-->>Konselor: JSON berisi ringkasan (summary)
    end
    deactivate DashCtrl
```

---

## 12. Notifikasi Real-time (Peringatan Mahasiswa Krisis)

Alur pengambilan notifikasi urgensi yang ditampilkan secara real-time di header dashboard konselor.

**Controller**: [DashboardController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/DashboardController.php) (fungsi `getUrgentNotifications` & `markUrgentRead`) & [AdminController.php](file:///e:/kuliah/PA3/TA-KEL-12/app/Http/Controllers/AdminController.php) (fungsi `notifications`)

```mermaid
sequenceDiagram
    autonumber
    actor Konselor as Konselor (Admin)
    participant Browser as Browser
    participant DashCtrl as DashboardController
    participant AdminCtrl as AdminController
    participant MongoDB as MongoDB
    participant DB as Database (MySQL)

    Note over Browser,DashCtrl: Polling otomatis setiap beberapa detik
    Browser->>DashCtrl: GET /konselor/notifications (AJAX polling)
    activate DashCtrl
    DashCtrl->>MongoDB: Ambil Student dengan mental_level=3 & mental_notif_read!=true
    activate MongoDB
    MongoDB-->>DashCtrl: Daftar mahasiswa krisis yang belum dibaca
    deactivate MongoDB
    DashCtrl-->>Browser: JSON (count, notifications[])
    deactivate DashCtrl
    Browser-->>Konselor: Update badge notifikasi di header

    Browser->>AdminCtrl: GET /admin/notifikasi (AJAX polling)
    activate AdminCtrl
    AdminCtrl->>DB: Ambil 6 notifikasi terakhir milik user konselor
    activate DB
    DB-->>AdminCtrl: Daftar notifikasi (pesan, status, waktu)
    deactivate DB
    AdminCtrl-->>Browser: JSON (unread_count, items[])
    deactivate AdminCtrl
    Browser-->>Konselor: Tampilkan dropdown notifikasi terbaru

    Konselor->>Browser: Klik notifikasi / ikon baca semua
    Browser->>DashCtrl: POST /konselor/notifications/{nim}/read
    activate DashCtrl
    DashCtrl->>MongoDB: Update mental_notif_read = true untuk mahasiswa bersangkutan
    DashCtrl-->>Browser: JSON (success: true)
    deactivate DashCtrl

    Browser->>AdminCtrl: POST /admin/notifikasi/baca (tandai semua dibaca)
    activate AdminCtrl
    AdminCtrl->>DB: Update status notifikasi jadi 'dibaca'
    AdminCtrl-->>Browser: JSON (success: true)
    deactivate AdminCtrl
    Browser-->>Konselor: Badge notifikasi hilang/berkurang
```

---

## Cara Melihat Diagram di VS Code

1. Buka file ini di VS Code.
2. Tekan **`Ctrl + Shift + V`** untuk membuka Markdown Preview.
3. Atau tekan **`Ctrl + K`** lalu **`V`** untuk membuka preview di samping.
4. Extension Mermaid akan otomatis merender semua blok ` ```mermaid ` menjadi diagram visual.
