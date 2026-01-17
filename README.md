# SIM LPPM ITSNU

**Sistem Informasi Manajemen (SIM) untuk Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM) Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan.**

Platform ini dirancang untuk menyederhanakan dan mengelola seluruh siklus hibah penelitian (Penelitian) dan pengabdian kepada masyarakat (Pengabdian), yang terinspirasi oleh sistem nasional BIMA Kemendikbud namun disesuaikan untuk kebutuhan internal ITSNU Pekalongan.

## Tentang Proyek

SIM LPPM ITSNU adalah aplikasi berbasis web yang komprehensif yang dibangun untuk mendigitalkan dan memusatkan administrasi kegiatan penelitian dan pengabdian kepada masyarakat di lingkungan universitas. Sistem ini memfasilitasi alur kerja yang lancar bagi para dosen dan peneliti, mulai dari pengajuan proposal awal hingga laporan akhir, memastikan transparansi, efisiensi, dan akuntabilitas di seluruh proses.

Platform internal ini bertujuan untuk meniru fungsionalitas sistem hibah nasional seperti BIMA, menyediakan lingkungan yang akrab namun disesuaikan bagi komunitas akademik di ITSNU Pekalongan untuk mengelola kegiatan ilmiah mereka.

## Fitur Utama

Sistem ini dibagi menjadi dua modul utama: **Penelitian** dan **Pengabdian kepada Masyarakat**. Kedua modul memiliki serangkaian fitur canggih yang konsisten:

*   **Pengajuan Proposal:** Dosen dapat dengan mudah mengajukan proposal penelitian atau pengabdian kepada masyarakat melalui formulir online yang terpandu.
*   **Revisi & Tinjauan Proposal:** Alur kerja terstruktur bagi peninjau untuk memberikan umpan balik dan bagi peneliti untuk mengajukan proposal yang direvisi.
*   **Pemantauan Kemajuan:**
    *   **Catatan Harian:** Memungkinkan pencatatan kegiatan dan kemajuan sehari-hari.
    *   **Laporan Kemajuan:** Tonggak pelaporan formal untuk melacak kemajuan proyek.
*   **Manajemen Keuangan:** (Implementasi di masa depan) Pelacakan penggunaan anggaran dan pelaporan keuangan.
*   **Pelaporan Akhir:** Pengajuan hasil akhir proyek, publikasi, dan hasil lainnya.
*   **Manajemen Pengguna:** Kontrol akses berbasis peran untuk administrator, peninjau, dan dosen.
*   **Pelaporan & Analitik:** Pembuatan laporan komprehensif untuk tujuan evaluasi dan akreditasi institusional.

## Memulai

Untuk mendapatkan salinan lokal dan menjalankannya, ikuti langkah-langkah sederhana berikut.

### Prasyarat

*   PHP 8.2 atau lebih tinggi
*   Composer
*   Node.js & NPM
*   MySQL atau basis data lain yang kompatibel

### Instalasi

1.  **Kloning repositori**
    ```sh
    git clone https://github.com/akrindev/sim-lppm-itsnu.git
    cd sim-lppm-itsnu
    ```
2.  **Instal dependensi PHP**
    ```sh
    composer install
    ```
3.  **Instal paket NPM / bun**
    ```sh
    npm install / bun install
    ```
4.  **Siapkan file lingkungan Anda**
    ```sh
    cp .env.example .env
    ```
    *Perbarui variabel `DB_*` di file `.env` Anda dengan kredensial basis data Anda.*

5.  **Hasilkan kunci aplikasi**
    ```sh
    php artisan key:generate
    ```
6.  **Jalankan migrasi dan seeder basis data**
    ```sh
    php artisan migrate --seed
    ```
    *Ini akan membuat tabel yang diperlukan dan mengisinya dengan data awal (misalnya, peran dan pengguna admin).*

7.  **Bangun aset frontend**
    ```sh
    npm run build / bun run build
    ```
8.  **Jalankan server pengembangan**
    ```sh
    php artisan serve
    ```
    Aplikasi Anda akan tersedia di `http://127.0.0.1:8000`.

## Konfigurasi Antrean (Supervisor)

Untuk menjalankan worker antrean secara otomatis di latar belakang pada server produksi, sangat disarankan menggunakan **Supervisor**.

### 1. Instalasi Supervisor
```sh
sudo apt-get update
sudo apt-get install supervisor
```

### 2. Buat File Konfigurasi
Buat file baru di `/etc/supervisor/conf.d/sim-lppm.conf`:
```ini
[program:sim-lppm-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/salju/Workspace/sim-lppm-itsnu/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=salju
numprocs=2
redirect_stderr=true
stdout_logfile=/home/salju/Workspace/sim-lppm-itsnu/storage/logs/worker.log
stopwaitsecs=3600
```

> **Catatan:** Sesuaikan `user` dan path `/home/salju/Workspace/sim-lppm-itsnu` dengan username dan direktori proyek Anda yang sebenarnya.

### 3. Aktifkan Konfigurasi
```sh
sudo reread
sudo update
sudo supervisorctl start sim-lppm-worker:*
```

## Lisensi

Didistribusikan di bawah Lisensi MIT.

*   [Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan](https://itsnupekalongan.ac.id/)
