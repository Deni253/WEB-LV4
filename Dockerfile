FROM php:8.2-apache

RUN a2enmod rewrite

# Copy all files
COPY . /var/www/html/

# Set permissions AFTER copying
RUN mkdir -p /var/www/html/slike && \
    chown -R www-data:www-data /var/www/html/slike && \
    chmod -R 755 /var/www/html/slike