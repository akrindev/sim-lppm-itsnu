# PRD – Dokumen Kebutuhan Produk (Bahasa Indonesia)
## SIM LPPM ITSNU – Sistem Manajemen Penelitian & Pengabdian

Versi Dokumen: 2.0  
Terakhir Diperbarui: 2025-11-09  
Pemilik Produk: LPPM ITSNU Pekalongan  
Tim Pengembang: Tim TI Internal

---

## 1. Ringkasan Eksekutif

SIM LPPM ITSNU adalah aplikasi web untuk mengelola seluruh siklus hibah penelitian (Penelitian) dan pengabdian kepada masyarakat (PKM) di ITSNU Pekalongan. Sistem ini menggantikan proses manual kertas menjadi alur kerja digital yang tertib, transparan, dan akuntabel. Terinspirasi BIMA Kemendikbud, namun disesuaikan kebutuhan internal ITSNU.

Nilai Utama:
- Efisiensi: waktu proses proposal berkurang dari minggu ke hari.
- Transparansi: status real-time untuk semua pihak.
- Akuntabilitas: rekam jejak lengkap (audit trail) dan notifikasi.
- Aksesibilitas: akses web 24/7 untuk sivitas.

---

## 2. Masalah yang Ingin Diselesaikan

Tantangan saat ini:
1) Manajemen proposal manual → rawan lambat dan dokumen terselip.  
2) Proses review tidak baku → sulit pantau progres penyelesaian.  
3) Monitoring progres lemah → sulit deteksi dini hambatan proyek.  
4) Akses data rendah → pelaporan akreditasi memakan waktu.  
5) Kolaborasi tim tersebar → undangan/koordinasi di luar sistem.

---

## 3. Tujuan & Sasaran

Tujuan utama:
- Digitalisasi penuh siklus hibah (pengajuan → laporan akhir).
- Persetujuan multi-peran yang jelas dan terstruktur.
- Transparansi + akuntabilitas melalui status & notifikasi.
- Data terpusat untuk analitik dan keputusan.

Tujuan pendukung:
- UX sederhana untuk semua peran (mobile-friendly).
- Kurangi beban administratif lewat otomatisasi & master data.
- Dukung kolaborasi tim & mitra (khusus PKM).
- Jaga mutu data lewat validasi & taksonomi berjenjang.

---

## 4. Fitur Inti

### 4.1 Modul Penelitian (Research)
- Form multi-langkah dengan validasi.  
- Metodologi, state-of-the-art, rencana roadmap (JSON), target TKT.  
- Keluaran riset: publikasi, paten, prototipe, dsb.

### 4.2 Modul PKM (Pengabdian)
- Identifikasi masalah komunitas & desain solusi.  
- Manajemen mitra & rencana dampak sosial.  
- Keluaran PKM: pelatihan, adopsi, layanan, media edukasi.

### 4.3 Sistem Persetujuan Bertahap
Alur ringkas:  
Tim terima undangan → SUBMITTED → Dekan (APPROVED) → Kepala LPPM (UNDER_REVIEW) → Admin tugaskan reviewer → semua reviewer selesai (REVIEWED) → Kepala LPPM putuskan (COMPLETED/REVISION_NEEDED/REJECTED).

Fitur: validasi transisi status, izin berbasis peran, notifikasi tiap tahap, jejak audit.

### 4.4 Review & Revisi
- Reviewer akses detail lengkap + form penilaian terstruktur.  
- Rekomendasi: approved / revision_needed / rejected.  
- Revisi mengembalikan proposal ke SUBMITTED dan ulang alur.

### 4.5 Tim & Anggaran
- Undang anggota (ketua/anggota), lacak status (pending/accepted/rejected).  
- Anggaran 2 level (Grup → Komponen), perhitungan otomatis volume × harga.

### 4.6 Laporan Kemajuan
- Jenis: semester_1, semester_2, tahunan.  
- Rekam luaran wajib & tambahan + bukti pendukung.

### 4.7 Notifikasi
- Kanal: in-app (database) + email (antrian/queue).  
- Pemicu: submit, penugasan, pengingat tenggat, keputusan, dll.

### 4.8 Akses Berbasis Peran (RBAC)
Peran sistem (9): superadmin, admin lppm, kepala lppm, dekan (umum/saintek/dekabita), dosen, reviewer, rektor.  
Izin akses mengikuti skop data (fakultas, milik sendiri, penugasan).

### 4.9 Data Master
- Taksonomi 3 level (Focus Area → Theme → Topic).  
- Rumpun Ilmu 3 level.  
- Skema penelitian, PRN (Prioritas Riset Nasional), kata kunci, mitra, grup/komponen anggaran, fakultas/prodi, institusi.

### 4.10 Pelaporan & Analitik
- Dashboard per peran, daftar proposal berfilter, ringkasan review, laporan progres, ringkasan anggaran.

---

## 5. User Stories (Ringkas)

### Dosen
- Buat, undang tim, submit proposal saat semua menerima.  
- Revisi bila diminta, ajukan laporan kemajuan.

### Reviewer
- Dapat penugasan, akses proposal, isi catatan & rekomendasi, submit review.

### Admin LPPM
- Tugaskan reviewer setelah persetujuan awal Kepala LPPM.  
- Kelola master data & pengguna, pantau progres review, buat laporan.

### Kepala LPPM
- Setujui awal (APPROVED → UNDER_REVIEW).  
- Ambil keputusan akhir setelah semua review selesai.

### Dekan
- Tinjau dan setujui proposal fakultasnya (SUBMITTED → APPROVED), atau minta perbaikan tim.

### Rektor
- Lihat semua proposal & analitik; persetujuan strategis tertentu.

---

## 6. Batasan (Out of Scope)
- Integrasi API eksternal (BIMA) belum ada.  
- Tidak memproses transaksi keuangan; hanya rencana anggaran.  
- Tidak ada aplikasi mobile native (hanya web responsif).  
- Tidak buat dokumen Word/PDF otomatis.  
- Tidak ada pemeriksa plagiarisme.  
- Tidak kelola distribusi dana atau hak kekayaan intelektual.

---

## 7. Tumpukan Teknis
- Backend: Laravel 12 (PHP 8.4), Fortify (2FA), Spatie Permission, Queue, Pest v4.  
- Frontend: Livewire 3, Tabler (Bootstrap 5), Lucide, Vite, Tailwind v4.  
- Database: MySQL, PK UUID, Eloquent.  
- Infrastruktur: Apache/Nginx, deployment manual, Telescope (dev).  
- Alat Dev: Pint, Git, Composer & Bun.

---

## 8. Metrik Keberhasilan (Contoh)
- Waktu proses proposal turun ke 2-3 minggu.  
- Uptimes 99% jam kerja.  
- Adopsi 80% dosen dalam 6 bulan.  
- Akurasi data tinggi, review selesai ≥90% dalam 14 hari, kepuasan pengguna ≥4/5.

---

## 9. Asumsi & Kendala
Asumsi: literasi komputer dasar, internet tersedia, email valid, data institusional stabil.  
Kendala: anggaran terbatas, timeline iteratif, hosting on-premise, SDM IT terbatas.

---

## 10. Peningkatan Mendatang (Roadmap)
- Banding versi dokumen, dashboard analitik lanjut, API integrasi, peningkatan mobile.  
- Deteksi kemiripan (AI), rekomendasi reviewer, integrasi keuangan, database proposal publik.

---

## Persetujuan Dokumen

| Peran                           | Nama        | Tanda Tangan | Tanggal     |
| ------------------------------- | ----------- | ------------ | ----------- |
| Product Owner                   | LPPM ITSNU  | ___________  | ___________ |
| Technical Lead                  | Tim TI      | ___________  | ___________ |
| Perwakilan Pemangku Kepentingan | Kepala LPPM | ___________  | ___________ |

---

Akhir Dokumen
