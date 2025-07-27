#!/bin/bash

echo "APP_ENV is $APP_ENV"

# -------------------------------
# Step 1: Link Nginx config based on environment
# -------------------------------
if [ "$APP_ENV" = "production" ]; then
    echo "🌐 Using production Nginx config"
    cp /etc/nginx/_available/nginx.prod.conf /etc/nginx/conf.d/default.conf
else
    echo "🌐 Using development Nginx config"
    cp /etc/nginx/_available/nginx.dev.conf /etc/nginx/conf.d/default.conf
fi
rm -f /etc/nginx/conf.d/nginx.*.conf

# -------------------------------
# Step 2: Ensure .env exists
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
# Step 3: Fix file permissions
# -------------------------------
echo "🔧 Setting correct permissions..."

# Fix Laravel writable dirs
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Fix entire Laravel project (safe if running in container)
chown -R www-data:www-data /var/www/html
find /var/www/html -type f -exec chmod 664 {} \;
find /var/www/html -type d -exec chmod 775 {} \;

# -------------------------------
# Step 4: Laravel Composer setup
# -------------------------------
# echo "📦 Installing Composer dependencies..."
# composer install --no-interaction --prefer-dist --optimize-autoloader

# -------------------------------
# Step 5: Wait for Database
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
# Step 6: Laravel setup
# -------------------------------
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations if not yet run
echo "🔍 Checking if migrations already ran..."
if ! php artisan migrate:status | grep -q 'Yes'; then
    echo "🛠️ Running migrations..."
    php artisan migrate --force
else
    echo "✅ Migrations already applied."
fi

# -------------------------------
# Step 7: Clean up any stale Octane process to avoid port conflicts
# -------------------------------
# echo "🔧 Killing any stale Octane processes on port 8000..."
# pkill -f "artisan octane:start" || true

echo "🔧 Checking if anything is using port 8000..."
if fuser 8000/tcp > /dev/null 2>&1; then
    echo "⚠️ Port 8000 is in use. Killing process..."
    fuser -k 8000/tcp || true
    echo "✅ Port 8000 freed."
else
    echo "✅ Port 8000 is already free."
fi

# -------------------------------
# Step 8: Start Supervisor
# -------------------------------
# echo "🚀 Starting Supervisor..."
# exec supervisord -n
echo "🚀 Starting Supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf