FROM php:8.3-fpm AS base

RUN apt-get update && apt-get install -y vim unzip libzip-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip

WORKDIR /var/www/html

FROM composer:2.7 AS composer_base

FROM base
COPY --from=composer_base /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
