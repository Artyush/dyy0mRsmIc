FROM php:8.4-fpm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip libicu-dev libzip-dev zlib1g-dev \
        libpng-dev libjpeg-dev libfreetype6-dev \
    ; \
    docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg; \
    docker-php-ext-install -j$(nproc) \
        intl zip pdo pdo_mysql gd \
    ; \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    apt-get clean; rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
