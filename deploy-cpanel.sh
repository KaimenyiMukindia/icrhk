#!/usr/bin/env bash
set -eu
set -o pipefail

REPOSITORY_ROOT=$(pwd)
LOG_FILE="$REPOSITORY_ROOT/deploy-cpanel.log"
exec > >(tee -a "$LOG_FILE") 2>&1

echo "[$(date -u '+%Y-%m-%dT%H:%M:%SZ')] deployment started"
echo "repository root: $REPOSITORY_ROOT"

DEPLOY_ROOT="$REPOSITORY_ROOT/../../icrhk.nyimuki.com"
if [ ! -d "$DEPLOY_ROOT" ]; then
    echo "ERROR: live document root does not exist: $DEPLOY_ROOT"
    exit 1
fi
DEPLOY_ROOT=$(cd "$DEPLOY_ROOT" && pwd)
echo "live document root: $DEPLOY_ROOT"

mkdir -p "$DEPLOY_ROOT/laravel-engine" \
    "$DEPLOY_ROOT/wp-content/plugins/custom-event-registration" \
    "$DEPLOY_ROOT/wp-content/themes/goodsoul"

echo 'copying Laravel application and committed dependencies'
cp -Rf laravel-engine/. "$DEPLOY_ROOT/laravel-engine/"
echo 'copying custom WordPress plugin and theme'
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
    echo 'Laravel .env not configured; runtime commands deferred until configuration is added.'
    echo "[$(date -u '+%Y-%m-%dT%H:%M:%SZ')] deployment completed"
    exit 0
fi

cd "$DEPLOY_ROOT/laravel-engine"
echo 'running Laravel migrations'
php artisan migrate --force --no-interaction
echo 'creating storage link'
php artisan storage:link || true
echo 'clearing Laravel configuration'
php artisan config:clear
echo "[$(date -u '+%Y-%m-%dT%H:%M:%SZ')] deployment completed"
