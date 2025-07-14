#!/bin/bash
echo "APP_ENV is $APP_ENV"

# Link environment-based nginx config
if [ "$APP_ENV" = "production" ]; then
    echo "Using production Nginx config"
    cp /etc/nginx/_available/nginx.prod.conf /etc/nginx/conf.d/default.conf
else
    echo "Using development Nginx config"
    cp /etc/nginx/_available/nginx.dev.conf /etc/nginx/conf.d/default.conf
fi

# Ensure only default.conf exists
rm -f /etc/nginx/conf.d/nginx.*.conf

# Ensure .env file exists
if [ ! -f /var/www/html/.env ]; then
    if [ "$APP_ENV" = "production" ]; then
        echo "📝 .env file not found. Creating from .env.production.example..."
        cp /var/www/html/.env.production.example /var/www/html/.env
    else
        echo "📝 .env file not found. Creating from .env.dev.example..."
        cp /var/www/html/.env.development.example /var/www/html/.env
    fi
else
    echo "✅ .env file already exists."
fi

# Laravel install commands
cd /var/www/html

composer update --no-interaction --prefer-dist

composer install --no-interaction --prefer-dist

# Check DB connection loop (wait until DB is up)
# Wait until DB is available (max 60 seconds)
MAX_TRIES=1
COUNT=0

echo "⏳ Waiting for database connection..."

until php artisan migrate:status > /dev/null 2>&1; do
    ((COUNT++))
    if [ "$COUNT" -ge "$MAX_TRIES" ]; then
        echo "❌ Database not reachable after $((MAX_TRIES * 5)) seconds. Exiting."
        exit 1
    fi
    echo "⏳ Attempt $COUNT/$MAX_TRIES: Waiting for DB..."
    sleep 5
done

echo "✅ Database is up!"
# Run migration only if migration table is not present or empty
if ! php artisan migrate:status | grep -q "Yes"; then
    echo "🛠️ Running initial migrations..."
    php artisan migrate --force
else
    echo "✅ Migrations already run. Skipping..."
fi

# Laravel cache commands
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start all services
# service php8.3-fpm start
# php-fpm -D
# service redis-server start
# service nginx start

# Run Supervisor (scheduler + websockets + queue)
supervisord -n
