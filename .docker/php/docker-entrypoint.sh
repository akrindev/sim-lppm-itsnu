#!/bin/sh
set -e

# Tunggu database siap (opsional tapi disarankan di entrypoint)
# php artisan db:wait --timeout=30

# Jalankan migrasi otomatis
echo "Running migrations..."
php artisan migrate --force

# Optimasi Laravel untuk performa maksimal
echo "Optimizing Laravel..."
php artisan optimize:clear
php artisan optimize

# Jalankan perintah utama (php-fpm)
exec "$@"
