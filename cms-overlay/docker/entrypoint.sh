#!/bin/sh
set -eu
cd /var/www

if [ -f composer.json ]; then
  composer install --no-interaction --prefer-dist --no-ansi
fi

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ -f artisan ]; then
  php artisan key:generate --force --no-interaction >/dev/null 2>&1 || true
fi

# Migrate only the Laravel-owned schema, and only on the web process.
if [ "${1:-}" = "php" ] && [ "${2:-}" = "artisan" ] && [ "${3:-}" = "serve" ]; then
  php artisan migrate --database=sys --force --no-interaction
fi

exec "$@"
