#!/bin/bash

set -e
set -o pipefail

echo "APP_ENV is $APP_ENV"

# -------------------------------
# Ensure .env exists
# -------------------------------
if [ ! -f /var/www/html/.env ]; then
    if [ "$APP_ENV" = "production" ]; then
        echo "📝 .env not found. Copying from .env.production.example..."
        cp /var/www/html/.env.production.example /var/www/html/.env
    else
        echo "📝 .env not found. Copying from .env.development.example..."
        cp /var/www/html/.env.development.example /var/www/html/.env
    fi
else
    echo "✅ .env already exists."
fi

cd /var/www/html

# -------------------------------
# Fix permissions
# -------------------------------
echo "🔧 Setting correct permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# -------------------------------
# Wait for database
# -------------------------------
MAX_TRIES=5
COUNT=0
echo "⏳ Waiting for database to be ready..."
until php artisan migrate:status > /dev/null 2>&1; do
    ((COUNT++))
    if [ "$COUNT" -ge "$MAX_TRIES" ]; then
        echo "❌ Database not reachable after $((MAX_TRIES * 5)) seconds. Exiting."
        exit 1
    fi
    echo "⏳ Attempt $COUNT/$MAX_TRIES: Still waiting..."
    sleep 5
done
echo "✅ Database is ready!"

# -------------------------------
# Laravel setup
# -------------------------------
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔍 Checking if migrations already ran..."
if ! php artisan migrate:status | grep -q 'Yes'; then
    echo "🛠️ Running migrations..."
    php artisan migrate --force
else
    echo "✅ Migrations already applied."
fi

# -------------------------------
# Fix bad log channel
# -------------------------------
if grep -q "LOG_CHANNEL=stackOA" .env; then
    echo "⚠️ Invalid LOG_CHANNEL=stackOA detected. Reverting to 'stack'."
    sed -i 's/LOG_CHANNEL=stackOA/LOG_CHANNEL=stack/' .env
fi

# -------------------------------
# Start Supervisor
# -------------------------------
echo "🚀 Starting Supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
