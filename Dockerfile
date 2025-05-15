FROM php:8.2-apache

# Install PostgreSQL PDO extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set correct permissions for image upload folder
RUN mkdir -p /var/www/html/slike && \
    chown -R www-data:www-data /var/www/html/slike && \
    chmod -R 755 /var/www/html/slike