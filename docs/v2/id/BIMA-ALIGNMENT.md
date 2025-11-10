# Penyelarasan dengan BIMA Kemdikbud (Bahasa Indonesia)
## Referensi & Panduan Implementasi

Versi Dokumen: 1.0  
Terakhir Diperbarui: 2025-11-09  
Sumber: BIMA Kemdikbud  
Tujuan: Menyamakan master data SIM LPPM ITSNU dengan standar nasional

---

## Ikhtisar

SIM LPPM ITSNU dirancang sejalan dengan BIMA (Basis Data Ilmu Pengetahuan Indonesia). Dokumen ini meringkas klasifikasi resmi yang dipakai BIMA agar data lokal seragam dan siap integrasi di masa depan.

---

## 1) Skema Penelitian (2024/2025)

- Penelitian Dasar: PDP, PDP Afirmasi, PPS, PF, PMDSU, PKDN, KATALIS.  
- Penelitian Terapan: PT.  
- Pengabdian Masyarakat: PW (Pemberdayaan Wilayah), PDB (Pemberdayaan Desa Binaan).

Catatan: Gunakan deskripsi singkat per skema pada seeder untuk konsistensi.

---

## 2) Prioritas Riset Nasional (PRN)

9 fokus PRN (Perpres No. 38/2018), contoh: Pangan, Energi, Kesehatan, Transportasi, Rekayasa Keteknikan, Pertahanan & Keamanan, Kemaritiman, Sosial-Humaniora-Pendidikan-Seni-Budaya, Multidisiplin & Lintas Sektoral.

---

## 3) Rumpun Ilmu (OECD FoS)

Level 1 (12 rumpun): MIPA, Ilmu Tanaman, Ilmu Hewani, Kedokteran, Kesehatan, Teknik, Bahasa, Ekonomi, Sosial Humaniora, Agama & Filsafat, Seni/Desain/Media, Pendidikan.  
Level 2/3: subrumpun & bidang rinci (contoh di MASTER-DATA.md).

---

## 4) Strategi Pengisian Data (Seeding)

1. `research_schemes`: isi semua skema 2024/2025 + deskripsi.  
2. `national_priorities`: isi 9 PRN + deskripsi.  
3. `science_clusters`: isi 3 level (kode & nama).  
4. `budget_groups`/`budget_components`: sesuaikan struktur RAB terbaru.

---

## 5) Manfaat Penyelarasan

- Standar nasional → mudah pelaporan & akreditasi.  
- Kategori konsisten → pencarian & analitik lebih baik.  
- Siap sinkronisasi BIMA ke depan.

---

Catatan: Detail contoh kode & daftar lengkap lihat versi Inggris (BIMA-ALIGNMENT.md). Dokumen ini memadatkan informasi ke Bahasa Indonesia yang mudah dipahami.
 
