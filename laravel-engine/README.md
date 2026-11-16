# ICRHK Laravel engine

This directory is the Laravel payment and registration service used by the ICRHK WordPress site. Production Composer dependencies and Vite assets are committed, so cPanel does not need Composer, npm, Node, or a build step.

## Deployment shape

WordPress remains the existing public site. cPanel checks out this repository under its `repositories/` directory, then `deploy-cpanel.sh` copies the tracked Laravel application beside WordPress at `laravel-engine/` and updates only the custom plugin and theme. WordPress core is never copied or replaced. Laravel is served through `laravel-engine/public/` and reads the WordPress `wp_evt_*` tables through its `wordpress` database connection.

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
2. Set the repository's cPanel deployment path to the existing website document root's parent repository layout used by this account: the checkout must be at `repositories/icrhk` and the live document root must be its sibling `icrhk.nyimuki.com`. Do not point the deployment path directly at WordPress core or edit the YAML.
3. Select PHP 8.2 or 8.3 in **MultiPHP Manager** and enable the extensions listed above. The cPanel PHP CLI must be available as `php` on the deployment hook's PATH.
4. Confirm Apache `mod_rewrite` is enabled. The Laravel front controller is `laravel-engine/public/index.php`.
5. Confirm the cPanel database user can read and update the WordPress event tables.

## Configure the server

After the first pull, create `laravel-engine/.env` from `.env.example` and edit every deployment value. Set `APP_KEY`, `APP_URL`, the Laravel-owned `DB_*` values, the WordPress `WP_DB_*` values, `CER_ENCRYPTION_KEY`, `CER_TICKET_CALLBACK_SECRET`, `WORDPRESS_URL`, and the Paystack keys. Use `APP_DEBUG=false` in production. Never commit `.env`.

For transactional ticket email, add these constants to the live WordPress `wp-config.php` without committing the API key:

```php
define( 'CER_RESEND_API_KEY', 're_...' );
define( 'CER_RESEND_FROM', 'ICRHK Events <tickets@your-verified-domain.example>' );
```

The sender domain must be verified in Resend. When both constants exist, the plugin sends through Resend's HTTPS API and does not use SMTP. When either constant is absent, the existing event SMTP path remains the fallback.

The deployment hook creates the writable directories automatically. To repair an older installation:

```bash
cd laravel-engine
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs
chmod -R 775 bootstrap/cache storage
```

## Automated deployment tasks

The root `.cpanel.yml` runs `deploy-cpanel.sh`. The script uses the relative path from `repositories/icrhk` to the sibling `icrhk.nyimuki.com` document root, copies Laravel and the tracked custom WordPress integration, creates `storage/framework/{cache,sessions,views}`, `storage/logs`, and `bootstrap/cache`, and applies `775` permissions. It never copies or overwrites `.env` or WordPress core. Once a configured `.env` exists, it runs migrations, creates the storage link, and clears configuration. Deployment regenerates Laravel package discovery after the live environment is configured.

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

Ticket delivery is completed by WordPress after Laravel calls the root site URL with the signed `cer_process_ticket` request. The live WordPress `wp-config.php` and Laravel `.env` must contain identical `CER_TICKET_CALLBACK_SECRET` and `CER_ENCRYPTION_KEY` values. The deployment script adds either missing WordPress constant without overwriting an existing one; copy those resulting values into `.env` before the second deployment. WordPress writes the PDF under `wp-content/uploads/tickets/` and sends it through Resend when a key and valid `CER_RESEND_FROM` are configured; otherwise it uses the normal WordPress mail transport.

### No-cost admission and event reminders

Tickets with no amount (including zero) now confirm admission without starting a payment. They use the existing `confirmed` status, consume ticket inventory and event capacity, and receive the same unique access code, QR/PDF ticket, and online ticket link. Confirmation thanks the attendee without discussing ticket prices or claiming a payment. Priced tickets retain their gateway flow; capacity checks also count confirmed no-cost admissions.

For no-cost admission, an email address can register once per event/ticket type (case-insensitive). Another ticket type remains available to that email. Event/ticket row locks serialize submissions so simultaneous requests cannot issue duplicate admissions or oversell no-cost inventory. Free admissions also respect active paid holds against event capacity, and paid submissions recheck capacity while holding the event lock. Existing unconfirmed contact-only records are not bulk-promoted to admission; when that attendee submits again, their pending no-cost contact record is reused and confirmed instead of inserting another registration.

If ticket generation or mail fails, admission stays confirmed, the response explicitly reports pending delivery, and the notification worker retries. Resend failures return `false` through WordPress's mail contract and emit `wp_mail_failed`; they must never be treated as successful delivery.

For Resend messages, the verified sender address remains unchanged and the event name is used as the display name. Attendee tickets and reminders use the event's Sender Email as Reply-To when valid. After the ticket email is accepted, a vendor notification is attempted at the event's Notification Email, with the attendee's email as Reply-To; no copies are sent to the Sender Email. Vendor notification failures are logged but not retried.

The `cer_event_notifications` WordPress cron hook runs every five minutes. It processes up to 25 unsent no-cost tickets and 25 individual reminder emails per run through the existing mail transport. Recipients are never exposed to each other. Each eligible attendee (`paid`, or `confirmed` with no amount) receives one reminder for the current event date, starting five calendar days before the event's start time in the WordPress timezone. Late registrations and delayed cron runs catch up until the event starts. Rescheduling permits a reminder for the new date; past, unpublished, trashed events and trashed registrations are excluded. Failed sends retry after 15 minutes without blocking unattempted recipients. Successful-send markers prevent normal repeat sends; mail-provider acceptance is not a guarantee of inbox delivery.

On the next WordPress initialization, the plugin installs three nullable notification tracking columns in its registration table and schedules the hook automatically. No Laravel migration or queue worker is required. Deactivating the plugin removes this hook.

**Reliable timing requires server cron**, because WP-Cron otherwise only runs when the site receives traffic. In cPanel, add a once-per-minute task using the account's PHP 8.2/8.3 CLI and the actual live WordPress path, for example:

```cron
* * * * * /usr/local/bin/php -q /home/ACCOUNT/public_html/wp-cron.php
```

Replace the account and PHP binary as appropriate. Do not disable traffic-triggered WP-Cron until the server task is verified. For large audiences, allow time for the bounded batches to drain (up to 300 reminder recipients per hour at the five-minute cadence).

Focused local checks (from the repository root):

```powershell
php wp-content\plugins\custom-event-registration\tests\registration-feedback-test.php
node --test wp-content\plugins\custom-event-registration\tests\registration-feedback.test.cjs
php wp-content\plugins\custom-event-registration\tests\event-notifications-integration-test.php
php wp-content\plugins\custom-event-registration\tests\resend-mail-contract-test.php
php laravel-engine\tests\payment-capacity-contract-test.php
```

The WordPress integration test requires a loopback MySQL/MariaDB database and permission to create/drop a uniquely named test database. It copies table structure only, intercepts all outbound mail/HTTP, generates real PDFs in a temporary directory, and removes its fixtures afterward. The Laravel capacity test uses isolated in-memory SQLite databases and intercepts HTTP.

After deployment, run the health check and an invalid-signature webhook check:

```bash
curl -i https://YOUR-DOMAIN/laravel-engine/public/up
curl -i -X POST https://YOUR-DOMAIN/laravel-engine/public/api/paystack-webhook \
	-H 'Content-Type: application/json' \
	-d '{"event":"charge.success","data":{}}'
```

The first request must return `200`; the second must return `400 invalid_signature`, proving the HTTPS route is reachable and signature protection is active. A real end-to-end payment requires a Paystack test/live key, a real registration row, and Paystack's callback delivery; complete one small test transaction after configuring those `.env` values.

For the second-site rehearsal, verify after deployment that the live `wp-config.php` contains both CER constants, the Laravel log records `IPN Processing: Making WordPress ticket callback`, and the WordPress PHP error log records `CER ticket callback completed`. Confirm `ticket_generated_at` and `ticket_sent_at` are populated on the paid `wp_evt_registrations` row and that the PDF exists in `wp-content/uploads/tickets/`.

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

**Payment succeeds but no ticket:** inspect Laravel `storage/logs/laravel.log` and the WordPress/PHP error log. `CER ticket callback unauthorized` means the two callback secrets differ; `Dompdf class unavailable`, `PDF could not be written`, or `wp_mail failed` identifies the ticket-stage failure directly.

## Rollback

In cPanel Git Version Control, deploy the previous known-good commit. If shell access is available, run this from the repository checkout directory:

```bash
git log --oneline -5
git checkout <known-good-commit>
```

Prefer deploying a new revert commit on the shared branch so the repository and cPanel checkout remain aligned. Keep the existing `.env` and database; rollback changes code only.
