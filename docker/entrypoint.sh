#!/bin/bash
set -e

cd /var/www
echo "APP is in {$APP_ENV}";
# Set environment
if [ "$APP_ENV" = "production" ]; then
  cp -n .env.production.example .env
else
  cp -n .env.development.example .env
fi

# Permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Laravel setup
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Start cron
# service cron start

exec "$@"
