FROM php:8.3-apache AS base

RUN apt-get update && apt-get install -y vim unzip libzip-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip

WORKDIR /var/www/html

COPY docker/apache/default.conf /etc/apache2/sites-available/000-default.conf

FROM composer:2.7 AS composer_base

FROM base
COPY --from=composer_base /usr/bin/composer /usr/bin/composer

EXPOSE 80
CMD ["apache2-foreground"]
