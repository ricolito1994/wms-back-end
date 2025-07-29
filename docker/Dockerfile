# Use an official PHP base image with required extensions
FROM php:8.3-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev nginx redis supervisor \
    && docker-php-ext-install pdo pdo_mysql zip opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Laravel Octane & Swoole
RUN pecl install swoole-5-1-1 \
    && docker-php-ext-enable swoole

# Create Laravel user and directory
RUN useradd -ms /bin/bash laravel
WORKDIR /var/www

# Copy Laravel project
COPY . /var/www
RUN chown -R laravel:laravel /var/www

# Entrypoint and permissions
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Nginx & Supervisor config
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose ports
EXPOSE 80

# Use custom entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord"]
