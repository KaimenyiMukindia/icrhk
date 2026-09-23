#!/usr/bin/env bash
set -eu

REPOSITORY_ROOT=$(pwd)
DEPLOY_ROOT=$(cd "$REPOSITORY_ROOT/../../icrhk.nyimuki.com" && pwd)

mkdir -p "$DEPLOY_ROOT/laravel-engine" \
    "$DEPLOY_ROOT/wp-content/plugins/custom-event-registration" \
    "$DEPLOY_ROOT/wp-content/themes/goodsoul"

cp -Rf laravel-engine/. "$DEPLOY_ROOT/laravel-engine/"
cp -Rf wp-content/plugins/custom-event-registration/. "$DEPLOY_ROOT/wp-content/plugins/custom-event-registration/"
cp -Rf wp-content/themes/goodsoul/. "$DEPLOY_ROOT/wp-content/themes/goodsoul/"

mkdir -p "$DEPLOY_ROOT/laravel-engine/bootstrap/cache" \
    "$DEPLOY_ROOT/laravel-engine/storage/framework/cache" \
    "$DEPLOY_ROOT/laravel-engine/storage/framework/sessions" \
    "$DEPLOY_ROOT/laravel-engine/storage/framework/views" \
    "$DEPLOY_ROOT/laravel-engine/storage/logs"
chmod -R 775 "$DEPLOY_ROOT/laravel-engine/storage" "$DEPLOY_ROOT/laravel-engine/bootstrap/cache"

# The first pull intentionally happens before the live .env is created.
if [ ! -f "$DEPLOY_ROOT/laravel-engine/.env" ] \
    || ! grep -q '^APP_KEY=[^[:space:]]' "$DEPLOY_ROOT/laravel-engine/.env" \
    || ! grep -q '^DB_DATABASE=[^[:space:]]' "$DEPLOY_ROOT/laravel-engine/.env"; then
    echo 'Laravel .env not present; runtime commands deferred until configuration is added.'
    exit 0
fi

cd "$DEPLOY_ROOT/laravel-engine"
php artisan migrate --force --no-interaction
php artisan storage:link || true
php artisan config:clear
