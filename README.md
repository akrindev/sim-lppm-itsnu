# SIM PPM — Sistem Informasi Manajemen Penelitian dan Pengabdian kepada Masyarakat

Pemangku Kepentingan: Rektor ITS NU Pekalongan, Kepala LPPM, Departemen TI Universitas, Pengembang Utama, QA Lead, Perwakilan Dosen

## 1. Pendahuluan & Tujuan

Dokumen ini menguraikan persyaratan fungsional dan non-fungsional untuk Sistem Informasi Manajemen Penelitian dan Pengabdian kepada Masyarakat (SIM PPM) untuk Institut Teknologi dan Sains Nahdlatul Ulama (ITS NU) Pekalongan.

### 1.1 Pernyataan Masalah

Proses saat ini untuk mengelola hibah penelitian dan pengabdian masyarakat di LPPM ITS NU Pekalongan masih manual, tidak efisien, dan kurang memberikan visibilitas tingkat tinggi bagi pimpinan universitas. Proses ini bergantung pada file yang terpisah dan entri data manual, yang menyebabkan:

- Keterlambatan Operasional: Pemrosesan proposal, penugasan review, dan pelacakan status yang lambat.
- Kurangnya Transparansi: Dosen memiliki visibilitas terbatas terhadap status pengajuan mereka, dan Rektor tidak memiliki pandangan agregat secara real-time terhadap output dan aktivitas penelitian universitas.
- Masalah Integritas Data: Risiko kehilangan data, informasi yang tidak konsisten, dan akses yang tidak sah.
- Pelaporan yang Membebani: Pembuatan laporan untuk evaluasi internal, perencanaan strategis, atau akreditasi eksternal adalah tugas manual yang memakan waktu bagi staf LPPM dan pimpinan.

### 1.2 Solusi yang Diusulkan

SIM PPM akan menjadi platform berbasis web yang terpusat dan mengotomatisasi seluruh siklus hidup pengelolaan hibah penelitian dan pengabdian masyarakat. Sistem ini akan menyediakan antarmuka berbasis peran yang berbeda untuk Dosen, Reviewer, Administrator LPPM, dan dashboard eksekutif hanya-baca untuk Rektor guna menyederhanakan pengajuan, evaluasi, pelaporan, dan pengawasan strategis.

### 1.3 Visi Produk

Menciptakan satu sumber kebenaran untuk semua aktivitas penelitian dan pengabdian masyarakat di ITS NU Pekalongan, mendorong budaya efisiensi dan transparansi yang memberdayakan akademisi, administrator, dan pimpinan universitas dengan wawasan berbasis data.

## 2. Tujuan dan Metode Keberhasilan

Tujuan | Metode Keberhasilan
---|---
Meningkatkan Efisiensi Operasional | - Mengurangi rata-rata waktu dari pengajuan proposal hingga keputusan akhir sebesar 50% dalam tahun akademik pertama penggunaan.
 | - Mengurangi waktu yang dihabiskan staf LPPM untuk tugas administratif sebesar 40%.
Meningkatkan Pengalaman Pengguna | - Mencapai skor kepuasan pengguna minimal 4,0/5,0 dari 80% dosen dan reviewer dalam survei pasca-peluncuran.
Meningkatkan Pengawasan Pimpinan | - Memungkinkan Rektor mengakses metrik penelitian utama secara real-time melalui dashboard, mengurangi ketergantungan pada laporan yang disiapkan secara manual sebesar 100%.
 | - Mencapai skor kepuasan tinggi dari kantor Rektor terkait ketersediaan dan kejelasan data.
Memastikan Integritas & Keamanan Data | - 100% data hibah disimpan dan dikelola dalam sistem.
 | - Tidak ada pelanggaran keamanan atau insiden akses data yang tidak sah.

## 3. Persona Pengguna

- Dr. Ani Suryani (Dosen)
  - Bio: Seorang dosen yang melek teknologi tetapi memiliki waktu terbatas di ITS NU Pekalongan.
  - Masalah: Frustrasi dengan proses yang tidak transparan dan harus mengejar staf untuk pembaruan status.
  - Kebutuhan: Antarmuka sederhana untuk mengajukan proposal, mengunggah laporan, dan melacak status aplikasi.

- Bapak Budi Hartono (Administrator LPPM)
  - Bio: Kepala LPPM di ITS NU Pekalongan, bertanggung jawab atas operasi siklus hibah.
  - Masalah: Kewalahan dengan pelacakan manual dan penyusunan laporan untuk pimpinan.
  - Kebutuhan: Dashboard yang kuat untuk mengelola seluruh sistem, dari peran pengguna hingga status proposal, dan menghasilkan laporan dengan mudah.

- Prof. Chandra Wijaya (Reviewer)
  - Bio: Seorang profesor senior yang diminta untuk meninjau proposal.
  - Masalah: Terganggu oleh penerimaan proposal melalui email tanpa template umpan balik standar.
  - Kebutuhan: Portal khusus untuk melihat proposal yang ditugaskan, menggunakan rubrik penilaian yang jelas, dan mengirimkan evaluasi dengan efisien.

- Dr. Ahmad Al-Faruqi (Rektor)
  - Bio: Rektor ITS NU Pekalongan, berfokus pada pertumbuhan strategis dan keunggulan akademik.
  - Masalah: Tidak memiliki akses langsung ke data tentang output penelitian, tren, dan produktivitas fakultas.
  - Kebutuhan: Dashboard tingkat tinggi yang mudah dipahami dengan indikator kinerja utama (KPI) untuk memantau kesehatan penelitian universitas dan membuat keputusan strategis yang terinformasi.

## 4. Fitur & Cerita Pengguna

### Modul 1: Sistem Inti & Manajemen Pengguna

Epik: Autentikasi Pengguna & Profil

- Cerita 1.1: Sebagai Dosen baru, saya ingin mendaftar akun menggunakan email universitas saya.
- Cerita 1.2: Sebagai Pengguna, saya ingin masuk dengan aman dan mereset kata sandi saya.
- Cerita 1.3: Sebagai Pengguna, saya ingin mengelola informasi profil saya.
- Cerita 1.4: Sebagai Admin, saya ingin melihat daftar semua pengguna dan menetapkan/mengubah peran mereka (Dosen, Reviewer, Admin, Rektor).

### Modul 2: Alur Kerja Dosen

Epik: Pengajuan Proposal & Laporan

- Cerita 2.1: Sebagai Dosen, saya ingin dashboard yang menampilkan proposal aktif saya dan tenggat waktu penting.
- Cerita 2.2: Sebagai Dosen, saya ingin mengajukan proposal baru melalui formulir panduan.
- Cerita 2.3: Sebagai Dosen, saya ingin menerima notifikasi sistem tentang status proposal saya.
- Cerita 2.4: Sebagai Dosen, saya ingin mengunggah laporan kemajuan, laporan akhir, dan output untuk proposal saya yang diterima.

### Modul 3: Alur Kerja Administrator (LPPM)

Epik: Manajemen Siklus Hibah

- Cerita 3.1: Sebagai Admin, saya ingin dashboard dengan metrik operasional utama.
- Cerita 3.2: Sebagai Admin, saya ingin menetapkan dan mengelola tenggat waktu untuk siklus hibah.
- Cerita 3.3: Sebagai Admin, saya ingin melihat semua proposal yang diajukan dan menetapkan Reviewer.
- Cerita 3.4: Sebagai Admin, saya ingin memantau kemajuan review dan melihat skor.
- Cerita 3.5: Sebagai Admin, saya ingin menetapkan status akhir proposal ("Diterima" atau "Ditolak").
- Cerita 3.6: Sebagai Admin, saya ingin menghasilkan dan mengekspor laporan ringkasan.

### Modul 4: Alur Kerja Reviewer

Epik: Evaluasi Proposal

- Cerita 4.1: Sebagai Reviewer, saya ingin dashboard yang mencantumkan semua proposal yang ditugaskan kepada saya.
- Cerita 4.2: Sebagai Reviewer, saya ingin melihat dan mengunduh dokumen proposal.
- Cerita 4.3: Sebagai Reviewer, saya ingin mengisi formulir penilaian digital yang terstandarisasi.
- Cerita 4.4: Sebagai Reviewer, saya ingin mengirimkan evaluasi saya yang telah selesai.

### Modul 5: Alur Kerja Rektor (Pimpinan)

Epik: Pengawasan Eksekutif & Pelaporan

- Cerita 5.1: Sebagai Rektor, saya ingin masuk ke dashboard khusus hanya-baca untuk melihat KPI penelitian dan pengabdian masyarakat universitas secara keseluruhan.
- Cerita 5.2: Sebagai Rektor, saya ingin dashboard menampilkan metrik utama seperti: total proposal yang diajukan, total pendanaan yang diminta/disetujui, tingkat penerimaan, dan jumlah output yang diterbitkan.
- Cerita 5.3: Sebagai Rektor, saya ingin memfilter data dashboard berdasarkan tahun akademik dan fakultas/departemen untuk menganalisis tren dan kinerja.
- Cerita 5.4: Sebagai Rektor, saya ingin melihat dan mengunduh laporan ringkasan yang sama yang dapat dihasilkan Admin untuk analisis strategis saya sendiri.

## 5. Persyaratan Non-Fungsional (NFR)

Kategori | Persyaratan
---|---
Kinerja | - Semua halaman harus dimuat dalam waktu kurang dari 2 detik. Dashboard Rektor, dengan agregasi datanya, harus dimuat dalam waktu kurang dari 4 detik.
 | - Pengunggahan file (hingga 5MB) harus selesai dalam waktu 10 detik.
Keamanan | - Kontrol Akses Berbasis Peran (RBAC) harus diterapkan secara ketat. Pengguna hanya dapat mengakses data yang diizinkan oleh peran mereka (misalnya, peran Rektor memiliki akses hanya-baca ke data dan laporan yang teragregasi).
 | - Semua kata sandi pengguna harus di-hash. Semua transmisi data harus melalui HTTPS.
Skalabilitas | - Sistem harus berfungsi dengan andal dengan hingga 500 pengguna bersamaan dan menangani setidaknya 1.000 proposal baru per tahun akademik.
Kegunaan | - Antarmuka pengguna harus intuitif dan memerlukan pelatihan minimal. Dashboard Rektor harus menyajikan data kompleks dalam format grafis yang jelas.
Keandalan | - Sistem harus memiliki target waktu aktif 99,9%.
 | - Cadangan basis data otomatis reguler harus diterapkan.

---

Untuk catatan pengembangan, kontribusi, atau menjalankan proyek secara lokal, lihat file repositori dan bagian `README` di modul individu. README ini secara sengaja berfokus pada persyaratan produk dan metadata proyek.
