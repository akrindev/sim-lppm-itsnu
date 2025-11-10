# Peningkatan Rekomendasi untuk Selaras BIMA (Bahasa Indonesia)
## Usulan Teknis Berbasis Analisis BIMA

Versi Dokumen: 1.0  
Tanggal: 2025-11-09  
Tujuan: Daftar peningkatan agar SIM LPPM sejalan penuh dengan standar BIMA

---

## Ringkasan

Temuan utama: perluasan master data, validasi anggaran berbasis persentase, standardisasi jenis luaran, pelacakan kelayakan (SINTA/PDDIKTI), dukungan MOU untuk PKM, dan perbaikan UX alur usulan.

---

## 1) Perbaikan Skema Basis Data

- Tambah `description` pada `research_schemes` & `national_priorities`.  
- Enum `strata` skema penelitian tambahkan nilai `PKM`.  
- Perbarui kode grup anggaran menyesuaikan struktur RAB terbaru.

---

## 2) Perbedaan Kritis Penelitian vs PKM

- Penelitian: TKT wajib + roadmap teknis; keluaran riset (publikasi/paten/prototipe).  
- PKM: mitra & dampak sosial; keluaran pemberdayaan, pelatihan, modul/media.  
- Form & validasi menyesuaikan per tipe proposal.

---

## 3) Taksonomi Luaran (Master `output_types`)

Buat tabel master jenis luaran dengan kategori, grup, tipe, dan target status (berdasarkan BIMA) untuk Penelitian & PKM.  
Gunakan referensi ini saat input luaran agar standar dan terukur.

---

## 4) Validasi Persentase Anggaran

Contoh aturan RAB terkini:
- Honor: maks 10%  
- Teknologi & Inovasi: min 50% (gabungan bahan/peralatan)  
- Pelatihan: maks 20%  
- Perjalanan: maks 15%  
- Lainnya: maks 5%

Tambahkan fungsi validasi rekap per grup sebelum submit.

---

## 5) Kelayakan (Eligibility)

Fase 1 (manual): tambahkan field SINTA, skor, klaster, jabatan fungsional, status aktif di profil pengguna.  
Fase 2 (opsional): integrasi API SINTA/PDDIKTI jika tersedia.

---

## 6) Alur Usulan 5 Tahap (UX)

Tetap satu halaman tetapi tampilkan indikator langkah: Identitas → Substansi → RAB → Dokumen → Konfirmasi.  
Kunci: status DRAFT bisa diedit bebas; SUBMITTED terkunci.

---

## 7) Manajemen MOU Mitra (PKM)

Tambahkan field pada `partners`: path dokumen MOU, tanggal tanda tangan, status (draft/signed/expired).

---

## 8) Laporan: Luaran vs Laporan Naratif

Bedakan `proposal_outputs` (luaran) dan `proposal_reports` (laporan kemajuan/akhir, naratif + file).  
Sediakan status dan catatan reviu laporan.

---

## 9) Peningkatan UI/UX

- Pemilihan tipe proposal jelas (Penelitian vs PKM).  
- Ringkasan RAB real-time (persentase per grup + peringatan).  
- Penanda progres pengisian usulan.

---

## 10) Rencana Implementasi Bertahap

Fase 1 (segera): migrasi deskripsi, enum `PKM`, seeders BIMA, validasi RAB dasar.  
Fase 2: master `output_types`, validasi persentase, DRAFT status tegas.  
Fase 3: field eligibility, tabel laporan, MOU partner, UI TKT.  
Fase 4: wizard multi-step, integrasi API, dashboard luaran.

---

Catatan: Detail contoh skema & kode terdapat pada versi Inggris. Dokumen ini menyederhanakan pesan inti agar mudah diikuti tim pengembang dan pemangku kepentingan.
 
