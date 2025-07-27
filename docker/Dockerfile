FROM php:8.3-fpm

# Install required packages (no nginx or cron)
RUN apt-get update && apt-get install -y \
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

# PHP extensions
RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install swoole-5.1.0 && docker-php-ext-enable swoole

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Configs
COPY docker/cron/scheduler /etc/cron.d/scheduler
RUN chmod 0644 /etc/cron.d/scheduler && crontab /etc/cron.d/scheduler

COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY docker/conf/nginx.dev.conf /etc/nginx/_available/nginx.dev.conf
COPY docker/conf/nginx.prod.conf /etc/nginx/_available/nginx.prod.conf

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Workdir
WORKDIR /var/www/html

# Ports (Octane 8000 only if needed)
EXPOSE 80 443

# Start everything
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
