<?php declare(strict_types=1);

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function requestToken(): array;
    public function registerIpn(string $ipnUrl): array;
    public function submitOrder(array $payload): array;
    public function getTransactionStatus(string $trackingId): array;
    public function verifyIpnSignature(array $payload, string $signature = ''): bool;
}
