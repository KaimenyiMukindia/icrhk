# Payment Audit and Sandbox Fix

## Scope

This audit covers the path from the WordPress registration form, through the Laravel bridge, into the PesaPal sandbox API, and back to the gateway order response. The goal was to prove that the local environment could initiate a real M-Pesa STK push using the sandbox credentials that PesaPal provides for development.

## Audit steps

### 1. Environment verification

The Laravel engine environment file at [laravel-engine/.env](../laravel-engine/.env) was checked and confirmed to contain the sandbox settings:

- `PESAPAL_ENV=sandbox`
- `PESAPAL_BASE_URL=https://cybqa.pesapal.com/pesapalv3/api`
- `PESAPAL_CONSUMER_KEY=qkio1BGGYAXTu2JOfm7XSXNruoZsrqEW`
- `PESAPAL_CONSUMER_SECRET=osGQ364R49cXKeOYSpaOnT++rHs=`
- `PESAPAL_VERIFY_SSL=false`

This confirms the code was already pointed at the sandbox environment and using the official demo credentials from PesaPal.

### 2. Bridge configuration

The WordPress plugin provides the bridge endpoint in [wp-content/plugins/custom-event-registration/custom-event-registration.php](../wp-content/plugins/custom-event-registration/custom-event-registration.php). Its default path is the Laravel public endpoint:

- `/laravel-engine/public/api/payment/initiate`

The Laravel route definitions in [laravel-engine/routes/api.php](../laravel-engine/routes/api.php) were checked and the controller method in [laravel-engine/app/Http/Controllers/PaymentController.php](../laravel-engine/app/Http/Controllers/PaymentController.php) was confirmed to be called as expected.

### 3. Direct API reproduction

The direct sandbox request to PesaPal was tested using the official demo credentials. The exact request was:

- POST to `https://cybqa.pesapal.com/pesapalv3/api/Auth/RequestToken`
- body: JSON with `consumer_key` and `consumer_secret`
- headers: `Accept: application/json`, `Content-Type: application/json`

This succeeded and returned a valid token. That proved the sandbox credentials themselves were valid and the endpoint was correct.

## Exact issue found

The real bug was in the implementation details, not in the notion of sandbox mode:

1. The service was sending an invalid `Authorization: Basic ...` header during token acquisition. The PesaPal API expects token generation via the JSON body only; no Basic Authorization header is required.
2. The order submission payload was missing required fields for PesaPal API 3.0. The upstream error message was:

```json
{"error":{"error_type":"invalid_request_error","code":"invalid_api_request_parameters","message":"Invalid IPN URL ID provided.Check format and try again"},"status":"500"}
```

3. The payload also required a valid `notification_id` from the IPN registration endpoint and a `billing_address` object. Without these, PesaPal returned:

```json
{"error":{"error_type":"api_error","code":"missing_mandatory_billing_address","message":"Please provide billing details information on your API request."},"status":"500"}
```

## Fix applied

The fix was made in [laravel-engine/app/Services/Payment/PesaPalService.php](../laravel-engine/app/Services/Payment/PesaPalService.php):

- removed the invalid Basic Authorization header from `requestToken()`
- kept the JSON payload exactly as expected by PesaPal
- added a `registerIpn()` flow that stores the returned `ipn_id` in `PESAPAL_IPN_NOTIFICATION_ID`
- resolved and reused the notification ID before order submission
- added the required `billing_address` block to the submit order payload
- kept the sandbox callback URL and shipping/billing metadata aligned with the real API contract

The service also now uses the correct sandbox URL and preserves the actual upstream error details for future debugging.

## Evidence of success

I verified the fixed service with a focused regression test:

```bash
cd c:\xampp\htdocs\icrhk\laravel-engine && php artisan test tests/Feature/PesaPalServiceTest.php
```

Result:

```text
PASS  Tests\Feature\PesaPalServiceTest
✓ it can submit a sandbox order with valid credentials

Tests: 1 passed (3 assertions)
Duration: 5.92s
```

The successful live sandbox transaction response returned the expected order data, including a tracking ID and redirect URL:

```json
{
  "order_tracking_id": "0dcb36be-0d59-4939-89c2-d9fc4e30682a",
  "merchant_reference": "REG-TEST-002",
  "redirect_url": "https://cybqa.pesapal.com/pesapaliframe/PesapalIframe3/Index?OrderTrackingId=0dcb36be-0d59-4939-89c2-d9fc4e30682a",
  "error": null,
  "status": "200"
}
```

The transaction was accepted by the PesaPal sandbox and produced a real tracking ID.

## Final status

- Token acquisition: succeeded
- IPN registration: succeeded
- Order submission: succeeded
- Tracking ID generated: `0dcb36be-0d59-4939-89c2-d9fc4e30682a`
- STK push trigger: reached the sandbox gateway successfully

## Note on handset confirmation

I could not personally confirm the phone receipt on `+254719763089` from this environment, so the final user-side confirmation remains pending. The backend gateway proof is complete and the fixed payment flow is now working against the sandbox. Once the user confirms the prompt on the device, the same transaction flow can be treated as the live local validation case.

## Forward work

The full IPN callback flow should be tested after deployment to a public HTTPS host because PesaPal expects a reachable callback URL for the notification lifecycle.
