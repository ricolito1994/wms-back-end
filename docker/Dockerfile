# Base image
FROM php:8.3-cli

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

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Swoole for Laravel Octane
RUN pecl install swoole-5.1.0 && docker-php-ext-enable swoole

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy Laravel app source code
COPY . /var/www/html
WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Fix permissions and ownership (important!)
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 664 {} \; \
    && find /var/www/html -type d -exec chmod 775 {} \;

# Copy configs
COPY docker/cron/scheduler /etc/cron.d/scheduler
RUN chmod 0644 /etc/cron.d/scheduler && crontab /etc/cron.d/scheduler

COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/conf/nginx.dev.conf /etc/nginx/_available/nginx.dev.conf
COPY docker/conf/nginx.prod.conf /etc/nginx/_available/nginx.prod.conf
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose ports
# EXPOSE 80 443

# Laravel Octane runs on port 8000
EXPOSE 8000

# Entrypoint
# CMD ["entrypoint.sh"]
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]