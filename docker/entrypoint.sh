#!/bin/bash
set -e

cd /var/www

echo "⚙️  Environment: $APP_ENV"

# Copy environment file if it doesn't exist
if [ "$APP_ENV" = "production" ]; then
  cp -n .env.production.example .env
else
  cp -n .env.development.example .env
fi

# Ensure correct permissions for storage and cache
echo "🔧 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

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

# Run migrations
echo "📂 Running migrations..."
php artisan migrate --force

# Optional: start cron if needed (usually handled via Supervisor)
# service cron start

echo "✅ Entrypoint completed. Starting supervisord..."
exec "$@"
