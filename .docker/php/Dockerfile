FROM php:8.2-fpm-alpine

RUN apk update && apk upgrade && \
	apk add git unzip icu-dev

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-configure intl && docker-php-ext-install intl

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY .docker/php/php.ini /usr/local/etc/php/conf.d/

WORKDIR /srv/
