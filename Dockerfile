FROM php:8.3-fpm

#install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Work directory
WORKDIR /var/www

# Desativa OPcache para desenvolvimento
RUN echo "opcache.enable=0\nopcache.enable_cli=0" > /usr/local/etc/php/conf.d/opcache-dev.ini

#Copy composer.json and composer.lock
COPY composer.json composer.lock ./

# Copy files
COPY . .

# Permissions for storage if needed
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Install composer dependencies
RUN composer install --optimize-autoloader

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
