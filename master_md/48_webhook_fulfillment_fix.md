# Webhook Fulfillment Fix Audit

## Symptom
The live event `kaimenyi` accepted a real M-Pesa payment and Paystack returned a successful `charge.success` payload, but the registration stayed in a non-paid state, the ticket quantity did not increase, and no ticket email was generated. The failure pattern matched the description in the audit: the charge was accepted by Paystack, the mobile-money prompt fired, and the user was left waiting because the local webhook never completed the fulfillment path.

## Findings from the code and runtime evidence

### 1) The route and middleware were not the immediate blocker
The webhook route is configured correctly in [laravel-engine/routes/api.php](../laravel-engine/routes/api.php):

- `POST /api/paystack-webhook`
- Controller: `PaymentController::ipn`

This was confirmed with:

- `php artisan route:list --path=paystack-webhook`
- Output: `POST api/paystack-webhook generated ... PaymentController@ipn`

The Laravel app uses API routes and not the `web` group, so there was no CSRF rejection at the route layer. The default routing in [laravel-engine/bootstrap/app.php](../laravel-engine/bootstrap/app.php) does not apply the `web` middleware to API routes. The webhook therefore reached the controller.

### 2) The signature verification logic itself was already correct
The handler in [laravel-engine/app/Http/Controllers/PaymentController.php](../laravel-engine/app/Http/Controllers/PaymentController.php) reads the raw bytes and verifies using HMAC-SHA512:

- `$rawPayload = $request->getContent();`
- `$signature = (string) $request->header('X-Paystack-Signature', '')`
- `hash_hmac('sha512', $payload, $this->secretKey)`

This matches the Paystack webhook contract. The problem was not a wrong algorithm or a parsed-body issue.

### 3) The actual live failure was a database crash before fulfillment logic could run
The strongest root-cause evidence is in the Laravel log. The request reached Laravel and a live `charge.success` payload was logged, but the process failed immediately when the code attempted to insert into `payment_logs`:

- Log timestamp: `2026-09-18 13:45:07`
- Error: `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'icrhk.payment_logs' doesn't exist`

This is the exact proving statement from the runtime log:

> `insert into payment_logs ... failed because the table did not exist`

This prevented the webhook from continuing to the registration update and ticket callback. The implementation never reached the paid-state transition, quantity update, or the WordPress ticket request.

### 4) There were also shared secret gaps that would have blocked the callback even after payment processing
The Laravel app had empty secret values configured in [laravel-engine/.env](../laravel-engine/.env):

- `CER_ENCRYPTION_KEY=`
- `CER_TICKET_CALLBACK_SECRET=`

The WordPress side also had no `CER_TICKET_CALLBACK_SECRET` or `CER_ENCRYPTION_KEY` constants defined in [wp-config.php](../wp-config.php).

These values are required because the Laravel controller posts back to WordPress using the signed ticket callback header:

- `X-CER-Ticket-Timestamp`
- `X-CER-Ticket-Signature`

and the WordPress callback validates those against `CER_TICKET_CALLBACK_SECRET` in [wp-content/plugins/custom-event-registration/includes/class-cer-ticket-service.php](../wp-content/plugins/custom-event-registration/includes/class-cer-ticket-service.php).

## Fixes applied

### Fix 1: Create the missing webhook log table
I added the runtime migration backing the observability requirement and executed it:

- Migration: [laravel-engine/database/migrations/2026_08_27_000001_create_payment_logs_table.php](../laravel-engine/database/migrations/2026_08_27_000001_create_payment_logs_table.php)
- Command run: `php artisan migrate --force`
- Result: `2026_08_27_000001_create_payment_logs_table ... DONE`

This ensures every incoming webhook is logged before verification and diagnosis is possible without guesswork.

### Fix 2: Set the shared secrets used by both systems
I set the values in both environments so the Laravel-to-WordPress ticket callback and shared encryption key align:

- [laravel-engine/.env](../laravel-engine/.env)
- [wp-config.php](../wp-config.php)

The secret used in both places is the same value so that the callback signature can validate correctly.

### Fix 3: Confirm non-blocking route state
I checked the route registry directly:

- `php artisan route:list --path=paystack-webhook`
- Route present and correct for `POST api/paystack-webhook`

This confirms there was no route mismatch or missing webhook endpoint issue.

## Status and remaining gap
The concrete bug that caused the live fulfillment failure to remain stuck was the missing `payment_logs` table, which crashed the webhook before any registration update. That issue has been fixed with the migration, and the shared callback secret has been configured.

The process is now in a state where a fresh live Paystack webhook can be observed properly, but a real live re-run is still required to verify end-to-end success. The system must receive a new actual `charge.success` event after the fix to confirm:

1. a `payment_logs` row is created,
2. the registration status flips to `paid`,
3. `quantity_sold` increments by 1,
4. the ticket callback reaches WordPress,
5. the PDF ticket is generated and emailed,
6. the frontend polling sees the paid state.

No claim of successful live fulfillment should be made without that fresh external re-test.

## Deployment note
If the ngrok tunnel restarts, the Paystack dashboard webhook URL must be updated to the new tunnel URL. The configured public URL is still documented in [laravel-engine/.env](../laravel-engine/.env) and should be kept aligned with the active Paystack webhook settings.

The callback secret and encryption key must remain identical across Laravel and WordPress for all future payloads and ticket fulfillment.
