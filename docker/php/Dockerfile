FROM php:8.1.4-fpm

RUN apt-get update -y && \
    apt-get install -y \
        zlib1g-dev \
        libfreetype6-dev \
        libjpeg-dev \
        libpng-dev

RUN docker-php-ext-install mysqli
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
RUN docker-php-ext-configure gd \
    --enable-gd \
    --with-freetype \
    --with-jpeg \
    && docker-php-ext-install gd
