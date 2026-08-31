#!/bin/bash
set -e

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

chown -R www-data:www-data storage bootstrap/cache

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --ansi
fi

until php artisan db:show > /dev/null 2>&1; do
    echo "Waiting for database connection..."
    sleep 2
done

php artisan migrate --force

exec "$@"
