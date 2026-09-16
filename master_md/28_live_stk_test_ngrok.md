# Live PesaPal STK Test via ngrok

## Test date

2026-08-24

## Scope

Live PesaPal authentication, IPN registration, and one KES 1.00 order to the test phone `+254719763089`. The consumer key and secret are intentionally redacted from this audit.

## Code and route verification

- `PesaPalService` selects `https://pay.pesapal.com/v3/api` for `PESAPAL_ENV=production`.
- Token authentication sends the consumer credentials in the JSON body with `Content-Type` and `Accept` headers; no Basic Authorization header is sent.
- Order submission uses the configured `PESAPAL_IPN_NOTIFICATION_ID` and nested `billing_address`.
- `routes/api.php` is registered by `bootstrap/app.php`.
- The original supplied path `/laravel-engine/public/api/...` returned Apache 404 because Apache serves `C:\xampp\htdocs`. The reachable path is:

  `https://usable-paltry-cameo.ngrok-free.dev/icrhk/laravel-engine/public/api/pesapal-ipn`

- Public health check returned `200` and `{"status":"ok"}`.
- Public POST to the IPN endpoint returned `200` and `{"status":"ok","payment_uuid":"","message":"IPN received."}`.

## ngrok

ngrok was installed with winget and an authenticated tunnel was already active for Apache port 80:

```text
ngrok http 80
```

Public hostname:

```text
https://usable-paltry-cameo.ngrok-free.dev
```

## Live environment configuration

The Laravel `.env` was updated and configuration was cleared:

```text
PESAPAL_ENV=production
PESAPAL_BASE_URL=https://pay.pesapal.com/v3/api
PESAPAL_CONSUMER_KEY=[redacted]
PESAPAL_CONSUMER_SECRET=[redacted]
PESAPAL_VERIFY_SSL=true
PESAPAL_CALLBACK_URL=https://usable-paltry-cameo.ngrok-free.dev/icrhk/laravel-engine/public/api/pesapal-ipn
PESAPAL_IPN_NOTIFICATION_ID=[registered value below]
```

Command:

```text
php artisan config:clear
INFO  Configuration cache cleared successfully.
```

## Authentication

The existing `PesaPalService::requestToken()` was executed against:

```text
POST https://pay.pesapal.com/v3/api/Auth/RequestToken
```

Redacted result:

```json
{"ok":true,"expires_in":300,"has_token":true}
```

The bearer token was not printed or stored in this document.

## IPN registration

The existing `PesaPalService::registerIpn()` registered the reachable POST endpoint:

```text
POST https://pay.pesapal.com/v3/api/URLSetup/RegisterIPN
url=https://usable-paltry-cameo.ngrok-free.dev/icrhk/laravel-engine/public/api/pesapal-ipn
ipn_notification_type=POST
```

PesaPal response:

```json
{
  "url": "https://usable-paltry-cameo.ngrok-free.dev/icrhk/laravel-engine/public/api/pesapal-ipn",
  "ipn_id": "e2da26ff-2cc8-4d6b-b7fd-d9fcbaebdb12",
  "notification_type": 1,
  "ipn_notification_type_description": "POST",
  "ipn_status": 1,
  "ipn_status_decription": "Active",
  "status": "200",
  "message": "Request processed successfully"
}
```

## Live order initiation

The existing `PesaPalService::submitOrder()` was called once with:

```text
amount=1.00
currency=KES
phone=254719763089
full_name=Test End User
email=test@example.com
```

The generated merchant reference was `ICRHK-LIVE-20260824111630`.

PesaPal response:

```json
{
  "ok": true,
  "tracking_id": "e4135373-97b3-4d69-9e08-d9fc9e23b1e8",
  "redirect_url": "https://pay.pesapal.com/iframe/PesapalIframe3/Index?OrderTrackingId=e4135373-97b3-4d69-9e08-d9fc9e23b1e8",
  "status": "pending",
  "response": {
    "order_tracking_id": "e4135373-97b3-4d69-9e08-d9fc9e23b1e8",
    "merchant_reference": "ICRHK-LIVE-20260824111630",
    "error": null,
    "status": "200"
  }
}
```

## Immediate status query

```text
tracking_id=e4135373-97b3-4d69-9e08-d9fc9e23b1e8
payment_status_description=INVALID
error.code=payment_details_not_found
error.message=Pending Payment
amount=1
currency=KES
payment_method=(empty)
confirmation_code=(empty)
```

This status does not prove that the handset prompt was delivered. It indicates that PesaPal has no completed payment details yet.

## Handset confirmation

The user confirmed that no prompt was received on `+254719763089`.

The final status query remained:

```text
payment_status_description=INVALID
error.code=payment_details_not_found
error.message=Pending Payment
payment_method=(empty)
confirmation_code=(empty)
```

## Conclusion

Backend live authentication, active IPN registration, and live order submission succeeded. However, the user confirmed that no handset prompt was received, and the final PesaPal status contains no payment details. Therefore this test does **not** prove that the system is capable of sending a real STK push. It proves only that the live account accepted the order request and returned a tracking ID.

Recommended next checks are PesaPal merchant-account enablement for live M-Pesa/mobile payments, whether PesaPal requires the hosted checkout flow to be completed before prompting, transaction status after additional propagation time, and ngrok callback/session health. No additional live charge was initiated during follow-up.
