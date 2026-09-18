# Live Paystack Integration Audit

## Scope

The payment path is WordPress registration -> Laravel payment bridge -> Paystack -> Laravel webhook or verification reconciliation -> WordPress ticket callback.

## Paystack contract verified

- Transaction initialization and charge requests send `amount` in the currency subunit. For KES, a KES amount is multiplied by 100.
- Verification uses `GET /transaction/verify/{reference}` with the secret key and the returned `data.status` and `data.amount` must be checked before delivery.
- Paystack signs the raw webhook body with HMAC-SHA512 using the secret key in `x-paystack-signature`.
- Successful transaction data includes `receipt_number`, `metadata`, and an `authorization` object. Card holder name is read from `authorization.account_name`; the customer first/last name is only a fallback.
- M-Pesa is sent through `/charge` in `mobile_money.phone` with provider `mpesa`. Paystack's M-PESA documentation recommends the Kenyan country-coded `+254XXXXXXXXX` format, so the application sends Kenyan `01...` and `07...` input in that form.

Sources: Paystack Transaction API, Charge API, Accept Payments, Metadata, and Webhooks documentation.

## Changes made

- `PaystackService` now centralizes KES subunit conversion and uses `PAYSTACK_CURRENCY`.
- Laravel initiation verifies that the submitted amount matches the amount stored for the registration identified by both UUIDs. This prevents a modified hidden form amount from changing the gateway charge.
- Sandbox amount and phone overrides remain available only when `PAYSTACK_ENV=sandbox`; the example environment now defaults to `live`.
- `resolveGatewayPhone()` now returns the required `254XXXXXXXXX` format for both the sandbox fixture and live inputs, including live values supplied with a leading `+`.
- Webhook reconciliation stores `authorization.account_name` in encrypted `payer_name`, with customer name fallback. It also reads the authorization code from the nested authorization object.
- The example webhook URL is `https://usable-paltry-cameo.ngrok-free.dev/laravel-engine/public/api/paystack-webhook`.

## Live configuration and verification checklist

1. Copy the untracked `laravel-engine/.env.example` to `laravel-engine/.env` if needed.
2. Set the real live `PAYSTACK_PUBLIC_KEY` and `PAYSTACK_SECRET_KEY` in `.env`; do not commit them.
3. Set `PAYSTACK_ENV=live`, `PAYSTACK_CURRENCY=KES`, and the correct `WORDPRESS_URL` and callback secret.
4. Register the webhook URL above in the live Paystack dashboard developer settings. Test and live dashboard webhook settings are separate.
5. Confirm the Laravel route responds at `POST /api/paystack-webhook`, the ngrok tunnel forwards to the Laravel public API, and a signed request returns HTTP 200.
6. Run the focused Laravel tests after installing dependencies with Composer. The current checkout cannot run them because `laravel-engine/vendor/autoload.php` is absent.
7. Perform one low-value live card and one live M-Pesa payment, then verify the registration has matching `confirmed_amount`, `gateway_reference`, `receipt_number`, `payment_method`, and encrypted `payer_name`.

## Focused test result

Command: `php artisan test --filter=Paystack`

Result: `3 passed, 0 failed; 8 assertions` in `1.00s`, including live KES subunit conversion, card authorization name extraction, and sandbox/live Kenyan phone normalization.

## M-Pesa false validation investigation (2026-09-18)

### Captured local reproduction

The scenario was reproduced at `http://localhost/icrhk/event/kaimenyi` in a real browser using the local event page. The rendered page showed event id `2`, event slug `kaimenyi`, one published ticket named `Ticket 1`, ticket id `4`, and price `KES 5.00`.

With M-Pesa selected and the visible fields filled, the submit button became enabled and the actual `FormData` contained:

```text
payment_method=mpesa
full_name=Live Test User
email=live-test@example.com
phone=0712345678
ticket_type=Ticket 1
amount=5.00
event_id=2
ticket_type_id=4
action=cer_submit_registration
```

The browser reported valid email and name controls, and the phone value passed the client-side non-empty check. The server normalizer accepts `0712345678`, `+254712345678`, and `0113881491` after stripping non-digits. The latter becomes `254113881491` for Paystack.

### Root cause and fix

The false-submit blocker was a late CSS rule in `cer-event.css` that applied `pointer-events: none !important` to `#cer-registration-form .cer-primary-button`. That prevented the user from activating the submit control. The rule has been disabled by commenting it out. The loaded page now computes the button as `pointer-events: auto`.

The current checkout does not contain separate `cer-mpesa-panel` and `cer-card-panel` elements. It uses one shared form grid; JavaScript clears and hides the name field in card mode. Therefore a panel enable/disable mismatch was not the cause in this checkout. The captured M-Pesa `FormData` confirms that the field names match the WordPress handler (`full_name`, `email`, `phone`, `ticket_type_id`, `event_id`, and `payment_method`).

### Amount and gateway errors

Paystack documentation requires transaction amounts in the currency subunit. The Laravel bridge therefore sends KES 5 as `500` and sends the configured currency as `KES`. Paystack Charge API responses expose the payment state in `data.status` and gateway failures in `data.message`; a gateway rejection is not the WordPress `Please complete all required fields.` validation response.

### Verification boundary

The browser reproduction stopped before initiating a live charge, so no payment success is claimed. A real M-Pesa verification requires the intended customer phone number and email; the test value above was not used to send an STK request. Once those real customer details are supplied, submit the same form and capture the AJAX response/reference, then confirm the STK prompt, webhook/verification result, and paid registration record.

### Requested `0113881491` attempt

The requested front-end submission was then run with the KES 5 `Ticket 1` selection, phone `0113881491`, full name `Live Test User`, and email `live-test@example.com`.

- Before the phone fix, WordPress returned `Please complete all required fields.` because its normalizer only accepted `07...` and `2547...` values.
- After the fix, WordPress created the registration and Laravel read it from the shared MySQL `wp_evt_registrations` table. The phone was normalized to `254113881491`.
- The first Laravel request exposed a missing `storage/framework/views` directory and missing `config/view.php`; both runtime prerequisites were restored.
- The next request exposed the local Laravel database mismatch (`sqlite` instead of the WordPress MySQL database); `.env` now uses the WordPress database `icrhk`.
- The final AJAX response was HTTP 200 at WordPress level with the underlying gateway error: `HTTP 422: {"status":"failed","message":"Paystack secret key is not configured."}`.

 The live key was subsequently configured and Laravel configuration was rebuilt. The first retry reached Paystack with `254113881491` and returned HTTP 422 `Invalid phone number format`. Paystack's documentation recommends a leading `+` in the country-coded phone value, so the bridge was corrected to send `+254113881491`. The same front-end submission then succeeded: WordPress returned `success=true`, `payment_status=pending`, `display_text=Please complete authorization process on your mobile phone`, and Paystack reference `80a06349-541d-4a5d-bd08-11f86b568d81`. This confirms that `0113881491` is accepted when sent in Paystack's documented format; the earlier failure was the missing `+`, not the `01` prefix.