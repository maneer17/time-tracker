#!/bin/sh
# Runs ON THE VM. Updates the code and restarts what changed.
set -e
cd "$(dirname "$0")/.."
COMPOSE="docker compose -f docker-compose.prod.yml"

git pull --ff-only origin main

$COMPOSE up -d --build db redis backend queue scheduler reverb nginx

# nginx caches the backend's IP at startup; refresh it after any recreate
$COMPOSE restart nginx

docker image prune -f
$COMPOSE ps