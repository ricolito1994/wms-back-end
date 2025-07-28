FROM php:8.3-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev \
    supervisor nginx redis-server cron \
    && docker-php-ext-install zip pdo pdo_mysql bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Swoole for Laravel Octane
RUN pecl install swoole && docker-php-ext-enable swoole

# Set working directory
WORKDIR /var/www

# Copy Laravel app
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Copy PHP config
COPY docker/php.ini /usr/local/etc/php/php.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy Nginx config
COPY docker/conf/nginx.dev.conf /etc/nginx/_available/nginx.dev.conf
COPY docker/conf/nginx.prod.conf /etc/nginx/_available/nginx.prod.conf

# Copy Supervisor config
COPY docker/conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy Cron scheduler
COPY docker/cron/scheduler /etc/cron.d/scheduler
RUN chmod 0644 /etc/cron.d/scheduler && crontab /etc/cron.d/scheduler

# Copy entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose HTTP and HTTPS ports
EXPOSE 80 443

CMD ["/usr/local/bin/entrypoint.sh"]
