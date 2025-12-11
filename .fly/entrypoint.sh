#!/usr/bin/env bash

# Fail on any error
set -e

# Run migrations (force for production)
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions for storage/database if it exists (for SQLite)
if [ -d "/var/www/html/storage/database" ]; then
    chown -R www-data:www-data /var/www/html/storage/database
    chmod -R 775 /var/www/html/storage/database
fi

# Also for /data if we use it
if [ -d "/data" ]; then
    chown -R www-data:www-data /data
    chmod -R 775 /data
    # If using /data/database.sqlite, ensure it exists or touch it
    if [ ! -f "/data/database.sqlite" ]; then
        touch /data/database.sqlite
        chown www-data:www-data /data/database.sqlite
    fi
fi

# Start Supervisord (which starts Nginx and PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
