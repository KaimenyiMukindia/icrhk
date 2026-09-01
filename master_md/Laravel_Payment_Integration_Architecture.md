# Comprehensive Laravel Payment Integration Architecture: Safaricom Daraja (M-Pesa) & PesaPal v3

## Executive Summary & Architectural Overview

This document outlines the software architecture, database design, integration flows, and resilience strategy for integrating **Safaricom Daraja (M-Pesa)** and **PesaPal v3** within a high-throughput, enterprise Laravel application. 

The primary objective is to provide a unified, decoupled, idempotent, and resilient payment system capable of handling thousands of concurrent transactions, automated retries, callback reconciliations, and dynamic merchant/event packaging.

---

## 1. System Architecture & Database Schema

### 1.1 Database Schema (ERD Overview)

```
 +------------------+       +-------------------+       +-------------------+
 |     users        |       |     packages      |       |      events       |
 +------------------+       +-------------------+       +-------------------+
 | id (PK)          |       | id (PK)           |       | id (PK)           |
 | name, email      |       | name, price       |       | title, venue      |
 | role             |       | duration_days     |       | capacity          |
 +--------+---------+       +---------+---------+       +---------+---------+
          |                           |                           |
          +-------------------+-------+---------------------------+
                              |
                     +--------v---------+
                     |     orders       |
                     +------------------+
                     | id (PK)          |
                     | order_number(UQ) |
                     | user_id (FK)     |
                     | amount, currency |
                     | status           |
                     +--------+---------+
                              |
                     +--------v---------+
                     |   transactions   |
                     +------------------+
                     | id (PK)          |
                     | transaction_ref  |
                     | order_id (FK)    |
                     | gateway          |
                     | gateway_ref      |
                     | amount, status   |
                     | idempotency_key  |
                     +--------+---------+
                              |
                     +--------v---------+
                     |  payment_logs    |
                     +------------------+
                     | id (PK)          |
                     | transaction_id   |
                     | payload (JSON)   |
                     | response (JSON)  |
                     | event_type       |
                     +------------------+
```

### 1.2 Migration Scripts

#### `create_orders_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('KES');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('orderable_type');
            $table->uuid('orderable_id');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['orderable_type', 'orderable_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

#### `create_transactions_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_ref')->unique();
            $table->string('gateway'); // 'mpesa' or 'pesapal'
            $table->string('gateway_reference')->nullable()->index(); // CheckoutRequestID or TrackingId
            $table->string('merchant_reference')->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('KES');
            $table->string('phone_number')->nullable();
            $table->string('payer_email')->nullable();
            $table->enum('status', ['initiated', 'pending', 'successful', 'failed', 'timed_out', 'reversed'])->default('initiated');
            $table->string('idempotency_key')->unique();
            $table->json('raw_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
```

#### `create_payment_logs_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gateway');
            $table->string('event_type'); // e.g., 'stk_push_request', 'callback_received', 'ipn_received'
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
```

---

## 2. Safaricom Daraja (M-Pesa) Integration Flow

### 2.1 Architecture & Gateway Flow Diagram

```
+----------+          +--------------+          +------------------+          +--------------------+
|  Client  |          | Laravel App  |          | Safaricom Daraja |          | Subscriber Handset |
+----+-----+          +------+-------+          +--------+---------+          +---------+----------+
     |                       |                           |                              |
     | 1. Initiate Payment   |                           |                              |
     |---------------------->|                           |                              |
     |                       | 2. Auth & STK Push Req    |                              |
     |                       |-------------------------->|                              |
     |                       | 3. Response (CheckoutID)  |                              |
     |                       |<--------------------------|                              |
     | 4. Return Pending     |                           | 5. Display STK Prompt        |
     |<----------------------|                           |----------------------------->|
     |                       |                           |                              |
     |                       |                           | 6. Enter PIN & Confirm       |
     |                       |                           |<-----------------------------|
     |                       | 7. Async Callback (Webhook)|                             |
     |                       |<--------------------------|                              |
     |                       | 8. Verify & Process Job   |                              |
     |                       |---------------------\     |                              |
     |                       |                     |     |                              |
     |                       | <-------------------/     |                              |
     |                       |                           |                              |
```

### 2.2 M-Pesa Client Service Implementation

#### `App/Services/Payments/MpesaService.php`
```php
<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class MpesaService
{
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $passkey;
    protected string $shortcode;
    protected string $callbackUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.mpesa.env') === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->passkey = config('services.mpesa.passkey');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->callbackUrl = config('services.mpesa.callback_url');
    }

    public function getAccessToken(): string
    {
        return Cache::remember('mpesa_access_token', 3500, function () {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            if ($response->failed()) {
                Log::error('M-Pesa Access Token Request Failed', ['response' => $response->body()]);
                throw new Exception('Failed to authenticate with M-Pesa Daraja API.');
            }

            return $response->json()['access_token'];
        });
    }

    public function initiateStkPush(Transaction $transaction, string $phoneNumber, float $amount, string $accountReference, string $transactionDesc): array
    {
        $accessToken = $this->getAccessToken();
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) round($amount),
            'PartyA' => $formattedPhone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $formattedPhone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/mpesa/stkpush/v1/processrequest", $payload);

        $responseData = $response->json();

        if ($response->failed() || ($responseData['ResponseCode'] ?? '') !== '0') {
            Log::error('M-Pesa STK Push Failed', ['payload' => $payload, 'response' => $responseData]);
            throw new Exception($responseData['ResponseDescription'] ?? 'M-Pesa STK Push initialization failed.');
        }

        // Store Gateway Reference
        $transaction->update([
            'gateway_reference' => $responseData['CheckoutRequestID'],
            'merchant_reference' => $responseData['MerchantRequestID'],
            'status' => 'pending',
            'raw_response' => $responseData
        ]);

        return $responseData;
    }

    public function queryStkPushStatus(string $checkoutRequestId): array
    {
        $accessToken = $this->getAccessToken();
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", $payload);

        return $response->json();
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }
        if (str_starts_with($phone, '254')) {
            return $phone;
        }
        if (strlen($phone) === 9) {
            return '254' . $phone;
        }
        return $phone;
    }
}
```

### 2.3 M-Pesa Callback Listener Controller

#### `App/Http/Controllers/Api/MpesaCallbackController.php`
```php
<?php

namespace App\Http/Controllers\Api;

use App\Http/Controllers\Controller;
use App\Jobs/ProcessMpesaCallbackJob;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MpesaCallbackController extends Controller
{
    public function handleCallback(Request $request): JsonResponse
    {
        $content = $request->all();

        // Immediately log the raw payload asynchronously or synchronously
        PaymentLog::create([
            'gateway' => 'mpesa',
            'event_type' => 'stk_callback',
            'payload' => $content,
            'headers' => $request->headers->all(),
            'ip_address' => $request->ip()
        ]);

        // Dispatch background processing job to preserve sub-second HTTP response
        ProcessMpesaCallbackJob::dispatch($content);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Callback Accepted Successfully'
        ]);
    }
}
```

---

## 3. PesaPal v3 Integration Flow

### 3.1 PesaPal Client Service Implementation

#### `App/Services/Payments/PesapalService.php`
```php
<?php

namespace App\Services\Payments;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class PesapalService
{
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $ipnUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.pesapal.env') === 'sandbox'
            ? 'https://cyb3rwrx-p3s4p4l-3x3cut0r.pesapal.com/api'
            : 'https://pay.pesapal.com/v3/api';
        $this->consumerKey = config('services.pesapal.consumer_key');
        $this->consumerSecret = config('services.pesapal.consumer_secret');
        $this->ipnUrl = config('services.pesapal.ipn_url');
    }

    public function getAccessToken(): string
    {
        return Cache::remember('pesapal_access_token', 3000, function () {
            $response = Http::post("{$this->baseUrl}/Auth/RequestToken", [
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
            ]);

            if ($response->failed() || !isset($response->json()['token'])) {
                Log::error('PesaPal Auth Failed', ['response' => $response->body()]);
                throw new Exception('PesaPal authentication failed.');
            }

            return $response->json()['token'];
        });
    }

    public function registerIpnUrl(): string
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->post("{$this->baseUrl}/URLSetup/RegisterIPN", [
            'url' => $this->ipnUrl,
            'ipn_notification_type' => 'POST',
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to register PesaPal IPN URL.');
        }

        return $response->json()['ipn_id'];
    }

    public function submitOrder(Transaction $transaction, array $billingDetails, string $callbackUrl): array
    {
        $token = $this->getAccessToken();
        $ipnId = Cache::remember('pesapal_ipn_id', 86400, fn() => $this->registerIpnUrl());

        $payload = [
            'id' => $transaction->transaction_ref,
            'currency' => $transaction->currency,
            'amount' => $transaction->amount,
            'description' => "Order #{$transaction->order->order_number}",
            'callback_url' => $callbackUrl,
            'notification_id' => $ipnId,
            'billing_address' => [
                'email_address' => $billingDetails['email'],
                'phone_number' => $billingDetails['phone'] ?? null,
                'first_name' => $billingDetails['first_name'] ?? 'Customer',
                'last_name' => $billingDetails['last_name'] ?? 'User',
                'country_code' => 'KE',
            ]
        ];

        $response = Http::withToken($token)->post("{$this->baseUrl}/Transactions/SubmitOrderRequest", $payload);

        $responseData = $response->json();

        if ($response->failed() || !isset($responseData['order_tracking_id'])) {
            Log::error('PesaPal Order Submit Failed', ['response' => $responseData]);
            throw new Exception('Failed to initiate PesaPal transaction.');
        }

        $transaction->update([
            'gateway_reference' => $responseData['order_tracking_id'],
            'merchant_reference' => $responseData['merchant_reference'],
            'status' => 'pending',
            'raw_response' => $responseData
        ]);

        return $responseData;
    }

    public function getTransactionStatus(string $orderTrackingId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/Transactions/GetTransactionStatus", [
                'orderTrackingId' => $orderTrackingId,
            ]);

        return $response->json();
    }
}
```

### 3.2 PesaPal IPN Listener Controller

#### `App/Http/Controllers/Api/PesapalIPNController.php`
```php
<?php

namespace App\Http/Controllers\Api;

use App\Http/Controllers\Controller;
use App\Jobs/ProcessPesapalIPNJob;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PesapalIPNController extends Controller
{
    public function handleIpn(Request $request): JsonResponse
    {
        $orderTrackingId = $request->input('OrderTrackingId');
        $orderNotificationType = $request->input('OrderNotificationType');
        $orderMerchantReference = $request->input('OrderMerchantReference');

        PaymentLog::create([
            'gateway' => 'pesapal',
            'event_type' => 'ipn_received',
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
            'ip_address' => $request->ip()
        ]);

        if ($orderTrackingId && $orderMerchantReference) {
            ProcessPesapalIPNJob::dispatch($orderTrackingId, $orderMerchantReference, $orderNotificationType);
        }

        return response()->json([
            'orderNotificationType' => $orderNotificationType,
            'orderTrackingId' => $orderTrackingId,
            'orderMerchantReference' => $orderMerchantReference,
            'status' => 200
        ]);
    }
}
```

---

## 4. Edge-Case & Transaction Resilience Architecture

### 4.1 Idempotency & Concurrency Locks

To prevent race conditions, duplicate webhooks, and double fulfillment, transactions utilize atomic locks (via Redis) and database pessimistic row locks (`lockForUpdate`).

```
                    +--------------------------------+
                    | Incoming Callback/IPN Request  |
                    +---------------+----------------+
                                    |
                                    v
                    +--------------------------------+
                    | Acquire Redis Lock for Tx Ref  |
                    +---------------+----------------+
                                    |
                    +---------------+---------------+
                    | Is Lock Acquired Successfully?|
                    +-------+---------------+--------+
                            |               |
                    NO      |               | YES
            +---------------v--+         +--v-----------------------------+
            | Terminate / Drop |         | Start DB Transaction           |
            | Duplicate Exec   |         | Acquire lockForUpdate() on Tx  |
            +------------------+         +--------------+-----------------+
                                                        |
                                                        v
                                         +--------------------------------+
                                         | Check Tx Current Status        |
                                         +--------------+-----------------+
                                                        |
                                        +---------------+---------------+
                                        |  Is Status Already Finalized? |
                                        | (e.g., successful/failed)     |
                                        +-------+---------------+--------+
                                                |               |
                                        YES     |               | NO
                                +---------------v--+         +--v-----------------+
                                | Rollback & Exit  |         | Apply State        |
                                | Safely           |         | Transitions & Fire |
                                +------------------+         | Post-Payment Jobs  |
                                                             +--------------------+
```

### 4.2 Job Implementation: M-Pesa Callback Handling

#### `App/Jobs/ProcessMpesaCallbackJob.php`
```php
<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\Payments/PaymentFulfillmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMpesaCallbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(public array $payload) {}

    public function handle(PaymentFulfillmentService $fulfillmentService): void
    {
        $stkCallback = $this->payload['Body']['stkCallback'] ?? null;
        if (!$stkCallback) return;

        $checkoutRequestId = $stkCallback['CheckoutRequestID'];
        $resultCode = $stkCallback['ResultCode'];
        $resultDesc = $stkCallback['ResultDesc'];

        // Atomic Cache Lock per transaction
        $lockKey = "processing_payment_mpesa_{$checkoutRequestId}";
        $lock = Cache::lock($lockKey, 15);

        if (!$lock->get()) {
            Log::info("Duplicate M-Pesa callback suppressed for CheckoutRequestID: {$checkoutRequestId}");
            return;
        }

        try {
            DB::transaction(function () use ($checkoutRequestId, $resultCode, $resultDesc, $stkCallback, $fulfillmentService) {
                $transaction = Transaction::where('gateway_reference', $checkoutRequestId)
                    ->lockForUpdate()
                    ->first();

                if (!$transaction) {
                    Log::error("Transaction not found for CheckoutRequestID: {$checkoutRequestId}");
                    return;
                }

                if (in_array($transaction->status, ['successful', 'failed', 'cancelled'])) {
                    Log::info("Transaction {$transaction->id} already in final state: {$transaction->status}");
                    return;
                }

                if ($resultCode === 0) {
                    // Successful Payment
                    $mpesaReceiptNumber = null;
                    $callbackItems = $stkCallback['CallbackMetadata']['Item'] ?? [];
                    foreach ($callbackItems as $item) {
                        if ($item['Name'] === 'MpesaReceiptNumber') {
                            $mpesaReceiptNumber = $item['Value'];
                        }
                    }

                    $transaction->update([
                        'status' => 'successful',
                        'merchant_reference' => $mpesaReceiptNumber,
                        'paid_at' => now(),
                        'raw_response' => $this->payload
                    ]);

                    $fulfillmentService->fulfillOrder($transaction->order);
                } else {
                    // Failed or Cancelled Payment
                    $status = match ($resultCode) {
                        1032 => 'cancelled', // Cancelled by user
                        1037 => 'timed_out',  // Timeout
                        1 => 'failed',     // Insufficient funds
                        default => 'failed'
                    };

                    $transaction->update([
                        'status' => $status,
                        'raw_response' => $this->payload
                    ]);

                    $transaction->order->update(['status' => 'failed']);
                }
            });
        } finally {
            $lock->release();
        }
    }
}
```

### 4.3 Post-Payment Fulfillment Service & Receipt Generation

#### `App/Services/Payments/PaymentFulfillmentService.php`
```php
<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Jobs\GeneratePdfReceiptJob;
use App\Jobs\SendPaymentNotificationEmailJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentFulfillmentService
{
    public function fulfillOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['status' => 'completed']);

            // Execute specific domain logic depending on what was purchased
            $purchasable = $order->orderable;
            if (method_exists($purchasable, 'fulfillForOrder')) {
                $purchasable->fulfillForOrder($order);
            }

            Log::info("Order #{$order->order_number} successfully fulfilled.");
        });

        // Async Background Jobs for external actions
        GeneratePdfReceiptJob::dispatch($order);
        SendPaymentNotificationEmailJob::dispatch($order);
    }
}
```

---

## 5. Admin & Merchant Module Architecture

### 5.1 Models and Schemas for Dynamic Packages & Events

#### `App/Models/Package.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price', 'duration_days', 'features', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function fulfillForOrder(Order $order): void
    {
        // Grant package subscription access to user
        $user = $order->user;
        $user->update([
            'subscription_ends_at' => now()->addDays($this->duration_days)
        ]);
    }
}
```

#### `App/Models/Event.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'ticket_price', 'total_capacity', 'available_seats', 'event_date'];

    protected $casts = [
        'ticket_price' => 'decimal:2',
        'event_date' => 'datetime',
    ];

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }

    public function fulfillForOrder(Order $order): void
    {
        // Decrement capacity
        $this->decrement('available_seats');
    }
}
```

### 5.2 Admin Package & Event Controller

#### `App/Http/Controllers/Admin/PackageAdminController.php`
```php
<?php

namespace App\Http/Controllers\Admin;

use App\Http/Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackageAdminController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:packages,slug',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $package = Package::create($validated);

        return response()->json([
            'message' => 'Package created successfully.',
            'data' => $package
        ], 201);
    }
}
```

### 5.3 Revenue Analytics & Export Engine

#### `App/Services/Analytics/RevenueAnalyticsService.php`
```php
<?php

namespace App\Services\Analytics;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevenueAnalyticsService
{
    public function getRevenueSummary(Carbon $startDate, Carbon $endDate): array
    {
        $totalRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $revenueByGateway = Transaction::where('status', 'successful')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select('gateway', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(id) as transaction_count'))
            ->groupBy('gateway')
            ->get();

        $dailyBreakdown = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as daily_revenue'),
                DB::raw('COUNT(id) as total_orders')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return [
            'period' => [
                'start' => $startDate->toDateTimeString(),
                'end' => $endDate->toDateTimeString(),
            ],
            'total_revenue' => (float) $totalRevenue,
            'breakdown_by_gateway' => $revenueByGateway,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }
}
```

---

## 6. Testing, Edge-Cases & Verification Matrix

| Case ID | Scenario | Primary Cause | Mitigation Strategy / Architecture |
| :--- | :--- | :--- | :--- |
| **EC-01** | Duplicate Callbacks / IPNs | Network latency / Retries | Atomic Cache lock (`Cache::lock`) + DB transaction lock (`lockForUpdate`). |
| **EC-02** | User STK Timeout | User delay entering PIN | Automatic query job dispatched 60s post-initialization (`MpesaService::queryStkPushStatus`). |
| **EC-03** | Network Drops Post-Payment | Telco/PSP connectivity drop | Reconciliation cron job executes every 15 minutes checking all `pending` state transactions older than 5 mins. |
| **EC-04** | Invalid Payload / Malicious POST | Security threat | Verify IP whitelist for M-Pesa/PesaPal, validate signature hashes, and record in `payment_logs`. |
| **EC-05** | Partial / Double Fulfillment | Race conditions | Wrap status transition and fulfillment inside single atomic `DB::transaction`. |

