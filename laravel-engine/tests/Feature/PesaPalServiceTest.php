<?php

namespace Tests\Feature;

use App\Services\Payment\PesaPalService;
use Tests\TestCase;

class PesaPalServiceTest extends TestCase
{
    public function test_it_normalizes_kenyan_phone_numbers_for_stk_orders(): void
    {
        $this->assertSame('254712345678', PesaPalService::normalizePhoneNumber('+254712345678'));
        $this->assertSame('254712345678', PesaPalService::normalizePhoneNumber('0712345678'));
        $this->assertSame('254712345678', PesaPalService::normalizePhoneNumber('712345678'));
        $this->assertSame('', PesaPalService::normalizePhoneNumber('abc'));
    }

    public function test_it_can_submit_a_sandbox_order_with_valid_credentials(): void
    {
        $service = new PesaPalService();

        $result = $service->submitOrder([
            'payment_uuid' => 'REG-TEST-' . uniqid('', true),
            'amount' => 100.00,
            'currency' => 'KES',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '254719763089',
            'callback_url' => 'https://example.com/pesapal/confirmation',
            'country_code' => 'KE',
            'billing_address' => '123 Test Street',
            'city' => 'Nairobi',
            'state' => 'Nairobi County',
            'zip_code' => '00100',
        ]);

        $this->assertTrue($result['ok'] ?? false, json_encode($result));
        $this->assertNotEmpty($result['tracking_id'] ?? null);
        $this->assertNotEmpty($result['redirect_url'] ?? null);
    }
}
