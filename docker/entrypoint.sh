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
    # sanity checks in production
        echo "📝 .env not found. Copying from .env.production.example..."

        if [ -f "${APP_DIR}/.env.production.example" ]; then 
            echo "${APP_DIR}/.env.production.example found";
        else
            echo "${APP_DIR}/.env.production.example not found";
        fi

        cp "${APP_DIR}/.env.production.example" "${APP_DIR}/.env"

        if [ -f "/etc/nginx/_available/nginx.prod.conf" ]; then 
            echo "/etc/nginx/_available/nginx.prod.conf found";
        else
            echo "/etc/nginx/_available/nginx.prod.conf not found";
        fi

        cp "/etc/nginx/_available/nginx.prod.conf" "/etc/nginx/nginx.conf"
    else
        echo "📝 .env not found. Copying from .env.development.example..."
        cp "${APP_DIR}/.env.development.example" "${APP_DIR}/.env"
        cp "/etc/nginx/_available/nginx.dev.conf" "/etc/nginx/nginx.conf"
    fi
#else
 #   echo "✅ .env already exists."
#fi

echo "✅ Verifying Laravel .env loading..."

if php artisan env > /dev/null 2>&1; then
    echo "✅ Laravel .env loaded successfully: $(php artisan env)"
else
    echo "❌ Failed to load .env file. Exiting..."
    exit 1
fi

echo "✅ Verifying Nginx status..."

if nginx -t 2>&1 | grep -q 'syntax is ok'; then
    echo "✅ Nginx configuration syntax is OK."
else
    echo "❌ Nginx configuration error:"
    nginx -t
    exit 1
fi

# 🧪 Load .env manually (only variables used in DB connection needed here)
export $(grep -v '^#' .env | xargs)

# cd "${APP_DIR}"
echo "🔍 DB_HOST is $DB_HOST"
echo "🔍 DB_PORT is $DB_PORT"
# -------------------------------
# Fix permissions
# -------------------------------
# echo "🔧 Setting correct permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Install dependencies (only if missing)
if [ ! -d "vendor" ]; then
  echo "Installing Composer dependencies..."
  composer install --no-dev --optimize-autoloader
fi

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
# Only generate APP_KEY if not set
if grep -q '^APP_KEY=$' "${APP_DIR}/.env"; then
    echo "🔑 Generating Laravel APP_KEY..."
    php artisan key:generate --force
else
    echo "✅ APP_KEY already exists. Skipping key generation."
fi

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
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf