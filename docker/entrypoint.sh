#!/bin/bash

set -e
set -o pipefail

echo "APP_ENV is ${APP_ENV}"

APP_DIR="/var/www"

# -------------------------------
# Ensure .env exists
# -------------------------------
#if [ ! -f "${APP_DIR}/.env" ]; then
    if [ "${APP_ENV}" = "production" ]; then
        echo "📝 .env not found. Copying from .env.production.example..."
        cp "${APP_DIR}/.env.production.example" "${APP_DIR}/.env"
    else
        echo "📝 .env not found. Copying from .env.development.example..."
        cp "${APP_DIR}/.env.development.example" "${APP_DIR}/.env"
    fi
#else
 #   echo "✅ .env already exists."
#fi

cd "${APP_DIR}"

# -------------------------------
# Fix permissions
# -------------------------------
# echo "🔧 Setting correct permissions..."
# chown -R www-data:www-data storage bootstrap/cache
# chmod -R 775 storage bootstrap/cache

# -------------------------------
# Wait for database
# -------------------------------
MAX_TRIES=5
COUNT=0
echo "⏳ Waiting for database to be ready..."
until php -r "new PDO(getenv('DB_CONNECTION') . ':host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" > /dev/null 2>&1; do
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
php artisan key:generate
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Always run migrations safely
echo "🛠️ Running migrations..."
php artisan migrate --force

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
