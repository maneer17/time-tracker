#!/bin/sh
set -e

# Dev convenience: install dependencies only if vendor/ is missing.
# (In the production image vendor/ is already baked in, so this is skipped.)
if [ ! -d vendor ]; then
  composer install --no-interaction --optimize-autoloader
fi

if [ "$APP_ENV" = "production" ]; then
  # Production: storage is a Docker volume, so changing its owner is safe.
  mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public
  chown -R www-data:www-data storage
else
  # Dev: keep your host user as owner, share access through the group.
  chgrp -R www-data storage bootstrap/cache
  chmod -R g+rwX storage bootstrap/cache
fi

php artisan migrate --force
php artisan storage:link --force

if [ "$APP_ENV" = "production" ]; then
  php artisan config:cache
  php artisan route:cache
else
  php artisan config:clear
  php artisan route:clear
fi

exec php-fpm