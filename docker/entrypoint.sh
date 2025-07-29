#!/bin/bash
set -e

# Ensure permissions
chown -R laravel:laravel /var/www

# Laravel setup
su laravel -c "composer install --no-dev --optimize-autoloader"
su laravel -c "php artisan config:cache"
su laravel -c "php artisan route:cache"
su laravel -c "php artisan view:cache"
su laravel -c "php artisan octane:reload"

exec "$@"
