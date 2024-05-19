FROM php:7.3-apache

USER root

RUN apt-get update && \
    apt-get install -y libpq-dev libzip-dev zip supervisor && \
    docker-php-ext-install pdo pdo_mysql pgsql zip opcache exif

RUN pecl install pcov && \
    docker-php-ext-enable pcov

RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/
WORKDIR /var/www/
