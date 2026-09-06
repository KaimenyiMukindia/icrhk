<?php declare(strict_types=1);

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;

final class PaystackService
{
    private string $baseUrl = 'https://api.paystack.co';
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = (string) env('PAYSTACK_SECRET_KEY', '');
    }

    public function initializeTransaction(string $email, float $amount, array $metadata = [], array $channels = ['card', 'mobile_money']): array
    {
        if ($this->secretKey === '') {
            return ['ok' => false, 'message' => 'Paystack secret key is not configured.'];
        }
        if ($amount <= 0) {
            return ['ok' => false, 'message' => 'A positive payment amount is required.'];
        }

        $amount = $this->sandboxAmount($amount);
        $fullName = trim((string) ($metadata['full_name'] ?? ''));
        $response = Http::timeout(15)
            ->withToken($this->secretKey)
            ->acceptJson()
            ->post($this->baseUrl . '/transaction/initialize', [
                'email' => $email,
                'amount' => (int) round($amount * 100),
                'currency' => 'KES',
                'reference' => (string) ($metadata['payment_uuid'] ?? ''),
                'first_name' => $fullName,
                'phone' => (string) ($metadata['phone'] ?? ''),
                'channels' => array_values($channels),
                'metadata' => $metadata,
            ]);

        if (! $response->successful() || $response->json('status') !== true) {
            logger()->error('Paystack transaction initialization failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            return [
                'ok' => false,
                'message' => (string) ($response->json('message') ?? 'Unable to initialize Paystack transaction.'),
            ];
        }

        $data = (array) $response->json('data', []);
        if (empty($data['access_code']) || empty($data['reference'])) {
            return ['ok' => false, 'message' => 'Paystack did not return a transaction reference.'];
        }

        return [
            'ok' => true,
            'access_code' => (string) $data['access_code'],
            'reference' => (string) $data['reference'],
            'response' => $data,
        ];
    }

    public function verifyTransaction(string $reference): array
    {
        if ($this->secretKey === '') {
            return ['ok' => false, 'message' => 'Paystack secret key is not configured.'];
        }

        $response = Http::timeout(15)
            ->withToken($this->secretKey)
            ->acceptJson()
            ->get($this->baseUrl . '/transaction/verify/' . rawurlencode($reference));

        if (! $response->successful() || $response->json('status') !== true) {
            return [
                'ok' => false,
                'message' => (string) ($response->json('message') ?? 'Unable to verify Paystack transaction.'),
            ];
        }

        return [
            'ok' => true,
            'status' => (string) $response->json('data.status', 'unknown'),
            'response' => (array) $response->json('data', []),
        ];
    }

    public function chargeWithMobileMoney(string $email, float $amount, string $phone, string $reference, array $metadata = []): array
    {
        if ($this->secretKey === '') {
            return ['ok' => false, 'message' => 'Paystack secret key is not configured.'];
        }
        if ($amount <= 0 || $phone === '') {
            return ['ok' => false, 'message' => 'A positive amount and mobile-money phone number are required.'];
        }

        $amount = $this->sandboxAmount($amount);
        $response = Http::timeout(15)
            ->withToken($this->secretKey)
            ->acceptJson()
            ->post($this->baseUrl . '/charge', [
                'email' => $email,
                'amount' => (int) round($amount * 100),
                'currency' => 'KES',
                'reference' => $reference,
                'mobile_money' => [
                    'phone' => str_starts_with($phone, '+') ? $phone : '+' . $phone,
                    'provider' => 'mpesa',
                ],
                'metadata' => $metadata,
            ]);

        $body = (array) $response->json();
        $data = (array) ($body['data'] ?? []);

        // Paystack's top-level "message" is always "Charge attempted" regardless of
        // outcome; the real reason for a decline/failure lives in data.message.
        if (! $response->successful() || ($body['status'] ?? false) !== true) {
            logger()->error('Paystack mobile money charge rejected', [
                'http_status' => $response->status(),
                'body' => $body,
            ]);
            return [
                'ok' => false,
                'message' => (string) ($data['message'] ?? $body['message'] ?? 'Unable to start the M-Pesa payment.'),
            ];
        }

        $chargeReference = (string) ($data['reference'] ?? $reference);
        $chargeStatus = strtolower((string) ($data['status'] ?? 'pending'));

        if (in_array($chargeStatus, ['failed', 'timeout'], true)) {
            return [
                'ok' => false,
                'message' => (string) ($data['message'] ?? 'The M-Pesa payment was declined.'),
            ];
        }

        return [
            'ok' => true,
            'reference' => $chargeReference,
            'status' => $chargeStatus,
            'display_text' => (string) ($data['display_text'] ?? 'Approve the payment request on your phone.'),
            'response' => $data,
        ];
    }

    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        if ($this->secretKey === '' || $signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $payload, $this->secretKey), $signature);
    }

    private function sandboxAmount(float $amount): float
    {
        return strtolower((string) env('PAYSTACK_ENV', 'live')) === 'sandbox' ? 1.00 : $amount;
    }
}