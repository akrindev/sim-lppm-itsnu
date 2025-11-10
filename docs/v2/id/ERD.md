# ERD – Diagram Relasi Entitas v2.0 (Bahasa Indonesia)
## SIM LPPM ITSNU – Dokumentasi Skema Basis Data

Versi Dokumen: 2.0  
Terakhir Diperbarui: 2025-11-09  
Database: MySQL  
Tabel Kustom: 45+

---

## Daftar Isi
1. Diagram ERD Lengkap  
2. Deskripsi Entitas (Ringkas)  
3. Ringkasan Relasi  
4. Ringkasan Sederhana (Plain Indonesian)

---

## 1) Diagram ERD Lengkap

```mermaid
erDiagram
    %% ========================================
    %% ENTITAS INTI - Otentikasi & Manajemen Pengguna
    %% ========================================
    
    users ||--|| identities : "memiliki profil"
    users ||--o{ proposals : "mengajukan"
    users ||--o{ proposal_user : "anggota tim dari"
    users ||--o{ proposal_reviewer : "mereview"
    users ||--o{ research_stages : "penanggung jawab"
    
    users {
        uuid id PK "UUID unik pengguna"
        string name "Nama lengkap pengguna"
        string email UK "Alamat email unik (untuk login)"
        string password "Password yang sudah di-hash (bcrypt)"
        timestamp email_verified_at "Waktu verifikasi email"
        string two_factor_secret "Kunci rahasia 2FA (terenkripsi, opsional)"
        text two_factor_recovery_codes "Kode pemulihan 2FA (JSON terenkripsi, opsional)"
        timestamp two_factor_confirmed_at "Waktu aktivasi 2FA (opsional)"
        string remember_token "Token sesi login (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    identities }o--|| users : "milik pengguna"
    identities }o--|| institutions : "afiliasi institusi"
    identities }o--|| study_programs : "terdaftar di prodi"
    identities }o--|| faculties : "milik fakultas"
    
    identities {
        uuid id PK "UUID unik identitas"
        uuid user_id FK "FK ke users.id (satu-satu)"
        string identity_id "NIDN (10 digit) atau NIM (16 digit)"
        string sinta_id "ID Sinta peneliti (opsional)"
        string type "Tipe pengguna: dosen atau mahasiswa"
        text address "Alamat lengkap (opsional)"
        string birthplace "Tempat lahir (opsional)"
        date birthdate "Tanggal lahir (opsional)"
        bigint institution_id FK "FK ke institutions.id (opsional, set null)"
        bigint study_program_id FK "FK ke study_programs.id (opsional, set null)"
        bigint faculty_id FK "FK ke faculties.id (opsional, set null)"
        string profile_picture "Path foto profil (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% SISTEM PROPOSAL - Entitas Pusat
    %% ========================================
    
    proposals ||--o| research : "bermorfosis ke (detailable)"
    proposals ||--o| community_services : "bermorfosis ke (detailable)"
    proposals }o--|| research_schemes : "mengikuti skema"
    proposals }o--|| focus_areas : "dikategorikan oleh"
    proposals }o--|| themes : "bertema"
    proposals }o--|| topics : "topik spesifik"
    proposals }o--|| national_priorities : "sesuai PRN"
    proposals }o--|| science_clusters : "klaster level 1"
    proposals }o--|| science_clusters : "klaster level 2"
    proposals }o--|| science_clusters : "klaster level 3"
    proposals ||--o{ proposal_outputs : "menghasilkan"
    proposals ||--o{ budget_items : "menganggarkan"
    proposals ||--o{ activity_schedules : "menjadwalkan"
    proposals ||--o{ research_stages : "tahapan"
    proposals ||--o{ progress_reports : "melaporkan"
    proposals ||--o{ proposal_reviewer : "direview oleh"
    
    proposals {
        uuid id PK "UUID unik proposal"
        string title "Judul/nama proposal"
        uuid submitter_id FK "FK ke users.id (pemilik proposal, cascade delete)"
        string detailable_type "Tipe polimorfik: App-Models-Research atau App-Models-CommunityService"
        uuid detailable_id "FK polimorfik ke research.id atau community_services.id"
        bigint research_scheme_id FK "FK ke research_schemes.id (opsional, set null)"
        bigint focus_area_id FK "FK ke focus_areas.id (opsional, set null)"
        bigint theme_id FK "FK ke themes.id (opsional, set null)"
        bigint topic_id FK "FK ke topics.id (opsional, set null)"
        bigint national_priority_id FK "FK ke national_priorities.id (opsional, set null)"
        bigint cluster_level1_id FK "FK ke science_clusters.id Level-1 (opsional, set null)"
        bigint cluster_level2_id FK "FK ke science_clusters.id Level-2 (opsional, set null)"
        bigint cluster_level3_id FK "FK ke science_clusters.id Level-3 (opsional, set null)"
        decimal sbk_value "Nilai SBK (Satuan Biaya Keluaran) dalam IDR (opsional)"
        integer duration_in_years "Durasi proyek (tahun, default: 1)"
        text summary "Ringkasan proposal (opsional)"
        enum status "draft | submitted | need_assignment | approved | under_review | reviewed | revision_needed | completed | rejected"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% DATA KHUSUS PENELITIAN
    %% ========================================
    
    research }o--|| macro_research_groups : "dikategorikan oleh"
    
    research {
        uuid id PK "UUID unik penelitian"
        bigint macro_research_group_id FK "FK ke macro_research_groups.id (opsional, set null)"
        integer final_tkt_target "Target TKT akhir 0-9 (opsional)"
        longText background "Latar belakang & rasional penelitian"
        longText state_of_the_art "Tinjauan pustaka & kondisi terkini"
        longText methodology "Metodologi & pendekatan penelitian"
        json roadmap_data "Roadmap multi-tahun (format JSON)"
        string substance_file "Path dokumen substansi (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% DATA KHUSUS PKM
    %% ========================================
    
    community_services }o--o| partners : "mitra utama"
    
    community_services {
        uuid id PK "UUID unik PKM"
        bigint partner_id FK "FK ke partners.id (mitra utama, opsional, set null)"
        text partner_issue_summary "Deskripsi masalah mitra (opsional)"
        text solution_offered "Solusi yang ditawarkan (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% TIM & KOLABORASI
    %% ========================================
    
    proposal_user }o--|| proposals : "milik proposal"
    proposal_user }o--|| users : "milik pengguna"
    
    proposal_user {
        bigint id PK "ID unik keanggotaan tim"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        uuid user_id FK "FK ke users.id (cascade delete)"
        enum role "Peran tim: ketua atau anggota"
        text tasks "Tugas & tanggung jawab (opsional)"
        enum status "Status undangan: pending | accepted | rejected (default: pending)"
        timestamp created_at "Waktu undangan tim"
        timestamp updated_at "Waktu update status terakhir"
    }
    
    proposal_reviewer }o--|| proposals : "milik proposal"
    proposal_reviewer }o--|| users : "milik reviewer"
    
    proposal_reviewer {
        bigint id PK "ID unik penugasan reviewer"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        uuid user_id FK "FK ke users.id (cascade delete, reviewer)"
        enum status "Status review: pending | reviewing | completed (default: pending)"
        text review_notes "Catatan hasil review (opsional)"
        enum recommendation "approved | rejected | revision_needed (opsional)"
        timestamp created_at "Waktu penugasan"
        timestamp updated_at "Waktu update terakhir (selesai review)"
    }
    
    %% ========================================
    %% MANAJEMEN ANGGARAN
    %% ========================================
    
    budget_groups ||--o{ budget_components : "memiliki"
    budget_items }o--|| budget_groups : "pakai grup"
    budget_items }o--|| budget_components : "pakai komponen"
    budget_items }o--|| proposals : "milik proposal"
    
    budget_groups {
        bigint id PK "ID unik grup anggaran"
        string code UK "Kode grup unik (misal: HONOR, BAHAN)"
        string name "Nama grup anggaran"
        text description "Deskripsi grup (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    budget_components {
        bigint id PK "ID unik komponen anggaran"
        bigint budget_group_id FK "FK ke budget_groups.id (cascade delete)"
        string code "Kode komponen (unik dalam grup)"
        string name "Nama komponen anggaran"
        text description "Deskripsi komponen (opsional)"
        string unit "Satuan (misal: per sesi, per liter)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    budget_items {
        uuid id PK "UUID unik item anggaran"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        bigint budget_group_id FK "FK ke budget_groups.id"
        bigint budget_component_id FK "FK ke budget_components.id"
        text item_description "Deskripsi detail item"
        integer volume "Jumlah/volume"
        decimal unit_price "Harga per unit (IDR)"
        decimal total_price "Total harga (volume x unit_price, otomatis)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% SISTEM TAKSONOMI
    %% ========================================
    
    focus_areas ||--o{ themes : "memiliki"
    focus_areas ||--o{ proposals : "mengelompokkan"
    themes }o--|| focus_areas : "milik focus area"
    themes ||--o{ topics : "memiliki"
    themes ||--o{ proposals : "mengelompokkan"
    topics }o--|| themes : "milik theme"
    topics ||--o{ proposals : "mengelompokkan"
    
    focus_areas {
        bigint id PK "ID unik focus area"
        string name "Nama focus area (misal: Kesehatan, Teknologi)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    themes {
        bigint id PK "ID unik tema"
        bigint focus_area_id FK "FK ke focus_areas.id (cascade delete)"
        string name "Nama tema (anak dari focus area)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    topics {
        bigint id PK "ID unik topik"
        bigint theme_id FK "FK ke themes.id (cascade delete)"
        string name "Nama topik (anak dari tema)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% KLASTER ILMU
    %% ========================================
    
    science_clusters ||--o{ science_clusters : "parent-child (self-referencing)"
    
    science_clusters {
        bigint id PK "ID unik klaster ilmu"
        string name "Nama klaster"
        integer level "Level hierarki: 1, 2, atau 3"
        bigint parent_id FK "FK ke science_clusters.id (parent, null untuk level 1)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% DATA REFERENSI
    %% ========================================
    
    research_schemes ||--o{ proposals : "mengelompokkan"
    national_priorities ||--o{ proposals : "sesuai PRN"
    macro_research_groups ||--o{ research : "mengelompokkan"
    
    research_schemes {
        bigint id PK "ID unik skema penelitian"
        string name "Nama skema (misal: Penelitian Dasar, Terapan)"
        string strata "Tingkat strata (opsional)"
        text description "Deskripsi skema (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    national_priorities {
        bigint id PK "ID unik prioritas nasional"
        string name "Nama prioritas (PRN)"
        text description "Deskripsi prioritas (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    macro_research_groups {
        bigint id PK "ID unik grup riset makro"
        string name "Nama grup riset"
        text description "Deskripsi grup (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% DATA PENDUKUNG PROPOSAL
    %% ========================================
    
    proposal_outputs }o--|| proposals : "milik proposal"
    activity_schedules }o--|| proposals : "milik proposal"
    research_stages }o--|| proposals : "milik proposal"
    research_stages }o--|| users : "penanggung jawab"
    
    proposal_outputs {
        uuid id PK "UUID unik luaran"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        integer output_year "Tahun target luaran"
        enum category "required | additional"
        string type "Tipe luaran (jurnal, paten, buku, dll)"
        string target_status "Target capaian (Q1, Q2, Granted, dll)"
        text output_description "Deskripsi detail luaran (opsional)"
        string journal_name "Nama jurnal/tempat publikasi (opsional)"
        date estimated_date "Estimasi selesai (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    activity_schedules {
        uuid id PK "UUID unik aktivitas"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        string activity_name "Deskripsi aktivitas"
        integer year "Tahun aktivitas"
        integer start_month "Bulan mulai (1-12)"
        integer end_month "Bulan selesai (1-12)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    research_stages {
        uuid id PK "UUID unik tahapan penelitian"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        integer stage_number "Nomor urut tahapan"
        string process_name "Nama tahapan/proses"
        text outputs "Luaran tahapan (opsional)"
        text indicator "Indikator keberhasilan (opsional)"
        uuid person_in_charge_id FK "FK ke users.id (anggota tim, opsional, set null)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% PELACAKAN PROGRES
    %% ========================================
    
    progress_reports }o--|| proposals : "milik proposal"
    progress_reports }o--|| users : "diunggah oleh"
    progress_reports ||--o{ mandatory_outputs : "memiliki wajib"
    progress_reports ||--o{ additional_outputs : "memiliki tambahan"
    
    progress_reports {
        uuid id PK "UUID unik laporan kemajuan"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        text summary_update "Ringkasan update proyek (opsional)"
        integer reporting_year "Tahun pelaporan"
        enum reporting_period "semester_1 | semester_2 | annual"
        enum status "draft | submitted | approved (default: draft)"
        uuid submitted_by FK "FK ke users.id (pengunggah, opsional)"
        timestamp submitted_at "Waktu pengajuan (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    mandatory_outputs {
        uuid id PK "UUID unik luaran wajib"
        uuid progress_report_id FK "FK ke progress_reports.id (cascade delete)"
        string output_type "Tipe luaran"
        text description "Deskripsi luaran"
        string status "Status penyelesaian"
        string evidence_file "Path dokumen pendukung (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    additional_outputs {
        uuid id PK "UUID unik luaran tambahan"
        uuid progress_report_id FK "FK ke progress_reports.id (cascade delete)"
        string output_type "Tipe luaran"
        text description "Deskripsi luaran"
        string status "Status penyelesaian"
        string evidence_file "Path dokumen pendukung (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% MITRA & KATA KUNCI
    %% ========================================
    
    partners ||--o{ community_services : "mitra utama"
    proposal_partner }o--|| proposals : "milik proposal"
    proposal_partner }o--|| partners : "milik mitra"
    proposal_keyword }o--|| proposals : "milik proposal"
    proposal_keyword }o--|| keywords : "milik keyword"
    progress_report_keyword }o--|| progress_reports : "milik laporan"
    progress_report_keyword }o--|| keywords : "milik keyword"
    
    partners {
        bigint id PK "ID unik mitra"
        string name "Nama organisasi"
        string type "Tipe organisasi (LSM, Komunitas, Pemerintah, dll)"
        text address "Alamat organisasi"
        string contact_person "Nama kontak (opsional)"
        string phone "No. telepon kontak (opsional)"
        string email "Email kontak (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    proposal_partner {
        uuid proposal_id PK,FK "FK ke proposals.id (cascade delete)"
        bigint partner_id PK,FK "FK ke partners.id (cascade delete)"
        timestamp created_at "Waktu pembuatan relasi"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    keywords {
        bigint id PK "ID unik keyword"
        string name "Teks keyword"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    proposal_keyword {
        bigint id PK "ID unik relasi proposal-keyword"
        uuid proposal_id FK "FK ke proposals.id (cascade delete)"
        bigint keyword_id FK "FK ke keywords.id (cascade delete)"
        timestamp created_at "Waktu pembuatan relasi"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    progress_report_keyword {
        bigint id PK "ID unik relasi laporan-keyword"
        uuid progress_report_id FK "FK ke progress_reports.id (cascade delete)"
        bigint keyword_id FK "FK ke keywords.id (cascade delete)"
        timestamp created_at "Waktu pembuatan relasi"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% STRUKTUR ORGANISASI
    %% ========================================
    
    institutions ||--o{ study_programs : "menyelenggarakan"
    institutions ||--o{ identities : "afiliasi identitas"
    faculties ||--o{ study_programs : "mengelola"
    faculties ||--o{ identities : "milik fakultas"
    study_programs }o--|| institutions : "milik institusi"
    study_programs }o--|| faculties : "milik fakultas"
    study_programs ||--o{ identities : "terdaftar identitas"
    
    institutions {
        bigint id PK "ID unik institusi"
        string name "Nama institusi"
        string type "Tipe institusi"
        text address "Alamat institusi"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    faculties {
        bigint id PK "ID unik fakultas"
        string code "Kode fakultas (misal: SAINTEK, DEKABITA)"
        string name "Nama fakultas"
        text description "Deskripsi fakultas (opsional)"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    study_programs {
        bigint id PK "ID unik program studi"
        bigint institution_id FK "FK ke institutions.id (cascade delete)"
        bigint faculty_id FK "FK ke faculties.id"
        string name "Nama program studi"
        timestamp created_at "Waktu pembuatan data"
        timestamp updated_at "Waktu terakhir diubah"
    }
    
    %% ========================================
    %% NOTIFIKASI
    %% ========================================
    
    notifications {
        uuid id PK "UUID unik notifikasi"
        string type "Nama class notifikasi"
        string notifiable_type "Polimorfik: tipe model notifiable"
        uuid notifiable_id "Polimorfik: ID model notifiable"
        json data "Payload notifikasi (judul, pesan, tautan, dll)"
        timestamp read_at "Waktu dibaca (opsional)"
        timestamp created_at "Waktu pembuatan notifikasi"
        timestamp updated_at "Waktu terakhir diubah"
    }
```


---

## 2) Deskripsi Entitas (Ringkas)

Inti:
- `users` + `identities`: akun & profil akademik (fakultas/prodi).  
- `proposals`: entitas pusat (polimorfik) ke `research` atau `community_services`.  
- `proposal_user`: keanggotaan tim (ketua/anggota, status undangan).  
- `proposal_reviewer`: penugasan & hasil review (status, catatan, rekomendasi).  
- `budget_groups`/`budget_components`/`budget_items`: struktur RAB 2 level + item.  
- `focus_areas`/`themes`/`topics`: taksonomi 3 level.  
- `science_clusters`: rumpun ilmu 3 level (self-referencing).  
- `proposal_outputs`, `activity_schedules`, `research_stages`: luaran, jadwal, tahapan.  
- `progress_reports` (+ `mandatory_outputs`/`additional_outputs`): laporan kemajuan & luaran terkait.  
- `partners` (+ `proposal_partner`): mitra PKM.  
- `keywords` (+ pivot untuk proposal & progress report).  
- `notifications`: notifikasi Laravel (database).

Karakteristik umum:
- Banyak tabel memakai UUID sebagai PK.  
- Relasi Eloquent dengan eager loading untuk performa.  
- Integritas referensial dengan FK (cascade/set null sesuai kebutuhan).

---

## 3) Ringkasan Relasi

Satu-ke-satu (1:1):
- `users` ↔ `identities`  
- `proposals` → `research` / `community_services` (polimorfik)

Satu-ke-banyak (1:N):
- `users` → `proposals` (submitter)  
- `proposals` → `proposal_outputs`, `budget_items`, `activity_schedules`, `research_stages`, `progress_reports`, `proposal_reviewer`  
- `focus_areas` → `themes` → `topics`  
- `budget_groups` → `budget_components`  
- `partners` → `community_services` (mitra utama)  
- `progress_reports` → `mandatory_outputs`/`additional_outputs`  
- `institutions`/`faculties`/`study_programs` → `identities`

Banyak-ke-banyak (M:N):
- `proposals` ↔ `users` via `proposal_user`  
- `proposals` ↔ `keywords` via `proposal_keyword`  
- `proposals` ↔ `partners` via `proposal_partner`  
- `progress_reports` ↔ `keywords` via `progress_report_keyword`

Polimorfik:
- `proposals.detailable` → `research` ATAU `community_services`  
- `notifications.notifiable` → model terkait (mis. users)

---

## 4) Ringkasan Sederhana (Plain Indonesian)

Desain basis data berpusat pada tabel `proposals` yang mendukung dua tipe: **Penelitian** dan **PKM** lewat relasi polimorfik. Data khusus Penelitian (metodologi, roadmap, TKT) tersimpan di `research`; data khusus PKM (isu mitra, solusi, mitra utama) tersimpan di `community_services`.

Keanggotaan tim dikelola lewat `proposal_user` (peran ketua/anggota dan status undangan). Rencana anggaran memakai struktur 2 tingkat (grup → komponen) dengan item per-proposal (`budget_items`) yang otomatis menghitung total (volume × harga). Jadwal kegiatan, luaran direncanakan, dan tahapan dikelola di tabel masing-masing.

Taksonomi berjenjang (Focus Area → Theme → Topic) dan Rumpun Ilmu 3 level membantu pengelompokan dan pelaporan. Laporan kemajuan (`progress_reports`) merekam luaran wajib/tambahan berikut bukti. Mitra (khusus PKM) dan kata kunci direlasikan lewat tabel pivot. Semua aktivitas notifikasi tersimpan di `notifications`.

Pendekatan ini memisahkan data umum proposal, data khusus tipe, kolaborasi tim, data referensi, serta tracking progres sehingga mudah dikembangkan, mudah diquery, dan mematuhi alur persetujuan bertahap sistem.

---

Catatan: Rincian kolom-per-kolom yang sangat panjang mengikuti versi Inggris. Dokumen Indonesia ini menyederhanakan bahasa namun mempertahankan makna dan cakupan inti. Bila dibutuhkan, saya dapat menerjemahkan bagian tabel secara bertahap per entitas.
