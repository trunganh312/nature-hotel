#
#--------------------------------------------------------------------------
# Image Setup
#--------------------------------------------------------------------------
#

FROM php:8.3-fpm

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

# Install dependencies
RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libwebp-dev \
            libxpm-dev \
            libmcrypt-dev \
            zlib1g-dev \
            libzip-dev \
            unzip\
            libonig-dev; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    docker-php-ext-install pdo_mysql zip pcntl pdo_pgsql; \
    docker-php-ext-configure gd --prefix=/usr --with-jpeg --with-webp --with-xpm --with-freetype; \
    docker-php-ext-install gd; \
    php -r 'var_dump(gd_info());'; \
    pecl install redis && docker-php-ext-enable redis; \
    docker-php-ext-install mysqli && docker-php-ext-enable mysqli; \
    docker-php-ext-install opcache && docker-php-ext-enable opcache

# Cài đặt Composer từ curl
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
