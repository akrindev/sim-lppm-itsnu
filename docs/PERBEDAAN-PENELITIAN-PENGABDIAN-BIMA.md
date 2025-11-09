# Perbedaan Struktur Data Penelitian dan Pengabdian pada Aplikasi BIMA Kemdikbudristek

**Dokumen Referensi**  
**Versi:** 1.0  
**Tanggal:** 2025-11-09  
**Sumber Utama:** Analisis Mendalam Sistem BIMA Kemdikbudristek Indonesia

---

## Ringkasan Eksekutif

Dokumen ini memberikan analisis komprehensif tentang perbedaan struktur data, formulir, dan persyaratan antara proposal **Penelitian** (Research) dan **Pengabdian kepada Masyarakat** (Community Service/PKM) dalam sistem **BIMA (Basis Informasi Penelitian dan Pengabdian kepada Masyarakat)** yang dikelola oleh Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi (Kemdikbudristek) Indonesia.

Meskipun kedua jenis proposal menggunakan platform BIMA yang sama dan melalui arsitektur pengajuan 5 tahap yang serupa, keduanya memiliki perbedaan mendasar dalam:
- Tujuan dan fokus program
- Struktur data dan field formulir
- Kriteria evaluasi
- Output yang diharapkan
- Struktur anggaran
- Komposisi tim
- Mekanisme pelaporan

---

## Daftar Isi

1. [Arsitektur Sistem BIMA](#1-arsitektur-sistem-bima)
2. [Perbedaan Tahap 1: Identitas Proposal](#2-perbedaan-tahap-1-identitas-proposal)
3. [Perbedaan Tahap 2: Substansi Proposal](#3-perbedaan-tahap-2-substansi-proposal)
4. [Perbedaan Tahap 3: Rencana Anggaran Biaya (RAB)](#4-perbedaan-tahap-3-rencana-anggaran-biaya-rab)
5. [Perbedaan Tahap 4: Dokumen Pendukung](#5-perbedaan-tahap-4-dokumen-pendukung)
6. [Perbedaan Komposisi Tim](#6-perbedaan-komposisi-tim)
7. [Perbedaan Kriteria Evaluasi](#7-perbedaan-kriteria-evaluasi)
8. [Perbedaan Output dan Luaran](#8-perbedaan-output-dan-luaran)
9. [Perbedaan Pelaporan dan Monitoring](#9-perbedaan-pelaporan-dan-monitoring)
10. [Tabel Perbandingan Komprehensif](#10-tabel-perbandingan-komprehensif)
11. [Rekomendasi Strategis](#11-rekomendasi-strategis)
12. [Referensi dan Sumber](#12-referensi-dan-sumber)

---

## 1. Arsitektur Sistem BIMA

### 1.1 Tentang Sistem BIMA

Sistem BIMA merupakan portal layanan terpadu yang dirancang untuk memfasilitasi seluruh siklus hidup program penelitian dan pengabdian kepada masyarakat di perguruan tinggi Indonesia. Sistem ini dapat diakses melalui https://bima.kemdiktisaintek.go.id dan mengelola alur kerja lengkap mulai dari pengajuan proposal awal hingga monitoring proyek, evaluasi, dan pelaporan akhir.

**Integrasi Sistem:**
- **SINTA** (Science and Technology Index for Indonesia)
- **PDDIKTI** (Pangkalan Data Pendidikan Tinggi)
- Database institusional perguruan tinggi
- Sistem identifikasi peneliti dan dosen

### 1.2 Arsitektur Pengajuan 5 Tahap

Baik proposal penelitian maupun pengabdian melewati **5 tahap pengajuan** yang terstandarisasi:

| Tahap | Nama Tahap | Fokus |
|-------|-----------|-------|
| **Tahap 1** | Identitas Proposal | Informasi dasar dan klasifikasi |
| **Tahap 2** | Substansi Proposal | Konten proposal (PDF upload) |
| **Tahap 3** | Rencana Anggaran Biaya (RAB) | Detail anggaran per item |
| **Tahap 4** | Dokumen Pendukung | Lampiran dan dokumen verifikasi |
| **Tahap 5** | Konfirmasi | Ringkasan dan persetujuan tim |

Meskipun strukturnya sama, **konten dan persyaratan** di setiap tahap berbeda signifikan antara penelitian dan pengabdian.

---

## 2. Perbedaan Tahap 1: Identitas Proposal

### 2.1 Penelitian (Research) - Field Khusus

#### A. Tingkat Kesiapan Teknologi (TKT)

**Field Wajib Penelitian:**
- **TKT Saat Ini** (Current Technology Readiness Level)
- **TKT Target Akhir** (Target Technology Readiness Level at completion)

**Penjelasan TKT:**
- TKT merupakan indikator kematangan teknologi/metodologi penelitian
- Penelitian Dasar: TKT 1-3 (pengetahuan fundamental)
- Penelitian Terapan: TKT 4-6 (aplikasi praktis)
- Penelitian Pengembangan: TKT 7-9 (komersialisasi)

**Proses Pengisian TKT:**
1. Dosen melakukan self-assessment
2. Evaluasi berdasarkan indikator multi-dimensi
3. Alokasi persentase untuk setiap komponen teknologi/metodologi
4. Sistem menentukan skema penelitian yang dapat diakses berdasarkan TKT

#### B. Field Klasifikasi Penelitian

**Field Tambahan:**
- **Kelompok Skema Penelitian** (Research Scheme Group)
- **Ruang Lingkup Penelitian** (Research Scope)
- **Besaran Dana Maksimal** (Maximum Funding Amount)
- **Bidang Fokus Penelitian** (Research Focus Area)
- **Tema Penelitian** (Research Theme)
- **Topik Penelitian** (Research Topic)
- **Rumpun Ilmu Level 1, 2, 3** (Discipline Code - 3 hierarchical levels)
- **Prioritas Riset** (Research Priority - alignment with RIRN)
- **Tahun Pertama Usulan** (Initial Submission Year)
- **Lama Kegiatan** (Project Duration in years)

**Tujuan:** Memetakan penelitian ke prioritas nasional, klasifikasi kapasitas institusi, dan taksonomi disiplin ilmu.

### 2.2 Pengabdian kepada Masyarakat (PKM) - Field Khusus

#### A. TIDAK ADA Penilaian TKT

Pengabdian **tidak menggunakan** TKT karena fokusnya bukan pada kesiapan teknologi, melainkan pada **pemberdayaan masyarakat** dan **penyelesaian masalah komunitas**.

#### B. Karakterisasi Mitra dan Beneficiary

**Field Wajib Pengabdian:**
- **Judul Pengabdian kepada Masyarakat**
- **Skema Pengabdian kepada Masyarakat** (Community Service Scheme)
- **Program Pengabdian kepada Masyarakat** (Specific Program Type)
- **Bidang Fokus** (Focus Area)
- **Prioritas Riset** (Research Priority)
- **Durasi Program** (Program Duration)
- **Tahun Pelaksanaan** (Implementation Year)

**Field Karakterisasi Kelompok Mitra:**
1. **Kelompok Mitra** (Beneficiary Group Category)
   - Contoh: Petani, UMKM, Kelompok Perempuan, Nelayan, dll.
2. **Jenis Kelompok Mitra** (Beneficiary Group Type)
   - Contoh: Koperasi, Kelompok Tani, Paguyuban, PKK, dll.
3. **Jumlah Anggota Kelompok** (Number of Group Members)
4. **Lingkup Permasalahan** (Problem Scope)
   - Contoh: Ekonomi, Kesehatan, Pendidikan, Lingkungan

**Field Lokasi Geografis Lengkap:**
- Nama Kelompok Mitra
- Nama Ketua/Pimpinan Kelompok
- Provinsi
- Kabupaten/Kota
- Kecamatan
- Desa/Kelurahan
- Alamat Lengkap

**Tujuan:** Memastikan mitra dapat diakses (maksimal 200 km dari institusi pengusul) dan masalah masyarakat terdokumentasi dengan baik.

### 2.3 Perbandingan Field Tahap 1

| Aspek | Penelitian | Pengabdian |
|-------|-----------|-----------|
| **TKT Assessment** | ✅ Wajib (TKT Saat Ini + Target) | ❌ Tidak ada |
| **Fokus Utama** | Kemajuan ilmu pengetahuan | Pemberdayaan masyarakat |
| **Klasifikasi Disiplin** | 3-level Rumpun Ilmu | Bidang Fokus |
| **Karakterisasi Mitra** | ❌ Tidak ada | ✅ Wajib (lengkap) |
| **Lokasi Geografis** | ❌ Tidak diwajibkan | ✅ Wajib (detail lengkap) |
| **Roadmap Penelitian** | ✅ 5 tahun | ❌ Tidak ada |
| **Durasi Standar** | 1-3 tahun | 6-12 bulan |

---

## 3. Perbedaan Tahap 2: Substansi Proposal

### 3.1 Penelitian - Konten Dokumen PDF

**Struktur Proposal Penelitian (Template BIMA):**

#### 1. Judul dan Ringkasan (maksimal 300 kata)
- Urgensi penelitian
- Tujuan penelitian
- Output yang ditargetkan

#### 2. Kata Kunci (maksimal 5 term)

#### 3. Pendahuluan (maksimal 1.000 kata)
- **Tinjauan pustaka** (literature review)
- **Rumusan masalah** (problem formulation)
- **Pendekatan metodologi** (methodology approach)
- **State-of-the-art review** dengan dokumentasi kebaruan penelitian
- **Roadmap penelitian 5 tahun** menunjukkan kemajuan progresif

#### 4. Metodologi Penelitian (Detail)
- Desain penelitian
- Pendekatan pengumpulan data
- Pendekatan analisis data
- Timeline penelitian

#### 5. Referensi (Lengkap)

#### 6. Luaran Wajib Setiap Tahun
- Jenis output (jurnal, prototipe, paten, dll.)
- Ditentukan berdasarkan skema penelitian

#### 7. Alignment dengan Framework Nasional
- **SDGs** (Sustainable Development Goals)
- **Asta Cita** Presiden
- **RIRN** (Rencana Inisiatif Riset Nasional)
- **IKU** (Indikator Kinerja Utama) institusi

**Penekanan:**
- Kebaruan ilmiah (novelty)
- Rigor metodologi
- Kontribusi terhadap teori/disiplin ilmu
- Publikasi jurnal internasional (Q1/Q2 Scopus untuk institusi Tier 1)

### 3.2 Pengabdian - Konten Dokumen PDF

**Struktur Proposal Pengabdian (Template BIMA):**

#### 1. Judul dan Ringkasan (maksimal 300 kata)
- Urgensi program
- Tujuan program
- Output yang ditargetkan untuk masyarakat

#### 2. Kata Kunci (maksimal 5 term)

#### 3. Analisis Situasi (maksimal 1.000 kata)
- **Kondisi masyarakat saat ini** (existing community conditions)
- **Masalah spesifik yang dihadapi masyarakat**
- **Hubungan sistematis** antara masalah dan intervensi yang diusulkan
- **Data dan dokumentasi fotografi** kondisi saat ini (wajib untuk program ekonomi)

#### 4. Metodologi Pelaksanaan (Detail)
- **Pendekatan bertahap** untuk mengatasi masalah
- **Minimum 2 domain masalah** yang ditangani
- **Mekanisme partisipasi masyarakat** dalam implementasi
- **Pendekatan evaluasi program** dan keberlanjutan
- **Peran dan tanggung jawab individu tim** sesuai kompetensi
- **Pengakuan kredit akademik** untuk mahasiswa (SKS)

#### 5. Referensi (Lengkap)

#### 6. Data Kelompok Mitra (Konfirmasi dari Tahap 1)

#### 7. Luaran Program per Tahun Pelaksanaan
- Dokumentasi pemberdayaan masyarakat
- Kegiatan demonstrasi komunitas
- Verifikasi transfer teknologi
- Pengaturan kelembagaan berkelanjutan

#### 8. Alignment dengan Framework Nasional
- SDGs, Asta Cita, RIRN, IKU (sama dengan penelitian)

**Penekanan:**
- Analisis masalah yang komprehensif
- Kesesuaian intervensi
- Partisipasi masyarakat yang autentik
- Mekanisme keberlanjutan pasca-program

### 3.3 Perbandingan Konten Substansi

| Aspek | Penelitian | Pengabdian |
|-------|-----------|-----------|
| **Fokus Konten** | Kebaruan ilmiah + Metodologi | Analisis masalah + Solusi praktis |
| **Literature Review** | ✅ Wajib (state-of-the-art) | ✅ Minimal (konteks masalah) |
| **Roadmap 5 Tahun** | ✅ Wajib | ❌ Tidak ada |
| **Data Kondisi Masyarakat** | ❌ Tidak diwajibkan | ✅ Wajib (data + foto) |
| **Partisipasi Masyarakat** | ❌ Tidak relevan | ✅ Wajib (mekanisme detail) |
| **Keberlanjutan Program** | ❌ Tidak diwajibkan | ✅ Wajib (post-implementation) |
| **Minimal Domain Masalah** | N/A | ✅ Minimum 2 domain |
| **SKS Mahasiswa** | ❌ Opsional | ✅ Wajib (dokumentasi) |
| **Ukuran File PDF** | Maksimal 5 MB | Maksimal 5 MB |

---

## 4. Perbedaan Tahap 3: Rencana Anggaran Biaya (RAB)

### 4.1 Kategori Anggaran yang Sama

Baik penelitian maupun pengabdian menggunakan kategori anggaran standar berdasarkan **Peraturan Menteri Keuangan (PMK)**:

1. **Biaya Personalia** (Personnel costs - gaji dan honorarium)
2. **Biaya Peralatan dan Bahan** (Equipment and materials - non-consumable)
3. **Biaya Bahan dan Persediaan** (Materials and supplies - consumable)
4. **Biaya Perjalanan** (Travel and transportation)
5. **Biaya Operasional Umum** (General operational costs)
6. **Biaya Komunikasi dan Publikasi** (Communication and publication costs)

**Format Entri RAB:**
- Nama item
- Satuan
- Volume
- Harga satuan
- Total biaya (volume × harga satuan)

### 4.2 Penelitian - Batasan dan Penekanan Anggaran

#### A. Alokasi Anggaran Khusus Penelitian
- **Pengumpulan data** (data collection expenses)
- **Perangkat lunak analitik** (analytical software)
- **Akuisisi peralatan** (equipment acquisition)
- **Biaya publikasi jurnal** (journal publication costs - terutama jurnal internasional)
- **Biaya presentasi konferensi** (conference presentation)

#### B. Multi-Year Budget
- Untuk penelitian multi-tahun (2-3 tahun)
- RAB harus dipecah per tahun (Tahun 1, Tahun 2, dll.)
- Total anggaran konstan atau dengan justifikasi penyesuaian

#### C. Kontribusi Mitra (untuk Penelitian Terapan/Pengembangan)
- Wajib ada **Surat Pernyataan Komitmen Mitra**
- Kontribusi dapat berupa:
  - **In-cash** (kontribusi tunai)
  - **In-kind** (fasilitas, peralatan, keahlian teknis)

### 4.3 Pengabdian - Batasan dan Penekanan Anggaran

#### A. Aturan Minimum 50% untuk Masyarakat

**KRITERIA WAJIB:**
> Minimum **50% dari total anggaran** harus diinvestasikan langsung ke masyarakat mitra melalui **Belanja Barang** (material expenditures)

**Yang Termasuk Investasi Langsung:**
- Peralatan/alat produksi untuk mitra
- Bahan baku/raw materials untuk mitra
- Pelatihan dan workshop untuk masyarakat
- Biaya demonstrasi dan uji coba teknologi

**Yang TIDAK Diperbolehkan:**
- Pembelian tanah (land acquisition)
- Pembangunan infrastruktur permanen
- Pembelian kendaraan
- Pengadaan aset tetap institusi

#### B. Alokasi Anggaran Khusus Pengabdian
- **Uang Harian Terjun Lapang** (Field work daily allowance untuk mahasiswa)
- **Rapat dan Koordinasi Tim** (Team meetings)
- **Workshop dan Pelatihan Masyarakat** (Community training sessions)
- **Kegiatan Demonstrasi** (Demonstration activities)
- **Bahan/Material untuk Transfer Teknologi**

#### C. Kontribusi Mitra Masyarakat
- Wajib ada **Surat Kesanggupan Bermitra**
- Kontribusi mitra dapat berupa:
  - **Tenaga kerja** (labor)
  - **Dukungan in-kind** (lahan, fasilitas lokal)
  - **Komitmen keberlanjutan** pasca-program

#### D. Jam Kerja Lapangan Masyarakat (JKEM)
- Minimum **8 kunjungan lapangan** ke lokasi mitra
- Minimum **144 jam** kerja lapangan tim gabungan

### 4.4 Perbandingan Anggaran

| Aspek | Penelitian | Pengabdian |
|-------|-----------|-----------|
| **Besaran Dana Tipikal** | Rp 30-500 juta | Rp 20-200 juta |
| **Durasi** | 1-3 tahun | 6-12 bulan (1 tahun fiskal) |
| **Aturan 50% Investasi Langsung** | ❌ Tidak ada | ✅ Wajib (minimum 50% untuk masyarakat) |
| **Multi-Year Budget** | ✅ Wajib breakdown per tahun | ❌ Single fiscal year |
| **Fokus Utama** | Metodologi + Publikasi | Field work + Material untuk masyarakat |
| **Kontribusi Mitra** | ✅ Optional (Penelitian Terapan) | ✅ Wajib (komitmen keberlanjutan) |
| **Biaya Publikasi Jurnal** | ✅ Signifikan (Q1/Q2 Scopus) | ❌ Minimal |
| **Stipend Mahasiswa** | ❌ Optional | ✅ Wajib (UHTL - Uang Harian Terjun Lapang) |
| **JKEM (Jam Kerja Lapangan)** | N/A | ✅ Minimum 144 jam |

---

## 5. Perbedaan Tahap 4: Dokumen Pendukung

### 5.1 Penelitian - Dokumen Pendukung

**Dokumen Wajib:**
1. **Verifikasi Afiliasi Institusi** (otomatis dari integrasi PDDIKTI)
2. **Konfirmasi Profil SINTA** peneliti
3. **Persetujuan Pimpinan Institusi** (institutional leadership approval)
4. **Dokumentasi Kolaborasi Internasional** (jika ada)
5. **Atestasi Ketersediaan Fasilitas dan Peralatan** institusi
6. **Sertifikat Persetujuan Etik Penelitian** (untuk penelitian yang melibatkan subjek manusia atau hewan)

**Dokumen Khusus untuk Penelitian dengan Subjek Manusia:**
- **IRB (Institutional Review Board)** atau
- **Ethical Clearance** dari komite etik institusi
- Diperlukan SEBELUM atau SELAMA review proposal

### 5.2 Pengabdian - Dokumen Pendukung

**Dokumen Wajib:**
1. **Persetujuan Pimpinan Institusi** (institutional leadership approval)
2. **Profil Institusional Mitra** (termasuk informasi registrasi legal dan struktur organisasi)
3. **Surat Kesanggupan Bermitra** (community partner commitment letters)
   - Konfirmasi kesediaan berpartisipasi
   - Komitmen keberlanjutan pasca-donor funding
4. **Bukti Manfaat Mitra** dari kegiatan yang diusulkan
5. **Dokumentasi Fotografi atau Deskriptif** kondisi masyarakat dan masalah yang teridentifikasi

**Dokumen Khusus untuk Transfer Teknologi:**
- **Spesifikasi Teknis** peralatan/teknologi
- **Manual Implementasi** untuk masyarakat

### 5.3 Perbandingan Dokumen Pendukung

| Jenis Dokumen | Penelitian | Pengabdian |
|---------------|-----------|-----------|
| **Persetujuan Institusi** | ✅ Wajib | ✅ Wajib |
| **Profil SINTA** | ✅ Wajib | ✅ Wajib |
| **Ethical Clearance** | ✅ Wajib (subjek manusia) | ❌ Tidak diwajibkan |
| **Surat Kesanggupan Mitra** | ❌ Optional | ✅ Wajib |
| **Profil Institusional Mitra** | ❌ Tidak ada | ✅ Wajib |
| **Dokumentasi Fotografi Masalah** | ❌ Tidak ada | ✅ Wajib |
| **Spesifikasi Teknis Alat** | ✅ Optional | ✅ Wajib (jika transfer teknologi) |
| **Kolaborasi Internasional** | ✅ Didorong | ❌ Jarang |

---

## 6. Perbedaan Komposisi Tim

### 6.1 Penelitian - Komposisi Tim

#### A. Ketua Penelitian
**Persyaratan:**
- Memiliki **NIDN** (Nomor Induk Dosen Nasional) atau **NIDK** (Nomor Induk Dosen Khusus) atau **NUPTK**
- Status aktif di **PDDIKTI**
- Memiliki **SINTA ID** aktif
- Tidak sedang memimpin atau terlibat dalam proyek penelitian aktif lainnya

**Batasan untuk Dosen Pemula:**
- Maksimal pangkat **Lektor** (Associate Professor)
- Dari institusi cluster Madya, Pratama, atau Binaan (untuk jalur afirmasi)

#### B. Anggota Tim Penelitian
**Minimum:** 2 orang (ketua + minimal 1 anggota)

**Tipe Anggota:**
1. **Dosen/Peneliti** dengan NIDN/NIDK/NUPTK
2. **Mahasiswa** dengan NIM (Nomor Induk Mahasiswa) dan status aktif
3. **Partisipan Masyarakat** (opsional) dengan NIK atau paspor

**Batasan Partisipasi:**
- Setiap dosen dapat menjadi **ketua maksimal 1 proposal**
- Dapat menjadi **anggota maksimal 1 proposal tambahan**
- Tidak boleh terlibat dalam skema yang sama secara simultan

#### C. Afiliasi Institusional
- Minimal **1 anggota tim dari institusi yang sama** dengan ketua
- Kolaborasi antar-institusi dan internasional **didorong**

### 6.2 Pengabdian - Komposisi Tim

#### A. Ketua Pengabdian
**Persyaratan:** (Sama dengan ketua penelitian)
- NIDN/NIDK/NUPTK
- Status aktif PDDIKTI
- SINTA ID aktif
- Pengalaman pengabdian sebelumnya yang relevan

#### B. Anggota Tim Pengabdian
**Minimum:** Ketua + **minimal 4 mahasiswa** + anggota dosen

**Tipe Anggota:**
1. **Dosen** dengan keahlian relevan (kesehatan, bisnis, pertanian, dll.)
2. **Mahasiswa** (MINIMUM 4 orang):
   - Status enrolled aktif
   - Implementer tingkat lapangan
   - Keterlibatan berkelanjutan dalam memahami masalah dan solusi
   - **Mendapat pengakuan SKS** (Satuan Kredit Semester)
3. **Anggota Masyarakat Mitra**:
   - Terdaftar sebagai beneficiary
   - Pemimpin atau tokoh masyarakat yang diakui

#### C. Persyaratan Geografis
- Mitra masyarakat maksimal **200 km** dari institusi pengusul
- Dapat melebihi jika:
  - Masih dalam provinsi yang sama
  - Institusi kontribusi dana tambahan untuk transportasi
  - Ada dokumentasi komitmen institusional

#### D. Dokumentasi Peran dan SKS
- Setiap anggota tim: spesifikasi peran dan tanggung jawab
- Mahasiswa: **wajib dokumentasi learning outcomes** dan pengakuan SKS
- Mencerminkan komitmen ganda terhadap penelitian dan kemajuan pedagogis

### 6.3 Perbandingan Komposisi Tim

| Aspek | Penelitian | Pengabdian |
|-------|-----------|-----------|
| **Minimum Tim** | 2 orang (ketua + 1 anggota) | Ketua + 4 mahasiswa + anggota dosen |
| **Keterlibatan Mahasiswa** | ✅ Optional | ✅ Wajib (minimum 4 orang) |
| **SKS untuk Mahasiswa** | ❌ Optional | ✅ Wajib (dokumentasi learning outcomes) |
| **Anggota Masyarakat** | ✅ Optional | ✅ Wajib (mitra beneficiary) |
| **Batasan Geografis** | ❌ Tidak ada | ✅ Maksimal 200 km (dengan pengecualian) |
| **Fokus Tim** | Rigor metodologi + Publikasi | Field work + Community engagement |
| **Kolaborasi Internasional** | ✅ Didorong | ❌ Jarang |
| **Pengalaman Lapangan** | ❌ Tidak diwajibkan | ✅ Wajib (pengalaman pengabdian sebelumnya) |

---

## 7. Perbedaan Kriteria Evaluasi

### 7.1 Evaluasi Administratif (Sama untuk Keduanya)

**Kriteria Verifikasi:**
1. Kelayakan pengusul (afiliasi, kredensial, status PDDIKTI aktif)
2. Kelayakan anggota tim dan komposisi yang sesuai
3. Akurasi teknis anggaran dan kesesuaian dengan aturan
4. Kelengkapan dokumen dan kepatuhan teknis (format file, ukuran, template)
5. Alignment dengan bidang fokus dan prioritas nasional
6. Bebas dari konflik atau pembatasan yang mendiskualifikasi

**Khusus Penelitian:**
- Verifikasi alignment TKT (TKT target sesuai dengan spesifikasi skema)

**Khusus Pengabdian:**
- Verifikasi aksesibilitas geografis masyarakat
- Konfirmasi validitas dokumentasi institusi mitra

### 7.2 Evaluasi Substansi - Penelitian

**Kriteria Penilaian Penelitian:**

#### 1. Kebaruan dan Orisinalitas Penelitian (Research Novelty)
- Apakah penelitian menghasilkan pengetahuan baru?
- Apakah ada kemajuan bermakna dari penelitian yang ada?

#### 2. Rigor Desain dan Metodologi Penelitian
- Apakah metodologi ilmiah yang diusulkan sound?
- Apakah cukup sophisticated untuk pertanyaan penelitian?

#### 3. Kelayakan dan Perencanaan Proyek yang Realistis
- Apakah dapat diselesaikan dalam anggaran dan timeframe yang dialokasikan?
- Apakah sumber daya tersedia cukup?

#### 4. Signifikansi dan Potensi Dampak
- Apakah temuan akan memajukan pengetahuan disipliner?
- Apakah berkontribusi pada pengembangan teori?

#### 5. Kesesuaian Output dan Ekspektasi Kualitas
- Apakah target output mandatory realistis?
- Apakah cukup ambisius untuk tipe penelitian?

#### 6. Kemampuan Tim dan Track Record Peneliti
- Apakah anggota tim memiliki produktivitas penelitian sebelumnya?
- Apakah capable melakukan penelitian yang diusulkan?

#### 7. Alignment dengan Prioritas Nasional dan SDGs
- Kontribusi terhadap tujuan pembangunan nasional

**Metode Evaluasi:**
- **Peer review** oleh ahli spesifik disiplin ilmu
- Penilaian teknikal merit, kemajuan ilmiah, rigor metodologi

### 7.3 Evaluasi Substansi - Pengabdian

**Kriteria Penilaian Pengabdian:**

#### 1. Komprehensivitas dan Validitas Analisis Masalah Masyarakat
- Apakah benar-benar ada masalah masyarakat yang teridentifikasi sistematis?
- Apakah analisis berbasis data/bukti?

#### 2. Kesesuaian Strategi Intervensi dan Potensi Efektivitas
- Apakah intervensi logis mengatasi masalah yang teridentifikasi?
- Apakah justified secara evidence-based atau teoritis?

#### 3. Mekanisme Partisipasi Masyarakat
- Apakah masyarakat benar-benar berpartisipasi dalam identifikasi masalah?
- Apakah involved dalam pengembangan solusi dan implementasi?

#### 4. Outcomes Capacity-Building dan Pemberdayaan
- Apakah dirancang untuk meningkatkan kapabilitas institusional masyarakat?
- Apakah meningkatkan kapasitas problem-solving otonom?

#### 5. Mekanisme Keberlanjutan
- Apakah ada pengaturan institusional atau sosial yang memungkinkan manfaat berkelanjutan?
- Apakah masyarakat dapat melanjutkan setelah pendanaan berakhir?

#### 6. Kemampuan Tim dan Pengalaman Berbasis Lapangan
- Apakah anggota tim memiliki expertise yang sesuai?
- Apakah punya pengalaman pengabdian masyarakat sebelumnya?

#### 7. Kesesuaian Anggaran untuk Scope
- Apakah anggaran mendukung intervensi masyarakat yang dijelaskan?
- Apakah mencapai persyaratan investasi langsung minimum 50%?

**Metode Evaluasi:**
- Peer review oleh praktisi pengabdian
- **Site visit (evaluasi lapangan)** untuk skema tertentu - menilai langsung kondisi masyarakat dan kapasitas institusi mitra

### 7.4 Perbandingan Kriteria Evaluasi

| Aspek Evaluasi | Penelitian | Pengabdian |
|----------------|-----------|-----------|
| **Fokus Utama** | Kebaruan ilmiah + Rigor metodologi | Analisis masalah + Kesesuaian intervensi |
| **Novelty** | ✅ KRITIS (research novelty) | ❌ Tidak ditekankan |
| **Metodologi** | ✅ Rigor ilmiah tinggi | ✅ Kesesuaian dengan masalah |
| **Partisipasi Masyarakat** | ❌ Tidak relevan | ✅ KRITIS |
| **Keberlanjutan** | ❌ Tidak diwajibkan | ✅ KRITIS (post-program) |
| **Track Record Tim** | ✅ Publikasi + Produktivitas | ✅ Pengalaman lapangan |
| **Peer Review** | ✅ Ahli disiplin ilmu | ✅ Praktisi pengabdian |
| **Site Visit** | ❌ Tidak ada | ✅ Optional (untuk skema tertentu) |
| **Ekspektasi Publikasi** | ✅ TINGGI (jurnal internasional) | ❌ RENDAH |

---

## 8. Perbedaan Output dan Luaran

### 8.1 Penelitian - Output Berdasarkan Tipe

#### A. Penelitian Dasar (TKT 1-3)

**Luaran Wajib:**
- **Publikasi jurnal peer-reviewed** di tingkat nasional atau internasional
- **Paper presentasi konferensi**
- **Laporan penelitian** mendokumentasikan temuan ilmiah

**Kualitas Jurnal:**
- Institusi Tier 1: Jurnal internasional terindeks **Scopus Q1/Q2**
- Institusi emerging: Jurnal nasional terindeks **SINTA**

#### B. Penelitian Terapan (TKT 4-6)

**Luaran Wajib:**
- **Prototipe atau model** (prototype/model development documentation)
- **Spesifikasi desain teknologi** (technology design specifications)
- **Dokumentasi studi kelayakan** (feasibility study documentation)
- **Publikasi jurnal** menunjukkan translasi penelitian ke aplikasi praktis

**Kekayaan Intelektual:**
- Aplikasi paten (patent applications)
- Registrasi model utilitas (utility model registrations)
- Sertifikasi varietas tanaman (untuk pertanian)

#### C. Penelitian Pengembangan (TKT 7-9)

**Luaran Wajib:**
- **Spesifikasi produk atau sistem fungsional** (fully functional product/system)
- **Dokumentasi sertifikasi produk komersial** (commercial product certification)
- **Verifikasi implementasi transfer teknologi** (technology transfer implementation)
- **Demonstrasi kesiapan pasar** atau adopsi institusional

**Publikasi:**
- Publikasi jurnal internasional (tetap common output untuk institusi riset)

### 8.2 Pengabdian - Output Berdasarkan Program

#### A. Pemberdayaan Berbasis Masyarakat

**Luaran Wajib:**
- **Dokumentasi institusi masyarakat** yang didirikan atau diperkuat
- **Dokumentasi skill development** anggota masyarakat (melalui pelatihan/sertifikasi)
- **Rencana atau keputusan masyarakat** untuk pengelolaan masalah berkelanjutan
- **Dokumentasi fotografi atau video** outcomes masyarakat

**Fokus:** Agency masyarakat dan kapasitas problem-solving mandiri

#### B. Pemberdayaan Berbasis Kewirausahaan

**Luaran Wajib:**
- **Rencana pengembangan bisnis UMKM** atau rencana strategis
- **Dokumentasi proses produksi** atau Standard Operating Procedures (SOP)
- **Adopsi teknologi** oleh mitra bisnis
- **Dokumentasi linkage pasar** (market linkage)
- **Data volume bisnis/profitabilitas** menunjukkan penguatan enterprise

**Fokus:** Keberlanjutan ekonomi dan viabilitas bisnis

#### C. Pemberdayaan Berbasis Wilayah

**Luaran Wajib:**
- **Rencana pengembangan teritorial** mengintegrasikan multi-inisiatif
- **Mekanisme koordinasi antar-institusi** yang dibentuk
- **Dokumentasi engagement multi-sektor** (kesehatan, pendidikan, lingkungan, ekonomi)

**Fokus:** Pengembangan teritorial sistemik, bukan outcomes proyek terisolasi

### 8.3 Format Dokumentasi Output

**Kedua Program:**
- Artikel jurnal (dengan bukti publikasi atau acceptance letter)
- Paper presentasi konferensi dengan dokumentasi proceeding
- Dokumentasi registrasi kekayaan intelektual (untuk penelitian)
- Laporan analisis institusional (untuk pengabdian)
- Manual implementasi transfer teknologi
- Dokumentasi fotografi atau video substansiating achievement output

### 8.4 Perbandingan Output

| Aspek Output | Penelitian | Pengabdian |
|--------------|-----------|-----------|
| **Publikasi Jurnal** | ✅ WAJIB (Q1/Q2 Scopus untuk Tier 1) | ❌ OPTIONAL (jarang) |
| **Prototipe/Model** | ✅ Penelitian Terapan/Pengembangan | ❌ Tidak diwajibkan |
| **Paten/HKI** | ✅ Penelitian Terapan/Pengembangan | ❌ Tidak diwajibkan |
| **Institusi Masyarakat** | ❌ Tidak relevan | ✅ KRITIS |
| **Skill Development Masyarakat** | ❌ Tidak relevan | ✅ WAJIB |
| **SOP/Business Plan** | ❌ Tidak relevan | ✅ Untuk skema kewirausahaan |
| **Dokumentasi Fotografi** | ❌ Optional | ✅ WAJIB |
| **Transfer Teknologi** | ✅ Penelitian Pengembangan | ✅ Semua skema pengabdian |
| **Fokus Utama** | Pengetahuan baru + Publikasi | Perubahan masyarakat + Keberlanjutan |

---

## 9. Perbedaan Pelaporan dan Monitoring

### 9.1 Penelitian - Struktur Pelaporan

#### A. Laporan Kemajuan (Progress Report)
**Frekuensi:** Pada interval yang ditentukan (biasanya 50% completion dan saat project conclusion)

**Konten:**
- Kemajuan penelitian terhadap spesifikasi timeline
- Progress pengumpulan data
- Temuan preliminer (preliminary findings)
- Hambatan yang dihadapi dan tindakan remedial
- Output yang diantisipasi dari sisa durasi proyek

#### B. Laporan Akhir (Final Report)
**Konten:**
- Dokumentasi lengkap implementasi penelitian
- Temuan penelitian yang finalized
- Status achievement output penelitian:
  - Outcomes publikasi
  - Registrasi kekayaan intelektual
  - Pengembangan prototipe
- Rekomendasi untuk penelitian lanjutan atau applied advancement
- Dokumentasi kontribusi semua anggota tim
- Fasilitas atau peralatan institusional yang digunakan

### 9.2 Pengabdian - Struktur Pelaporan

#### A. Laporan Kemajuan
**Konten:**
- Aktivitas implementasi
- Tingkat partisipasi dan engagement masyarakat
- Progress problem-solving
- Outcomes capacity-building masyarakat
- Pembentukan mekanisme keberlanjutan

**Fokus Tambahan:**
- Aktivitas anggota tim
- Learning outcomes mahasiswa dan pencapaian SKS
- Perubahan institusional mitra atau peningkatan kapabilitas
- Tantangan yang teridentifikasi yang memerlukan modifikasi program

#### B. Laporan Akhir
**Konten:**
- Dokumentasi lengkap masalah masyarakat yang ditangani
- Aktivitas intervensi yang dilakukan
- Pencapaian capacity-building masyarakat (diukur melalui instrumen validated)
- Mekanisme keberlanjutan masyarakat yang dibentuk
- **Testimonial beneficiary** atau dokumentasi kepuasan
- Rekomendasi untuk ongoing community service atau pendekatan alternatif

### 9.3 Monitoring dan Evaluasi (Monev)

#### A. Penelitian - Proses Monev

**Internal Monev (oleh LPPM institusi):**
- Alignment implementasi dengan desain proposal
- Efektivitas koordinasi dan kolaborasi tim
- Achievement output dan kualitas output
- Kesesuaian penggunaan anggaran
- Adherence terhadap metodologi dan standar etik

**Eksternal Monev (oleh reviewer DRTPM):**
- Evaluasi independen oleh expert nasional
- Skor evaluasi pada rubrik standar
- Berita Acara (official meeting record) submitted ke BIMA

#### B. Pengabdian - Proses Monev

**Internal Monev (oleh LPPM institusi):**
- Alignment aktivitas implementasi dengan desain proposal
- Kesesuaian identifikasi masalah dan relevansi intervensi
- Outcomes partisipasi dan capacity-building masyarakat
- Koordinasi tim dan pemenuhan peran individual
- Keterlibatan dan komitmen mitra
- Fungsionalitas mekanisme keberlanjutan

**Eksternal Monev (oleh reviewer DRTPM):**
- Evaluasi independen oleh praktisi pengabdian
- **Site visit (evaluasi lapangan)** untuk skema tertentu
- Skor evaluasi standar dan Berita Acara

### 9.4 Perbandingan Pelaporan dan Monev

| Aspek | Penelitian | Pengabdian |
|-------|-----------|-----------|
| **Laporan Kemajuan** | ✅ 50% completion | ✅ Sesuai timeline program |
| **Laporan Akhir** | ✅ Wajib | ✅ Wajib |
| **Fokus Laporan** | Temuan penelitian + Publikasi | Outcomes masyarakat + Keberlanjutan |
| **Dokumentasi SKS Mahasiswa** | ❌ Optional | ✅ Wajib |
| **Testimonial Beneficiary** | ❌ Tidak ada | ✅ WAJIB |
| **Site Visit Monev** | ❌ Tidak ada | ✅ Optional (untuk skema tertentu) |
| **Instrumen Penilaian** | Rubrik metodologi + Publikasi | Rubrik partisipasi + Keberlanjutan |
| **Berita Acara** | ✅ Wajib | ✅ Wajib |

---

## 10. Tabel Perbandingan Komprehensif

### Ringkasan Perbedaan Utama

| # | Aspek | Penelitian | Pengabdian kepada Masyarakat |
|---|-------|-----------|------------------------------|
| **1** | **Tujuan Utama** | Kemajuan pengetahuan ilmiah | Pemberdayaan dan penyelesaian masalah masyarakat |
| **2** | **TKT Assessment** | ✅ Wajib (TKT 1-9) | ❌ Tidak ada |
| **3** | **Karakterisasi Mitra** | ❌ Tidak diwajibkan | ✅ Wajib (detail lengkap) |
| **4** | **Roadmap 5 Tahun** | ✅ Wajib | ❌ Tidak ada |
| **5** | **Analisis Masalah Masyarakat** | ❌ Tidak relevan | ✅ Wajib (dengan data + foto) |
| **6** | **Partisipasi Masyarakat** | ❌ Tidak ditekankan | ✅ KRITIS (mekanisme detail) |
| **7** | **Keberlanjutan Program** | ❌ Tidak diwajibkan | ✅ Wajib (post-implementation) |
| **8** | **Minimum Mahasiswa Tim** | ❌ Optional | ✅ 4 mahasiswa (wajib) |
| **9** | **SKS Mahasiswa** | ❌ Optional | ✅ Wajib (learning outcomes) |
| **10** | **Batasan Geografis** | ❌ Tidak ada | ✅ Maksimal 200 km |
| **11** | **Aturan 50% Anggaran** | ❌ Tidak ada | ✅ Wajib (minimum untuk masyarakat) |
| **12** | **Surat Kesanggupan Mitra** | ❌ Optional | ✅ Wajib |
| **13** | **Ethical Clearance** | ✅ Wajib (subjek manusia) | ❌ Tidak diwajibkan |
| **14** | **Dokumentasi Fotografi** | ❌ Optional | ✅ Wajib |
| **15** | **Publikasi Jurnal** | ✅ WAJIB (Q1/Q2 Scopus) | ❌ Optional (jarang) |
| **16** | **Prototipe/Paten** | ✅ Penelitian Terapan | ❌ Tidak diwajibkan |
| **17** | **Institusi Masyarakat** | ❌ Tidak relevan | ✅ Wajib (documented) |
| **18** | **Site Visit Monev** | ❌ Tidak ada | ✅ Optional |
| **19** | **Testimonial Beneficiary** | ❌ Tidak ada | ✅ Wajib |
| **20** | **Durasi Tipikal** | 1-3 tahun | 6-12 bulan |
| **21** | **Besaran Dana** | Rp 30-500 juta | Rp 20-200 juta |
| **22** | **Fokus Evaluasi** | Novelty + Rigor metodologi | Problem analysis + Sustainability |

---

## 11. Rekomendasi Strategis

### 11.1 Untuk Dosen/Peneliti

#### A. Pemilihan Jenis Program

**Pilih PENELITIAN jika:**
- Fokus utama: menghasilkan pengetahuan baru atau teori
- Target: publikasi jurnal internasional (Q1/Q2 Scopus)
- Memiliki roadmap penelitian 5 tahun yang jelas
- Dapat mengukur kemajuan menggunakan TKT
- Tim kecil (2-5 orang) dengan focus pada rigor metodologi

**Pilih PENGABDIAN jika:**
- Fokus utama: menyelesaikan masalah konkret masyarakat
- Target: perubahan nyata dan keberlanjutan di masyarakat
- Ada mitra masyarakat yang committed (maksimal 200 km)
- Melibatkan minimal 4 mahasiswa dalam field work
- Dapat mengalokasikan minimum 50% anggaran langsung ke masyarakat

#### B. Persiapan Proposal

**Untuk Penelitian:**
1. Investasikan waktu substansial pada **literature review** dan dokumentasi state-of-the-art
2. **TKT assessment** harus akurat - incorrect TKT dapat diskualifikasi
3. Roadmap 5 tahun harus realistis dan progressive
4. Budget fokus pada: metodologi, data collection, publication costs
5. Target publikasi harus ambisius sesuai tier institusi

**Untuk Pengabdian:**
1. **Analisis masalah masyarakat** harus komprehensif dengan data + fotografi
2. Pastikan mitra sudah identified dan committed (Surat Kesanggupan)
3. Mekanisme **partisipasi masyarakat** harus jelas dan autentik
4. **Keberlanjutan** harus direncanakan sejak awal (bukan afterthought)
5. Budget minimum 50% untuk material/equipment masyarakat
6. Dokumentasi learning outcomes mahasiswa (SKS)

### 11.2 Untuk LPPM Institusi

#### A. Pelatihan Fakultas

**Sistem BIMA:**
- Lakukan training sistematis untuk dosen baru
- Fokus pada perbedaan antara penelitian dan pengabdian
- Latih TKT assessment (untuk penelitian)
- Latih analisis masalah masyarakat (untuk pengabdian)

**Mengurangi Kesalahan Administratif:**
- Buat checklist per tahap untuk penelitian dan pengabdian
- Sediakan template proposal institusional
- Verifikasi SINTA ID dan PDDIKTI status sebelum submission

#### B. Dukungan Strategis

**Kolaborasi Penelitian:**
- Dorong research collaboration antar-institusi
- Fasilitasi international research partnerships
- Support publikasi jurnal Q1/Q2 (biaya APC, proofreading, dll.)

**Partnership Pengabdian:**
- Bangun database mitra masyarakat potensial dalam radius 200 km
- Fasilitasi MoU dengan komunitas/kelompok masyarakat
- Support site visit awal sebelum proposal submission

#### C. Quality Assurance

**Internal Review:**
- Lakukan internal peer review sebelum submit ke BIMA
- Verifikasi alignment dengan SDGs, Asta Cita, RIRN, IKU
- Check budget compliance (terutama 50% rule untuk pengabdian)

**Monitoring Berkala:**
- Monitor progress report submission
- Ensure output luaran tercapai on-time
- Prevent outstanding obligations (luaran tanggungan)

### 11.3 Untuk Kemdikbudristek / BIMA System

#### A. User Experience

**Simplifikasi Interface:**
- Kurangi conditional field logic yang membingungkan
- Sediakan tooltip/help text untuk setiap field
- Mobile-responsive interface untuk field work

**AI-Assisted Tools:**
- Suggestion systems untuk TKT assessment
- Auto-check common errors sebelum submission
- Rekomendasi metodologi based on past proposals

#### B. Peningkatan Integrasi

**Database Synchronization:**
- Improve SINTA dan PDDIKTI sync speed
- Real-time update status (tidak delay beberapa hari)
- Auto-update institutional affiliations

**Disciplinary Sensitivity:**
- TKT framework yang lebih flexible untuk social sciences/humanities
- Output categories yang accommodate diverse disciplines
- Evaluation rubrics yang context-specific

---

## 12. Referensi dan Sumber

### 12.1 Dokumen Resmi Kemdikbudristek

1. **Panduan Teknis Pengusulan Proposal Penelitian dan Pengabdian kepada Masyarakat 2023 melalui BIMA**  
   Repository Universitas Negeri Gorontalo  
   URL: https://repository.ung.ac.id/get/kms/31313/

2. **Buku Panduan Penelitian dan Pengabdian kepada Masyarakat 2025**  
   LLDIKTI Wilayah 6 Kemdikbudristek  
   URL: https://lldikti6.id/wp-content/uploads/2025/03/Buku-Panduan-Penelitian-dan-Pengabdian-kepada-Masyarakat-2025__b12ecfea.pdf

3. **Panduan Pengelolaan Penelitian dan Pengabdian kepada Masyarakat Tahun 2025**  
   Universitas Hasanuddin (Pedoman DPPM)  
   URL: https://pdlppm.unhas.ac.id/fileupload/2025/panduan_20250131170806_2025.pdf

4. **Panduan Teknis Usulan BIMA Ver 01-4**  
   LPPM Universitas Trunojoyo Madura  
   URL: https://lppm.trunojoyo.ac.id/wp-content/uploads/2023/09/Panduan-Teknis-Usulan-BIMA-Ver-01-4.pdf

5. **Format Proposal BIMA - Template Usulan Substansi**  
   Scribd Document (Official Template)  
   URL: https://id.scribd.com/document/697628511/Format-proposal-BIMA

### 12.2 Tutorial dan Sosialisasi

6. **Tutorial Usulan Proposal Penelitian di Website BIMA**  
   LPPM Universitas Trunojoyo Madura  
   URL: https://lppm.trunojoyo.ac.id/tutorial-usulan-proposal-penelitian-di-website-bima/

7. **Video Tutorial: Tahap Pengajuan Proposal di BIMA**  
   YouTube Channel: Dunia Dosen  
   URL: https://www.youtube.com/watch?v=3PHoT7rGnMs

8. **Video Tutorial: Pengisian Laporan Kemajuan dan SPTB pada Aplikasi BIMA**  
   LLDIKTI Wilayah 4  
   URL: https://lldikti4.kemdiktisaintek.go.id/wp-content/uploads/2022/09/PENGISIAN-LAPORAN-KEMAJUAN-DAN-SPTB-PADA-APLIKASI-edit-26092022.pdf

### 12.3 Artikel dan Analisis

9. **TKT Penelitian: Pengertian, Tingkatan, dan Cara Menghitungnya**  
   Penerbit Deepublish  
   URL: https://penerbitdeepublish.com/tkt-penelitian/

10. **Penelitian Dosen Pemula: Panduan dan Persyaratan**  
    Dunia Dosen  
    URL: https://duniadosen.com/penelitian-dosen-pemula/

11. **Tahap Pengajuan Proposal di BIMA: Panduan Lengkap**  
    Dunia Dosen  
    URL: https://duniadosen.com/tahap-pengajuan-proposal/

12. **Contoh Proposal Hibah Dosen Pemula**  
    Dunia Dosen  
    URL: https://duniadosen.com/contoh-proposal-hibah-dosen-pemula/

### 12.4 Monitoring dan Evaluasi

13. **Pedoman Monev RIKUB 2025**  
    LPPM Universitas Diponegoro  
    URL: https://lppm.undip.ac.id/wp-content/uploads/Pedoman-Monev-RIKUB-2025a.pdf

14. **Panduan Revisi Proposal dan RAB di BIMA**  
    LPPM Universitas Sebelas Maret  
    URL: https://lppm.uns.ac.id/wp-content/uploads/2024/06/Panduan-Revisi-Proposal-dan-RAB-di-BIMA.pdf

### 12.5 Kebijakan dan Framework Nasional

15. **Bidang Fokus Penelitian dan Pengabdian - E-Catalog**  
    BIMA Kemdikbudristek  
    URL: https://bima.kemdiktisaintek.go.id/e-catalog/public/bidang-fokus

16. **Kemdiktisaintek Luncurkan Program Penelitian dan Pengabdian kepada Masyarakat Tahun 2025**  
    Kabar Dikti Kemdikbudristek  
    URL: https://kemdiktisaintek.go.id/kabar-dikti/kabar/kemdiktisaintek-luncurkan-program-penelitian-dan-pengabdian-kepada-masyarakat-tahun-2025/

### 12.6 Portal Resmi

17. **BIMA - Basis Informasi Penelitian dan Pengabdian kepada Masyarakat**  
    Portal Resmi Kemdikbudristek  
    URL: https://bima.kemdiktisaintek.go.id

18. **SINTA - Science and Technology Index**  
    Portal Resmi  
    URL: https://sinta.kemdikbud.go.id

19. **PDDIKTI - Pangkalan Data Pendidikan Tinggi**  
    Portal Resmi  
    URL: https://pddikti.kemdikbud.go.id

### 12.7 Institutional Guidelines

20. **Panduan Penelitian dan Pengabdian Universitas Airlangga 2023**  
    LPPM Universitas Airlangga  
    URL: https://lppm.unair.ac.id/images/Informasi_KKN_BBM_Pengmas/Pengmas/2023/Panduan-Pengelolaan-Penelitian-dan-Pengabdian-kepada-Masyarakat-Tahun-2023.pdf

21. **Buku Pedoman PPM LPPM 2020**  
    IAIM Bima  
    URL: https://iaimbima.ac.id/wp-content/uploads/2021/03/4.-BUKU-PEDOMAN-PPM-LPPM-2020.pdf

### 12.8 Alignment dengan Framework Global

22. **Sustainable Development Goals (SDGs) Alignment**  
    UN 2030 Agenda - Indonesia Context  
    Berbagai referensi dalam panduan BIMA tentang integrasi SDGs

23. **Presidential Asta Cita (9 Strategic Initiatives)**  
    Framework prioritas pemerintah Indonesia  
    Terintegrasi dalam proposal submission BIMA

24. **RIRN (Rencana Inisiatif Riset Nasional)**  
    National Research Priority Plan  
    Kemdikbudristek Research Priority Framework

---

## Penutup

Dokumen ini memberikan analisis komprehensif tentang perbedaan struktural, formulir, dan persyaratan antara proposal **Penelitian** dan **Pengabdian kepada Masyarakat** dalam sistem BIMA Kemdikbudristek. 

**Kesimpulan Utama:**
- Meskipun menggunakan platform dan arsitektur pengajuan yang sama (5 tahap), kedua jenis program memiliki tujuan, struktur data, kriteria evaluasi, dan ekspektasi output yang **sangat berbeda**
- **Penelitian** menekankan: TKT progression, scientific novelty, metodologi rigor, dan publikasi jurnal internasional
- **Pengabdian** menekankan: analisis masalah masyarakat, partisipasi komunitas, keberlanjutan program, dan perubahan nyata di tingkat masyarakat

**Rekomendasi:**
- Dosen harus memilih jenis program berdasarkan tujuan sebenarnya (knowledge advancement vs. community empowerment)
- LPPM institusi harus menyediakan training dan support yang berbeda untuk penelitian dan pengabdian
- Proposal harus dirancang sejak awal dengan pemahaman jelas tentang perbedaan persyaratan dan evaluasi

---

**Dokumen ini disusun berdasarkan riset mendalam menggunakan 60+ sumber resmi dari Kemdikbudristek, panduan institusional perguruan tinggi, dan dokumentasi sistem BIMA.**

**Terakhir Diperbarui:** 2025-11-09  
**Status:** Final - Approved for Reference
