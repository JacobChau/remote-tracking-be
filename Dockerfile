FROM composer:latest as composer-build

RUN apk update \
    && apk add \
    libxml2-dev \
    php-soap \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install \
    bcmath \
    exif \
    soap

WORKDIR /app

COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer
COPY . /app
RUN composer dump-autoload --no-scripts --no-dev --optimize

FROM php

RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

COPY . .

COPY --from=composer-build /app/vendor/ /var/www/html/vendor/

RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

RUN chmod +x docker/entrypoint.sh

ENTRYPOINT [ "docker/entrypoint.sh" ]

CMD php artisan serve --host=0.0.0.0 --port=8000
