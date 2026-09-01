# Payment Integration Reconstruction Audit

Date: 2026-08-28

## Scope

Restored the working PesaPal v3 integration pattern and refined the two payment UI branches while preserving the existing event, admin, encryption, ticket, email, and cache behavior.

## Requirements and Changes

### PesaPal service

- `PesaPalService::requestToken()` continues to authenticate with a JSON POST body containing `consumer_key` and `consumer_secret`.
- No Basic authentication header is used for token requests.
- `submitOrder()` sends the payment UUID, ticket amount, callback URL, registered IPN ID, and nested `billing_address`.
- Kenyan phone values are normalized to `254XXXXXXXXX` without a plus sign.
- Added the `normalizePhone()` alias required by the reproduction guide.
- Production configuration and the registered ngrok callback were restored in `laravel-engine/.env`.

Files: `laravel-engine/app/Services/Payment/PesaPalService.php`, `laravel-engine/.env`

### M-Pesa

- Full Name, Email, Phone, and Ticket Type remain visible.
- The form submits to the WordPress to Laravel bridge.
- The PesaPal order response is used for tracking and status polling.
- No iframe is created and no browser redirect is performed.
- A successful IPN changes the registration to `paid` and invokes the existing idempotent WordPress ticket callback.

Files: `wp-content/plugins/custom-event-registration/assets/js/cer-registration.js`, `wp-content/plugins/custom-event-registration/custom-event-registration.php`

### Card

- Card mode hides the WordPress Full Name and Phone fields.
- Email and Ticket Type remain as the two visible fields.
- The WordPress submit button is hidden in Card mode.
- Selecting a ticket after entering a valid email automatically creates the PesaPal order.
- The returned hosted checkout URL loads in the responsive iframe below the form.
- Card PAN, CVV, expiry, and payer name are never collected by WordPress or Laravel.

Files: `wp-content/plugins/custom-event-registration/assets/js/cer-registration.js`, `wp-content/plugins/custom-event-registration/assets/css/cer-event.css`

### Confirmed payment persistence

Verified paid IPNs now extract payer name, receipt number, serial number, confirmation code, and confirmed amount from the PesaPal transaction response. Payer name is encrypted with `RegistrationCrypto` before storage. Existing duplicate-IPN handling remains in place and prevents duplicate ticket delivery.

Files: `laravel-engine/app/Http/Controllers/PaymentController.php`, `laravel-engine/app/Services/WordPress/RegistrationCrypto.php`

### Schema

Added nullable fields to `wp_evt_registrations`:

- `payer_name`
- `receipt_number`
- `serial_number`
- `confirmation_code`
- `confirmed_amount`

The plugin schema definition and its existing upgrade path both include these fields. The live local database was upgraded successfully.

File: `wp-content/plugins/custom-event-registration/custom-event-registration.php`

## Verification Evidence

### Syntax and whitespace checks

Commands:

```text
php -l wp-content/plugins/custom-event-registration/custom-event-registration.php
php -l wp-content/plugins/custom-event-registration/includes/registration-functions.php
php -l laravel-engine/app/Http/Controllers/PaymentController.php
php -l laravel-engine/app/Services/Payment/PesaPalService.php
node --check wp-content/plugins/custom-event-registration/assets/js/cer-registration.js
git diff --check
```

Result: all PHP files reported `No syntax errors detected`; JavaScript syntax check and `git diff --check` passed.

### Database migration

Command:

```text
ALTER TABLE wp_evt_registrations ADD COLUMN IF NOT EXISTS payer_name VARCHAR(512) NULL AFTER full_name, ADD COLUMN IF NOT EXISTS receipt_number VARCHAR(255) NULL AFTER payer_name, ADD COLUMN IF NOT EXISTS serial_number VARCHAR(255) NULL AFTER receipt_number, ADD COLUMN IF NOT EXISTS confirmation_code VARCHAR(255) NULL AFTER serial_number, ADD COLUMN IF NOT EXISTS confirmed_amount DECIMAL(10,2) NULL AFTER amount;
```

Result: all five columns are present in the live local table.

### Live PesaPal authentication

Laravel command:

```text
php artisan tinker --execute="print_r(app(\\App\\Services\\Payment\\PesaPalService::class)->requestToken());"
```

Redacted result:

```text
[ok] => 1
[token] => [redacted]
[expires_in] => 300
```

### Live Card order initiation

A non-chargeable unpaid KES 1 Card order was created to validate the production payload and checkout contract. No card data was submitted.

Redacted result:

```text
[ok] => 1
[tracking_id] => b03dc485-6cb6-4bfa-a2b8-d9f8ddc0e4fe
[redirect_url] => https://pay.pesapal.com/iframe/PesapalIframe3/Index?OrderTrackingId=b03dc485-6cb6-4bfa-a2b8-d9f8ddc0e4fe
[status] => pending
```

### Browser verification

The local event page was opened at:

```text
http://localhost/icrhk/event/kamgc-2026-from-silence-to-systems/
```

Card toggle verification confirmed that the Card branch activates and hides the WordPress name, phone, and submit controls. M-Pesa remains the default branch with the full registration fields.

## Database State

Before migration, `wp_evt_registrations` had no dedicated payer, receipt, serial, confirmation-code, or confirmed-amount columns. After migration, all five nullable columns exist and are available to the IPN update path.

Existing registration rows and payment logs were not rewritten.

## Verification Limits

The live authentication and order-initiation contract passed. A paid M-Pesa STK confirmation, paid Card transaction, and duplicate confirmed IPN were not fabricated during this reconstruction. Those require a real handset/card completion and a PesaPal callback. The existing IPN handler remains the authority for paid status, metadata persistence, ticket generation, and duplicate suppression.
