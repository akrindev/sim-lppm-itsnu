# SIM-LPPM Sequence Diagrams

## Gambaran Sistem
Dokumen ini berisi diagram urutan untuk SIM-LPPM (Sistem Manajemen Penelitian Akademik dan Pengabdian kepada Masyarakat) yang menunjukkan bagaimana berbagai peran pengguna berinteraksi dengan sistem sepanjang siklus proposal.

## Peran Pengguna & Matriks Otorisasi

### Hierarki Peran & Izin
```
superadmin (Level Sistem)
├── Akses sistem penuh
├── Pengembang IT/perawatan
└── Dapat memodifikasi semua data

rektor (Level Universitas)
├── Lihat semua proposal
├── Setujui penelitian prioritas tinggi
├── Pengawasan strategis
└── Otoritas persetujuan akhir

dekan (Level Fakultas)
├── Proposal khusus fakultas
├── Persetujuan teknis
├── Pengawasan anggaran fakultas
└── Koordinasi penelitian fakultas

kepala lppm (Direktur LPPM)
├── Pengawasan operasi LPPM
├── Koordinasi lintas disiplin
├── Implementasi kebijakan
└── Standarisasi proses

admin lppm (Admin LPPM Umum)
├── Manajemen pengguna
├── Administrasi proposal dasar
├── Konfigurasi sistem
└── Perawatan umum

admin lppm saintek (Admin LPPM Saintek)
└── Spesialisasi proposal sains/teknologi

admin lppm dekabita (Admin LPPM Dekabita)
└── Spesialisasi proposal sosial/humaniora

reviewer (Reviewer Khusus)
├── Evaluasi teknis
├── Input keahlian domain
├── Penilaian proposal
└── Rekomendasi review

dosen (Dosen)
├── Pengiriman proposal
├── Pelaporan kemajuan
├── Manajemen proyek
└── Penyampaian penelitian
```

---

## 1. Research Proposal Submission Workflow

```mermaid
sequenceDiagram
    participant D as Dosen
    participant SI as Sistem SIM-LPPM
    participant DB as Database
    participant N as Notifikasi

    D->>SI: Akses halaman pembuatan proposal
    SI->>DB: Load data referensi (skema, bidang fokus, tema, topik)
    DB-->>SI: Return opsi data master
    SI-->>D: Tampilkan form proposal

    D->>SI: Kirim draft proposal (judul, abstrak, tujuan)
    SI->>SI: Validasi input data
    SI->>DB: Simpan proposal (status: draft)
    DB-->>SI: Return proposal_id
    SI-->>D: Tampilkan form detail Research

    D->>SI: Kirim detail Research (metodologi, roadmap, output)
    SI->>SI: Validasi data Research
    SI->>DB: Simpan detail Research (link polymorphic)

    D->>SI: Kirim anggota tim & anggaran
    SI->>SI: Validasi struktur tim & anggaran
    SI->>DB: Simpan proposal_user (roles) & budget_items

    D->>SI: Kirim jadwal kegiatan
    SI->>DB: Simpan activity_schedules (timeline tahunan)

    D->>SI: Kirim kata kunci & perencanaan output
    SI->>DB: Simpan proposal_keywords & proposal_outputs

    D->>SI: Submit akhir (status: submitted)
    SI->>SI: Ubah status proposal ke 'submitted'
    DB-->>SI: Update berhasil
    SI->>N: Trigger notifikasi ke reviewer/admin
    N-->>D: Konfirmasi email/SMS dikirim
    SI-->>D: Pengiriman berhasil + nomor referensi
```

---

## 2. Proposal Review & Approval Process

```mermaid
sequenceDiagram
    participant DO as Dosen
    participant SI as Sistem SIM-LPPM
    participant DB as Database
    participant A as Admin LPPM
    participant R as Reviewer
    participant N as Notifikasi Email/SMS
    participant AS as Admin Saintek
    participant AD as Admin Dekabita
    participant KA as Kepala LPPM
    participant RE as Rektor

    DO->>SI: Kirim proposal (research OR community service)
    SI->>DB: Simpan proposal (status: submitted)
    SI->>N: Beritahu Admin LPPM untuk penugasan
    N-->>A: Proposal baru memerlukan penugasan reviewer

    A->>SI: Login untuk menugaskan reviewer
    SI->>DB: Load reviewer yang tersedia
    DB-->>SI: Return daftar reviewer
    A->>SI: Tugaskan reviewer yang sesuai
    SI->>DB: Simpan penugasan reviewer
    SI->>N: Beritahu reviewer penugasannya
    N-->>R: Proposal baru untuk direview

    R->>SI: Login untuk review proposal
    SI->>DB: Load detail proposal yang ditugaskan
    DB-->>SI: Return data proposal lengkap
    SI-->>R: Tampilkan proposal untuk review

    R->>SI: Kirim review (nilai, komentar, rekomendasi)
    SI->>DB: Simpan temuan review (status: reviewed)
    SI->>N: Beritahu admin untuk langkah persetujuan selanjutnya

    AS->>SI: Login sebagai admin saintek (jika proposal sains)
    AD->>SI: Login sebagai admin dekabita (jika proposal sosial)
    SI->>DB: Load proposal dengan review
    DB-->>SI: Return data evaluasi
    AS->>SI: Review admin & persetujuan awal
    SI->>DB: Update status (admin_approved)
    SI->>N: Teruskan ke Kepala LPPM

    KA->>SI: Login sebagai kepala LPPM
    SI->>DB: Load paket proposal lengkap
    DB-->>SI: Return paket keputusan
    KA->>SI: Persetujuan akhir LPPM/batal
    SI->>DB: Update status (approved/rejected)
    SI->>N: Beritahu dekan untuk persetujuan fakultas

    RE->>SI: Login sebagai rektor (kasus prioritas tinggi)
    SI->>DB: Load data proposal strategis
    RE->>SI: Persetujuan strategis/batal
    SI->>DB: Update status akhir

    SI->>DO: Notifikasi: Proposal disetujui/ditolak
    DO->>SI: Lihat feedback/alasan detail
    SI->>DB: Load komentar & saran review

    opt Proposal Ditolak
        DO->>SI: Kirim revisi
        SI->>DB: Update status ke 'revision'
        SI->>N: Beritahu revisi diperlukan
    end

    alt Proposal Disetujui
        SI->>DO: Notifikasi: Siap untuk eksekusi
        SI->>DB: Update status ke 'ready_execution'
    end
```

---

## 3. Community Service Partnership Management

```mermaid
sequenceDiagram
    participant D as Dosen
    participant SI as Sistem SIM-LPPM
    participant DB as Database
    participant P as Partner (Mitra)
    participant N as Notifikasi

    D->>SI: Akses pembuatan proposal community service
    SI->>DB: Load mitra yang tersedia
    DB-->>SI: Return daftar mitra
    SI-->>D: Tampilkan interface pemilihan mitra

    D->>SI: Pilih atau tambah mitra baru
    SI->>DB: Buat/update record mitra
    DB-->>SI: Return partner_id
    SI-->>D: Tampilkan form community service

    D->>SI: Kirim detail layanan (partner_issue, solution_offered)
    SI->>DB: Simpan CommunityService (link polymorphic)
    SI->>P: Generate surat notifikasi mitra
    P-->>SI: Pengakuan mitra (opsional)

    D->>SI: Rencanakan kegiatan & anggaran (jadwal tahunan)
    SI->>DB: Simpan activity_schedules & budget_items
    SI->>N: Beritahu mitra untuk perencanaan kolaborasi

    P->>D: Komunikasi langsung untuk detail implementasi
    D->>SI: Update laporan kemajuan
    SI->>DB: Simpan daily_notes & progress_reports

    SI->>P: Kirim update kemajuan (jika diminta)
    SI->>D: Monitor milestone pengiriman layanan
    D->>SI: Kirim laporan akhir
    SI->>DB: Simpan final_report
    SI->>P: Generate sertifikat penyelesaian
```

---

## 4. Progress Monitoring & Reporting System

```mermaid
sequenceDiagram
    participant D as Dosen
    participant SI as Sistem SIM-LPPM
    participant DB as Database
    participant A as Admin LPPM
    participant N as Notifikasi

    loop Update Kemajuan Mingguan/Bulanan
        SI->>D: Pengingat: Update catatan harian
        D->>SI: Login untuk kirim catatan harian
        SI-->>D: Tampilkan form catatan harian
        D->>SI: Kirim log aktivitas & tantangan
        SI->>DB: Simpan daily_notes (dengan proposal_id)
        SI-->>D: Konfirmasi tersimpan
    end

    alt Deadline Laporan Kemajuan
        SI->>D: Notifikasi: Laporan kemajuan jatuh tempo
        D->>SI: Akses form laporan kemajuan
        SI->>DB: Load detail proposal & laporan sebelumnya
        DB-->>SI: Return data kemajuan
        SI-->>D: Tampilkan template laporan
        D->>SI: Kirim laporan kemajuan (pencapaian, sasaran berikutnya)
        SI->>DB: Simpan progress_reports
        SI->>A: Teruskan ke administrator monitoring
        A->>SI: Review laporan kemajuan
        SI->>DB: Simpan feedback admin
        SI->>N: Beritahu feedback ke dosen
    end

    SI->>D: Pengingat deadline laporan akhir
    D->>SI: Kirim laporan akhir komprehensif
    SI->>DB: Simpan final_reports
    SI->>A: Evaluasi & persetujuan akhir
    SI->>DB: Update status proposal ke 'completed'
    SI->>N: Notifikasi pembuatan sertifikat
    SI->>D: Sertifikat penyelesaian & penutupan proyek
```

---

## 5. Authorization & Access Control Flow

```mermaid
sequenceDiagram
    participant U as User
    participant A as Authentication
    participant SI as Sistem SIM-LPPM
    participant DB as Database
    participant P as Permission System
    participant R as Requested Resource

    U->>A: Login attempt (email/password)
    A->>SI: Validate credentials
    SI->>DB: Check user record
    DB-->>SI: Return user data
    SI->>P: Load user roles & permissions

    alt 2FA Enabled
        A->>U: Request 2FA code
        U->>A: Enter 2FA code
        A->>SI: Verify 2FA token
    end

    P-->>SI: Return role permissions
    SI->>SI: Create session with ACL
    SI-->>U: Login successful + dashboard

    U->>SI: Access protected resource (e.g. proposal create)
    SI->>P: Check permission (hasRole: 'dosen' + can: 'create-proposal')
    P-->>SI: Allow/Deny

    alt Permission Granted
        SI->>R: Access resource
        R-->>SI: Return data
        SI-->>U: Display resource
    else Permission Denied
        SI-->>U: 403 Forbidden error
    end

    U->>SI: Access admin-only feature
    SI->>P: Check admin role (hasRole: 'admin lppm')
    P-->>SI: Permission matrix violation
    SI-->>U: Access denied

    opt Escalation for Approval
        U->>SI: Request role upgrade
        SI->>A: Forward to administrator
        A->>SI: Approval decision
        SI->>DB: Update user roles
    end
```

---

## 6. Budget Management Workflow

```mermaid
sequenceDiagram
    participant D as Dosen
    participant SI as Sistem SIM-LPPM
    participant DB as Database
    participant V as Validator
    participant A as Admin LPPM
    participant F as Sistem Keuangan
    participant N as Notifikasi

    D->>SI: Akses form perencanaan anggaran
    SI->>DB: Load skema penelitian yang disetujui
    DB-->>SI: Return panduan pendanaan
    SI-->>D: Tampilkan template anggaran

    D->>SI: Tambah item anggaran (komponen, volume, harga_satuan)
    SI->>V: Validasi struktur anggaran
    V-->>SI: Hasil validasi

    loop Entri Anggaran
        D->>SI: Hitung total_harga (volume × harga_satuan)
        SI->>SI: Hitung sub-total secara otomatis
        SI-->>D: Update ringkasan anggaran
    end

    D->>SI: Kirim anggaran lengkap
    SI->>V: Validasi anggaran komprehensif
    alt Anggaran Valid
        V-->>SI: Disetujui
        SI->>DB: Simpan budget_items
        SI->>A: Teruskan untuk review administratif
    else Anggaran Tidak Valid
        V-->>SI: Alasan penolakan
        SI-->>D: Tampilkan error validasi
    end

    A->>SI: Review alokasi anggaran
    SI->>DB: Load detail anggaran & panduan
    DB-->>SI: Return data validasi
    A->>SI: Setujui/tolak anggaran
    SI->>DB: Update status anggaran
    SI->>N: Beritahu keputusan ke dosen

    alt Anggaran Disetujui
        SI->>F: Ekspor untuk alokasi pendanaan
        F-->>SI: Konfirmasi pendanaan
        SI->>DB: Catat status pendanaan
    else Anggaran Ditolak
        SI->>D: Tampilkan persyaratan revisi
        SI->>D: Kirim ulang anggaran yang direvisi
    end
```

---

## Aturan Bisnis & Kendala Sistem

### 1. Kendala Tipe Proposal
- **Proposal Penelitian**: Harus memiliki metodologi, roadmap_data, final_tkt_target
- **Pengabdian kepada Masyarakat**: Harus memiliki partner_id, partner_issue_summary
- **Eksklusif Bersama**: Tidak dapat menjadi penelitian DAN pengabdian masyarakat bersamaan

### 2. Aturan Struktur Tim
- **Kepemimpinan**: Setiap proposal harus memiliki satu 'ketua' (pemimpin)
- **Batas Ukuran**: Minimum 1 anggota, maksimum dapat dikontrol berdasarkan peran
- **Penugasan Peran**: Hanya satu ketua per proposal
- **Penugasan Tugas**: Setiap anggota harus memiliki tugas yang ditentukan

### 3. Aturan Validasi Anggaran
- **Integrasi SBK**: Nilai anggaran harus sesuai dengan SBK_value
- **Klasifikasi Kategori**: Harus mengikuti kelompok anggaran standar
- **Perhitungan Total**: Dihitung otomatis, mencegah penggantian manual
- **Level Persetujuan**: Hierarki persetujuan berbeda berdasarkan ukuran anggaran

### 4. Corrected Approval Workflow Matrix

| Stage Order | Role/Process                         | Description                                                   |
| ----------- | ------------------------------------ | ------------------------------------------------------------- |
| 1           | **Admin LPPM Assignment**            | Assigns appropriate reviewer based on proposal type           |
| 2           | **Reviewer Evaluation**              | Technical/domain expert reviews proposal content              |
| 3           | **Admin LPPM Review**                | LPPM admin reviews based on specialization (saintek/dekabita) |
| 4           | **Kepala LPPM Approval**             | LPPM director final institutional approval                    |
| 5           | **Dekan Approval** (Optional)        | Faculty dean approval for certain proposals                   |
| 6           | **Rektor Approval** (Strategic only) | University rector approval for high-impact proposals          |

*Only for strategic/high-impact proposals

### 5. Kadensi Pelaporan
- **Catatan Harian**: Basis mingguan selama eksekusi aktif
- **Laporan Kemajuan**: Bulanan untuk monitoring
- **Laporan Keuangan**: Triwulanan untuk kepatuhan anggaran
- **Laporan Akhir**: Milestone penyelesaian proyek

### 6. Proses Penugasan Reviewer
- **Penugasan Admin LPPM**: Semua proposal yang dikirim memerlukan penugasan reviewer manual
- **Pencocokan Spesialisasi**: Reviewer ditugaskan berdasarkan tipe proposal (penelitian/pengabdian)
- **Keahlian Domain**: Reviewer teknis dicocokkan dengan bidang ilmu proposal
- **Penyeimbangan Beban Kerja**: Sistem harus menyeimbangkan beban kerja reviewer
- **Pelacakan Penugasan**: Semua penugasan dicatat dengan timestamp

### 7. Eskalasi Otorisasi
- **Penugasan Otomatis**: Berdasarkan karakteristik proposal
- **Penggantian Manual**: Admin senior dapat meningkatkan/mengalihkan
- **Pemrosesan Paralel**: Beberapa reviewer dapat bekerja secara simultan
- **Persyaratan Konsensus**: Ambang batas persetujuan yang dapat dikonfigurasi

---

## Penanganan Error & Alur Pengecualian

### Skenario Kegagalan Umum

1. **Alur Izin Ditolak**
```
User → Request → System → Check ACL → DENIED → Halaman Error → Proses Banding User
```

2. **Kegagalan Validasi Data**
```
User → Submit → System → Validate → ERRORS → Tampilkan Form dengan Error → User Perbaiki
```

3. **Kegagalan Layanan Eksternal**
```
System → External API → TIMEOUT → Fallback → Notifikasi → Antrian Retry → Sukses
```

### Mekanisme Pemulihan
- **Rollback Transaksi**: Kendala database memastikan atomisitas
- **Jejak Audit**: Semua tindakan dicatat untuk penyelesaian perselisihan
- **Kontrol Versi**: Perubahan proposal dilacak dengan riwayat
- **Auto-save**: Pencegahan kehilangan data pada draft
