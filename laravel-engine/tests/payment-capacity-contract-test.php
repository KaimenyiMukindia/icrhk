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
$wordpress->statement('CREATE TABLE evt_registrations (id INTEGER PRIMARY KEY, event_id INTEGER, ticket_type_id INTEGER, payment_uuid TEXT, gateway_reference TEXT, status TEXT, amount NUMERIC, ticket_generated_at TEXT, ticket_sent_at TEXT, confirmed_amount NUMERIC, updated_at TEXT, full_name TEXT, payer_name TEXT, receipt_number TEXT, serial_number TEXT, confirmation_code TEXT)');

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

echo "Payment capacity contract tests passed (isolated SQLite; HTTP intercepted).\n";
