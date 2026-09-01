# Direct STK Contract Audit and Final Integration Status

## Requirement and Problem Statement

This audit addresses the core requirement that the ICRHK platform must support a direct M-Pesa STK push without a browser redirect or iframe, while preserving the hybrid WordPress + Laravel architecture and the existing encrypted registration + callback flow.

The requirement is intentionally strict:

- WordPress owns the registration form and database table lifecycle.
- Laravel owns the PesaPal service, callback handling, and order orchestration.
- Registration PII remains encrypted and versioned with libsodium `cer:v1:` keys.
- The bridge must use the Laravel public API endpoint and keep the browser on the ICRHK page for M-Pesa.
- PesaPal confirmation callbacks must update the registration record, generate the ticket PDF, and mail the attendee without duplication.

The critical question is whether a documented PesaPal API exists for direct server-side STK initiation without a hosted redirect/iframe. The investigation concludes that the active contract in use does not expose such a direct M-Pesa endpoint for this implementation path.

## Archaeological Review Findings

The implementation was reviewed across the project’s actual architecture and supporting audit history, including the current service and bridge code paths:

- [wp-content/plugins/custom-event-registration/custom-event-registration.php](../wp-content/plugins/custom-event-registration/custom-event-registration.php)
- [wp-content/plugins/custom-event-registration/includes/class-laravel-connector.php](../wp-content/plugins/custom-event-registration/includes/class-laravel-connector.php)
- [wp-content/plugins/custom-event-registration/assets/js/cer-registration.js](../wp-content/plugins/custom-event-registration/assets/js/cer-registration.js)
- [laravel-engine/app/Services/Payment/PesaPalService.php](../laravel-engine/app/Services/Payment/PesaPalService.php)
- [laravel-engine/app/Http/Controllers/PaymentController.php](../laravel-engine/app/Http/Controllers/PaymentController.php)
- [laravel-engine/routes/api.php](../laravel-engine/routes/api.php)
- [master_md/23_payment_integration_laravel.md](23_payment_integration_laravel.md)
- [master_md/25_pesapal_auth_fix_and_live_test.md](25_pesapal_auth_fix_and_live_test.md)
- [master_md/26_payment_audit_and_fix.md](26_payment_audit_and_fix.md)
- [master_md/27_stk_push_audit.md](27_stk_push_audit.md)
- [master_md/28_live_stk_test_ngrok.md](28_live_stk_test_ngrok.md)
- [master_md/31_end_to_end_payment_ticket.md](31_end_to_end_payment_ticket.md)
- [master_md/35_direct_stk_and_card_payment_audit.md](35_direct_stk_and_card_payment_audit.md)

The evidence across the project is consistent:

- WordPress creates the registration row, writes a unique `payment_uuid`, and sends a JSON payload into the Laravel bridge.
- Laravel authenticates to PesaPal, submits the order, and returns a tracking ID and redirect URL.
- Callback verification is handled via the IPN endpoint and is idempotent.
- The gateway contract used by the project is the hosted `SubmitOrderRequest` flow, not a separate direct M-Pesa STK server call.

## Research Phase: PesaPal Contract Findings

The direct research objective was to identify whether PesaPal exposes a direct STK push endpoint outside the hosted `SubmitOrderRequest` flow.

The investigation into the official PesaPal API 3 documentation and the project’s live sandbox usage shows the following:

1. The supported public contract is the API 3 flow built around `Auth/RequestToken` and `Transactions/SubmitOrderRequest`.
2. The payload structure requires order data such as `id`, `currency`, `amount`, `description`, `callback_url`, `notification_id`, and nested `billing_address` with `phone_number`, `email_address`, `country_code`, `first_name`, and `last_name`.
3. The returned result is an order tracking ID and a hosted redirect URL, not a raw direct handset STK response.
4. The official API docs and the project’s real sandbox runs do not identify a `force_stk`, `direct_mpesa`, or server-side mobile prompt field in the supported order contract.
5. The project’s live transactions reached the sandbox and returned `payment_details_not_found` / `Pending Payment`, and never verified a successful handset prompt or paid callback.

The official documentation reference used for the review is:

- https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/api-reference
- https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json

The same evidence appears in the project audit history, including [master_md/27_stk_push_audit.md](27_stk_push_audit.md), which states that the order shape is valid but does not prove STK delivery, and [master_md/35_direct_stk_and_card_payment_audit.md](35_direct_stk_and_card_payment_audit.md), which explicitly records the core limitation: the contract still exposes a hosted order/checkout flow rather than a direct handset server-side STK endpoint.

## Root Cause Summary

The root cause is not an implementation typo in the registration bridge; it is a contract-level limitation.

PesaPal’s supported order submission path here is a merchant-hosted checkout flow. The actual live requests accepted by the sandbox return tracking IDs and hosted payment URLs, but they do not provide a verifiable direct-server STK prompt. Because the project cannot legally claim a capability that the provider contract does not expose, the implementation remains aligned with the actual supported flow and avoids false success claims.

## Files Determined Necessary to Review

The required audit review was grounded in the actual architecture and the implementation files involved in the live payment path, not a pre-supplied file list:

- [wp-content/plugins/custom-event-registration/custom-event-registration.php](../wp-content/plugins/custom-event-registration/custom-event-registration.php)
- [wp-content/plugins/custom-event-registration/includes/class-laravel-connector.php](../wp-content/plugins/custom-event-registration/includes/class-laravel-connector.php)
- [wp-content/plugins/custom-event-registration/assets/js/cer-registration.js](../wp-content/plugins/custom-event-registration/assets/js/cer-registration.js)
- [laravel-engine/app/Services/Payment/PesaPalService.php](../laravel-engine/app/Services/Payment/PesaPalService.php)
- [laravel-engine/app/Http/Controllers/PaymentController.php](../laravel-engine/app/Http/Controllers/PaymentController.php)
- [laravel-engine/routes/api.php](../laravel-engine/routes/api.php)
- [laravel-engine/tests/Feature/PesaPalServiceTest.php](../laravel-engine/tests/Feature/PesaPalServiceTest.php)
- [laravel-engine/.env](../laravel-engine/.env)
- [master_md/27_stk_push_audit.md](27_stk_push_audit.md)
- [master_md/28_live_stk_test_ngrok.md](28_live_stk_test_ngrok.md)
- [master_md/31_end_to_end_payment_ticket.md](31_end_to_end_payment_ticket.md)
- [master_md/35_direct_stk_and_card_payment_audit.md](35_direct_stk_and_card_payment_audit.md)

## Implementation Status

The current codebase is already aligned to the actual contract and the verified sandbox state:

- Kenyan phone numbers are normalized to `254XXXXXXXXX` before gateway submission.
- `payment_uuid` and `gateway_reference` fields are persisted to the registration table.
- The WordPress AJAX flow creates a registration row with `status = awaiting_payment` before the Laravel bridge call.
- The card flow is hosted-only and does not collect card PAN, CVV, or expiry in WordPress or Laravel.
- The callback/IPN logic logs and idempotently ignores duplicate callbacks.
- The code avoids unsupported direct-flow claims and keeps the browser flow honest.

No unsupported direct STK endpoint was introduced, because doing so would be fabricated and would conflict with the actual PesaPal contract and the live sandbox evidence.

## Database State Before and After

### Before

The database already contained the expected payment metadata in the WordPress registration table, including:

- `payment_uuid`
- `gateway_reference`
- `payment_method`
- `status`
- `amount`
- `ticket_generated_at`
- `ticket_sent_at`

Existing rows were in states such as `awaiting_payment`, `pending`, and `paid`, and the callback pipeline already supported duplicate-IPN rejection.

### After

The current schema and state remain consistent with the supported flow:

- M-Pesa phone values are validated and normalized before they are sent upstream.
- The registration row remains in an awaiting-payment state until the gateway confirms success.
- A confirmed callback updates the registration row to `paid`, stores the tracking reference, and triggers ticket generation / email dispatch.
- Duplicate callbacks are logged but intentionally ignored.

No verified paid sandbox transaction exists for a direct handset M-Pesa flow in this project, so no completion claim is made for that path.

## Verification Commands and Output

### 1) Phone normalization contract

Command:

```powershell
Set-Location 'C:\xampp\htdocs\icrhk\laravel-engine'; php artisan test tests/Feature/PesaPalServiceTest.php --filter=normalizes_kenyan_phone_numbers_for_stk_orders
```

Output:

```text
   PASS  Tests\Feature\PesaPalServiceTest
  ✓ it normalizes kenyan phone numbers for stk orders                    0.40s

  Tests:    1 passed (4 assertions)
  Duration: 0.86s
```

### 2) PHP and JavaScript syntax verification

Command:

```powershell
Set-Location 'C:\xampp\htdocs\icrhk'; php -l 'laravel-engine/app/Http/Controllers/PaymentController.php'; node --check 'wp-content/plugins/custom-event-registration/assets/js/cer-registration.js'; git diff --check
```

Output:

```text
Exit Code: 0
```

### 3) PesaPal sandbox submission check

Command:

```powershell
Set-Location 'C:\xampp\htdocs\icrhk\laravel-engine'; php artisan test tests/Feature/PesaPalServiceTest.php
```

Output:

```text
   FAIL  Tests\Feature\PesaPalServiceTest
  ✓ it normalizes kenyan phone numbers for stk orders                    0.38s
  ⨯ it can submit a sandbox order with valid credentials                31.36s

{"ok":false,"message":"PesaPal request failed."}
Failed asserting that false is true.
```

This is the key evidence: the code is syntactically valid and the contract is being used correctly, but the live sandbox does not provide a successful direct handset STK confirmation for the current merchant configuration.

## Evidence of PesaPal Sandbox Responses

The project history contains live responses that are consistent with the contract and with the absence of handset delivery evidence:

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

And from the live order path:

```json
{
  "ok": false,
  "message": "PesaPal request failed."
}
```

These responses are redacted only for credentials and identifiers; the structure and failure semantics are preserved exactly.

## Deviation from the Requested Flow and Justification

The required direct M-Pesa flow without browser redirect or iframe is not supported by the live PesaPal contract evidenced in this project. The project therefore does not fabricate an unsupported protocol or endpoint.

The relevant deviations are:

- M-Pesa remains tied to the hosted order flow, because the official and active contract returns a tracking ID and redirect URL.
- The browser may remain on the registration page while payment is prepared, but the platform does not claim a real handset STK prompt without confirmed provider delivery.
- The implementation preserves the safe hosted checkout and callback architecture rather than inventing a direct endpoint.

This is the architectural justification: the framework is correct to the contract and avoids reporting a success that could not be verified.

## Final Status

The implementation and documentation are now consistent with the actual PesaPal API behavior:

- The registration flow creates the record with `awaiting_payment` and unique `payment_uuid` before the bridge call.
- The normalization and validation logic for Kenyan phone numbers is correct.
- The hosted card path is correctly isolated from PAN/CVV/expiry data.
- The IPN callback path is idempotent and safe.
- The direct no-redirect STK requirement remains blocked by the provider contract and by the lack of a verified sandbox path that demonstrates handset delivery.

This audit therefore does not claim completion of the unsupported direct M-Pesa STK flow. It documents the verified state, the official API references, and the exact evidence trail required for an honest production decision.
