# PesaPal STK Push Audit

## Scope

This audit covers the Laravel API path `PaymentController::initiate()` to `PesaPalService::submitOrder()` for the test end user phone `+254719763089`.

## Evidence Before the Fix

The checkout request accepted a nullable phone and forwarded it unchanged. The service built a hybrid order containing top-level `first_name`, `last_name`, `email`, `phone`, and `country_code`, while the PesaPal contract expects the customer fields inside `billing_address`.

The local `.env` had sandbox credentials, no `PESAPAL_IPN_NOTIFICATION_ID`, and `PESAPAL_CALLBACK_URL=https://example.com/pesapal/confirmation`. Existing logs show PesaPal rejected authentication with `invalid_api_credentials_provided` on August 23-24, 2026. Therefore the previously reported tracking ID cannot be reproduced from the current checkout configuration, and no claim can be made that an STK prompt was sent.

## Contract Comparison

The official PesaPal API 3 sample requires `id`, `currency`, `amount`, `description`, `callback_url`, and `notification_id`. It places `phone_number`, `email_address`, `country_code`, `first_name`, and `last_name` in `billing_address`. The phone is normalized to digits and the implementation now emits Kenyan MSISDN format such as `254719763089`.

## Fix Applied

- `phone` is required by the initiate endpoint.
- Phone values are normalized from `+254719763089`, `0719763089`, or `719763089` to `254719763089`; other values are rejected.
- The billing object now contains the required customer identity and contact fields.
- Unsupported top-level customer fields were removed from the submitted order.
- A missing IPN ID now aborts the order instead of silently submitting an invalid request.
- The outbound payload is logged with the phone, email, and notification ID redacted.
- `language=EN` and an empty `terms_and_conditions_id` are included as shown in the official sample.

## Captured Payload Shape

The production log intentionally redacts personal and account data. The resulting shape is:

```json
{
  "id": "<payment UUID>",
  "currency": "KES",
  "amount": 100,
  "description": "Event registration payment",
  "callback_url": "<public HTTPS callback>",
  "notification_id": "[redacted]",
  "language": "EN",
  "terms_and_conditions_id": "",
  "billing_address": {
    "email_address": "j***@example.com",
    "phone_number": "254719***89",
    "country_code": "KE",
    "first_name": "<first name>",
    "last_name": "<last name>"
  }
}
```

The real log entry for the test run at 2026-08-24 06:09:52 recorded the same shape with `id=ICRHK-STK-20260824060949`, `phone_number=254719***89`, `email_address=t***@example.com`, and a redacted IPN ID.

## Test Status

The corrected order was submitted successfully using the configured sandbox credentials:

- `tracking_id`: `329bd048-8c49-44a5-ac48-d9fcf90493d8`
- PesaPal response status: `200`
- redirect URL: `https://cybqa.pesapal.com/pesapaliframe/PesapalIframe3/Index?OrderTrackingId=329bd048-8c49-44a5-ac48-d9fcf90493d8`
- immediate status query: `payment_details_not_found` with message `Pending Payment`

The API accepted the order, but the user has not yet confirmed whether the STK prompt appeared on `+254719763089`. A pending order or redirect URL is not proof of handset delivery.

The follow-up status query returned the same result: `payment_status_description=INVALID`, `error.code=payment_details_not_found`, and `error.message=Pending Payment`, with no payment method or confirmation code. The user has now confirmed that no STK push was received.

## Delivery Finding

There is no remaining required-field discrepancy in the captured `SubmitOrderRequest`. The official API 3 sample and contract define this endpoint as creating an order and returning a hosted payment URL; they do not define a separate `force_stk` or `payment_method=MPESA` request field. The official demo sample instructs developers to use generated dummy codes for mobile-payment sandbox testing. Consequently, a valid order response and a correctly populated Kenyan phone do not prove that the sandbox will send a real handset prompt.

This run therefore proves that the application sent the end-user number correctly and PesaPal accepted the order, but it does not prove STK delivery. The missing prompt is not resolved by registering the phone, and the application must not claim that it was delivered.

## Required Account Setup

1. Keep the active sandbox credentials and set `PESAPAL_ENV=sandbox`.
2. Replace `PESAPAL_CALLBACK_URL=https://example.com/pesapal/confirmation` with the real public HTTPS callback for this application. The current URL is a placeholder and cannot deliver application callbacks.
3. Persist the registered IPN ID as `PESAPAL_IPN_NOTIFICATION_ID` after registering the real callback URL.
4. Open the returned hosted payment URL and use the sandbox's documented mobile-payment dummy-code flow. A real handset STK test requires PesaPal to enable/provide that capability for the merchant account; it cannot be forced by adding another documented SubmitOrder field.
5. For production handset prompts, validate the same corrected payload with an active live merchant configuration and confirm directly on `+254719763089`. A pending or redirect response alone is never confirmation of delivery.