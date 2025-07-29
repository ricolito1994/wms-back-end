FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    nginx supervisor cron build-essential pkg-config libssl-dev zlib1g-dev \
    libcurl4-openssl-dev libpq-dev libpcre3-dev libedit-dev libsqlite3-dev libicu-dev \
    libbrotli-dev \
    && docker-php-ext-install pdo_mysql zip opcache \
    && pecl install swoole-5.1.3 redis \
    && docker-php-ext-enable swoole redis opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy PHP config
COPY docker/php.ini /usr/local/etc/php/php.ini

# Copy application source code
COPY . .

# Install Laravel Octane + optimized production dependencies
RUN composer require laravel/octane:^2.0 --no-interaction --no-scripts --no-plugins && \
    php artisan octane:install --server=swoole && \
    composer install --no-dev --optimize-autoloader && \
    php artisan config:cache && php artisan route:cache && php artisan view:cache

# Copy configs
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/conf/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/conf/nginx.prod.conf /etc/nginx/nginx.conf
COPY docker/cron/scheduler /etc/cron.d/laravel

# Set permissions
RUN chmod +x /usr/local/bin/entrypoint.sh \
 && chmod 0644 /etc/cron.d/laravel \
 && crontab /etc/cron.d/laravel

# Expose HTTP port
EXPOSE 80

# Entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
