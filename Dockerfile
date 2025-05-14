FROM php:8.2-apache

# Instalacija potrebnih ekstenzija uključujući PostgreSQL driver
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql


RUN a2enmod rewrite


COPY . /var/www/html/