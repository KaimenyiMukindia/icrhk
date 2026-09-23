#!/usr/bin/env bash
set -eu

mkdir -p laravel-engine/bootstrap/cache \
    laravel-engine/storage/framework/cache \
    laravel-engine/storage/framework/sessions \
    laravel-engine/storage/framework/views \
    laravel-engine/storage/logs
chmod -R 775 laravel-engine/storage laravel-engine/bootstrap/cache

# The first pull intentionally happens before the live .env is created.
if [ ! -f laravel-engine/.env ]; then
    echo 'Laravel .env not present; runtime commands deferred until configuration is added.'
    exit 0
fi

cd laravel-engine
php artisan migrate --force --no-interaction
php artisan storage:link || true
php artisan config:clear
