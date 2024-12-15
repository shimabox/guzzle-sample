FROM php:8.3-fpm AS base

RUN apt-get update && apt-get install -y vim unzip libzip-dev gcc make autoconf pkg-config && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

FROM composer:2.7 AS composer_base

FROM base
COPY --from=composer_base /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
