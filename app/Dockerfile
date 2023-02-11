FROM php:8-fpm

RUN apt-get update \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && apt-get install -y \
    git \
    curl \
    zip \
    unzip

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer self-update
WORKDIR /var/www/html
COPY *.json .
COPY *.php .

RUN composer install