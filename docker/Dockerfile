FROM php:8.3-fpm-alpine

# Install deps
RUN apk add --no-cache bash curl git zip unzip supervisor shadow \
    libzip-dev icu-dev libpng-dev libjpeg-turbo-dev libwebp-dev oniguruma-dev \
    linux-headers autoconf make g++ openssl

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql zip intl gd \
    && pecl install redis \
    && docker-php-ext-enable redis opcache

# Copy php.ini
# COPY docker/php.prod.ini /usr/local/etc/php/php.ini
COPY docker/php.prod.ini /usr/local/etc/php/php.prod.ini
COPY docker/php.dev.ini /usr/local/etc/php/php.dev.ini

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# User = wms
RUN useradd -G www-data,root -u 1000 -d /home/wms wms \
    && mkdir -p /home/wms/.composer \
    && chown -R wms:wms /home/wms

WORKDIR /var/www

# Copy only composer files for caching
COPY --chown=wms:wms composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

# Copy app
COPY --chown=wms:wms . .

# Supervisor config
COPY docker/supervisord.conf /etc/supervisord.conf

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER wms

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]