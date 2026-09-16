# Payment Flow Separation Fix

Date: 2026-08-28

## Summary

Separated the WordPress payment-method behavior so M-Pesa opens the PesaPal checkout returned by `SubmitOrderRequest`, while Card remains in the dedicated hosted iframe. Existing Laravel bridge, PesaPal v3 authentication, IPN processing, encrypted PII, ticket generation, email delivery, and duplicate-IPN handling remain in place.

## Issues Found

- The M-Pesa branch received `redirect_url` but did not open it, preventing the hosted PesaPal flow from initiating the handset prompt.
- Card and M-Pesa shared stale iframe and polling state when the toggle changed.
- Card orders could submit an empty `phone_number` in `billing_address`.
- Submit-state validation was not synchronized with the active method.
- Card's hosted-payment area had no explicit pre-submit placeholder/loading state.

## Changes

### Frontend

`wp-content/plugins/custom-event-registration/assets/js/cer-registration.js`

- M-Pesa now navigates to the returned PesaPal `redirect_url` after a successful bridge response.
- Card loads the returned URL into the existing in-page iframe.
- Method toggles clear the iframe source, active poll timer, tracking ID, submitted state, and payment panel classes.
- M-Pesa requires Full Name, Email, Phone, and Ticket Type.
- Card hides Full Name and Phone but keeps Email and Ticket Type.
- The submit control is disabled until the active method has valid required fields.
- Card uses a method-specific button label and disables the form while the iframe checkout is active.

`wp-content/plugins/custom-event-registration/assets/css/cer-event.css`

- Added responsive Card placeholder and order-preparation loading states.
- Preserved the existing iframe dimensions and event-page styling.

### Backend

`laravel-engine/app/Http/Controllers/PaymentController.php`

- M-Pesa validation still requires Full Name, Email, and Phone.
- Card accepts Email without Phone or Full Name and supplies `Card Holder` as the PesaPal fallback name.
- Existing IPN handling remains unchanged in its status, idempotency, ticket, and email responsibilities.

`laravel-engine/app/Services/Payment/PesaPalService.php`

- `billing_address.phone_number` is included only when a valid phone is supplied.
- Card orders therefore omit phone from the billing address; M-Pesa orders retain normalized `254XXXXXXXXX` phone values.
- JSON-body PesaPal authentication and the existing production/sandbox endpoint selection are preserved.

## Verification

Commands run from `C:\xampp\htdocs\icrhk`:

```text
php -l wp-content/plugins/custom-event-registration/custom-event-registration.php
php -l wp-content/plugins/custom-event-registration/includes/registration-functions.php
php -l laravel-engine/app/Http/Controllers/PaymentController.php
php -l laravel-engine/app/Services/Payment/PesaPalService.php
node --check wp-content/plugins/custom-event-registration/assets/js/cer-registration.js
git diff --check
```

Result: all PHP files reported `No syntax errors detected`; JavaScript syntax and whitespace checks passed.

A live PesaPal order probe from the existing production configuration previously returned:

```text
ok: 1
tracking_id: b03dc485-6cb6-4bfa-a2b8-d9f8ddc0e4fe
redirect_url: https://pay.pesapal.com/iframe/PesapalIframe3/Index?OrderTrackingId=b03dc485-6cb6-4bfa-a2b8-d9f8ddc0e4fe
status: pending
```

No card data was submitted by the probe. It created an unpaid KES 1 order only.

The browser page reload used for the final DOM check timed out while the local WordPress page was loading, so no paid M-Pesa or Card transaction is claimed here. No handset STK confirmation, paid IPN, ticket email, or duplicate-IPN result was fabricated. Those require completing a real gateway transaction and receiving the callback through the active public endpoint.

## Database State

The existing `wp_evt_registrations` payment fields remain intact, including `payment_uuid`, `gateway_reference`, `payment_method`, `status`, `amount`, payer metadata, and ticket timestamps. No existing registration rows were rewritten by this fix.

The existing `payment_logs` table continues to receive IPN and duplicate-IPN records through `PaymentController::ipn()`.

## Deviations

- The requested sandbox test was not run because the active local environment remains configured for the previously working production PesaPal endpoint and registered callback. Configuration was not changed during this fix.
- A real paid flow was not claimed because the available checks only verified code validity and successful order initiation; gateway completion and callback delivery require external handset/card action.

## Structural UI Correction: 2026-08-28

The first implementation still used one shared field grid and one shared visible action, which allowed hidden method state to remain mixed. The form was corrected to contain two isolated panels:

- `cer-mpesa-panel`: Full Name, Email, Phone, Ticket Type, and the only WordPress submit button.
- `cer-card-panel`: Email and Ticket Type only, with no WordPress submit button.

When the method changes, inactive controls are disabled and therefore excluded from `FormData`. The iframe source, polling timer, tracking ID, submitted state, and payment-panel classes are cleared. Card starts its order only when its own email and ticket controls are valid; M-Pesa submits only from its own panel.

Browser DOM verification on the local event page confirmed:

```text
M-Pesa selected: M-Pesa panel visible, Card panel hidden, M-Pesa submit visible.
Card selected: M-Pesa panel hidden, Card panel visible, Card email/ticket enabled, WordPress submit display=none.
```

## Bridge Responsiveness Correction: 2026-08-28

An interaction test exposed a separate failure: Card sent the expected isolated payload to `admin-ajax.php`, but the browser remained at `Submitting registration...` and the iframe stayed `about:blank` while the WordPress bridge waited for its Laravel response. The bridge timeout was reduced to 20 seconds, the PesaPal HTTP timeout to 15 seconds, and the WordPress handler now displays the actual Laravel/PesaPal error instead of masking it with a generic indefinite-looking state. Card also shows its loading panel before the bridge response arrives.

The local Laravel health endpoint returned `HTTP 200` after this correction. The final fresh browser navigation intermittently timed out while loading the local WordPress page, so no new paid transaction is claimed. The previously accepted live Card order and the live KES 1 M-Pesa order remain unpaid/pending according to PesaPal status; no ticket or email was generated without a paid IPN.

## Immediate Card Surface Correction: 2026-08-28

The Card toggle was then verified against the live DOM. Selecting Card now immediately produces:

```text
Card panel: visible
M-Pesa panel: hidden
M-Pesa submit button: hidden
Card hosted-payment container: visible
PesaPal iframe element: present
```

The iframe `src` remains empty until Card Email and Ticket are valid, at which point the WordPress bridge creates the PesaPal order and assigns its returned `redirect_url`. This is required because PesaPal cannot produce a checkout URL without an order payload; the payment surface itself is visible immediately on Card selection.

## Live Verification Update: 2026-08-28

The existing production configuration was tested against the active ngrok endpoint:

```text
PESAPAL_BASE_URL=https://pay.pesapal.com/v3/api
PESAPAL_CALLBACK_URL=https://usable-paltry-cameo.ngrok-free.dev/icrhk/laravel-engine/public/api/pesapal-ipn
PESAPAL_IPN_NOTIFICATION_ID=[configured and redacted]
```

The public callback responded through Apache/Laravel with `405 Method Not Allowed` to a GET probe, which is expected because the route accepts POST only.

Live authentication succeeded. A live M-Pesa order for the selected KES 2,500 Student ticket was rejected before checkout:

```text
message: Transaction amount exceeds limit.Contact support for assistance
```

A controlled live KES 1 M-Pesa order was accepted:

```text
registration_uuid: e1fbb3c2-7825-4375-8200-2e2b09d4c50d
payment_uuid: 5a7164d7-4a4a-4b6b-8aff-25148af30ea1
tracking_id: efd40a6b-1b1a-4ff8-b5bd-d9f8a97d85ac
redirect_url: https://pay.pesapal.com/iframe/PesapalIframe3/Index?OrderTrackingId=efd40a6b-1b1a-4ff8-b5bd-d9f8a97d85ac
status: pending
```

The hosted PesaPal page emitted a browser-side `TypeError: Right-hand side of 'instanceof' is not an object`. PesaPal transaction status then returned:

```text
payment_status_description: INVALID
error.code: payment_details_not_found
error.message: Pending Payment
status_code: 0
```

No handset prompt or paid IPN was received. Registration `26` remains `awaiting_payment` with the tracking ID; no ticket PDF or email was generated. The rejected KES 2,500 test registration `25` was marked `failed`. This is recorded as an upstream merchant-limit/hosted-checkout blocker, not as a successful payment.
