#!/bin/sh
set -e

# Tunggu database siap (opsional tapi disarankan di entrypoint)
# php artisan db:wait --timeout=30

if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "Composer dependencies not found. Skipping artisan tasks."
    exec "$@"
fi

LOCK_FILE="/var/www/storage/app/.installed"

if [ -f "$LOCK_FILE" ]; then
    echo "Installer lock found. Running migrations..."
    php artisan migrate --force

    echo "Optimizing Laravel..."
    php artisan optimize:clear
    php artisan optimize
else
    echo "Installer lock not found. Skipping migrations and optimize."
fi

# Jalankan perintah utama (php-fpm)
exec "$@"
