# Payment Flow Contract and Live Verification Audit

Date: 2026-08-27

## Scope

This audit records the payment-flow corrections made after reviewing the existing WordPress plugin, Laravel bridge, PesaPal service, callback path, ticket service, mailer, schema, and payment audit history.

## Requirements and Files Changed

| Requirement | File | Change |
| --- | --- | --- |
| Accept Kenyan M-Pesa formats and persist the normalized MSISDN | `wp-content/plugins/custom-event-registration/custom-event-registration.php` | Server-side normalization converts `+254...`, `0711...`, and `711...` to `254XXXXXXXXX`; invalid M-Pesa numbers are rejected before insertion/bridge submission. |
| Keep card collection PCI-safe and email-only | `wp-content/plugins/custom-event-registration/custom-event-registration.php` | Card submissions no longer require or persist name and phone; the schema-compatible stored values are empty. |
| Match browser validation to the selected method | `wp-content/plugins/custom-event-registration/assets/js/cer-registration.js` | Name and phone are required only for M-Pesa. Card keeps only email and ticket selection required. |
| Enforce the same contract at Laravel | `laravel-engine/app/Http/Controllers/PaymentController.php` | `full_name` is nullable for card; M-Pesa requires name and phone. |
| Permit hosted card orders without a phone | `laravel-engine/app/Services/Payment/PesaPalService.php` | Phone validation is conditional; hosted checkout still receives the required nested `billing_address` with a fallback payer name. |
| Preserve exact gateway transport failures | `laravel-engine/app/Services/Payment/PesaPalService.php` | Failed stream requests now return HTTP status, stream error, and response headers. Order failures retain structured PesaPal response data. |
| Make confirmed callback idempotency observable | `laravel-engine/app/Http/Controllers/PaymentController.php` | A repeated confirmed callback creates an `ipn_duplicate_ignored` log entry and does not modify ticket or email timestamps. |
| Interpret PesaPal status correctly | `laravel-engine/app/Services/Payment/PesaPalService.php` | Transaction status now prefers `payment_status_description` over the HTTP status field. |

## Architecture Preserved

- WordPress creates the registration row before calling the Laravel bridge.
- Registration PII continues through `cer_prepare_registration_row()` and the shared `CER_ENCRYPTION_KEY` contract.
- Card PAN, CVV, and expiry fields remain absent from the WordPress form and are entered only in PesaPal hosted checkout.
- PesaPal v3 authentication, registered IPN notification ID, hosted `SubmitOrderRequest`, shared tables, ticket generation, SMTP mail, cache invalidation, and asynchronous sync remain in their existing ownership boundaries.

## Verification Commands and Results

Syntax and client checks:

```text
php -l wp-content/plugins/custom-event-registration/custom-event-registration.php
No syntax errors detected
php -l laravel-engine/app/Http/Controllers/PaymentController.php
No syntax errors detected
php -l laravel-engine/app/Services/Payment/PesaPalService.php
No syntax errors detected
node --check wp-content/plugins/custom-event-registration/assets/js/cer-registration.js
PASS
git diff --check
PASS
```

Focused regression test:

```text
cd laravel-engine
php artisan test --filter=test_it_normalizes_kenyan_phone_numbers_for_stk_orders
PASS Tests\Feature\PesaPalServiceTest
1 passed (4 assertions)
```

The existing live-order test was also rerun:

```text
php artisan test --filter=test_it_can_submit_a_sandbox_order_with_valid_credentials
FAIL: {"ok":false,"message":"PesaPal token not returned"}
```

Laravel recorded the provider attempt as HTTP `200` with an empty response body after the stream timeout. A direct PowerShell POST to the same sandbox token URL also timed out. No usable token, order response, STK initiation response, or new hosted checkout URL was returned during this run.

## Callback Idempotency Live Check

The public ngrok endpoint used was:

```text
https://usable-paltry-cameo.ngrok-free.dev/icrhk/laravel-engine/public/api/pesapal-ipn
```

The same confirmed payload was POSTed twice:

```json
{"payment_uuid":"3eacb6e2-a1fd-11f1-b90f-4c796eb29980","status":"PAID"}
```

Both requests returned HTTP `200`. Database state after the requests:

```text
wp_evt_registrations id=10: status=paid
ticket_generated_at=2026-08-27 12:38:46
ticket_sent_at=2026-08-27 12:38:51

payment_logs:
id=11 event=ipn_received status=paid
id=12 event=ipn_duplicate_ignored status=ignored
```

The paid row and both ticket timestamps remained unchanged, proving that the duplicate callback did not regenerate a ticket or resend mail.

## Direct STK and Card Status

The active implementation still uses PesaPal v3 `Transactions/SubmitOrderRequest`, which returns a hosted checkout URL. The archaeological records and live sandbox runs contain no separate direct server-side STK endpoint or successful handset-prompt response. The current sandbox token endpoint timed out before an order could be tested, so this audit does not claim direct STK delivery, paid M-Pesa completion, paid card completion, payer extraction, or new ticket/email delivery for either flow.

The existing evidence remains:

- Previous M-Pesa orders were accepted by PesaPal and remained `Pending Payment` / `payment_details_not_found`; no handset prompt or paid IPN was verified.
- The previously completed card registration and ticket/email delivery remain in the database and are not altered by this patch.
- The current live blocker is an empty/timed-out sandbox authentication response, not a fabricated success response from the application.

## Remaining Acceptance Items

The following require a reachable PesaPal sandbox response and valid sandbox transaction credentials/session: direct handset STK delivery, paid M-Pesa IPN, card iframe completion, payer/receipt/serial extraction, and new ticket/email delivery for both payment methods. They must be tested before marking the payment checklist complete.