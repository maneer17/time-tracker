#!/bin/bash

echo "Installing packages..."
composer install --no-interaction --optimize-autoloader

echo "Generating app key..."
php artisan key:generate --no-interaction --force

echo "Clearing old cache first..."
php artisan config:clear      
php artisan cache:clear


sleep 5


echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force

echo "Creating storage link..."
php artisan storage:link --force

echo "Fixing storage permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "Caching config..."
php artisan config:cache     
php artisan route:cache

echo "Starting PHP-FPM..."
php-fpm