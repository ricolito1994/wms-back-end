FROM php:8.3-fpm

# Install packages
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
    # certbot \
    && docker-php-ext-install pdo pdo_mysql zip opcache bcmath

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Laravel Octane + Swoole
RUN pecl install swoole-5.1.0 \
    && docker-php-ext-enable swoole

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

COPY docker/cron/scheduler /etc/cron.d/scheduler

RUN chmod 0644 /etc/cron.d/scheduler && \
    crontab /etc/cron.d/scheduler

# PHP INI and OPCache
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Nginx conf
COPY docker/conf/nginx.dev.conf /etc/nginx/_available/nginx.dev.conf
COPY docker/conf/nginx.prod.conf /etc/nginx/_available/nginx.prod.conf
# Supervisor conf
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Entry point
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set workdir
WORKDIR /var/www/html

EXPOSE 80 443

CMD ["entrypoint.sh"]
