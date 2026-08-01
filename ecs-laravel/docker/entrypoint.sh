#!/bin/sh
# Runs once every time the container starts (on Railway/Render/any Docker host).
set -e

# Generate an APP_KEY on first boot if one isn't set yet
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config/routes/views for production performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Apply any pending migrations automatically on deploy
php artisan migrate --force

# Make sure /storage/complaints is reachable at /public/storage
php artisan storage:link || true

echo "Starting Apache..."
apache2-foreground
