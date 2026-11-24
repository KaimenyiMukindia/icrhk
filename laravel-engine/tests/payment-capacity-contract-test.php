<?php

declare(strict_types=1);

use App\Http\Controllers\PaymentController;
use App\Services\Payment\PaystackService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Monolog\Handler\NullHandler;

if (PHP_SAPI !== 'cli') {
    exit("Run this test from the command line.\n");
}

$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require $base . '/bootstrap/app.php';
$app->useEnvironmentPath(sys_get_temp_dir());
$app->loadEnvironmentFrom('cer-capacity-test-' . bin2hex(random_bytes(8)));
$app->make(Kernel::class)->bootstrap();

$sqlite = ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true];
config([
    'database.default' => 'sqlite',
    'database.connections.sqlite' => $sqlite,
    'database.connections.wordpress' => $sqlite,
    'logging.default' => 'capacity_test',
    'logging.channels.capacity_test' => ['driver' => 'monolog', 'handler' => NullHandler::class],
    'services.paystack.secret_key' => 'fixture-webhook-secret',
    'services.paystack.environment' => 'live',
    'services.wordpress.url' => 'http://127.0.0.1',
    'services.wordpress.ticket_callback_secret' => 'fixture-ticket-secret',
]);
DB::purge('sqlite');
DB::purge('wordpress');
$logs = DB::connection('sqlite');
$wordpress = DB::connection('wordpress');
$logs->statement('CREATE TABLE payment_logs (id INTEGER PRIMARY KEY, payment_uuid TEXT, event TEXT, payload TEXT, status TEXT, message TEXT, created_at TEXT, updated_at TEXT)');
$wordpress->statement('CREATE TABLE evt_events (id INTEGER PRIMARY KEY, max_attendees INTEGER)');
$wordpress->statement('CREATE TABLE evt_ticket_types (id INTEGER PRIMARY KEY, event_id INTEGER, quantity_available INTEGER, quantity_sold INTEGER, updated_at TEXT)');
$wordpress->statement('CREATE TABLE evt_registrations (id INTEGER PRIMARY KEY, registration_uuid TEXT, event_id INTEGER, ticket_type_id INTEGER, payment_uuid TEXT, gateway_reference TEXT, payment_error TEXT, status TEXT, amount NUMERIC, reserved_until TEXT, ticket_generated_at TEXT, ticket_sent_at TEXT, confirmed_amount NUMERIC, updated_at TEXT, full_name TEXT, payer_name TEXT, receipt_number TEXT, serial_number TEXT, confirmation_code TEXT, ticket_quantity INTEGER NOT NULL DEFAULT 1, admission_quantity INTEGER NOT NULL DEFAULT 1, parent_registration_id INTEGER, checkout_uuid TEXT)');

$callbacks = 0;
Http::preventStrayRequests();
Http::fake(static function ($request) use ($wordpress, &$callbacks) {
    if ($request->url() !== 'http://127.0.0.1/') {
        throw new RuntimeException('Unexpected outgoing request in capacity test.');
    }
    ++$callbacks;
    $wordpress->table('evt_registrations')->where('id', 2)->update([
        'ticket_generated_at' => '2026-01-01 10:00:00',
        'ticket_sent_at' => '2026-01-01 10:00:00',
    ]);

    return Http::response(['status' => 'ok'], 200);
});

$controller = new PaymentController(new PaystackService());
$cases = [
    ['confirmed', 0, 1, false],
    ['paid', 100, 1, false],
    ['confirmed', 0, 2, true],
    ['confirmed', 100, 1, true],
    ['pending', 0, 1, true],
];
foreach ($cases as [$existingStatus, $existingAmount, $capacity, $expectConfirmed]) {
    $wordpress->table('evt_registrations')->delete();
    $wordpress->table('evt_ticket_types')->delete();
    $wordpress->table('evt_events')->delete();
    $wordpress->table('evt_events')->insert(['id' => 1, 'max_attendees' => $capacity]);
    $wordpress->table('evt_ticket_types')->insert(['id' => 1, 'event_id' => 1, 'quantity_available' => 10, 'quantity_sold' => 0]);
    $wordpress->table('evt_registrations')->insert([
        ['id' => 1, 'event_id' => 1, 'ticket_type_id' => 1, 'payment_uuid' => 'existing', 'status' => $existingStatus, 'amount' => $existingAmount],
        ['id' => 2, 'event_id' => 1, 'ticket_type_id' => 1, 'payment_uuid' => 'fixture-payment', 'status' => 'pending', 'amount' => 100],
    ]);
    $payload = json_encode([
        'event' => 'charge.success',
        'data' => [
            'status' => 'success', 'amount' => 10000, 'reference' => 'fixture-reference',
            'channel' => 'mobile_money', 'metadata' => ['payment_uuid' => 'fixture-payment'],
        ],
    ], JSON_THROW_ON_ERROR);
    $request = Request::create('/', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'fixture-webhook-secret'),
    ], $payload);
    $before = $callbacks;
    $response = $controller->ipn($request);
    $result = $response->getData(true);
    $status = $wordpress->table('evt_registrations')->where('id', 2)->value('status');
    $sold = (int) $wordpress->table('evt_ticket_types')->where('id', 1)->value('quantity_sold');
    if ($expectConfirmed) {
        if ($status !== 'paid' || $sold !== 1 || $callbacks !== $before + 1 || $result['message'] !== 'Paystack webhook processed.') {
            throw new RuntimeException("Paid confirmation was changed for {$existingStatus}/{$existingAmount} at capacity {$capacity}.");
        }
    } elseif ($status !== 'pending' || $sold !== 0 || $callbacks !== $before || $result['message'] !== 'Payment recorded for manual capacity review.') {
        throw new RuntimeException("Confirmed admission did not count toward capacity for {$existingStatus}/{$existingAmount}.");
    }
}

$wordpress->table('evt_registrations')->delete();
$wordpress->table('evt_ticket_types')->delete();
$wordpress->table('evt_events')->delete();
$wordpress->table('evt_events')->insert(['id' => 1, 'max_attendees' => 1]);
$wordpress->table('evt_ticket_types')->insert(['id' => 1, 'event_id' => 1, 'quantity_available' => 1, 'quantity_sold' => 1]);
$wordpress->table('evt_registrations')->insert([
    'id' => 2, 'event_id' => 1, 'ticket_type_id' => 1, 'payment_uuid' => 'fixture-payment',
    'status' => 'pending', 'amount' => 100, 'reserved_until' => '2030-01-01 00:00:00',
]);
$payload = json_encode([
    'event' => 'charge.success',
    'data' => ['status' => 'success', 'amount' => 10000, 'reference' => 'fixture-reference',
        'channel' => 'mobile_money', 'metadata' => ['payment_uuid' => 'fixture-payment']],
], JSON_THROW_ON_ERROR);
$request = Request::create('/', 'POST', [], [], [], [
    'CONTENT_TYPE' => 'application/json',
    'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'fixture-webhook-secret'),
], $payload);
$controller->ipn($request);
$row = $wordpress->table('evt_registrations')->where('id', 2)->first();
if ($row->status !== 'paid' || $row->reserved_until !== null || (int) $wordpress->table('evt_ticket_types')->where('id', 1)->value('quantity_sold') !== 1) {
    throw new RuntimeException('An existing reservation must be confirmed without counting it twice.');
}

$wordpress->table('evt_registrations')->where('id', 2)->update([
    'registration_uuid' => 'fixture-registration', 'payment_uuid' => 'new-attempt', 'gateway_reference' => 'new-attempt',
    'status' => 'pending', 'reserved_until' => '2030-01-01 00:00:00', 'ticket_generated_at' => null, 'ticket_sent_at' => null,
]);
foreach (['old-attempt', 'new-attempt'] as $attempt) {
    $payload = json_encode([
        'event' => 'charge.success',
        'data' => ['status' => 'success', 'amount' => 10000, 'reference' => $attempt, 'channel' => 'mobile_money',
            'metadata' => ['payment_uuid' => $attempt, 'registration_uuid' => 'fixture-registration']],
    ], JSON_THROW_ON_ERROR);
    $request = Request::create('/', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'fixture-webhook-secret'),
    ], $payload);
    $controller->ipn($request);
}
$row = $wordpress->table('evt_registrations')->where('id', 2)->first();
if ($row->status !== 'paid' || $row->gateway_reference !== 'old-attempt'
    || ! str_contains((string) $row->payment_error, 'possible duplicate')
    || (int) $wordpress->table('evt_ticket_types')->where('id', 1)->value('quantity_sold') !== 1
    || ! $logs->table('payment_logs')->where('event', 'possible_duplicate_payment')->exists()) {
    throw new RuntimeException('Late successful attempts must remain reconcilable and a second payment must be flagged without consuming inventory twice.');
}

foreach ([
    // capacity, ticket stock, existing sold, held group, other held seats, expect paid
    [3, 3, 0, false, 0, true],
    [3, 3, 3, true, 0, true],
    [3, 2, 0, false, 0, false],
    [2, 10, 0, false, 0, false],
    [5, 10, 2, false, 3, false],
] as [$capacity, $stock, $sold, $held, $otherHeld, $expectPaid]) {
    $wordpress->table('evt_registrations')->delete();
    $wordpress->table('evt_ticket_types')->delete();
    $wordpress->table('evt_events')->delete();
    $wordpress->table('evt_events')->insert(['id' => 1, 'max_attendees' => $capacity]);
    $wordpress->table('evt_ticket_types')->insert(['id' => 1, 'event_id' => 1, 'quantity_available' => $stock, 'quantity_sold' => $sold]);
    $wordpress->table('evt_registrations')->insert([
        'id' => 2, 'event_id' => 1, 'ticket_type_id' => 1, 'payment_uuid' => 'bulk-fixture', 'status' => 'pending',
        'amount' => 300, 'ticket_quantity' => 3, 'admission_quantity' => 3, 'reserved_until' => $held ? '2000-01-01 00:00:00' : null,
    ]);
    // Already allocated individual seats must not increase occupancy.
    $wordpress->table('evt_registrations')->insert([
        'id' => 3, 'event_id' => 1, 'ticket_type_id' => 1, 'payment_uuid' => 'child', 'status' => 'confirmed',
        'amount' => 0, 'ticket_quantity' => 1, 'admission_quantity' => 1, 'parent_registration_id' => 2,
    ]);
    if ($otherHeld) {
        $wordpress->table('evt_registrations')->insert([
            'id' => 4, 'event_id' => 1, 'ticket_type_id' => 1, 'payment_uuid' => 'other-hold', 'status' => 'failed',
            'amount' => 300, 'ticket_quantity' => $otherHeld, 'reserved_until' => '2030-01-01 00:00:00',
        ]);
    }
    $payload = json_encode([
        'event' => 'charge.success',
        'data' => ['status' => 'success', 'amount' => 30000, 'reference' => 'bulk-fixture',
            'channel' => 'mobile_money', 'metadata' => ['payment_uuid' => 'bulk-fixture']],
    ], JSON_THROW_ON_ERROR);
    $request = Request::create('/', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'fixture-webhook-secret'),
    ], $payload);
    $before = $callbacks;
    $controller->ipn($request);
    $row = $wordpress->table('evt_registrations')->where('id', 2)->first();
    $actualSold = (int) $wordpress->table('evt_ticket_types')->where('id', 1)->value('quantity_sold');
    if ($expectPaid) {
        if ($row->status !== 'paid' || $actualSold !== ($held ? $sold : $sold + 3) || $callbacks !== $before + 1) {
            throw new RuntimeException('A three-seat callback must consume the right quantity and exclude child registrations.');
        }
        $wordpress->table('evt_registrations')->where('id', 2)->update(['admission_quantity' => 0, 'ticket_sent_at' => null, 'ticket_generated_at' => null]);
        $controller->ipn($request);
        if ($callbacks !== $before + 1 || (int) $wordpress->table('evt_ticket_types')->where('id', 1)->value('quantity_sold') !== $actualSold) {
            throw new RuntimeException('A duplicate callback must not reissue a fully allocated group or consume additional seats.');
        }
    } elseif ($row->status !== 'pending' || $actualSold !== $sold || $callbacks !== $before) {
        throw new RuntimeException('Group capacity shortage must retain the payment for review without overselling.');
    }
}
foreach ([true, false] as $held) {
    $wordpress->table('evt_registrations')->delete();
    $wordpress->table('evt_ticket_types')->delete();
    $wordpress->table('evt_events')->delete();
    $wordpress->table('evt_events')->insert(['id' => 1, 'max_attendees' => 3]);
    $wordpress->table('evt_ticket_types')->insert([
        ['id' => 1, 'event_id' => 1, 'quantity_available' => 2, 'quantity_sold' => $held ? 2 : 0],
        ['id' => 2, 'event_id' => 1, 'quantity_available' => 1, 'quantity_sold' => $held ? 1 : 0],
    ]);
    $wordpress->table('evt_registrations')->insert([
        ['id' => 2, 'event_id' => 1, 'ticket_type_id' => 1, 'payment_uuid' => 'basket-payment', 'registration_uuid' => 'basket-leader',
            'status' => 'pending', 'amount' => 200, 'ticket_quantity' => 2, 'admission_quantity' => 2, 'checkout_uuid' => 'basket',
            'reserved_until' => $held ? '2000-01-01 00:00:00' : null],
        ['id' => 3, 'event_id' => 1, 'ticket_type_id' => 2, 'payment_uuid' => 'basket-other', 'registration_uuid' => 'basket-other',
            'status' => 'pending', 'amount' => 50, 'ticket_quantity' => 1, 'admission_quantity' => 1, 'checkout_uuid' => 'basket',
            'reserved_until' => $held ? '2000-01-01 00:00:00' : null],
    ]);
    foreach ([['basket-leader', 'basket-payment', 200], ['basket-other', 'basket-other', 50]] as [$uuid, $attempt, $amount]) {
        $initiation = $controller->initiate(Request::create('/', 'POST', [
            'registration_uuid' => $uuid, 'payment_uuid' => $attempt, 'amount' => $amount,
            'full_name' => 'Basket Buyer', 'email' => 'basket@example.test', 'phone' => '254712345678',
            'payment_method' => 'mpesa', 'event_id' => 1, 'ticket_type_id' => 1,
        ]));
        if ($initiation->getStatusCode() !== 422) {
            throw new RuntimeException('Initiation must reject a line-only amount or non-coordinating registration.');
        }
    }
    foreach ([20000, 25000] as $cents) {
        $payload = json_encode(['event' => 'charge.success', 'data' => [
            'status' => 'success', 'amount' => $cents, 'reference' => 'basket-payment', 'channel' => 'mobile_money',
            'metadata' => ['payment_uuid' => 'basket-payment', 'registration_uuid' => 'basket-leader'],
        ]], JSON_THROW_ON_ERROR);
        $request = Request::create('/', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac('sha512', $payload, 'fixture-webhook-secret'),
        ], $payload);
        if (! $held && $cents === 25000) {
            $wordpress->table('evt_ticket_types')->where('id', 2)->update(['quantity_available' => 0]);
            $controller->ipn($request);
            if ($wordpress->table('evt_registrations')->where('status', 'paid')->exists()
                || (int) $wordpress->table('evt_ticket_types')->where('id', 1)->value('quantity_sold') !== 0) {
                throw new RuntimeException('A shortage on the second type must not partially confirm or reserve the first type.');
            }
            $wordpress->table('evt_ticket_types')->where('id', 2)->update(['quantity_available' => 1]);
        }
        $controller->ipn($request);
        $rows = $wordpress->table('evt_registrations')->orderBy('id')->get();
        if ($cents === 20000) {
            if ($rows->contains(fn ($row) => $row->status !== 'pending')) {
                throw new RuntimeException('A partial basket amount must not confirm either type.');
            }
        } elseif ($rows->contains(fn ($row) => $row->status !== 'paid')
            || (float) $rows[0]->confirmed_amount !== 200.0 || (float) $rows[1]->confirmed_amount !== 50.0
            || (int) $wordpress->table('evt_ticket_types')->where('id', 1)->value('quantity_sold') !== 2
            || (int) $wordpress->table('evt_ticket_types')->where('id', 2)->value('quantity_sold') !== 1) {
            throw new RuntimeException('One full payment must confirm both type UUIDs and their amounts without double-counting holds.');
        }
    }
    $before = $callbacks;
    $controller->ipn($request);
    if ($callbacks <= $before) {
        throw new RuntimeException('A duplicate must recover an unsent second type even if the first type was already delivered.');
    }
}
echo "Payment capacity contract tests passed (single, bulk and mixed types, isolated SQLite; HTTP intercepted).\n";
