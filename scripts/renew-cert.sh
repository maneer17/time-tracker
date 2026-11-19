#!/bin/sh
# Renews the Let's Encrypt certificate. Nginx is stopped briefly because
# certbot needs port 80 (standalone mode).
cd "$(dirname "$0")/.."
COMPOSE="docker compose -f docker-compose.prod.yml"

$COMPOSE stop nginx
docker run --rm -p 80:80 -v letsencrypt:/etc/letsencrypt certbot/certbot renew
STATUS=$?
$COMPOSE start nginx
exit $STATUS