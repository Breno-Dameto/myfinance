#!/usr/bin/env bash

# Remove strict error checking so the server starts even if migration fails
# This helps us debug via the browser/logs instead of crashing the container
set +e

echo "--- Entrypoint Starting ---"

# 1. Setup SQLite Database on Persistent Volume
if [ -d "/data" ]; then
    echo "Configuring /data volume..."
    
    # Create the database file if it doesn't exist
    if [ ! -f "/data/database.sqlite" ]; then
        echo "Creating empty database.sqlite in /data"
        touch /data/database.sqlite
    fi
    
    # Create sessions directory to avoid 419 errors
    if [ ! -d "/data/sessions" ]; then
        echo "Creating /data/sessions..."
        mkdir -p /data/sessions
    fi

    # Ensure permissions are correct for the www-data user
    echo "Setting permissions for /data..."
    chown -R www-data:www-data /data
    chmod -R 775 /data
fi

# 2. Clear Configuration (Safer than caching if env vars are missing)
echo "Clearing configuration..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Run Migrations
echo "Running migrations..."
php artisan migrate --force

# Check if migration failed
if [ $? -ne 0 ]; then
    echo "ERROR: Migrations failed. Check your APP_KEY and database configuration."
else
    echo "Migrations finished successfully."
fi

# 4. Start Supervisord
echo "Starting Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
