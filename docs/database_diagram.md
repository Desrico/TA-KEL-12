# ERD Konseptual & Data Model (MongoDB)

Dokumen ini berisi empat jenis diagram database dari proyek TA-KEL-12 (Aplikasi Mobile & Interaksi Web) yang dibuat menggunakan Mermaid. 

---

## 1. Diagram ERD (Crow's Foot Notation)

Ini adalah format modern standar (Crow's Foot) yang lebih ringkas dan rapi untuk mendeskripsikan interaksi antara Konselor dan Mahasiswa.

```mermaid
erDiagram
    KONSELOR {
        string id_konselor PK
        string name
    }
    MAHASISWA {
        string nim PK
        string name
        string jenis_kelamin
        string prodi
        string tingkatan
        string angkatan
        string phone_number
        int point
        float energy_score
        int mental_level
        string mental_label
        float mental_confidence
        string mental_red_flag
        text mental_insight
        boolean mental_notif_read
        timestamp mental_scanned_at
    }
    MODUL {
        string module_id PK
        string title
        string thumbnail
        string description
        string content_url
        int reward_point
        boolean status
        string kategori
        string target_audiens
    }
    JURNAL_TEXT {
        string text_id PK
        string deskripsi
        timestamp created_at
    }
    DAILY_CHECKIN {
        string checkin_id PK
        timestamp created_at
    }
    MOOD {
        string mood_id PK
        string mood_name
    }
    FEELING {
        string feeling_id PK
        string feeling_name
    }

    %% Relasi
    KONSELOR ||--o{ MODUL : "mengelola"
    KONSELOR ||--o{ JURNAL_TEXT : "membaca"
    KONSELOR ||--o{ DAILY_CHECKIN : "memantau"
    MAHASISWA ||--o{ MODUL : "mengakses"
    MAHASISWA ||--o{ JURNAL_TEXT : "menulis"
    MAHASISWA ||--o{ DAILY_CHECKIN : "melakukan"
    DAILY_CHECKIN }o--|| MOOD : "memiliki"
    DAILY_CHECKIN }o--|| FEELING : "memiliki"
```

---

## 2. Diagram ERD (Chen Notation - Klasik)

Mermaid tidak memiliki tipe khusus untuk "Chen ERD", namun kita **mengakalinya menggunakan Flowchart Mermaid** dengan bentuk *Diamond* untuk relasi dan *Oval* untuk atribut persis seperti gambar yang Anda buat.

*(Catatan: Atribut dibatasi hanya yang penting-penting saja agar garis tidak terlalu saling menumpuk/kusut saat di-render oleh Mermaid)*

```mermaid
flowchart TD
    %% ==========================================
    %% ENTITAS (Persegi Panjang)
    %% ==========================================
    KONSELOR[KONSELOR]
    MAHASISWA[MAHASISWA]
    MODUL[MODUL]
    JURNAL_TEXT[JURNAL_TEXT]
    DAILY_CHECKIN[DAILY_CHECKIN]
    MOOD[MOOD]
    FEELING[FEELING]

    %% ==========================================
    %% RELASI (Belah Ketupat / Diamond)
    %% ==========================================
    rel_k_m{mengelola}
    
    rel_m_m{mengakses}
    rel_m_j{menulis}
    rel_m_d{melakukan}

    rel_d_mo{memiliki}
    rel_d_fe{memiliki}

    %% ==========================================
    %% GARIS HUBUNGAN ENTITAS <--> RELASI
    %% ==========================================
    KONSELOR ---|1| rel_k_m ---|N| MODUL

    MAHASISWA ---|N| rel_m_m ---|N| MODUL
    MAHASISWA ---|1| rel_m_j ---|N| JURNAL_TEXT
    MAHASISWA ---|1| rel_m_d ---|N| DAILY_CHECKIN

    DAILY_CHECKIN ---|N| rel_d_mo ---|1| MOOD
    DAILY_CHECKIN ---|N| rel_d_fe ---|1| FEELING

    %% ==========================================
    %% ATRIBUT (Oval)
    %% ==========================================
    k_id([id_konselor])
    k_name([name])
    KONSELOR -.- k_id
    KONSELOR -.- k_name

    m_id([id_mahasiswa])
    m_name([name])
    m_prodi([prodi])
    m_level([mental_level])
    MAHASISWA -.- m_id
    MAHASISWA -.- m_name
    MAHASISWA -.- m_prodi
    MAHASISWA -.- m_level

    mod_id([module_id])
    mod_title([title])
    MODUL -.- mod_id
    MODUL -.- mod_title

    j_id([text_id])
    j_desc([deskripsi])
    JURNAL_TEXT -.- j_id
    JURNAL_TEXT -.- j_desc

    dc_id([checkin_id])
    DAILY_CHECKIN -.- dc_id

    mo_id([mood_id])
    mo_name([mood_name])
    MOOD -.- mo_id
    MOOD -.- mo_name

    f_id([feeling_id])
    f_name([feeling_name])
    FEELING -.- f_id
    FEELING -.- f_name
```

---

## 3. CDM (Conceptual Data Model)

CDM menggambarkan **konsep entitas bisnis dan hubungannya** dari sudut pandang logika bisnis (fokus pada data MongoDB yang tersimpan dari aplikasi Mobile).

```mermaid
erDiagram
    KONSELOR {
        string id_konselor
        string name
    }
    MAHASISWA {
        string nim
        string name
        string jenis_kelamin
        string prodi
        string tingkatan
        string angkatan
        string phone_number
        int point
        float energy_score
        int mental_level
        string mental_label
        float mental_confidence
        string mental_red_flag
        text mental_insight
        boolean mental_notif_read
        timestamp mental_scanned_at
    }
    JURNAL {
        string nim
        text isi_jurnal
    }
    DAILY_CHECKIN {
        string nim
        string mood
        string feeling
    }
    MOOD {
        string nama_mood
    }
    FEELING {
        string nama_feeling
    }
    MODUL_EDUKASI {
        string judul
        string kategori
        string target_audiens
        int reward_point
        boolean status
    }
    NOTIFIKASI {
        string nim
        string pesan
        string status
    }

    %% Relasi
    KONSELOR ||--o{ MODUL_EDUKASI : "mengelola"
    KONSELOR ||--o{ JURNAL : "membaca"
    KONSELOR ||--o{ DAILY_CHECKIN : "memantau"
    MAHASISWA ||--o{ MODUL_EDUKASI : "mengakses"
    MAHASISWA ||--o{ JURNAL : "menulis"
    MAHASISWA ||--o{ DAILY_CHECKIN : "melakukan"
    MAHASISWA ||--o{ NOTIFIKASI : "menerima"
    DAILY_CHECKIN }o--|| MOOD : "memiliki"
    DAILY_CHECKIN }o--|| FEELING : "memiliki"
```

---

## 4. PDM (Physical Data Model)

PDM menggambarkan struktur **implementasi teknis koleksi MongoDB** lengkap dengan tipe data, Primary Key, dan skema referensi.

```mermaid
erDiagram
    konselor_mysql["konselor (MySQL DB)"] {
        bigint id PK
        string nama
    }
    users_mongodb["users (MongoDB)"] {
        ObjectId _id PK
        string nim UK
        string name
        string jenis_kelamin
        string prodi
        string tingkatan
        string angkatan
        string phone_number
        int point
        float energy_score
        int mental_level "0=Positif 1=Ringan 2=Pemantauan 3=Krisis"
        string mental_label
        float mental_confidence
        string mental_red_flag
        text mental_insight
        boolean mental_notif_read
        timestamp mental_scanned_at
        string password
        timestamp created_at
        timestamp updated_at
    }
    journal_texts {
        ObjectId _id PK
        string nim
        text description
        timestamp created_at
        timestamp updated_at
    }
    daily_checkins {
        ObjectId _id PK
        string nim
        ObjectId mood_id
        ObjectId feeling_id
        timestamp created_at
        timestamp updated_at
    }
    moods {
        ObjectId _id PK
        string mood_name
    }
    feelings {
        ObjectId _id PK
        string feeling_name
    }
    modules {
        ObjectId _id PK
        string title
        string thumbnail
        text description
        string content_url
        int reward_point
        boolean status
        string kategori
        string target_audiens
        timestamp created_at
        timestamp updated_at
    }
    ai_analyses {
        ObjectId _id PK
        ObjectId daily_checkin_id
        string final_label
        text text_analysis
    }
    notifications {
        ObjectId _id PK
        string nim
        string pesan
        string status
    }

    %% Relasi
    konselor_mysql ||--o{ modules : "mengelola (via Aplikasi Web)"
    konselor_mysql ||--o{ journal_texts : "membaca (via Aplikasi Web)"
    konselor_mysql ||--o{ daily_checkins : "memantau (via Aplikasi Web)"
    
    users_mongodb ||--o{ modules : "mengakses (via API)"
    users_mongodb ||--o{ journal_texts : "nim (ref)"
    users_mongodb ||--o{ daily_checkins : "nim (ref)"
    users_mongodb ||--o{ notifications : "nim (ref)"
    
    daily_checkins }o--|| moods : "mood_id (ref)"
    daily_checkins }o--|| feelings : "feeling_id (ref)"
    daily_checkins ||--o| ai_analyses : "daily_checkin_id (ref)"
```

---

## Penjelasan Relasi (Sesuai Konseptual Gambar)

1. **Konselor - Modul (`mengelola`)**
   Konselor memiliki peran sebagai admin yang membuat, mengubah, atau menghapus konten modul edukasi.
2. **Mahasiswa - Modul (`mengakses`)**
   Mahasiswa melihat modul edukasi untuk mendapatkan *reward points*.
3. **Mahasiswa - Jurnal Text (`menulis`)**
   Satu mahasiswa dapat menulis banyak jurnal.
4. **Mahasiswa - Daily Checkin (`melakukan`)**
   Satu mahasiswa dapat melakukan banyak daily checkin.
5. **Daily Checkin - Mood/Feeling (`memiliki`)**
   Setiap satu daily checkin berasosiasi dengan satu Mood dan satu Feeling.
