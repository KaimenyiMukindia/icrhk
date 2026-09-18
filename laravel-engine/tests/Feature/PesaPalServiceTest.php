<?php

namespace Tests\Feature;

use App\Services\Payment\PaystackService;
use App\Services\Payment\PesaPalService;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PesaPalServiceTest extends TestCase
{
    public function test_it_normalizes_kenyan_phone_numbers_for_stk_orders(): void
    {
        $this->assertSame('254712345678', PesaPalService::normalizePhoneNumber('+254712345678'));
        $this->assertSame('254712345678', PesaPalService::normalizePhoneNumber('0712345678'));
        $this->assertSame('254712345678', PesaPalService::normalizePhoneNumber('712345678'));
        $this->assertSame('', PesaPalService::normalizePhoneNumber('abc'));
    }

    public function test_it_uses_the_paystack_sandbox_mobile_money_phone_in_sandbox_mode(): void
    {
        Config::set('services.paystack.environment', 'sandbox');

        $this->assertSame('+254710000000', PaystackService::resolveGatewayPhone('254719763089'));
        $this->assertSame('+254710000000', PaystackService::resolveGatewayPhone('0719763089'));

        Config::set('services.paystack.environment', 'live');
        $this->assertSame('+254719763089', PaystackService::resolveGatewayPhone('254719763089'));
        $this->assertSame('+254719763089', PaystackService::resolveGatewayPhone('+254719763089'));
        $this->assertSame('+254113881491', PaystackService::resolveGatewayPhone('0113881491'));
        $this->assertSame('+254113881491', PaystackService::resolveGatewayPhone('+254113881491'));
    }

    public function test_it_converts_live_kes_amounts_to_paystack_subunits(): void
    {
        Config::set('services.paystack.environment', 'live');
        Config::set('services.paystack.secret_key', 'sk_live_test');

        $this->assertSame(125050, PaystackService::amountToSubunit(1250.50));

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'access_code' => 'access-code',
                    'reference' => 'reference',
                ],
            ]),
        ]);

        $service = new PaystackService();
        $result = $service->initializeTransaction('jane@example.com', 1250.50, [
            'payment_uuid' => 'payment-uuid',
            'phone' => '0712345678',
        ]);

        $this->assertTrue($result['ok']);
        Http::assertSent(function ($request): bool {
            return $request['amount'] === 125050
                && $request['currency'] === 'KES'
                && $request['phone'] === '+254712345678';
        });
    }

    public function test_it_reads_card_holder_name_from_paystack_authorization(): void
    {
        $this->assertSame('Jane Doe', PaystackService::extractPayerName([
            'authorization' => ['account_name' => 'Jane Doe'],
            'customer' => ['first_name' => 'Gateway', 'last_name' => 'Fallback'],
        ]));
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
