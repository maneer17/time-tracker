
FROM php:8.1-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer is PHP's package manager (like npm for Node).
# Instead of installing it manually, we copy the binary
# directly from Composer's official image. Clever shortcut.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .


RUN composer install --no-interaction  --optimize-autoloader


RUN chown -R www-data:www-data /var/www/html/storage \
    /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]