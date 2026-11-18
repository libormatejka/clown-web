FROM php:8.2-fpm-alpine

RUN apk update && apk upgrade && \
	apk add git unzip icu-dev libjpeg-turbo-dev libpng-dev libwebp-dev freetype-dev

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype && docker-php-ext-install gd

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY .docker/php/php.ini /usr/local/etc/php/conf.d/

WORKDIR /srv/
