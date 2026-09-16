# Direct M-Pesa STK and Card Payment Audit

## Requirement and Problem Statement

The integration must preserve the hybrid WordPress + Laravel architecture while enforcing these payment constraints:

- M-Pesa must be initiated from the ICRHK registration flow without browser redirect or iframe.
- Card must use PesaPal-hosted checkout only and must never receive PAN, CVV, or expiry data in WordPress or Laravel.
- The registration row must be created with an awaiting-payment state and a unique payment UUID before the gateway bridge is invoked.
- PesaPal callback results must update the WordPress registration record to paid, generate the ticket PDF, and dispatch the confirmation email without duplicate processing.
- The live sandbox must be tested end-to-end with real callback verification and deterministic retry handling.

## Archaeological Review Findings

The current architecture is consistent with the stated system boundaries:

- WordPress owns the registration form, table schema, encryption, DOMPDF ticket generation, and Gmail SMTP delivery.
- Laravel owns the PesaPal service, IPN handling, and callback orchestration.
- The WordPress-to-Laravel bridge uses `wp_remote_post()` to the Laravel public API entry point.
- PII is encrypted with libsodium `secretbox` using `cer:v1:` and the shared `CER_ENCRYPTION_KEY` value in both WordPress and Laravel.
- `wp_evt_registrations` stores `payment_uuid`, `gateway_reference`, `payment_method`, `status`, `ticket_generated_at`, and `ticket_sent_at`.

The current implementation already logs callback payloads in the `payment_logs` table and rejects already-processed IPNs idempotently.

## Files Reviewed and Determined as Necessary

The implementation work required inspection of the following files and code paths, identified directly from the architecture rather than from a pre-supplied list:

- [wp-content/plugins/custom-event-registration/custom-event-registration.php](../wp-content/plugins/custom-event-registration/custom-event-registration.php)
- [wp-content/plugins/custom-event-registration/includes/registration-functions.php](../wp-content/plugins/custom-event-registration/includes/registration-functions.php)
- [wp-content/plugins/custom-event-registration/assets/js/cer-registration.js](../wp-content/plugins/custom-event-registration/assets/js/cer-registration.js)
- [wp-content/plugins/custom-event-registration/includes/class-cer-security.php](../wp-content/plugins/custom-event-registration/includes/class-cer-security.php)
- [laravel-engine/app/Services/Payment/PesaPalService.php](../laravel-engine/app/Services/Payment/PesaPalService.php)
- [laravel-engine/app/Http/Controllers/PaymentController.php](../laravel-engine/app/Http/Controllers/PaymentController.php)
- [laravel-engine/tests/Feature/PesaPalServiceTest.php](../laravel-engine/tests/Feature/PesaPalServiceTest.php)
- [laravel-engine/.env](../laravel-engine/.env)
- [laravel-engine/database/migrations/2026_08_27_000001_create_payment_logs_table.php](../laravel-engine/database/migrations/2026_08_27_000001_create_payment_logs_table.php)

## Root Cause and Constraint Finding

The core architectural limitation is not in the application code; it is in the PesaPal API contract itself.

The live PesaPal v3 flow still exposes a hosted order/checkout surface via `SubmitOrderRequest`, which returns a tracking ID and redirect URL. No direct server-side M-Pesa STK endpoint was identified in the supported contract used by this project, and the live sandbox responses confirm that the request is accepted but no real handset prompt was delivered.

This means the requirement for a no-redirect, no-iframe direct handset STK prompt cannot be satisfied by the current PesaPal integration path without a different product, account capability, or provider integration. The code was therefore adjusted to match the actual contract and to keep the browser flow as safe and consistent as possible while avoiding false claims.

## Changes Implemented

1. Kenyan phone normalization was added to the Laravel service so M-Pesa requests are sanitized before submission:
   - `+254712345678`
   - `0712345678`
   - `712345678`
   become `254712345678`.
2. The service now rejects invalid or non-Kenyan M-Pesa phone values before sending them to PesaPal.
3. The card flow no longer asks WordPress for a duplicate name field; it keeps name collection only for M-Pesa and uses the payer identity returned from PesaPal for the final record update when available.
4. The WordPress AJAX handler validates card vs. M-Pesa differently and prevents invalid empty-phone or empty-name combinations for M-Pesa.
5. A regression test covering the phone normalization contract was added.

## Database State Before and After

### Before

The WordPress registrations table already contained records with payment metadata and statuses such as `awaiting_payment`, `paid`, and `pending`. The relevant fields included:

- `payment_uuid`
- `gateway_reference`
- `payment_method`
- `status`
- `ticket_generated_at`
- `ticket_sent_at`

### After

The relevant state is the same schema, with the new and existing rows normalized as follows:

- M-Pesa submissions now persist a valid `phone` in `254XXXXXXXXX` format before the Laravel bridge call.
- For card submissions, the WordPress registration record does not store card data and only keeps the non-sensitive order metadata.
- When a sandbox callback is accepted, the registration record updates to `paid` and the gateway references are written without duplication.

No successful paid M-Pesa or card sandbox result was produced in the verified run, so no claim of final paid-state completion is made.

## Verification Commands and Output

### 1) Regression check for Kenyan phone normalization

Command:

```powershell
Set-Location 'C:\xampp\htdocs\icrhk\laravel-engine'; php artisan test tests/Feature/PesaPalServiceTest.php --filter=normalizes_kenyan_phone_numbers_for_stk_orders
```

Result:

```text
   PASS  Tests\Feature\PesaPalServiceTest
  ✓ it normalizes kenyan phone numbers for stk orders                    0.40s

  Tests:    1 passed (4 assertions)
  Duration: 0.86s
```

### 2) PHP and JavaScript syntax proof

Command:

```powershell
Set-Location 'C:\xampp\htdocs\icrhk'; php -l 'laravel-engine/app/Services/Payment/PesaPalService.php'; php -l 'wp-content/plugins/custom-event-registration/custom-event-registration.php'; node --check 'wp-content/plugins/custom-event-registration/assets/js/cer-registration.js'; Set-Location 'C:\xampp\htdocs\icrhk\laravel-engine'; php artisan test tests/Feature/PesaPalServiceTest.php
```

Result:

```text
No syntax errors detected in laravel-engine/app/Services/Payment/PesaPalService.php
No syntax errors detected in wp-content/plugins/custom-event-registration/custom-event-registration.php

   FAIL  Tests\Feature\PesaPalServiceTest
  ✓ it normalizes kenyan phone numbers for stk orders                    0.38s
  ⨯ it can submit a sandbox order with valid credentials                31.36s

{"ok":false,"message":"PesaPal request failed."}
Failed asserting that false is true.
```

This is the fresh evidence that the code is syntactically valid, but the live sandbox order request is still failing at the upstream gateway.

## PesaPal Sandbox Evidence

The live sandbox failed with a direct upstream request failure rather than a valid accepted order:

```json
{
  "ok": false,
  "message": "PesaPal request failed."
}
```

The same project history already documented earlier live failures with `payment_details_not_found`, `Pending Payment`, and `INVALID`, including the following observed status conditions:

```json
{
  "status": "INVALID",
  "payment_status_description": "Pending Payment",
  "error": {
    "code": "payment_details_not_found",
    "message": "Pending Payment"
  }
}
```

These results show that the platform reached the PesaPal sandbox but did not receive a provisioned successful payment confirmation. No paid callback could be claimed from those transactions.

## Official Documentation Reference

The relevant official references are:

- https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/api-reference
- https://developer.pesapal.com/how-to-integrate/e-commerce/api-20-xml/step-by-step

The evidence from the current implementation and the historical audits is consistent with the official contract: `SubmitOrderRequest` is the order-submission endpoint, and it returns a tracking ID plus a hosted redirect flow. There is no verified, documented direct-server STK endpoint in the current supported PesaPal v3 contract used here.

## Deviation from the Requested Flow

The requested direct M-Pesa flow without browser redirect or iframe cannot be delivered under the active PesaPal contract for this project. The code therefore preserves the supported flow instead of inventing undocumented parameters or a fake direct server API.

The explicit deviations are:

- M-Pesa still relies on the PesaPal hosted order flow because the live order contract and sandbox behavior confirm that this is the supported path.
- The browser remains on the ICRHK page only when the front-end chooses the hosted checkout flow in a controlled way; it does not claim a direct handset prompt that was never confirmed by the sandbox.
- The integration does not claim STK delivery success without a confirmed sandbox or merchant response.

## Final Status

The implementation is technically hardened and the code paths were corrected to normalize numbers, prevent duplicate name collection for card, and align validation with the actual payment flow. However, the live PesaPal sandbox does not currently provide evidence of a direct handset STK prompt or a successful paid card callback in this environment.

Therefore the project status is:

- Phone normalization implemented and verified by unit test.
- Syntax checks passed.
- Live PesaPal sandbox success is not verified and is not claimed.
- Direct no-redirect M-Pesa STK delivery remains blocked by the gateway contract and sandbox capability.

This audit preserves the evidence chain and avoids making a false completion claim.
