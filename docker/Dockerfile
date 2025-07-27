# Base image for CLI + Swoole (not FPM)
FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    cron \
    supervisor \
    curl \
    git \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    pkg-config \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip opcache bcmath pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Redis and Swoole extensions
RUN pecl install redis swoole-5.1.0 \
    && docker-php-ext-enable redis swoole

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy Laravel application
COPY . /var/www/html
WORKDIR /var/www/html

# Install Laravel dependencies and Octane
RUN composer install --no-dev --prefer-dist --optimize-autoloader \
    && php artisan octane:install --server=swoole

# Fix file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copy Supervisor and entrypoint configs
COPY docker/cron/scheduler /etc/cron.d/scheduler
RUN chmod 0644 /etc/cron.d/scheduler && crontab /etc/cron.d/scheduler

COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Laravel Octane uses port 8000
EXPOSE 8000

# Entrypoint (which starts Supervisor)
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
