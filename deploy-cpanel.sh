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

# Initialize shared secrets in the existing WordPress configuration once.
# Existing values are preserved so encrypted registration data remains readable.
if [ -f "$DEPLOY_ROOT/wp-config.php" ]; then
    php - "$DEPLOY_ROOT/wp-config.php" <<'PHP'
<?php

$path = $argv[1];
$contents = file_get_contents($path);
if ($contents === false) {
    fwrite(STDERR, "Unable to read wp-config.php\n");
    exit(1);
}

$definitions = [];
foreach (['CER_ENCRYPTION_KEY', 'CER_TICKET_CALLBACK_SECRET'] as $name) {
    if (! preg_match("/define\s*\(\s*['\"]" . preg_quote($name, '/') . "['\"]\s*,/", $contents)) {
        $definitions[] = "define( '{$name}', '" . base64_encode(random_bytes(32)) . "' );";
    }
}

if ($definitions !== []) {
    $block = implode(PHP_EOL, $definitions) . PHP_EOL . PHP_EOL;
    $marker = "/* That's all, stop editing! Happy publishing. */";
    if (strpos($contents, $marker) !== false) {
        $contents = str_replace($marker, $block . $marker, $contents);
    } else {
        $marker = "require_once ABSPATH . 'wp-settings.php';";
        $contents = str_replace($marker, $block . $marker, $contents);
    }
    if (file_put_contents($path, $contents, LOCK_EX) === false) {
        fwrite(STDERR, "Unable to update wp-config.php\n");
        exit(1);
    }
}
PHP
fi

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
