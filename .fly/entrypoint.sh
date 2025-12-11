#!/usr/bin/env bash

# Fail on any error
set -e

# 1. Setup SQLite Database on Persistent Volume
# We do this FIRST because the application needs the DB to start/migrate
if [ -d "/data" ]; then
    echo "Configuring /data volume..."
    
    # Create the database file if it doesn't exist
    if [ ! -f "/data/database.sqlite" ]; then
        echo "Creating empty database.sqlite in /data"
        touch /data/database.sqlite
    fi
    
    # Ensure permissions are correct for the www-data user
    echo "Setting permissions for /data..."
    chown -R www-data:www-data /data
    chmod -R 775 /data
fi

# 2. Clear and Cache Configuration
echo "Caching configuration..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Run Migrations
# This will create the tables in the empty /data/database.sqlite
echo "Running migrations..."
php artisan migrate --force

# 4. Start Supervisord
echo "Starting Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf