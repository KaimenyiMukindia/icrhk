# ICRHK Laravel engine

This directory is the Laravel payment and registration service used by the ICRHK WordPress site. Production Composer dependencies and Vite assets are committed, so cPanel does not need Composer, npm, Node, or a build step.

## Deployment shape

WordPress remains the existing public site. The Laravel application is deployed beside it at `laravel-engine/` and is served through `laravel-engine/public/`. The root `.cpanel.yml` assumes cPanel's deployment path is already the existing website document root; it does not copy or replace WordPress core. Laravel reads the WordPress `wp_evt_*` tables through its `wordpress` database connection.

## Requirements

- PHP 8.2 or 8.3. Laravel 12 requires PHP 8.2 or newer; the project deliberately does not fake compatibility with PHP 8.1.
- PHP extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, and `zip`.
- Apache with `mod_rewrite` and an existing MySQL/MariaDB WordPress database.
- cPanel Git Version Control access and a writable Laravel `storage/` and `bootstrap/cache/`.

## First release from a clean clone

Run locally on the release machine:

```powershell
cd laravel-engine
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --classmap-authoritative
npm install
npm run build
cd ..
git add .gitignore .cpanel.yml laravel-engine/composer.json laravel-engine/composer.lock laravel-engine/vendor laravel-engine/public/build laravel-engine/package-lock.json laravel-engine/.env.example laravel-engine/README.md
git commit -m "Prepare self-contained cPanel deployment"
git push origin <branch>
```

Do not add `.env`, `wp-config.php`, logs, uploads, cache, `node_modules/`, or database dumps. The committed `vendor/` and `public/build/` directories are intentional release artifacts.

## cPanel setup

1. In **Git Version Control**, clone this repository.
2. Set the deployment path once in cPanel to the website document root. That directory must contain the checked-out repository root, including `laravel-engine/`; do not edit `.cpanel.yml` per account.
3. Select PHP 8.2 or 8.3 in **MultiPHP Manager** and enable the extensions listed above. The cPanel PHP CLI must be available as `php` on the deployment hook's PATH.
4. Confirm Apache `mod_rewrite` is enabled. The Laravel front controller is `laravel-engine/public/index.php`.
5. Confirm the cPanel database user can read and update the WordPress event tables.

## Configure the server

After the first pull, create `laravel-engine/.env` from `.env.example` and edit every deployment value. Set `APP_KEY`, `APP_URL`, the Laravel-owned `DB_*` values, the WordPress `WP_DB_*` values, `CER_ENCRYPTION_KEY`, `CER_TICKET_CALLBACK_SECRET`, `WORDPRESS_URL`, and the Paystack keys. Use `APP_DEBUG=false` in production. Never commit `.env`.

The deployment hook creates the writable directories automatically. To repair an older installation:

```bash
cd laravel-engine
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs
chmod -R 775 bootstrap/cache storage
```

## Automated deployment tasks

The root `.cpanel.yml` uses only paths relative to the configured deployment directory. On every deployment it creates `storage/framework/{cache,sessions,views}`, `storage/logs`, and `bootstrap/cache`, applies `775` permissions, runs `php artisan migrate --force` when `laravel-engine/.env` already exists, creates the storage link, and clears the configuration cache. It never copies or overwrites `.env`.

The migration command is intentional: Laravel owns `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, and `payment_logs`. The WordPress plugin owns all `wp_evt_*` tables and continues to manage those separately. On a brand-new Laravel installation where `.env` did not exist during the first pull, edit `.env` and run the migration command once from the `laravel-engine` directory; subsequent pulls run it automatically.

If the hosting provider does not allow PHP commands in cPanel deployment hooks, configure the cPanel deployment path normally and run the same commands from a provider-approved post-deploy hook:

```bash
cd laravel-engine
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs
chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan storage:link || true
php artisan config:clear
```

## Every deployment

Push a tested commit, then use cPanel **Deploy HEAD Commit**. If shell access is available, run the pull from the repository checkout directory:

```bash
git pull --ff-only origin <branch>
```

The cPanel deployment uses the tracked Laravel release, including `vendor/` and `public/build/`. It does not install packages and does not touch the existing WordPress core. Edit only the server's untracked `.env` when configuration changes.

## Paystack payments

This application uses Paystack for card and M-Pesa payments. Set `PAYSTACK_PUBLIC_KEY`, `PAYSTACK_SECRET_KEY`, `PAYSTACK_ENV`, and `PAYSTACK_CURRENCY` in `.env`. Keep `PAYSTACK_ENV=live` for production. Also set `WORDPRESS_URL`, `CER_ENCRYPTION_KEY`, and `CER_TICKET_CALLBACK_SECRET`; these are required for payment fulfillment and ticket delivery.

Configure this HTTPS callback in Paystack:

```text
https://YOUR-DOMAIN/laravel-engine/public/api/paystack-webhook
```

The webhook is a POST route in `routes/api.php`, outside the web CSRF middleware. It verifies Paystack's `X-Paystack-Signature` header before updating the WordPress registration and delivering the ticket. Payment fulfillment runs synchronously, so no queue worker or cron job is required.

After deployment, run the health check and an invalid-signature webhook check:

```bash
curl -i https://YOUR-DOMAIN/laravel-engine/public/up
curl -i -X POST https://YOUR-DOMAIN/laravel-engine/public/api/paystack-webhook \
	-H 'Content-Type: application/json' \
	-d '{"event":"charge.success","data":{}}'
```

The first request must return `200`; the second must return `400 invalid_signature`, proving the HTTPS route is reachable and signature protection is active. A real end-to-end payment requires a Paystack test/live key, a real registration row, and Paystack's callback delivery; complete one small test transaction after configuring those `.env` values.

## Verification checklist

- `laravel-engine/vendor/autoload.php` exists on the server.
- `laravel-engine/public/build/manifest.json` exists.
- `laravel-engine/.env` exists, has a real `APP_KEY`, and has `APP_DEBUG=false`.
- Open `https://YOUR-DOMAIN/laravel-engine/public/up` and confirm HTTP 200.
- Open the registration/payment flow and confirm the browser loads compiled CSS and JavaScript.
- Check `storage/logs/laravel.log` after a test request; it must be writable and contain no missing-class or missing-key errors.

## Troubleshooting

**500 error mentioning `vendor/autoload.php`:** the deployment hook did not deploy the release tree. Confirm that `vendor/` is tracked with `git ls-files laravel-engine/vendor | head`.

**500 error about `bootstrap/cache` or storage:** recreate the directories and permissions above, then retry the request.

**Database connection failure:** verify the cPanel database name includes its account prefix, the user is assigned to the database, and both `DB_*` and `WP_DB_*` values are correct.

**CSS/JavaScript 404:** verify `public/build/manifest.json` is present and that the URL includes the correct `laravel-engine/public` path.

**Paystack callback failure:** use the public HTTPS URL ending in `/laravel-engine/public/api/paystack-webhook`, then verify `PAYSTACK_SECRET_KEY` and the callback secrets match WordPress.

## Rollback

In cPanel Git Version Control, deploy the previous known-good commit. If shell access is available, run this from the repository checkout directory:

```bash
git log --oneline -5
git checkout <known-good-commit>
```

Prefer deploying a new revert commit on the shared branch so the repository and cPanel checkout remain aligned. Keep the existing `.env` and database; rollback changes code only.
