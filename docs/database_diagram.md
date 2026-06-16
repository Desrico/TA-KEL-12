# Diagram Database & Data Model (MongoDB Only)

Dokumen ini berisi dokumentasi diagram database terstruktur untuk **MongoDB** yang digunakan pada proyek Anda (Aplikasi Mobile & AI Scanning). Dokumen ini mencakup Diagram ERD, CDM, dan PDM yang diselaraskan dengan skema database riil.

---

## 1. Diagram ERD (Crow's Foot Notation)

Diagram ERD tingkat logis yang menggambarkan entitas inti sistem dan relasinya khusus untuk MongoDB.

```mermaid
erDiagram
    STUDENT {
        string nim PK
        string name
        string jenis_kelamin
        string prodi
        string angkatan
        string phone_number
        int point
        int mental_level
        string mental_label
        datetime mental_scanned_at
        datetime mental_updated_manual_at
    }
    MODULE {
        string _id PK
        string title
        string description
        string content_url
        int reward_point
        boolean status
        string kategori
        string target_audiens
    }
    JOURNAL_TEXT {
        string _id PK
        string nim FK
        string description
        int ai_level
        string ai_label
        datetime created_at
    }
    DAILY_CHECKIN {
        string _id PK
        string nim FK
        int mood_id FK
        int feeling_id FK
        string mood_label
        string perasaan
        datetime recorded_at
    }
    MOOD {
        int mood_id PK
        string mood_name
        int mood_code
    }
    FEELING {
        int feeling_id PK
        string feeling_name
        int feeling_code
    }
    NOTIFICATION {
        string _id PK
        string nim FK
        string pesan
        string status
        datetime created_at
    }

    %% Relasi
    STUDENT ||--o{ JOURNAL_TEXT : "menulis"
    STUDENT ||--o{ DAILY_CHECKIN : "melakukan"
    STUDENT ||--o{ NOTIFICATION : "menerima"
    STUDENT ||--o{ MODULE : "membaca (logika poin)"
    
    DAILY_CHECKIN }o--|| MOOD : "referensi mood"
    DAILY_CHECKIN }o--|| FEELING : "referensi feeling"
```

---

## 2. CDM (Conceptual Data Model)

CDM menggambarkan konsep data dan relasi dari sudut pandang bisnis sebelum diimplementasikan secara teknis.

```mermaid
erDiagram
    MAHASISWA {
        string nim
        string nama
        string jenis_kelamin
        string prodi
        string angkatan
        int point
        int mental_level
        string mental_label
    }
    JURNAL {
        string nim
        text isi_jurnal
        int ai_level
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
        string konten_url
        int reward_point
        boolean status
    }
    NOTIFIKASI {
        string nim
        string pesan
        string status
    }

    %% Relasi
    MAHASISWA ||--o{ MODUL_EDUKASI : "membaca"
    MAHASISWA ||--o{ JURNAL : "menulis"
    MAHASISWA ||--o{ DAILY_CHECKIN : "melakukan"
    MAHASISWA ||--o{ NOTIFIKASI : "menerima"
    DAILY_CHECKIN }o--|| MOOD : "memiliki"
    DAILY_CHECKIN }o--|| FEELING : "memiliki"
```

---

## 3. PDM (Physical Data Model)

PDM menggambarkan skema fisik penyimpanan dokumen koleksi MongoDB secara presisi, lengkap dengan tipe data BSON, Primary Key (`PK`), Foreign Key (`FK`), dan unique constraint.

```mermaid
erDiagram
    users {
        ObjectId _id PK
        string nim UK "Index unik untuk relasi"
        string name
        string jenis_kelamin
        string prodi
        string angkatan
        string phone_number
        int point
        int mental_level
        string mental_label
        float mental_confidence
        boolean mental_red_flag
        string mental_insight
        datetime mental_scanned_at
        datetime mental_updated_manual_at
        string password "Hash login mobile"
        datetime created_at
        datetime updated_at
    }
    journal_texts {
        ObjectId _id PK
        string nim FK "users.nim"
        string description "Teks jurnal terenkripsi"
        int ai_level
        string ai_label
        datetime created_at
        datetime updated_at
    }
    daily_checkins {
        ObjectId _id PK
        string nim FK "users.nim"
        int mood_id FK "moods.mood_id"
        int feeling_id FK "feelings.feeling_id"
        string mood_label "Denormalized"
        string perasaan "Denormalized"
        datetime recorded_at
        datetime created_at
        datetime updated_at
    }
    moods {
        int mood_id PK
        string mood_name
        int mood_code
    }
    feelings {
        int feeling_id PK
        string feeling_name
        int feeling_code
    }
    modules {
        ObjectId _id PK
        string title
        string thumbnail
        string description
        string content_url
        int reward_point
        boolean status
        string kategori
        string target_audiens
        datetime created_at
        datetime updated_at
    }
    notifications {
        ObjectId _id PK
        string nim FK "users.nim"
        string pesan
        string status "belum / sudah dibaca"
        datetime created_at
        datetime updated_at
    }

    %% Relasi Fisik MongoDB (Skema Reference)
    users ||--o{ journal_texts : "nim (ref)"
    users ||--o{ daily_checkins : "nim (ref)"
    users ||--o{ notifications : "nim (ref)"
    
    daily_checkins }o--|| moods : "mood_id (ref)"
    daily_checkins }o--|| feelings : "feeling_id (ref)"
```

---

## 4. Penjelasan Teknis PDM MongoDB

1. **Autentikasi & Profil (`users`)**:
   Koleksi ini merepresentasikan mahasiswa. Field `nim` diatur sebagai indeks unik (`UK`) karena digunakan sebagai kunci referensi (`FK`) di koleksi lainnya demi portabilitas relasi antara Web (Laravel) dan Mobile (Flutter).
2. **Relasi Mood & Perasaan (`daily_checkins`)**:
   Berbeda dengan database relasional, field `mood_label` dan `perasaan` disimpan langsung secara denormalisasi di dalam koleksi `daily_checkins` agar performa *read* di mobile cepat tanpa memerlukan join. Namun relasi fisik tetap dihubungkan ke tabel lookup `moods` dan `feelings` melalui `mood_id` (Integer) dan `feeling_id` (Integer).
3. **Penyimpanan Jurnal Terenkripsi (`journal_texts`)**:
   Untuk alasan privasi data medis mahasiswa, field `description` disimpan dalam bentuk *ciphertext* terenkripsi (AES-256) menggunakan App Key Laravel Mobile.
4. **Pembersihan Skema Lama**:
   Koleksi redundan `ai_analyses` telah dihapus. Data klasifikasi AI sekarang disimpan langsung pada tingkat dokumen terkait (`users` dan `journal_texts`) demi efisiensi dan keselarasan model.
