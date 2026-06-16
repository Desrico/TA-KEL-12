# BPMN Diagrams - Sistem Monitoring Kesehatan Mental Mahasiswa (TA-KEL-12)

Dokumen ini menyediakan diagram **BPMN (Business Process Model and Notation)** lengkap untuk seluruh proses utama di bawah tanggung jawab Anda pada proyek monitoring ini.

Dokumen ini disajikan dalam dua format:
1. **Visual Mermaid (Bizagi Style)**: Dapat dipreview langsung di VS Code. Warna dimodelkan agar mirip dengan Bizagi Modeler default (Start = Hijau, Task = Biru, Gateway = Kuning, End = Merah).
2. **File XML BPMN 2.0 Standar**: Tersedia dalam file standalone berekstensi `.bpmn` di folder `docs/` yang sudah dilengkapi dengan data koordinat visual (`BPMNDiagram` layout). Ini menjamin **Bizagi Modeler (termasuk versi 4.3)** dapat membukanya secara instan tanpa mengalami *infinite loading/freeze*.

---

## Daftar File BPMN Standar (Siap Import ke Bizagi)
* **[docs/bpmn_edukasi.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_edukasi.bpmn)**: Alur Pengelolaan & Penggunaan Modul Edukasi.
* **[docs/bpmn_laporan.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_laporan.bpmn)**: Alur Penanganan Kasus Urgent & Notifikasi Peringatan.
* **[docs/bpmn_scan_notifikasi.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_scan_notifikasi.bpmn)**: Alur Pindai Ulang (Scan AI) - Manual & Otomatis.
* **[docs/bpmn_kirim_notifikasi.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_kirim_notifikasi.bpmn)**: Alur Pengiriman Notifikasi Kustom ke Mahasiswa.
* **[docs/bpmn_laporan_l3.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_laporan_l3.bpmn)**: Alur Generate Laporan Mahasiswa Level 3 (Kasus Urgent).
* **[docs/bpmn_laporan_grafik.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_laporan_grafik.bpmn)**: Alur Generate Laporan Grafik Mood & Perasaan.
* **[docs/bpmn_koreksi_ai.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_koreksi_ai.bpmn)**: Alur Mengubah & Mengoreksi Label Klasifikasi AI.
* **[docs/bpmn_login.bpmn](file:///e:/kuliah/PA3/TA-KEL-12/docs/bpmn_login.bpmn)**: Alur Autentikasi Pengguna (Login).

---

## 1. Alur Pengelolaan & Penggunaan Modul Edukasi
Proses pembuatan modul edukasi oleh Konselor melalui dashboard web admin hingga dibaca oleh Mahasiswa di aplikasi mobile.

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef gateway fill:#FFF2CC,stroke:#833C0C,stroke-width:2px,rx:10px,ry:10px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolEdu ["Kolaborasi Proses Edukasi"]
        subgraph LaneKonselor ["👤 Konselor (Web)"]
            direction LR
            S1([Start]) --> T1[Mengisi Data Modul] --> G1{Status Modul?}
            G1 -- Publish --> T2[Set Active]
            G1 -- Draft --> T3[Set Draft]
        end
        subgraph LaneSistem ["💻 Sistem"]
            direction LR
            T4[Menyimpan ke MongoDB] --> T5[Broadcast ke API Mobile]
            T6[Simpan Draft ke MongoDB] --> E1([End Draft])
        end
        subgraph LaneMhs ["🎓 Mahasiswa (Mobile)"]
            direction LR
            T7[Akses Menu Edukasi] --> T8[Membaca Modul] --> E2([End Read])
        end
    end
    T2 --> T4
    T3 --> T6
    T5 --> T7
    class S1 startEvent; class T1,T2,T3,T4,T5,T6,T7,T8 task; class G1 gateway; class E1,E2 endEvent;
```

---

## 2. Alur Penanganan Kasus Urgent & Notifikasi Peringatan
Proses deteksi dini krisis mental mahasiswa lewat analisis sentimen jurnal harian (AI) hingga pengiriman notifikasi peringatan kustom langsung oleh Konselor ke perangkat Mahasiswa.

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef gateway fill:#FFF2CC,stroke:#833C0C,stroke-width:2px,rx:10px,ry:10px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolLaporan ["Kolaborasi Kasus Urgent & Notifikasi"]
        subgraph LaneMhsL ["🎓 Mahasiswa"]
            direction LR
            S2([Start]) --> T9[Input Jurnal & Mood]
            T21[Terima & Baca Notifikasi di HP] --> E4([Selesai])
        end
        subgraph LaneSistemL ["💻 Sistem"]
            direction LR
            T10[Simpan ke MongoDB] --> T11[Analisis Jurnal via AI] --> G2{Level 3 Urgent?}
            G2 -- Tidak --> T12[Simpan Level Reguler] --> E3([Selesai])
            G2 -- Ya --> T13[Set mental_level = 3] --> T14[Kirim Notifikasi Urgent]
            T20[Kirim Notifikasi Push ke MongoDB]
        end
        subgraph LaneKonselorL ["👤 Konselor"]
            direction LR
            T15[Melihat Alert di Dashboard] --> T16[Buka Detail Mahasiswa] --> T17[Request Ringkasan AI] --> T18[Menulis Pesan Notifikasi] --> T19[Klik Kirim Notifikasi]
        end
    end
    T9 --> T10
    T14 --> T15
    T19 --> T20
    T20 --> T21
    class S2 startEvent; class T9,T10,T11,T12,T13,T14,T15,T16,T17,T18,T19,T20,T21 task; class G2 gateway; class E3,E4 endEvent;
```

---

## 3. Alur Pindai Ulang (Scan AI) - Manual & Otomatis
Proses memindai ulang kondisi mental seluruh mahasiswa menggunakan model AI. Proses ini dapat dipicu secara manual oleh Konselor dari Dashboard, atau berjalan secara otomatis setiap jam melalui Laravel Scheduler (cron job).

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolScan ["Proses Pindai Ulang (Scan AI)"]
        subgraph LaneKonselorS ["👤 Konselor (Web)"]
            direction LR
            S3_Manual([Start: Klik Pindai Ulang])
        end
        subgraph LaneSistemS ["💻 Sistem (Backend & AI)"]
            direction LR
            S3_Timer([Start: Tiap 1 Jam])
            T22[Eksekusi scanLevel3] --> T23[Analisis data Jurnal via AI Model] --> T24[Update DB & mental_level] --> T25[Refresh Realtime Dashboard] --> E5([Database & Dashboard Terupdate])
        end
    end
    S3_Manual --> T22
    S3_Timer --> T22
    class S3_Manual,S3_Timer startEvent; class T22,T23,T24,T25 task; class E5 endEvent;
```

---

## 4. Alur Pengiriman Notifikasi Kustom ke Mahasiswa
Proses Konselor mengirimkan pesan intervensi/peringatan kustom secara manual ke mahasiswa tertentu dari Halaman Detail Mahasiswa.

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolKirimNotif ["Pengiriman Notifikasi Kustom"]
        subgraph LaneKonselorN ["👤 Konselor"]
            direction LR
            S4_N([Start]) --> T26[Buka Detail Profil Mahasiswa] --> T27[Tulis Pesan Notifikasi] --> T28[Klik Kirim Pesan]
        end
        subgraph LaneSistemN ["💻 Sistem (Laravel)"]
            direction LR
            T29[Simpan Notifikasi ke MongoDB]
        end
        subgraph LaneMhsN ["🎓 Mahasiswa"]
            direction LR
            T30[Terima Notifikasi di HP] --> T31[Membuka & Membaca Notifikasi] --> E6_N([Pesan Diterima])
        end
    end
    T28 --> T29
    T29 --> T30
    class S4_N startEvent; class T26,T27,T28,T29,T30,T31 task; class E6_N endEvent;
```

---

## 5. Alur Generate Laporan Mahasiswa Level 3
Proses mengekstrak, menghitung statistik, dan menyajikan visualisasi data khusus untuk seluruh mahasiswa krisis (Level 3) pada menu prioritas.

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolL3 ["Laporan Mahasiswa Level 3"]
        subgraph LaneKonselorL3 ["👤 Konselor"]
            direction LR
            S4([Start]) --> T33[Buka Menu Mahasiswa Prioritas]
            T38[Tinjau Sebaran Kasus & Statistik] --> T39[Cetak Laporan / Save PDF] --> E6([Selesai])
        end
        subgraph LaneSistemL3 ["💻 Sistem"]
            direction LR
            T34[Query Mahasiswa Level 3] --> T35[Hitung Distribusi Prodi & Angkatan] --> T36[Tarik Mood 4 Bulan Terakhir] --> T37[Render View prioritas]
        end
    end
    T33 --> T34
    T37 --> T38
    class S4 startEvent; class T33,T34,T35,T36,T37,T38,T39 task; class E6 endEvent;
```

---

## 6. Alur Generate Laporan Grafik Mood & Perasaan
Proses mengekspor diagram perkembangan emosional kolektif mingguan dan bulanan ke dalam dokumen cetak resmi.

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolGrafik ["Laporan Grafik Mood & Perasaan"]
        subgraph LaneKonselorG ["👤 Konselor"]
            direction LR
            S5([Start]) --> T40[Pilih Filter Waktu] --> T41[Klik Cetak Laporan Tren]
            T46[Simpan PDF / Print Fisik] --> E7([Selesai])
        end
        subgraph LaneSistemG ["💻 Sistem"]
            direction LR
            T42[Ambil DailyCheckin & Eager Load] --> T43[Hitung Persentase Distribusi] --> T44[Render View khusus cetak] --> T45[Picu window.print]
        end
    end
    T41 --> T42
    T45 --> T46
    class S5 startEvent; class T40,T41,T42,T43,T44,T45,T46 task; class E7 endEvent;
```

---

## 7. Alur Mengubah & Mengoreksi Label Klasifikasi AI
Proses Konselor membatalkan (*override*) label tingkat depresi/kecemasan yang dihasilkan AI karena hasil klasifikasi keliru atau kondisi mahasiswa telah membaik secara manual.

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolKoreksi ["Koreksi Label Klasifikasi AI"]
        subgraph LaneKonselorK ["👤 Konselor"]
            direction LR
            S6([Start]) --> T47[Buka Detail Mahasiswa] --> T48[Pilih Dropdown Level Mental Baru] --> T49[Klik Simpan Koreksi]
            T53[Lihat Profil Terkini] --> E8([Selesai])
        end
        subgraph LaneSistemK ["💻 Sistem"]
            direction LR
            T50[Validasi Parameter level 0-3] --> T51[Perbarui DB Student (Confidence=100)] --> T52[Kirim Response Sukses JSON]
        end
    end
    T49 --> T50
    T52 --> T53
    class S6 startEvent; class T47,T48,T49,T50,T51,T52,T53 task; class E8 endEvent;
```

---

## 8. Alur Autentikasi Pengguna (Login)
Proses validasi akun, pembuatan session, dan pengalihan (*redirecting*) halaman utama berdasarkan role pengguna (Konselor atau Mahasiswa).

```mermaid
flowchart TD
    classDef startEvent fill:#E2F0D9,stroke:#385723,stroke-width:2px,rx:20px,ry:20px;
    classDef task fill:#D9E1F2,stroke:#1F4E78,stroke-width:2px,rx:5px,ry:5px;
    classDef gateway fill:#FFF2CC,stroke:#833C0C,stroke-width:2px,rx:10px,ry:10px;
    classDef endEvent fill:#F8CECC,stroke:#C00000,stroke-width:2px,rx:20px,ry:20px;
    
    subgraph PoolLogin ["Autentikasi Pengguna"]
        subgraph LaneUserA ["👤 Pengguna (Mahasiswa/Konselor)"]
            direction LR
            S7([Start]) --> T54[Akses Halaman Login] --> T55[Isi Username & Password]
            T58[Lihat Notifikasi Error]
        end
        subgraph LaneSistemA ["💻 Sistem (Laravel Auth)"]
            direction LR
            T56[Validasi DB Kredensial] --> G3{Kredensial Valid?}
            T57[Buat Session Pengguna] --> G4{Role Pengguna?}
            G4 -- Konselor --> T59[Redirect ke /admin/dashboard]
            G4 -- Mahasiswa --> T60[Redirect ke /dashboard]
        end
    end
    T55 --> T56
    G3 -- Tidak --> T58 --> T55
    G3 -- Ya --> T57
    T59 --> E9([Halaman Utama])
    T60 --> E9
    class S7 startEvent; class T54,T55,T56,T57,T58,T59,T60 task; class G3,G4 gateway; class E9 endEvent;
```

---

## 💡 Panduan Import File .bpmn ke Bizagi Modeler
1. Jalankan aplikasi **Bizagi Modeler**.
2. Klik **File** $\rightarrow$ **Import** $\rightarrow$ Pilih **BPMN**.
3. Pilih salah satu file `.bpmn` yang Anda butuhkan di folder `docs/`.
4. Diagram akan ter-import sempurna dan langsung tampil rapi dengan warna bawaan Bizagi (tanpa loading lama!).
