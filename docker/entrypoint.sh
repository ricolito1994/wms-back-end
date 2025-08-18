#!/bin/bash
set -e

cd /var/www

echo "⚙️  Environment: $APP_ENV"

# echo "COPYING php.ini ...";
# if [ "$APP_ENV" = "production" ]; then
#    cp /usr/local/etc/php/php.prod.ini /usr/local/etc/php/php.ini
# else
#    cp /usr/local/etc/php/php.dev.ini /usr/local/etc/php/php.ini
# fi

echo "COPYING .env ...";
# Detect env
if [ "$APP_ENV" = "production" ]; then
    ENV_FILE="/var/www/.env.production.example"
else
    ENV_FILE="/var/www/.env.development.example"
fi

# 1️⃣ Overwrite .env from correct example file
if [ -f "$ENV_FILE" ]; then
    echo "📝 Using $ENV_FILE → .env"
    cp -f "$ENV_FILE" /var/www/.env
else
    echo "⚠️ $ENV_FILE not found!"
fi

MAX_TRIES=3
NUM_TRIES=0

# Extract DB_HOST and DB_PORT from .env
DB_HOST=$(grep DB_HOST /var/www/.env | cut -d '=' -f2)
DB_PORT=$(grep DB_PORT /var/www/.env | cut -d '=' -f2)

echo "⏳ Waiting for database ($DB_HOST:$DB_PORT) to be ready..."
until nc -z "$DB_HOST" "$DB_PORT"; do
    NUM_TRIES=$((NUM_TRIES+1))
    if [ "$NUM_TRIES" -ge "$MAX_TRIES" ]; then
        echo "❌ Could not connect to database after $MAX_TRIES attempts, exiting..."
        exit 1
    fi
    echo "   ⏳ DB not ready attempt $NUM_TRIES/$MAX_TRIES..., retrying in 3s..."
    sleep 3
done
echo "✅ Database is ready!"

# Ensure correct permissions for storage and cache
echo "🔧 Setting permissions..."
chown -R wms:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache

# Install optimized dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Laravel setup
echo "🛠️  Setting up Laravel..."
php artisan key:generate --force
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan jwt:secret

# Run migrations
echo "📂 Running migrations..."
php artisan migrate --force

# Optional: start cron if needed (usually handled via Supervisor)
# service cron start

echo "✅ [wms-entrypoint] Init complete, starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
# exec "$@"
