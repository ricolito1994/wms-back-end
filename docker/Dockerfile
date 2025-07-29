FROM php:8.3-fpm-alpine

# -------------------------------------------------------
# Install dependencies
# -------------------------------------------------------
RUN apk add --no-cache bash curl git zip unzip supervisor shadow busybox-extras \
    libzip-dev icu-dev libpng-dev libjpeg-turbo-dev libwebp-dev oniguruma-dev \
    linux-headers autoconf make g++ openssl

# -------------------------------------------------------
# Install PHP extensions (Redis + Opcache)
# -------------------------------------------------------
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql zip intl gd \
    && pecl install redis \
    && docker-php-ext-enable redis opcache

# -------------------------------------------------------
# Copy final php.ini (PROD baked in)
# -------------------------------------------------------
COPY docker/php.prod.ini /usr/local/etc/php/php.ini

# -------------------------------------------------------
# Install Composer
# -------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------------------------------------
# Create user
# -------------------------------------------------------
RUN useradd -G www-data,root -u 1000 -d /home/wms wms \
    && mkdir -p /home/wms/.composer \
    && chown -R wms:wms /home/wms

WORKDIR /var/www

# -------------------------------------------------------
# Copy only composer files for cache
# -------------------------------------------------------
COPY --chown=wms:wms composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader

# -------------------------------------------------------
# Copy Laravel app
# -------------------------------------------------------
COPY --chown=wms:wms . .

# -------------------------------------------------------
# Supervisor config
# -------------------------------------------------------
COPY docker/supervisor.conf /etc/supervisord.conf

# -------------------------------------------------------
# Entrypoint
# -------------------------------------------------------
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER wms

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
