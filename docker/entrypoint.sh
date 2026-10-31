#!/bin/sh
set -e

# Buat symbolic link storage jika belum ada
php artisan storage:link || true

# Jalankan cache konfigurasi produksi
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan migrasi database otomatis saat deploy
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
    if [ "$RUN_SEED" = "true" ]; then
        echo "Running seeders..."
        php artisan db:seed --force || true
    fi
fi

# Start supervisord (Nginx + PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisord.conf
