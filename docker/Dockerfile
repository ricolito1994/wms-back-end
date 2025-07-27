FROM php:8.3-fpm

# Install system packages
RUN apt-get update && apt-get install -y \
    cron \
    nginx \
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
    redis-server \
    && docker-php-ext-install pdo pdo_mysql zip opcache bcmath

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Laravel Octane + Swoole
RUN pecl install swoole-5.1.0 && docker-php-ext-enable swoole

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy application
COPY . /var/www/html
WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Cron
COPY docker/cron/scheduler /etc/cron.d/scheduler
RUN chmod 0644 /etc/cron.d/scheduler && crontab /etc/cron.d/scheduler

# PHP config
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Nginx config
COPY docker/conf/nginx.dev.conf /etc/nginx/_available/nginx.dev.conf
COPY docker/conf/nginx.prod.conf /etc/nginx/_available/nginx.prod.conf

# Supervisor config
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000 80 443

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
