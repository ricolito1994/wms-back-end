FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    nginx supervisor cron \
    build-essential pkg-config libssl-dev zlib1g-dev \
    && docker-php-ext-install pdo_mysql zip opcache \
    && pecl install redis swoole \
    && docker-php-ext-enable redis swoole opcache

# Copy PHP config
COPY docker/php.ini /usr/local/etc/php/php.ini

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www
COPY . .

# Install Octane and PHP dependencies inside the container (requires network in container)
RUN composer require laravel/octane:^2.0 --no-interaction --no-scripts --no-plugins && \
    php artisan octane:install --server=swoole && \
    composer install --no-dev --optimize-autoloader

# Entrypoint and configs
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/conf/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/conf/nginx.prod.conf /etc/nginx/nginx.conf
COPY docker/cron/scheduler /etc/cron.d/laravel

# Permissions & cron
RUN chmod +x /usr/local/bin/entrypoint.sh \
 && chmod 0644 /etc/cron.d/laravel \
 && crontab /etc/cron.d/laravel

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
