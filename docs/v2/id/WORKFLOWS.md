# Dokumentasi Alur Kerja v2.0 (Bahasa Indonesia)
## SIM LPPM ITSNU – Alur Proses Lengkap

**Versi Dokumen:** 2.0  
**Terakhir Diperbarui:** 2025-11-09

---

## Daftar Isi
1. [Siklus Proposal Lengkap](#siklus-proposal-lengkap)
2. [Alur Dosen](#alur-dosen)
3. [Alur Dekan](#alur-dekan)
4. [Alur Kepala LPPM](#alur-kepala-lppm)
5. [Alur Admin LPPM](#alur-admin-lppm)
6. [Alur Reviewer](#alur-reviewer)
7. [Ringkasan Eksekutif](#ringkasan-eksekutif)

---

## Siklus Proposal Lengkap

### Diagram Ikhtisar

```mermaid
graph TD
    A[Dosen Membuat Proposal] --> B[Status: DRAFT]
    B --> C[Undang Anggota Tim]
    C --> D{Semua Anggota<br/>Menerima?}
    D -->|Tidak| E[Status: NEED_ASSIGNMENT]
    E --> C
    D -->|Ya| F[Dosen Submit]
    F --> G[Status: SUBMITTED]
    G --> H[Dekan Meninjau]
    H --> I{Keputusan<br/>Dekan}
    I -->|Setujui| J[Status: APPROVED]
    I -->|Perbaiki Tim| E
    I -->|Tolak| K[Status: REJECTED]
    J --> L[Persetujuan Awal Kepala LPPM]
    L --> M[Status: UNDER_REVIEW]
    M --> N[Admin LPPM Menugaskan Reviewer]
    N --> O[Reviewer Melakukan Penilaian]
    O --> P{Semua Review<br/>Selesai?}
    P -->|Tidak| O
    P -->|Ya| Q[Status: REVIEWED]
    Q --> R[Keputusan Akhir Kepala LPPM]
    R --> S{Keputusan<br/>Akhir}
    S -->|Setujui| T[Status: COMPLETED]
    S -->|Revisi| U[Status: REVISION_NEEDED]
    S -->|Tolak| K
    U --> V[Dosen Melakukan Revisi]
    V --> F
    
    style T fill:#90EE90
    style K fill:#FFB6C1
    style M fill:#87CEEB
    style Q fill:#DDA0DD
```

### Tabel Progres Status

| Tahap | Status                                 | Aktor       | Durasi     | Langkah Berikutnya           |
| ----- | -------------------------------------- | ----------- | ---------- | ---------------------------- |
| 1     | DRAFT                                  | Dosen       | Variabel   | Undangan tim                 |
| 2     | NEED_ASSIGNMENT                        | Dosen/Tim   | 1-2 minggu | Persetujuan tim              |
| 3     | SUBMITTED                              | Dekan       | 3-5 hari   | Review Dekan                 |
| 4     | APPROVED                               | Kepala LPPM | 2-3 hari   | Persetujuan awal Kepala LPPM |
| 5     | UNDER_REVIEW                           | Admin LPPM  | 1-2 hari   | Penugasan reviewer           |
| 6     | UNDER_REVIEW                           | Reviewer    | 7-14 hari  | Penyelesaian review          |
| 7     | REVIEWED                               | Kepala LPPM | 2-3 hari   | Keputusan akhir              |
| 8     | COMPLETED / REVISION_NEEDED / REJECTED | -           | -          | Terminal atau loop revisi    |

**Durasi Rata-rata Total:** 2-3 minggu (tanpa revisi)

---

## Ringkasan Workflow Keseluruhan

### Gambaran Umum Proses

Sistem SIM LPPM ITSNU mengimplementasikan alur persetujuan multi-tahap yang menyeluruh untuk mengelola siklus hidup proposal penelitian dan pengabdian kepada masyarakat. Proses ini dirancang untuk memastikan transparansi, kolaborasi tim yang efektif, dan evaluasi berkualitas tinggi sebelum persetujuan final.

#### Workflow Lengkap Antar Role

```mermaid
sequenceDiagram
    participant D as Dosen
    participant T as Anggota Tim
    participant Dekan
    participant KL as Kepala LPPM
    participant A as Admin LPPM
    participant R as Reviewer
    participant S as Sistem

    %% Fase 1: Dosen - Draft & Undangan Tim
    Note over D,S: Fase 1: DRAFT & Tim Assembly (Dosen)
    D->>S: Buat proposal → DRAFT
    D->>S: Undang anggota tim
    S->>T: Kirim undangan
    
    %% Fase 2: Tim Persetujuan
    Note over T,S: Fase 2: Persetujuan Tim
    loop Setiap anggota
        T->>S: Terima/Tolak
        alt Tolak
            S->>D: Status → NEED_ASSIGNMENT
        end
    end
    
    %% Fase 3: Dosen Submit
    Note over D,Dekan: Fase 3: Submit & Review Dekan
    D->>S: Submit proposal (semua terima) → SUBMITTED
    S->>Dekan: Notifikasi: Review proposal
    
    %% Fase 4: Dekan Approval
    Dekan->>S: Review proposal
    alt Dekan: Setujui
        S->>S: Status → APPROVED
    else Dekan: Perbaiki Tim
        S->>D: Status → NEED_ASSIGNMENT
        D->>S: Perbaiki tim & resubmit
    else Dekan: Tolak
        S->>S: Status → REJECTED
    end
    
    %% Fase 5: Kepala LPPM Awal
    Note over KL,Admin: Fase 4: Persetujuan Awal Kepala LPPM
    S->>KL: Notifikasi: Persetujuan awal
    KL->>S: Review & setujui
    S->>S: Status → UNDER_REVIEW
    
    %% Fase 6: Admin & Reviewer
    Note over A,R: Fase 5: Penugasan & Review
    S->>A: Notifikasi: Tugaskan reviewer
    A->>S: Tugaskan reviewer
    S->>R: Notifikasi: Anda ditugaskan
    
    loop Setiap reviewer
        R->>S: Review proposal
        R->>S: Submit rekomendasi
    end
    S->>S: Semua selesai → REVIEWED
    
    %% Fase 7: Kepala LPPM Final
    Note over KL,S: Fase 6: Keputusan Akhir Kepala LPPM
    S->>KL: Notifikasi: Semua review selesai
    KL->>S: Review hasil review
    alt Setujui
        S->>S: Status → COMPLETED
    else Revisi
        S->>D: Status → REVISION_NEEDED
        D->>S: Revisi & resubmit → SUBMITTED
    else Tolak
        S->>S: Status → REJECTED
    end
```

#### Status Workflow & Transisi

```
DRAFT
  ↓
NEED_ASSIGNMENT ← (jika ada penolakan tim)
  ↓
SUBMITTED → APPROVED → UNDER_REVIEW → REVIEWED → COMPLETED
  ↓           ↓           ↓              ↓
  ├─REJECTED  ├─REJECTED  └─REJECTED    ├─REJECTED
  └─REVISION_NEEDED (from REVIEWED, loop ke SUBMITTED)
```

---

### Workflow Berdasarkan Role

#### 1. DOSEN - Peran Pemrakarsa & Revisi

```mermaid
sequenceDiagram
    participant D as Dosen
    participant T as Anggota Tim
    participant S as Sistem
    participant N as Notifikasi

    %% Tahap 1: Buat Draft
    Note over D,S: Tahap 1: Membuat & Menyusun Proposal
    D->>S: Membuat proposal baru
    S->>D: Status: DRAFT
    D->>S: Mengisi detail (judul, metodologi, anggaran, jadwal, luaran)
    D->>S: Simpan sebagai DRAFT
    
    %% Tahap 2: Undang Tim
    Note over D,T: Tahap 2: Merekrut Anggota Tim
    D->>S: Undang anggota (ketua/anggota)
    S->>T: Kirim email + notifikasi aplikasi
    
    loop Respons Anggota
        T->>S: Terima atau Tolak
        alt Tolak
            S->>S: Proposal → NEED_ASSIGNMENT
            N->>D: Beri tahu: ada yang menolak
        end
    end
    
    %% Tahap 3: Submit
    Note over D,S: Tahap 3: Submit Proposal
    D->>S: Cek: Semua anggota sudah terima?
    alt Ya, Semua Terima
        D->>S: Submit proposal
        S->>S: Status → SUBMITTED
        N->>D: Konfirmasi: Proposal disubmit
    else Ada yang Tolak/Pending
        N->>D: Tidak bisa submit: Perbaiki tim
        D->>S: Hapus/ganti anggota
        D->>S: Undang anggota baru
        loop Tunggu Respons Baru
            T->>S: Terima undangan
        end
        D->>S: Submit ulang
    end
    
    %% Tahap 4: Tunggu Review
    Note over D,S: Tahap 4: Menunggu Persetujuan (Passive)
    S->>N: Review sedang berlangsung
    N->>D: Status updates (Dekan approve, Reviewer assigned, dll.)
    
    %% Tahap 5: Revisi (jika diperlukan)
    Note over D,S: Tahap 5: Revisi Jika Diminta
    alt Kepala LPPM: Minta Revisi
        N->>D: Status → REVISION_NEEDED + catatan reviewer
        D->>S: Baca catatan reviewer
        D->>S: Edit proposal sesuai masukan
        D->>S: Submit revisi
        S->>S: Status → SUBMITTED (ulang alur)
    else Approved atau Rejected
        N->>D: Status final: COMPLETED / REJECTED
    end
```

**Aksi Utama Dosen:**
- Membuat proposal (DRAFT)
- Mengundang anggota tim → semua harus menerima
- Submit proposal (SUBMITTED)
- Menerima feedback dari reviewer
- Revisi jika diminta (loop ke SUBMITTED)
- Submit laporan kemajuan (untuk proposal COMPLETED)

---

#### 2. DEKAN - Persetujuan Pertama

```mermaid
sequenceDiagram
    participant Dekan
    participant S as Sistem
    participant D as Dosen
    participant N as Notifikasi

    %% Tahap 1: Menerima Notifikasi
    Note over Dekan,S: Tahap 1: Notifikasi & Akses Proposal
    N->>Dekan: Proposal disubmit dari fakultas Anda
    Dekan->>S: Login & buka notifikasi
    Dekan->>S: Lihat daftar proposal SUBMITTED dari fakultas
    
    %% Tahap 2: Review
    Note over Dekan,S: Tahap 2: Meninjau Proposal (3-5 hari)
    Dekan->>S: Buka detail proposal
    S->>Dekan: Tampilkan proposal lengkap:
    Note over S,Dekan: - Judul, ringkasan, tujuan
    Note over S,Dekan: - Metodologi / solusi
    Note over S,Dekan: - Anggaran & item biaya
    Note over S,Dekan: - Komposisi tim & kualifikasi
    Note over S,Dekan: - Jadwal kegiatan
    
    Dekan->>S: Evaluasi kelayakan & keselarasan
    
    %% Tahap 3: Keputusan
    Note over Dekan,S: Tahap 3: Membuat Keputusan
    alt Setujui
        Dekan->>S: Klik "Approve"
        S->>S: Status → APPROVED
        N->>D: Email: Proposal disetujui Dekan
        N->>Dekan: Konfirmasi: Approval berhasil
    else Perlu Perbaikan Tim
        Dekan->>S: Klik "Need Assignment"
        S->>S: Status → NEED_ASSIGNMENT
        N->>D: Email: Perbaiki komposisi tim
        N->>Dekan: Keputusan tercatat
    else Tolak (Jarang)
        Dekan->>S: Klik "Reject" + alasan
        S->>S: Status → REJECTED
        N->>D: Email: Proposal ditolak
    end
    
    %% Tahap 4: Selesai
    Note over Dekan,S: Tahap 4: Proposal Berlanjut ke Kepala LPPM
    Dekan->>S: Proposal diperbarui (ditinjau)
    S->>S: Dashboard update count
```

**Aksi Utama Dekan:**
- Menerima notifikasi proposal dari fakultas sendiri
- Meninjau proposal dalam 3-5 hari
- Memutuskan: Setujui → APPROVED | Perbaikan Tim → NEED_ASSIGNMENT | Tolak (jarang)
- Hanya melihat proposal dari fakultasnya

---

#### 3. KEPALA LPPM - Dua Tahap Persetujuan

```mermaid
sequenceDiagram
    participant KL as Kepala LPPM
    participant S as Sistem
    participant A as Admin LPPM
    participant D as Dosen
    participant R as Reviewer
    participant N as Notifikasi

    %% Tahap 1: Persetujuan Awal
    Note over KL,S: Tahap 1: Persetujuan Awal (APPROVED → UNDER_REVIEW)
    N->>KL: Proposal disetujui Dekan
    KL->>S: Login & review proposal
    KL->>S: Verifikasi: Admin, kewajaran anggaran, kelayakan
    
    alt Setujui untuk Review
        KL->>S: Klik "Approve for Review"
        S->>S: Status → UNDER_REVIEW
        S->>A: Notifikasi: Tugaskan reviewer
        N->>KL: Konfirmasi: Siap penugasan
    else Tolak (Jarang)
        KL->>S: Klik "Reject"
        S->>S: Status → REJECTED
        N->>D: Proposal ditolak
    end
    
    %% Tahap 2: Penugasan Reviewer (Admin LPPM)
    Note over A,R: Tahap 2: Admin LPPM Menugaskan Reviewer
    A->>S: Pilih reviewer sesuai keahlian
    S->>R: Notifikasi: Anda ditugaskan review
    A->>S: Monitor progres review (7-14 hari)
    
    %% Tahap 3: Review Selesai
    Note over R,S: Tahap 3: Reviewer Menyelesaikan Review
    loop Setiap reviewer
        R->>S: Submit review + rekomendasi
    end
    S->>S: Semua selesai? → Status REVIEWED
    N->>KL: Notifikasi: Semua review selesai
    
    %% Tahap 4: Keputusan Akhir
    Note over KL,S: Tahap 4: Keputusan Akhir (REVIEWED → COMPLETED/REVISION_NEEDED)
    KL->>S: Review ringkasan dari semua reviewer
    S->>KL: Tampilkan skor, rekomendasi, catatan
    KL->>S: Analisis & tentukan keputusan final
    
    alt Setujui (COMPLETED)
        KL->>S: Klik "Approve"
        S->>S: Status → COMPLETED
        N->>D: Proposal disetujui! Siap eksekusi
        N->>A: Proposal COMPLETED
    else Minta Revisi
        KL->>S: Klik "Request Revision" + catatan
        S->>S: Status → REVISION_NEEDED
        N->>D: Email: Revisi proposal + catatan reviewer
        D->>S: Revisi & resubmit → SUBMITTED (ulang alur)
    else Tolak
        KL->>S: Klik "Reject" + alasan
        S->>S: Status → REJECTED
        N->>D: Proposal ditolak final
    end
```

**Aksi Utama Kepala LPPM:**
- **Tahap 1 (Awal):** Menerima proposal APPROVED → verifikasi → UNDER_REVIEW
- **Tahap 2 (Monitor):** Monitoring penugasan reviewer (koordinasi dgn Admin LPPM)
- **Tahap 3 (Akhir):** Menerima proposal REVIEWED → review hasil → keputusan final
  - COMPLETED: Siap eksekusi
  - REVISION_NEEDED: Dosen revisi, resubmit
  - REJECTED: Terminal

---

#### 4. ADMIN LPPM - Koordinator Operasional

```mermaid
sequenceDiagram
    participant A as Admin LPPM
    participant S as Sistem
    participant DB as Database
    participant R as Reviewer
    participant KL as Kepala LPPM
    participant N as Notifikasi

    %% Tahap 1: Menerima Notifikasi
    Note over A,S: Tahap 1: Menerima Request Penugasan
    N->>A: Proposal UNDER_REVIEW - Tugaskan reviewer
    A->>S: Login & buka proposal
    S->>A: Tampilkan detail proposal
    
    %% Tahap 2: Memilih Reviewer
    Note over A,S: Tahap 2: Memilih Reviewer Tepat
    A->>S: Query reviewer tersedia
    S->>DB: Cari reviewer by expertise/focus area
    DB->>S: Kembalikan daftar reviewer
    A->>S: Evaluasi: Tidak ada conflict of interest?
    A->>S: Pilih 1-3 reviewer sesuai keahlian
    
    %% Tahap 3: Penugasan
    Note over A,R: Tahap 3: Menugaskan Reviewer
    loop Setiap reviewer
        A->>S: Tugaskan reviewer + deadline (7-14 hari)
        S->>DB: INSERT proposal_reviewer (status: pending)
        N->>R: Email: Anda ditugaskan review proposal X
        N->>R: Deadline: [date]
    end
    N->>A: Konfirmasi: Semua reviewer ditugaskan
    
    %% Tahap 4: Monitoring
    Note over A,S: Tahap 4: Monitoring Progres Review
    A->>S: Dashboard: Status review setiap reviewer
    S->>DB: Query proposal_reviewer WHERE status != 'completed'
    
    loop Setiap hari
        A->>S: Cek: Ada yang overdue?
        alt 3 Hari Sebelum Deadline
            N->>R: Pengingat: Review jatuh tempo 3 hari
        end
        alt 1 Hari Setelah Deadline
            N->>R: Overdue: Batas waktu terlewati
            N->>A: Alert: Reviewer X terlambat
        end
    end
    
    %% Tahap 5: Review Selesai
    Note over A,S: Tahap 5: Menunggu Semua Review Selesai
    loop Setiap reviewer
        R->>S: Submit review
        S->>DB: Update status = 'completed'
        N->>A: Reviewer X selesai review
    end
    
    alt Semua Selesai
        S->>S: Status proposal → REVIEWED
        N->>KL: Notifikasi: Semua reviewer selesai
        N->>A: Ringkasan: Review summary
    end
```

**Aksi Utama Admin LPPM:**
- Menerima notifikasi penugasan reviewer
- Memilih reviewer tepat (expertise, no conflict)
- Menugaskan reviewer (1-3 orang) dengan deadline
- Monitoring progres review (reminder, overdue alerts)
- Dokumentasi hasil penugasan

---

#### 5. REVIEWER - Evaluator Ahli

```mermaid
sequenceDiagram
    participant R as Reviewer
    participant S as Sistem
    participant DB as Database
    participant A as Admin LPPM
    participant N as Notifikasi

    %% Tahap 1: Notifikasi Penugasan
    Note over R,S: Tahap 1: Menerima Penugasan
    N->>R: Email: Anda ditugaskan review proposal X
    N->>R: Deadline: [date] (7-14 hari)
    R->>S: Login & buka notifikasi
    
    %% Tahap 2: Akses Proposal
    Note over R,S: Tahap 2: Mengakses Proposal
    R->>S: Buka detail proposal
    S->>DB: Muat proposal lengkap
    DB->>S: Kembalikan proposal + detailable (research/PKM)
    S->>R: Tampilkan:
    Note over S,R: - Judul, ringkasan, tujuan
    Note over S,R: - Metodologi / solusi
    Note over S,R: - Luaran rencana
    Note over S,R: - Anggaran & kewajaran
    Note over S,R: - Jadwal & feasibility
    
    %% Tahap 3: Review (Active)
    Note over R,S: Tahap 3: Melakukan Review (7-14 hari)
    R->>S: Update status → "reviewing"
    R->>S: Baca & analisis proposal detail
    R->>S: Evaluasi:
    Note over R,S: - Keaslian ide
    Note over R,S: - Kelayakan metodologi
    Note over R,S: - Kewajaran anggaran
    Note over R,S: - Feasibility timeline
    Note over R,S: - Kualitas luaran
    
    %% Tahap 4: Submit Review
    Note over R,S: Tahap 4: Mensubmit Review
    R->>S: Buka form review
    R->>S: Isi catatan review
    R->>S: Pilih rekomendasi:
    Note over R,S: - APPROVED (recommended)
    Note over R,S: - REVISION_NEEDED (with comments)
    Note over R,S: - REJECTED (not recommended)
    R->>S: Klik "Submit Review"
    S->>DB: INSERT/UPDATE review
    S->>DB: Update proposal_reviewer.status = 'completed'
    
    %% Tahap 5: Konfirmasi & Selesai
    Note over R,S: Tahap 5: Review Tercatat
    N->>R: Konfirmasi: Review berhasil disubmit
    N->>A: Alert: Reviewer X selesai
    
    Note over R,S: Catatan: Reviewer TIDAK BISA edit review setelah submit
    Note over R,S: (untuk integritas data)
```

**Aksi Utama Reviewer:**
- Menerima notifikasi penugasan + deadline
- Mengakses dan membaca proposal
- Melakukan evaluasi detail (metodologi, anggaran, feasibility)
- Mengisi review + memilih rekomendasi
- Submit review (final, tidak bisa diubah)
- Menunggu notifikasi keputusan akhir

---

#### Blokir Kritis dalam Workflow

| Kondisi                    | Impact                | Solusi                | Role Terkait        |
| -------------------------- | --------------------- | --------------------- | ------------------- |
| Ada anggota tim menolak    | Tidak bisa submit     | Hapus/ganti anggota   | **Dosen**           |
| Ada anggota tim pending    | Tidak bisa submit     | Tunggu respons semua  | **Dosen, Anggota**  |
| Dekan belum approve        | Terhenti di SUBMITTED | Review dalam 3-5 hari | **Dekan**           |
| Ada reviewer belum selesai | Status UNDER_REVIEW   | Selesaikan review     | **Reviewer, Admin** |
| Kepala LPPM minta revisi   | Loop ke SUBMITTED     | Revisi & resubmit     | **Dosen**           |

---

## Alur Dosen

### Alur 1: Buat & Submit Proposal

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as Sistem
    participant DB as Basis Data
    participant T as Anggota Tim
    participant N as Layanan Notifikasi
    participant Dekan
    participant Admin

    %% Fase Pembuatan
    D->>S: Buka halaman buat proposal
    S->>DB: Muat data master (skema, focus area, tema, kata kunci, dll.)
    DB-->>S: Kembalikan data referensi
    S-->>D: Tampilkan formulir proposal
    
    D->>S: Isi informasi dasar (judul, ringkasan, durasi)
    D->>S: Pilih taksonomi (focus area, tema, topik)
    D->>S: Pilih skema, prioritas nasional, rumpun ilmu
    
    alt Proposal Penelitian
        D->>S: Isi metodologi, state-of-the-art, roadmap_data (JSON)
        D->>S: Set target TKT, latar belakang
        D->>S: Pilih kelompok riset makro
    else Proposal PKM
        D->>S: Isi ringkasan isu mitra, solusi yang ditawarkan
        D->>S: Pilih/tambah organisasi mitra
    end
    
    D->>S: Tambah item anggaran (grup, komponen, volume, unit_price)
    S->>S: Hitung total_price per item
    D->>S: Tambah jadwal kegiatan (tahun, start_month, end_month)
    D->>S: Tambah luaran direncanakan (tipe, kategori, target_status)
    D->>S: Tambah tahapan riset (process_name, outputs, indikator)
    D->>S: Tambah kata kunci
    
    D->>S: Simpan sebagai DRAFT
    S->>DB: Simpan proposal (status: DRAFT)
    DB-->>S: Kembalikan proposal_id
    S-->>D: Konfirmasi: proposal disimpan
    
    %% Fase Undangan Tim
    D->>S: Undang anggota tim (email, peran: ketua/anggota, tugas)
    S->>DB: Buat record proposal_user (status: pending)
    S->>N: Trigger TeamInvitationSent
    N->>T: Kirim email + notifikasi aplikasi
    
    %% Persetujuan Tim
    loop Untuk setiap anggota
        T->>S: Login dan lihat undangan
        T->>S: Terima atau Tolak
        alt Terima
            S->>DB: Update proposal_user.status = 'accepted'
            S->>N: Trigger TeamInvitationAccepted
            N->>D: Beri tahu pengusul (diterima)
        else Tolak
            S->>DB: Update proposal_user.status = 'rejected'
            S->>DB: Update proposal.status = 'need_assignment'
            S->>N: Trigger TeamInvitationRejected
            N->>D: Beri tahu pengusul (ditolak)
        end
    end
    
    %% Fase Submit
    D->>S: Cek status tim
    S->>S: Validasi semua anggota sudah menerima
    
    alt Semua Menerima
        D->>S: Klik "Submit Proposal"
        S->>S: Validasi kelengkapan
        S->>DB: Update proposal.status = 'submitted'
        S->>N: Trigger ProposalSubmitted
        N->>Dekan: Email + Notifikasi DB
        N->>Admin: Email + Notifikasi DB
        N->>T: Email + Notifikasi DB (anggota tim)
        S-->>D: Sukses + redirect ke detail proposal
    else Ada yang Ditolak/Pending
        S-->>D: Error: Tidak dapat submit (anggota pending/ditolak)
    end
```

**Poin Penting:**
- DRAFT bisa disimpan berkali-kali sebelum submit
- Persetujuan anggota tim WAJIB sebelum submit
- Status menjadi NEED_ASSIGNMENT jika ada anggota menolak
- Submit memicu notifikasi ke Dekan, Admin LPPM, dan Tim

---

### Alur 2: Tangani Penolakan Tim & Resubmit

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as Sistem
    participant DB as Basis Data
    participant T1 as Anggota Menolak
    participant T2 as Anggota Baru
    participant N as Notifikasi

    %% Skenario Penolakan
    T1->>S: Tolak undangan
    S->>DB: Update proposal_user.status = 'rejected'
    S->>DB: Update proposal.status = 'need_assignment'
    S->>N: Trigger notifikasi penolakan
    N->>D: Beri tahu Dosen (penolakan tim)
    
    %% Perbaiki Komposisi Tim
    D->>S: Lihat proposal (status: NEED_ASSIGNMENT)
    S-->>D: Tampilkan anggota yang menolak + tidak dapat submit
    
    D->>S: Hapus anggota yang menolak
    S->>DB: Hapus record proposal_user (yang menolak)
    
    D->>S: Undang anggota baru
    S->>DB: Buat proposal_user baru (status: pending)
    S->>N: Kirim undangan
    N->>T2: Email + Notifikasi DB
    
    T2->>S: Terima undangan
    S->>DB: Update proposal_user.status = 'accepted'
    S->>N: Trigger notifikasi penerimaan
    N->>D: Beri tahu Dosen (diterima)
    
    %% Resubmit
    D->>S: Cek semua anggota sudah menerima
    S->>S: Validasi status tim
    D->>S: Submit ulang proposal
    S->>DB: Update proposal.status = 'submitted'
    S->>N: Trigger ProposalSubmitted
    N->>Dekan: Notifikasi (resubmitted)
```

**Poin Penting:**
- Anggota yang menolak harus diganti sebelum submit ulang
- Status otomatis menjadi NEED_ASSIGNMENT saat ada penolakan
- Dosen dapat menghapus dan mengundang anggota baru
- Resubmit mengikuti alur submit normal

---

### Alur 3: Revisi & Submit Ulang

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as Sistem
    participant DB as Basis Data
    participant KL as Kepala LPPM
    participant N as Notifikasi

    %% Terima Permintaan Revisi
    KL->>S: Tandai proposal sebagai REVISION_NEEDED
    S->>DB: Update proposal.status = 'revision_needed'
    S->>N: Trigger FinalDecisionMade
    N->>D: Email + Notifikasi (perlu revisi)
    
    %% Fase Revisi
    D->>S: Login dan buka notifikasi
    D->>S: Buka detail proposal
    S->>DB: Muat proposal + catatan reviewer
    S-->>D: Tampilkan proposal beserta catatan
    
    D->>S: Edit bagian-bagian proposal
    D->>S: Perbarui metodologi/solusi (sesuai masukan)
    D->>S: Perbarui anggaran (bila perlu)
    D->>S: Perbarui luaran/jadwal (bila perlu)
    D->>S: Simpan perubahan
    S->>DB: Perbarui data proposal
    
    %% Submit Ulang
    D->>S: Klik "Submit Proposal Revisi"
    S->>S: Validasi kelengkapan
    S->>DB: Update proposal.status = 'submitted'
    S->>N: Trigger ProposalSubmitted
    N->>Dekan: Notifikasi (resubmitted)
    N->>Admin: Notifikasi (resubmitted)
    S-->>D: Sukses
    
    Note over S,DB: Proposal masuk kembali ke alur persetujuan<br/>dari status SUBMITTED
```

**Poin Penting:**
- Proposal revisi kembali ke SUBMITTED
- Harus melalui seluruh alur persetujuan lagi (Dekan → Kepala LPPM → Reviewer)
- Dosen dapat melihat semua umpan balik reviewer sebelum revisi
- Tidak ada batas jumlah siklus revisi (dapat ditetapkan sebagai kebijakan)

---

### Alur 4: Submit Laporan Kemajuan

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as Sistem
    participant DB as Basis Data
    participant A as Admin LPPM
    participant N as Notifikasi

    %% Pembuatan Laporan
    D->>S: Buka proposal yang disetujui
    D->>S: Klik "Submit Laporan Kemajuan"
    S-->>D: Tampilkan formulir laporan
    
    D->>S: Pilih periode (semester_1/semester_2/tahunan)
    D->>S: Isi tahun pelaporan
    D->>S: Isi ringkasan pembaruan
    
    %% Luaran Wajib
    D->>S: Tambah luaran wajib (tipe, deskripsi, status)
    D->>S: Unggah bukti
    S->>DB: Simpan mandatory_outputs
    
    %% Luaran Tambahan
    D->>S: Tambah luaran tambahan (opsional)
    D->>S: Unggah bukti
    S->>DB: Simpan additional_outputs
    
    %% Kata Kunci
    D->>S: Pilih/tambah kata kunci
    S->>DB: Buat progress_report_keyword
    
    %% Submit
    D->>S: Simpan sebagai DRAFT atau Submit
    
    alt Submit
        S->>DB: Buat progress_report (status: submitted)
        S->>DB: Set submitted_by, submitted_at
        S->>N: Beri tahu Admin LPPM
        N->>A: Email + Notifikasi DB (laporan masuk)
        S-->>D: Sukses: Laporan disubmit
    else Simpan Draft
        S->>DB: Buat progress_report (status: draft)
        S-->>D: Laporan tersimpan sebagai draft
    end
```

**Poin Penting:**
- Laporan kemajuan dapat semester 1, semester 2, atau tahunan
- Luaran wajib harus diisi (berdasarkan luaran rencana)
- Luaran tambahan untuk pencapaian ekstra
- Laporan bisa disimpan sebagai draft dan disubmit kemudian
- Admin LPPM menerima notifikasi untuk review/persetujuan

---

## Alur Dekan

### Alur 5: Review & Setujui Proposal

```mermaid
sequenceDiagram
    participant N as Notifikasi
    participant De as Dekan
    participant S as Sistem
    participant DB as Basis Data
    participant KL as Kepala LPPM
    participant D as Dosen (Pengusul)
    participant T as Anggota Tim

    %% Terima Notifikasi
    N->>De: Notifikasi: Proposal disubmit
    De->>S: Login dan buka notifikasi
    De->>S: Buka detail proposal
    
    %% Fase Review
    S->>DB: Muat proposal + detailable (research/PKM)
    S->>DB: Muat tim + anggaran + jadwal
    DB-->>S: Kembalikan data lengkap
    S-->>De: Tampilkan proposal untuk review
    
    De->>S: Tinjau judul, ringkasan, tujuan
    De->>S: Tinjau item anggaran + total
    De->>S: Tinjau komposisi tim + tugas
    De->>S: Tinjau metodologi/solusi
    De->>S: Cek keselarasan dengan fakultas
    
    %% Keputusan
    alt Setujui
        De->>S: Klik "Approve Proposal"
        S->>S: Validasi status = 'submitted'
        S->>DB: Update proposal.status = 'approved'
        S->>N: Trigger DekanApprovalDecision (approved)
        N->>KL: Email + Notifikasi DB (menunggu persetujuan awal)
        N->>D: Email + Notifikasi DB (disetujui Dekan)
        N->>T: Email + Notifikasi DB (notifikasi tim)
        S-->>De: Sukses: Proposal disetujui
    else Perlu Perbaikan Tim
        De->>S: Klik "Need Assignment"
        S->>DB: Update proposal.status = 'need_assignment'
        S->>N: Trigger DekanApprovalDecision (need_assignment)
        N->>D: Email + Notifikasi DB (perbaiki komposisi tim)
        N->>T: Email + Notifikasi DB (anggota pending)
        S-->>De: Proposal dikembalikan ke pengusul
    end
```

**Poin Penting:**
- Dekan adalah pemberi persetujuan PERTAMA (setelah submit)
- Dapat menyetujui atau meminta perbaikan tim
- Tidak dapat menolak langsung (penolakan oleh Kepala LPPM)
- Transisi: SUBMITTED → APPROVED atau NEED_ASSIGNMENT
- Notifikasi ke Kepala LPPM, Pengusul, dan Tim

---

### Alur 6: Penyaringan Tingkat Fakultas

```mermaid
sequenceDiagram
    participant De as Dekan
    participant S as Sistem
    participant DB as Basis Data

    De->>S: Login (role: dekan)
    S->>S: Cek role aktif + fakultas terkait
    
    De->>S: Buka daftar proposal
    S->>DB: Query proposal WHERE submitter.faculty = dekan.faculty
    S->>DB: Filter by status (submitted, approved, dll.)
    DB-->>S: Kembalikan proposal lingkup fakultas
    S-->>De: Tampilkan proposal hanya dari fakultas sendiri
    
    De->>S: Lihat statistik dashboard
    S->>DB: Agregasi proposal fakultas berdasar status
    DB-->>S: Kembalikan statistik
    S-->>De: Tampilkan analitik fakultas
```

**Poin Penting:**
- Dekan hanya dapat melihat proposal dari fakultasnya
- Sistem otomatis memfilter berdasarkan afiliasi fakultas
- Dashboard menampilkan statistik lingkup fakultas

---

## Alur Kepala LPPM

### Alur 7: Persetujuan Awal (APPROVED → UNDER_REVIEW)

```mermaid
sequenceDiagram
    participant N as Notifikasi
    participant KL as Kepala LPPM
    participant S as Sistem
    participant DB as Basis Data
    participant A as Admin LPPM

    %% Terima Notifikasi
    N->>KL: Notifikasi: Proposal disetujui Dekan
    KL->>S: Login dan buka notifikasi
    KL->>S: Buka detail proposal
    
    %% Fase Review
    S->>DB: Muat proposal + detailable
    S->>DB: Muat ringkasan anggaran + tim
    DB-->>S: Kembalikan data
    S-->>KL: Tampilkan proposal
    
    KL->>S: Tinjau keselarasan dengan strategi LPPM
    KL->>S: Cek kewajaran anggaran
    KL->>S: Verifikasi persetujuan Dekan
    
    %% Keputusan Persetujuan Awal
    alt Setujui untuk Review
        KL->>S: Klik "Approve for Review"
        S->>S: Validasi status = 'approved'
        S->>S: Validasi transisi ke 'under_review'
        S->>DB: Update proposal.status = 'under_review'
        S->>N: Trigger ReviewerAssignment
        N->>A: Email + Notifikasi DB (tugaskan reviewer)
        S-->>KL: Sukses: Siap penugasan reviewer
    else Tolak
        KL->>S: Klik "Reject"
        S->>DB: Update proposal.status = 'rejected'
        S->>N: Trigger notifikasi penolakan
        N->>Dosen: Email + Notifikasi DB (ditolak)
        S-->>KL: Proposal ditolak
    end
```

**Poin Penting:**
- Kepala LPPM memberi PERSETUJUAN AWAL setelah Dekan
- Transisi: APPROVED → UNDER_REVIEW
- Memicu Admin LPPM menugaskan reviewer
- Kepala LPPM dapat menolak di tahap ini (jarang)
- Tidak menugaskan reviewer langsung (tugas Admin LPPM)

---

### Alur 8: Keputusan Akhir (REVIEWED → COMPLETED/REVISION_NEEDED)

```mermaid
sequenceDiagram
    participant N as Notifikasi
    participant KL as Kepala LPPM
    participant S as Sistem
    participant DB as Basis Data
    participant D as Dosen
    participant T as Tim
    participant De as Dekan
    participant A as Admin LPPM

    %% Terima Notifikasi
    N->>KL: Notifikasi: Semua reviewer selesai
    KL->>S: Login dan buka notifikasi
    KL->>S: Buka detail proposal
    
    %% Ringkasan Review
    S->>DB: Muat proposal + semua rekomendasi reviewer
    DB-->>S: Kembalikan proposal + review
    S->>S: Buat ringkasan review (approved/revision/rejected)
    S-->>KL: Tampilkan proposal + ringkasan review
    
    KL->>S: Baca catatan tiap reviewer
    KL->>S: Tinjau rekomendasi
    KL->>S: Analisis konsistensi masukan
    
    %% Keputusan Akhir
    alt Setujui (Complete)
        KL->>S: Klik "Approve Proposal"
        KL->>S: Opsional: Isi catatan akhir
        S->>S: Validasi status = 'reviewed'
        S->>DB: Update proposal.status = 'completed'
        S->>N: Trigger FinalDecisionMade (completed)
        N->>D: Email + Notifikasi DB (disetujui - siap eksekusi)
        N->>T: Email + Notifikasi DB (notifikasi tim)
        N->>De: Email + Notifikasi DB (notifikasi Dekan)
        N->>A: Email + Notifikasi DB (notifikasi Admin)
        S-->>KL: Sukses: Proposal disetujui dan selesai
    else Perlu Revisi
        KL->>S: Klik "Request Revision"
        KL->>S: Isi catatan revisi
        S->>DB: Update proposal.status = 'revision_needed'
        S->>N: Trigger FinalDecisionMade (revision_needed)
        N->>D: Email + Notifikasi DB (revisi proposal)
        N->>T: Email + Notifikasi DB
        S-->>KL: Proposal dikembalikan untuk revisi
    else Tolak
        KL->>S: Klik "Reject Proposal"
        KL->>S: Isi alasan penolakan
        S->>DB: Update proposal.status = 'rejected'
        S->>N: Trigger FinalDecisionMade (rejected)
        N->>D: Email + Notifikasi DB (ditolak)
        N->>T: Email + Notifikasi DB
        S-->>KL: Proposal ditolak
    end
```

**Poin Penting:**
- Keputusan akhir oleh Kepala LPPM SETELAH semua review selesai
- Hanya dapat dilakukan saat status = REVIEWED
- Opsi: Setujui (COMPLETED), Minta Revisi, atau Tolak
- Proposal disetujui siap dieksekusi (kegiatan penelitian/PKM)
- Revisi mengembalikan ke Dosen, lalu ulang alur

---

## Alur Admin LPPM

### Alur 9: Menugaskan Reviewer

```mermaid
sequenceDiagram
    participant N as Notifikasi
    participant A as Admin LPPM
    participant S as Sistem
    participant DB as Basis Data
    participant R as Reviewer

    %% Terima Notifikasi
    N->>A: Notifikasi: Siap untuk review (disetujui Kepala LPPM)
    A->>S: Login dan buka notifikasi
    A->>S: Buka detail proposal
    
    %% Muat Reviewer Tersedia
    S->>DB: Muat detail proposal (jenis, focus area, tema)
    S->>DB: Query users WHERE role = 'reviewer'
    DB-->>S: Kembalikan reviewer tersedia
    S-->>A: Tampilkan proposal + reviewer tersedia
    
    %% Pemilihan Reviewer
    A->>S: Cek jenis proposal (penelitian vs PKM)
    A->>S: Cek focus area/keahlian diperlukan
    A->>S: Pilih reviewer dari daftar
    
    loop Untuk tiap reviewer terpilih
        A->>S: Tugaskan reviewer
        S->>S: Validasi tidak duplikat
        S->>DB: INSERT proposal_reviewer (status: pending)
        S->>N: Trigger ReviewerAssigned
        N->>R: Email + Notifikasi DB (penugasan baru)
    end
    
    S-->>A: Sukses: Reviewer ditugaskan
    
    %% Pantau Progres Review
    A->>S: Monitor penyelesaian review
    S->>DB: Query proposal_reviewer WHERE status != 'completed'
    DB-->>S: Kembalikan review pending
    S-->>A: Tampilkan dashboard progres review
```

**Poin Penting:**
- Admin LPPM menugaskan reviewer SETELAH persetujuan awal Kepala LPPM
- Dapat menugaskan beberapa reviewer untuk satu proposal
- Pemilihan reviewer berdasarkan keahlian (focus area, tipe riset)
- Sistem mencegah penugasan duplikat (unique constraint)
- Admin dapat memantau progres review
- Dapat melepas/menugaskan ulang sebelum review selesai

---

## Alur Reviewer

### Alur 11: Review Proposal

```mermaid
sequenceDiagram
    participant N as Notifikasi
    participant R as Reviewer
    participant S as Sistem
    participant DB as Basis Data
    participant KL as Kepala LPPM
    participant A as Admin LPPM

    %% Terima Penugasan
    N->>R: Notifikasi: Anda ditugaskan mereview proposal
    R->>S: Login dan buka notifikasi
    R->>S: Buka proposal yang ditugaskan
    
    %% Muat Proposal
    S->>DB: Muat proposal + detailable (research/PKM)
    S->>DB: Muat tim, anggaran, jadwal, luaran
    DB-->>S: Kembalikan proposal lengkap
    S-->>R: Tampilkan proposal
    
    %% Fase Review
    R->>S: Update proposal_reviewer.status = 'reviewing'
    S->>DB: Update status
    
    R->>S: Baca judul, ringkasan, tujuan
    R->>S: Tinjau metodologi/solusi
    R->>S: Evaluasi kewajaran anggaran
    R->>S: Cek kelayakan luaran
    R->>S: Tinjau kualifikasi tim
    
    %% Submit Review
    R->>S: Buka formulir review
    S-->>R: Tampilkan form (catatan, rekomendasi)
    
    R->>S: Isi catatan review
    R->>S: Pilih rekomendasi (approved/revision_needed/rejected)
    R->>S: Klik "Submit Review"
    
    S->>S: Validasi kelengkapan review
    S->>DB: UPDATE proposal_reviewer SET status='completed', review_notes, recommendation
    
    %% Cek Semua Review
    S->>DB: COUNT(*) WHERE proposal_id AND status='completed'
    S->>DB: COUNT(*) WHERE proposal_id (total reviewer)
    
    alt Semua Review Selesai
        S->>DB: Update proposal.status = 'reviewed'
        S->>N: Trigger ReviewCompleted
        N->>KL: Email + Notifikasi DB (siap keputusan akhir)
        N->>A: Email + Notifikasi DB (semua review selesai)
        S-->>R: Sukses: Review disubmit (semua selesai)
    else Ada yang Pending
        S->>N: Trigger ReviewCompleted (reviewer ini saja)
        N->>A: Notifikasi DB (satu review selesai)
        S-->>R: Sukses: Review disubmit (menunggu lainnya)
    end
```

**Poin Penting:**
- Reviewer hanya dapat melihat proposal yang ditugaskan
- Status review: 'pending' → 'reviewing' → 'completed'
- Catatan review berisi umpan balik untuk Dosen & Kepala LPPM
- Rekomendasi wajib (approved/revision_needed/rejected)
- Status proposal menjadi REVIEWED hanya ketika SEMUA reviewer selesai
- Kepala LPPM diberi tahu saat semua review selesai

---

### Alur 12: Sistem Pengingat Review

```mermaid
sequenceDiagram
    participant Cron as Penjadwal
    participant S as Sistem
    participant DB as Basis Data
    participant N as Notifikasi
    participant R as Reviewer
    participant A as Admin LPPM

    %% Pengecekan Terjadwal (Harian)
    Cron->>S: Jalankan cek pengingat review (harian 08.00)
    S->>DB: Query proposal_reviewer WHERE status != 'completed'
    DB-->>S: Kembalikan review pending
    
    loop Untuk setiap review pending
        S->>S: Hitung hari sejak penugasan (created_at)
        
        alt 3 Hari Sebelum Deadline
            S->>N: Trigger ReviewReminder
            N->>R: Email: "Pengingat: Review jatuh tempo 3 hari lagi"
        else 1 Hari Setelah Deadline
            S->>N: Trigger ReviewOverdue
            N->>R: Email: "Terlambat: Batas waktu review terlewati"
            N->>A: Email: "Reviewer X terlambat menyelesaikan review"
        end
    end
```

**Poin Penting:**
- Pengingat otomatis (pekerjaan terjadwal)
- Pengingat 3 hari sebelum deadline
- Notifikasi overdue 1 hari setelah deadline
- Admin LPPM diberi tahu untuk tindak lanjut
- Reviewer dapat meminta perpanjangan ke Admin

---

## Ringkasan Eksekutif

### Rantai Persetujuan Lengkap

**Urutan yang Benar:**

```
1. DRAFT → Dosen membuat proposal
2. Undangan Tim → Semua harus MENERIMA sebelum submit
3. SUBMITTED → Dosen submit (jika semua menerima)
4. APPROVED → Dekan menyetujui (persetujuan pertama)
5. UNDER_REVIEW → Persetujuan awal Kepala LPPM (kedua)
6. Penugasan Reviewer → Admin LPPM menugaskan reviewer
7. Review → Reviewer menilai dan merekomendasikan
8. REVIEWED → Semua reviewer selesai (otomatis)
9. COMPLETED/REVISION_NEEDED → Keputusan akhir Kepala LPPM (ketiga)
```

### Aktor & Tanggung Jawab Utama

| Aktor           | Tanggung Jawab Utama         | Aksi Kritis                                                                                         |
| --------------- | ---------------------------- | --------------------------------------------------------------------------------------------------- |
| **Dosen**       | Pembuatan & submit proposal  | Buat, undang tim, submit, revisi, laporan kemajuan                                                  |
| **Anggota Tim** | Penerimaan kolaborasi        | Terima/tolak undangan                                                                               |
| **Dekan**       | Persetujuan tingkat fakultas | Setujui proposal (SUBMITTED → APPROVED)                                                             |
| **Kepala LPPM** | Pengawasan strategis         | Persetujuan awal (APPROVED → UNDER_REVIEW) + keputusan akhir (REVIEWED → COMPLETED/REVISION_NEEDED) |
| **Admin LPPM**  | Koordinasi operasional       | Tugaskan reviewer, kelola master data, kelola pengguna                                              |
| **Reviewer**    | Evaluasi ahli                | Review proposal, beri rekomendasi                                                                   |

### Pemicu Notifikasi

| Kejadian                     | Penerima                    | Kanal      |
| ---------------------------- | --------------------------- | ---------- |
| Proposal Disubmit            | Dekan, Admin LPPM, Tim      | Email + DB |
| Undangan Tim                 | Anggota yang diundang       | Email + DB |
| Penerimaan Tim               | Pengusul                    | DB         |
| Penolakan Tim                | Pengusul                    | DB         |
| Persetujuan Dekan            | Kepala LPPM, Pengusul, Tim  | Email + DB |
| Persetujuan Awal Kepala LPPM | Admin LPPM                  | Email + DB |
| Reviewer Ditugaskan          | Reviewer                    | Email + DB |
| Review Selesai (satu)        | Admin LPPM                  | DB         |
| Semua Review Selesai         | Kepala LPPM, Admin LPPM     | Email + DB |
| Keputusan Akhir              | Pengusul, Tim, Dekan, Admin | Email + DB |
| Pengingat Review             | Reviewer                    | Email      |
| Review Overdue               | Reviewer, Admin LPPM        | Email      |

### Jalur Alternatif

**Jalur Penolakan:**
```
SUBMITTED → (Dekan tolak) → REJECTED (terminal)
APPROVED → (Kepala LPPM tolak) → REJECTED (terminal)
REVIEWED → (Kepala LPPM tolak) → REJECTED (terminal)
```

**Jalur Revisi:**
```
REVIEWED → (Kepala LPPM minta revisi) → REVISION_NEEDED
REVISION_NEEDED → (Dosen revisi) → SUBMITTED (ulang alur)
```

**Jalur Perbaikan Tim:**
```
SUBMITTED → (Dekan minta perbaikan tim) → NEED_ASSIGNMENT
NEED_ASSIGNMENT → (Dosen perbaiki tim) → SUBMITTED (lanjut alur)
Status apa pun → (Anggota tim menolak) → NEED_ASSIGNMENT
```

### Waktu Proses Rata-rata

| Tahap                         | Durasi         | Risiko Bottleneck                  |
| ----------------------------- | -------------- | ---------------------------------- |
| Pembuatan Proposal            | 3-7 hari       | Beban Dosen                        |
| Persetujuan Tim               | 1-2 minggu     | Respons anggota                    |
| Review Dekan                  | 3-5 hari       | Beban fakultas                     |
| Persetujuan Awal Kepala       | 2-3 hari       | Waktu telaah strategis             |
| Penugasan Reviewer            | 1-2 hari       | Koordinasi Admin                   |
| Evaluasi Reviewer             | 7-14 hari      | **TINGGI** - Ketersediaan reviewer |
| Keputusan Akhir Kepala        | 2-3 hari       | Analisis hasil review              |
| **TOTAL (tanpa revisi)**      | **2-3 minggu** | -                                  |
| **Dengan satu siklus revisi** | **4-6 minggu** | -                                  |

### Otomasi Sistem

**Aksi Otomatis:**
1. Transisi ke NEED_ASSIGNMENT saat ada penolakan tim
2. Transisi ke REVIEWED saat semua reviewer selesai
3. Pengiriman notifikasi di setiap tahap alur
4. Perhitungan total anggaran (volume × unit_price)
5. Pengingat tenggat review (3 hari sebelum)
6. Pemberitahuan review terlambat (1 hari sesudah)
7. Laporan ringkas harian/mingguan ke Admin/Kepala

**Aksi Manual:**
1. Pembuatan dan pengisian proposal
2. Keputusan terima/tolak undangan tim
3. Keputusan persetujuan Dekan
4. Persetujuan awal Kepala LPPM
5. Penugasan reviewer oleh Admin
6. Evaluasi & rekomendasi oleh Reviewer
7. Keputusan akhir Kepala LPPM
8. Pengajuan laporan kemajuan

---

**Akhir Dokumen**
