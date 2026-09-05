<?php declare(strict_types=1);

namespace App\Services\Payment;

final class PesaPalService implements PaymentGatewayInterface
{
    public static function normalizePhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', trim($phone));
        if ($digits === '' || ! preg_match('/^(?:254|0)?7\d{8}$/', $digits)) {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '254' . substr($digits, 1);
        }

        if (str_starts_with($digits, '254')) {
            return $digits;
        }

        return '254' . $digits;
    }

    public static function normalizePhone(string $phone): string
    {
        return self::normalizePhoneNumber($phone);
    }

    private string $baseUrl;
    private string $consumerKey;
    private string $consumerSecret;
    private string $callbackUrl;
    private string $ipnNotificationId;

    public function __construct()
    {
        $env = strtolower((string) env('PESAPAL_ENV', 'live'));
        $defaultBaseUrl = in_array($env, ['production', 'prod', 'live'], true)
            ? 'https://pay.pesapal.com/v3/api'
            : 'https://cybqa.pesapal.com/pesapalv3/api';

        $this->baseUrl = (string) env('PESAPAL_BASE_URL', $defaultBaseUrl);
        $this->consumerKey = (string) env('PESAPAL_CONSUMER_KEY', '');
        $this->consumerSecret = (string) env('PESAPAL_CONSUMER_SECRET', '');
        $this->callbackUrl = (string) env('PESAPAL_CALLBACK_URL', '');
        $this->ipnNotificationId = (string) env('PESAPAL_IPN_NOTIFICATION_ID', '');
    }

    public function requestToken(): array
    {
        $url = rtrim($this->baseUrl, '/') . '/Auth/RequestToken';
        $payload = [
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
        ];

        $response = $this->httpRequest('POST', $url, json_encode($payload), [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        if (($response['status'] ?? 0) >= 400) {
            logger()->error('PesaPal token request failed', [
                'url' => $url,
                'status' => $response['status'] ?? null,
                'body' => $response['body'] ?? null,
            ]);

            return [
                'ok' => false,
                'message' => $response['message'] ?? 'Token request failed',
                'details' => $response['data'] ?? null,
            ];
        }

        $data = $response['data'] ?? [];
        $token = $data['token'] ?? $data['access_token'] ?? null;

        if (empty($token)) {
            logger()->error('PesaPal token not returned', [
                'url' => $url,
                'status' => $response['status'] ?? null,
                'response' => $response['body'] ?? $data,
            ]);

            return ['ok' => false, 'message' => 'PesaPal token not returned', 'details' => $data];
        }

        return ['ok' => true, 'token' => $token, 'expires_in' => $data['expires_in'] ?? 300];
    }

    public function registerIpn(string $ipnUrl): array
    {
        $url = rtrim($this->baseUrl, '/') . '/URLSetup/RegisterIPN';
        $payload = [
            'url' => $ipnUrl,
            'ipn_notification_type' => 'POST',
            'status' => 'ACTIVE',
        ];

        $token = $this->requestToken();
        if (($token['ok'] ?? false) !== true) {
            return ['ok' => false, 'message' => $token['message'] ?? 'Unable to acquire PesaPal token.'];
        }

        $response = $this->httpRequest('POST', $url, json_encode($payload), [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        ]);

        return $response['data'] ?? ['ok' => (bool) ($response['status'] ?? 0) < 400, 'message' => $response['message'] ?? 'IPN registration response'];
    }

    public function submitOrder(array $payload): array
    {
        if ($this->ipnNotificationId === '') {
            return ['ok' => false, 'message' => 'PesaPal IPN notification ID is not configured.'];
        }

        $url = rtrim($this->baseUrl, '/') . '/Transactions/SubmitOrderRequest';
        $token = $this->requestToken();
        if (($token['ok'] ?? false) !== true) {
            return ['ok' => false, 'message' => $token['message'] ?? 'Unable to acquire PesaPal token.'];
        }

        $phoneNumber = self::normalizePhoneNumber((string) ($payload['phone'] ?? ''));
        $isMpesa = strtolower((string) ($payload['payment_method'] ?? 'mpesa')) === 'mpesa';
        if ($isMpesa && $phoneNumber === '') {
            return ['ok' => false, 'message' => 'A valid Kenyan phone number is required.'];
        }

        $requestedAmount = (float) ($payload['amount'] ?? 0);
        if ($requestedAmount <= 0) {
            return ['ok' => false, 'message' => 'A positive payment amount is required.'];
        }
        $isSandbox = strtolower((string) env('PESAPAL_ENV', 'live')) === 'sandbox';
        $amount = $isSandbox ? (float) env('PESAPAL_TEST_AMOUNT', 1.00) : $requestedAmount;

        $billingAddress = [
            'email_address' => (string) ($payload['email'] ?? ''),
            'country_code' => 'KE',
            'first_name' => (string) ($payload['full_name'] ?? '') ?: 'Card Holder',
            'last_name' => '',
        ];
        if ($phoneNumber !== '') {
            $billingAddress['phone_number'] = $phoneNumber;
        }

        $order = [
            'id' => (string) ($payload['payment_uuid'] ?? $payload['id'] ?? uniqid('pesapal_', true)),
            'currency' => strtoupper((string) ($payload['currency'] ?? 'KES')),
            'amount' => $amount,
            'description' => (string) ($payload['description'] ?? 'Event registration payment'),
            'callback_url' => ! empty($payload['callback_url']) ? $payload['callback_url'] : $this->callbackUrl,
            'type' => 'MERCHANT',
            'redirect_mode' => 'REDIRECT',
            'notification_id' => $this->ipnNotificationId,
            'language' => 'EN',
            'terms_and_conditions_id' => '',
            'billing_address' => $billingAddress,
        ];

        $response = $this->httpRequest('POST', $url, json_encode($order), [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['token'],
        ]);

        if (($response['status'] ?? 0) >= 400) {
            return [
                'ok' => false,
                'message' => $response['message'] ?? 'PesaPal order submission failed',
                'response' => $response['data'] ?? [],
                'http_status' => $response['status'] ?? 0,
            ];
        }

        $data = $response['data'] ?? [];
        $trackingId = $data['tracking_id'] ?? $data['order_tracking_id'] ?? null;
        $redirectUrl = $data['redirect_url'] ?? $data['redirectUrl'] ?? null;

        if (empty($trackingId) && ! empty($data['merchant_reference'])) {
            $trackingId = $data['merchant_reference'];
        }

        if (empty($trackingId) || empty($redirectUrl)) {
            return [
                'ok' => false,
                'message' => $data['error']['message'] ?? 'PesaPal did not return a checkout tracking ID and redirect URL.',
                'response' => $data,
            ];
        }

        return [
            'ok' => true,
            'tracking_id' => $trackingId,
            'redirect_url' => $redirectUrl,
            'status' => 'pending',
            'response' => $data,
        ];
    }

    public function getTransactionStatus(string $trackingId): array
    {
        $url = rtrim($this->baseUrl, '/') . '/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($trackingId);
        $token = $this->requestToken();
        if (($token['ok'] ?? false) !== true) {
            return ['ok' => false, 'message' => $token['message'] ?? 'Unable to acquire PesaPal token.'];
        }

        $response = $this->httpRequest('GET', $url, '', [
            'Accept: application/json',
            'Authorization: Bearer ' . $token['token'],
        ]);

        if (($response['status'] ?? 0) >= 400) {
            return ['ok' => false, 'message' => $response['message'] ?? 'Unable to fetch payment status.'];
        }

        $data = $response['data'] ?? [];

        return [
            'ok' => true,
            'status' => $data['payment_status_description'] ?? $data['payment_status'] ?? $data['status'] ?? 'unknown',
            'tracking_id' => $trackingId,
            'response' => $data,
        ];
    }

    public function verifyIpnSignature(array $payload, string $signature = ''): bool
    {
        if (empty($signature)) {
            return true;
        }

        $expected = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES), (string) env('PESAPAL_IPN_SECRET', $this->consumerSecret));
        return hash_equals($expected, $signature);
    }

    private function basicHeaders(): array
    {
        return [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret),
        ];
    }

    private function httpRequest(string $method, string $url, string $body = '', array $headers = []): array
    {
        $verifySsl = filter_var(env('PESAPAL_VERIFY_SSL', app()->environment('local') ? false : true), FILTER_VALIDATE_BOOLEAN);

        $contextOptions = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'ignore_errors' => true,
                'timeout' => 15,
            ],
            'ssl' => [
                'verify_peer' => $verifySsl,
                'verify_peer_name' => $verifySsl,
            ],
        ];

        $context = stream_context_create($contextOptions);
        $result = @file_get_contents($url, false, $context);

        $statusCode = 200;
        $responseHeaders = $http_response_header ?? [];
        foreach ($responseHeaders as $header) {
            if (preg_match('/^HTTP\//', $header)) {
                $statusCode = (int) preg_replace('/^HTTP\/\d+\.\d+\s+(\d+).*$/', '$1', $header);
            }
        }

        if ($result === false) {
            $lastError = error_get_last();
            return [
                'status' => $statusCode,
                'message' => 'PesaPal request failed.',
                'body' => null,
                'error' => $lastError['message'] ?? null,
                'headers' => $responseHeaders,
            ];
        }

        $decoded = json_decode($result, true);

        $message = 'Request completed';
        if (is_array($decoded)) {
            if (! empty($decoded['message']) && is_string($decoded['message'])) {
                $message = $decoded['message'];
            } elseif (! empty($decoded['error']['message']) && is_string($decoded['error']['message'])) {
                $message = $decoded['error']['message'];
            } elseif (! empty($decoded['error']['code']) && is_string($decoded['error']['code'])) {
                $message = $decoded['error']['code'];
            } elseif (! empty($decoded['error_description']) && is_string($decoded['error_description'])) {
                $message = $decoded['error_description'];
            }
        }

        return [
            'status' => $statusCode,
            'data' => is_array($decoded) ? $decoded : [],
            'body' => $result,
            'message' => $message,
        ];
    }
}
