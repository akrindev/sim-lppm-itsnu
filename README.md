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

*   PHP 8.2+ (disarankan 8.4)
*   Composer
*   Bun (direkomendasikan) atau Node.js & NPM
*   MySQL/MariaDB atau basis data lain yang kompatibel
*   Redis (opsional, jika memakai cache/queue redis)

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
    Composer akan menjalankan `php artisan app:install` otomatis. Jika ingin menggunakan installer web, jalankan `composer install --no-scripts`.
3.  **Jalankan installer (jika belum)**
    ```sh
    php artisan app:install
    ```
    Installer akan membuat `.env`, menjalankan migrasi + seeder, dan membuat admin. Siapkan Cloudflare Turnstile Site Key & Secret Key (wajib). Gunakan `--quick` untuk konfigurasi cepat; `--force` untuk reinstall (menghapus data).
4.  **Instal paket frontend**
    ```sh
    bun install
    # atau
    npm install
    ```
5.  **Bangun aset frontend**
    ```sh
    bun run build
    # atau
    npm run build
    ```
6.  **Jalankan server pengembangan**
    ```sh
    php artisan serve
    ```
    Aplikasi Anda akan tersedia di `http://127.0.0.1:8000`.

### 9. Menggunakan Docker (Opsional - Rekomendasi Dev)

Jika ingin menjalankan proyek menggunakan Docker:

1.  **Salin .env dan sesuaikan konfigurasi layanan**
    ```sh
    cp .env.example .env
    ```
    Pastikan `DB_HOST=mariadb`, `DB_PORT=3306`, `REDIS_HOST=redis`, dan isi `TURNSTILE_SITE_KEY` serta `TURNSTILE_SECRET_KEY`.
2.  **Jalankan Docker Compose**
    ```sh
    docker compose up -d --build
    ```
3.  **Instal dependensi & jalankan installer**
    ```sh
    docker compose exec app composer install
    ```
    Composer akan menjalankan `php artisan app:install` otomatis. Jika ingin memakai installer web, gunakan `docker compose exec app composer install --no-scripts` lalu buka `http://localhost:8000/install`.
4.  **Bangun aset frontend (di host)**
    ```sh
    bun install
    bun run build
    # atau
    npm install
    npm run build
    ```
    Aplikasi akan tersedia di `http://localhost:8000`.

### 10. Integrasi dengan Reverse Proxy (VPS)

Jika VPS sudah memiliki Nginx/Apache/Caddy, jangan arahkan Docker ke port 80/443. Gunakan port default `8000` dan buatlah *Reverse Proxy*.

#### Contoh Nginx (Host):
```nginx
server {
    listen 80;
    server_name sim-lppm.itsnu.ac.id;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

#### Contoh Caddy (Host):
```caddy
sim-lppm.itsnu.ac.id {
    reverse_proxy localhost:8000
}
```

> **Catatan Keamanan:** Database (port 33060) dan Redis (port 63790) pada Docker ini hanya di-bind ke `127.0.0.1` secara default agar tidak bisa diakses langsung dari internet, sehingga tidak akan bentrok dengan MySQL/MariaDB yang mungkin sudah ada di host VPS Anda.

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

## Konfigurasi Server Produksi (Optimal)

Untuk performa terbaik pada PHP 8.4 dan dukungan upload file besar (hingga 100MB), gunakan konfigurasi berikut:

### 1. Nginx Configuration
Simpan di `/etc/nginx/sites-available/sim-lppm.conf`:
```nginx
server {
    listen 80;
    server_name sim-lppm.example.com;
    root /home/user/sim-lppm-itsnu/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    # Kapasitas upload 100MB
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Timeout lebih lama untuk upload file besar
        fastcgi_read_timeout 300;
        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 2. PHP 8.4 Configuration (php.ini)
Sesuaikan nilai berikut pada `/etc/php/8.4/fpm/php.ini`:
```ini
; Upload & Resource Limits
upload_max_filesize = 100M
post_max_size = 105M
memory_limit = 512M
max_execution_time = 300
max_input_time = 300

; OpCache & JIT (Essential for PHP 8.4)
opcache.enable=1
opcache.memory_consumption=192
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.jit=tracing
opcache.jit_buffer_size=128M
```

### 3. PHP-FPM Pool Configuration (www.conf)
Optimasi worker pada `/etc/php/8.4/fpm/pool.d/www.conf`:
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 15
pm.max_requests = 1000
```

## CI/CD dengan GitHub Actions

Proyek ini dilengkapi dengan pipeline CI/CD otomatis melalui GitHub Actions yang berjalan pada branch `main`:

1.  **Test Job:** Melakukan unit & feature tests menggunakan Pest.
2.  **Build Job:** Melakukan build Docker image dengan assets yang sudah dikompilasi Bun, lalu melakukan push ke **GitHub Container Registry (GHCR)**.
3.  **Deploy Job:** Melakukan SSH ke VPS, login ke GHCR, menarik image terbaru, dan melakukan restart container.

### Konfigurasi GitHub Secrets
(hanya untuk deploy)

Agar pipeline berjalan, tambahkan secret berikut di repositori GitHub Anda:

*   `GHCR_TOKEN`: Personal Access Token (PAT) GitHub dengan scope `read:packages` (digunakan oleh VPS untuk pull image).
*   `VPS_HOST`: IP atau Domain VPS.
*   `VPS_USERNAME`: User SSH VPS.
*   `VPS_SSH_KEY`: Private Key SSH untuk akses ke VPS.

## Lisensi

Didistribusikan di bawah Lisensi MIT.

*   [Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan](https://itsnupekalongan.ac.id/)
