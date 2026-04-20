#!/bin/bash
composer install --no-interaction --optimize-autoloader
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate --force
php artisan db:seed --force        

echo "Fixing storage permissions"
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

php-fpm