# PesaPal Authentication Fix and Live Test Audit

## Summary

The payment gateway service was failing before order submission because it never authenticated with PesaPal successfully. The root cause was a combination of:

- an incorrect local default endpoint pattern for the sandbox environment,
- a request payload that did not send the actual `consumer_key` and `consumer_secret` values,
- and a generic response handler that masked the real upstream error from PesaPal.

The service was corrected to use the official PesaPal sandbox host and to send the real credentials in the JSON request body.

## Issue Found

The original implementation in [laravel-engine/app/Services/Payment/PesaPalService.php](../laravel-engine/app/Services/Payment/PesaPalService.php) was doing this:

- building the token URL from a default production/base value,
- creating an empty JSON body (`{}`),
- then checking only for the existence of a `token` field while discarding the full PesaPal error payload.

This resulted in the generic response:

```php
array (
  'ok' => false,
  'message' => 'PesaPal token not returned',
)
```

## Environment Verification

The environment values in [laravel-engine/.env](../laravel-engine/.env) were checked and were set to:

- `PESAPAL_ENV=sandbox`
- `PESAPAL_CONSUMER_KEY=ZlxSgE6gJJ/IbOd9m/ircqc60zXDB6Ly`
- `PESAPAL_CONSUMER_SECRET=frJT6j9R/7wsek4YW7OaLps0iRE=`

The official PesaPal documentation states the sandbox authentication endpoint is:

- `https://cybqa.pesapal.com/pesapalv3/api/Auth/RequestToken`

The production endpoint is:

- `https://pay.pesapal.com/v3/api/Auth/RequestToken`

## Fix Applied

The service was updated to:

1. pick the sandbox URL when `PESAPAL_ENV` is `sandbox`,
2. send the real consumer credentials as JSON:

```json
{
  "consumer_key": "...",
  "consumer_secret": "..."
}
```

3. preserve the full upstream error response for debugging,
4. allow local testing with `PESAPAL_VERIFY_SSL=false` while keeping the option explicit.

## Validation Command and Exact Result

The direct verification command used after the fix was a Laravel bootstrap script that calls the service method directly.

```bash
cd /xampp/htdocs/icrhk/laravel-engine
php -r "require 'vendor/autoload.php'; ... "
```

The resulting output was:

```php
array (
  'ok' => false,
  'message' => 'Invalid Access Token',
  'details' => 
  array (
    'error' => 
    array (
      'error_type' => 'authentication_error',
      'code' => 'invalid_api_credentials_provided',
      'message' => 'Invalid Access Token',
    ),
    'status' => NULL,
  ),
)
```

We also confirmed the same error from the direct upstream request to the sandbox host:

```json
{"error":{"error_type":"invalid_request_error","code":"invalid_api_request_parameters","message":""},"status":"500"}
```

## Conclusion

The code path is now fixed, but the supplied PesaPal consumer key and consumer secret are not valid for the sandbox account currently configured in this environment. That means no valid access token can be issued, so the STK push can never be triggered until the merchant account credentials are replaced with active sandbox credentials.

## Live STK Push Status

Status: blocked by invalid credentials.

No valid tracking ID or STK push could be generated because PesaPal rejected the authentication request:

- `invalid_api_credentials_provided`
- `Invalid Access Token`

## Next Required Action

Provide a valid PesaPal sandbox consumer key and consumer secret from the merchant dashboard, then re-run the initiation test. Once the credentials are valid, the service will be able to obtain a token and submit the order, after which a live STK push can be triggered to `254719763089`.

## Note on IPN

IPN testing on localhost is not meaningful without a public HTTPS callback URL. It should be tested only after deployment to a public host with a valid PesaPal callback registration.

## Live Retry: 2026-08-27

The live account credentials were applied to the Laravel environment. The public ngrok callback was registered successfully with the live account and returned this active notification ID:

`e2da26ff-2cc8-4d6b-b7fd-d9fcbaebdb12`

A fresh live M-Pesa initiation was then submitted for `KES 1.00` to `254719763089`.

Result:

```php
array (
  'ok' => true,
  'tracking_id' => '8025eca6-1278-4c3c-905f-d9f9b8668274',
  'status' => 'pending',
)
```

PesaPal also returned this checkout URL:

`https://pay.pesapal.com/iframe/PesapalIframe3/Index?OrderTrackingId=8025eca6-1278-4c3c-905f-d9f9b8668274`

The order was accepted by PesaPal and is pending customer completion. PesaPal API v3 returns a hosted checkout URL; it does not send an M-Pesa STK prompt from `SubmitOrderRequest` alone. The customer must open the returned checkout, select M-Pesa, enter or confirm the phone number, and proceed before the prompt can arrive.

The frontend was corrected in [wp-content/plugins/custom-event-registration/assets/js/cer-registration.js](../wp-content/plugins/custom-event-registration/assets/js/cer-registration.js) to load the returned PesaPal checkout for M-Pesa orders. Previously, the M-Pesa branch returned early and incorrectly told the user to wait for a prompt without opening the checkout.
