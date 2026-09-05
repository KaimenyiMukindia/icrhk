<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Payment\PesaPalService;
use App\Services\WordPress\RegistrationCrypto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PaymentController extends Controller
{
    public function __construct(private readonly PesaPalService $service)
    {
    }

    public function initiate(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'registration_uuid' => ['required', 'string'],
            'payment_uuid' => ['required', 'string'],
            'full_name' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string'],
            'payment_method' => ['required', 'string'],
            'event_id' => ['nullable', 'integer'],
            'ticket_type_id' => ['nullable', 'integer'],
        ]);

        if ($payload['payment_method'] === 'mpesa' && (empty($payload['full_name']) || empty($payload['phone']))) {
            return response()->json(['status' => 'failed', 'message' => 'Full name and a valid Kenyan phone number are required for M-Pesa.'], 422);
        }

        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $result = $this->service->submitOrder([
            'payment_uuid' => $payload['payment_uuid'],
            'registration_uuid' => $payload['registration_uuid'],
            'amount' => (float) $payload['amount'],
            'currency' => $payload['currency'] ?? 'KES',
            'full_name' => $fullName !== '' ? $fullName : 'Card Holder',
            'email' => $payload['email'],
            'phone' => $payload['phone'] ?? '',
            'payment_method' => $payload['payment_method'],
            'description' => 'Event registration payment',
            'callback_url' => env('PESAPAL_CALLBACK_URL', ''),
        ]);

        if (($result['ok'] ?? false) !== true) {
            return response()->json([
                'status' => 'failed',
                'message' => $result['message'] ?? 'Unable to initiate payment.',
            ], 422);
        }

        return response()->json([
            'status' => 'pending',
            'payment_uuid' => $payload['payment_uuid'],
            'tracking_id' => $result['tracking_id'] ?? null,
            'redirect_url' => $result['redirect_url'] ?? null,
            'message' => 'Payment initiated successfully.',
        ], 200);
    }

    public function ipn(Request $request): JsonResponse
    {
        $payload = $request->all();
        $signature = (string) ($request->header('X-Pesapal-Signature') ?: $request->input('signature', ''));
        $verified = $this->service->verifyIpnSignature($payload, $signature);

        if (! $verified) {
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        $paymentUuid = (string) ($payload['payment_uuid'] ?? $payload['OrderMerchantReference'] ?? $payload['id'] ?? '');
        $trackingId = (string) ($payload['tracking_id'] ?? $payload['OrderTrackingId'] ?? '');
        $paymentStatus = strtolower((string) ($payload['status'] ?? $payload['payment_status'] ?? $payload['OrderStatus'] ?? ''));

        if ($trackingId !== '') {
            $transaction = $this->service->getTransactionStatus($trackingId);
            if (($transaction['ok'] ?? false) !== true) {
                return response()->json(['status' => 'retry', 'payment_uuid' => $paymentUuid, 'message' => 'Unable to verify the transaction status.'], 503);
            }

            $paymentStatus = strtolower((string) ($transaction['status'] ?? ''));
            $payload = array_merge($payload, (array) ($transaction['response'] ?? []));
        }

        $paidStatuses = ['paid', 'completed', 'success', 'successful'];

        DB::table('payment_logs')->insert([
            'payment_uuid' => $paymentUuid ?: null,
            'event' => 'ipn_received',
            'payload' => json_encode($payload, JSON_UNESCAPED_SLASHES),
            'status' => $paymentStatus ?: 'received',
            'message' => 'PesaPal IPN received.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! in_array($paymentStatus, $paidStatuses, true)) {
            return response()->json(['status' => 'ok', 'payment_uuid' => $paymentUuid, 'message' => 'IPN logged without payment confirmation.']);
        }

        $payerName = $this->firstPaymentValue($payload, ['payer_name', 'payerName', 'full_name', 'customer_name', 'customerName']);
        $receiptNumber = $this->firstPaymentValue($payload, ['receipt_number', 'receiptNumber', 'receipt']);
        $serialNumber = $this->firstPaymentValue($payload, ['serial_number', 'serialNumber', 'serial']);
        $confirmationCode = $this->firstPaymentValue($payload, ['confirmation_code', 'confirmationCode', 'confirmation']);
        $confirmedAmount = $this->firstPaymentValue($payload, ['amount', 'payment_amount', 'amount_paid']);
        $updates = [
            'status' => 'paid',
            'updated_at' => now(),
        ];
        if ($payerName !== '') {
            $updates['full_name'] = RegistrationCrypto::encrypt($payerName);
            $updates['payer_name'] = RegistrationCrypto::encrypt($payerName);
        }
        foreach ([
            'receipt_number' => $receiptNumber,
            'serial_number' => $serialNumber,
            'confirmation_code' => $confirmationCode,
        ] as $column => $value) {
            if ($value !== '') {
                $updates[$column] = $value;
            }
        }
        if ($confirmedAmount !== '' && is_numeric($confirmedAmount)) {
            $updates['confirmed_amount'] = (float) $confirmedAmount;
        }
        $confirmation = DB::transaction(function () use ($paymentUuid, $trackingId, $updates): array {
            $registration = DB::table('wp_evt_registrations')
                ->where(function ($query) use ($paymentUuid, $trackingId): void {
                    $query->where('payment_uuid', $paymentUuid);
                    if ($trackingId !== '') {
                        $query->orWhere('gateway_reference', $trackingId);
                    }
                })
                ->lockForUpdate()
                ->first();

            if (! $registration) {
                return ['state' => 'missing'];
            }
            if ($registration->status === 'paid') {
                return ['state' => 'duplicate'];
            }

            $event = DB::table('wp_evt_events')->where('id', $registration->event_id)->lockForUpdate()->first(['id', 'max_attendees']);
            $ticket = DB::table('wp_evt_ticket_types')
                ->where('id', $registration->ticket_type_id)
                ->where('event_id', $registration->event_id)
                ->lockForUpdate()
                ->first(['id', 'quantity_available', 'quantity_sold']);
            if (! $event || ! $ticket) {
                return ['state' => 'unavailable'];
            }

            $paidCount = DB::table('wp_evt_registrations')
                ->where('event_id', $event->id)
                ->where('status', 'paid')
                ->count();
            if ($event->max_attendees !== null && $paidCount >= (int) $event->max_attendees) {
                return ['state' => 'event_full'];
            }
            if ($ticket->quantity_available !== null && (int) $ticket->quantity_sold >= (int) $ticket->quantity_available) {
                return ['state' => 'ticket_sold_out'];
            }

            DB::table('wp_evt_ticket_types')->where('id', $ticket->id)->update([
                'quantity_sold' => DB::raw('quantity_sold + 1'),
                'updated_at' => now(),
            ]);
            $registrationUpdates = $updates;
            $registrationUpdates['gateway_reference'] = $trackingId ?: $registration->gateway_reference;
            DB::table('wp_evt_registrations')->where('id', $registration->id)->update($registrationUpdates);

            return ['state' => 'confirmed', 'registration_id' => (int) $registration->id];
        });

        if ($confirmation['state'] === 'missing') {
            return response()->json(['status' => 'ok', 'payment_uuid' => $paymentUuid, 'message' => 'Registration not found.']);
        }
        if ($confirmation['state'] === 'duplicate') {
            DB::table('payment_logs')->insert([
                'payment_uuid' => $paymentUuid ?: null,
                'event' => 'ipn_duplicate_ignored',
                'payload' => json_encode($payload, JSON_UNESCAPED_SLASHES),
                'status' => 'ignored',
                'message' => 'Duplicate confirmed IPN ignored; registration was already paid.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['status' => 'ok', 'payment_uuid' => $paymentUuid, 'message' => 'Already processed.']);
        }
        if ($confirmation['state'] !== 'confirmed') {
            logger()->critical('Confirmed payment could not reserve event capacity.', ['payment_uuid' => $paymentUuid, 'state' => $confirmation['state']]);
            return response()->json(['status' => 'ok', 'payment_uuid' => $paymentUuid, 'message' => 'Payment recorded for manual capacity review.']);
        }

        $wordpressUrl = (string) env('WORDPRESS_URL', 'http://localhost/icrhk');
        $ticketTimestamp = (string) time();
        $ticketSecret = (string) env('CER_TICKET_CALLBACK_SECRET', '');
        $ticketResponse = Http::timeout(15)->withHeaders([
            'X-CER-Ticket-Timestamp' => $ticketTimestamp,
            'X-CER-Ticket-Signature' => hash_hmac('sha256', $confirmation['registration_id'] . '|' . $ticketTimestamp, $ticketSecret),
        ])->post(rtrim($wordpressUrl, '/') . '/', ['cer_process_ticket' => $confirmation['registration_id']]);
        if (! $ticketResponse->successful()) {
            logger()->error('WordPress ticket delivery callback failed', ['registration_id' => $confirmation['registration_id'], 'status' => $ticketResponse->status()]);
        }

        return response()->json([
            'status' => 'ok',
            'payment_uuid' => $paymentUuid,
            'message' => 'IPN processed.',
        ]);
    }

    public function status(string $trackingId): JsonResponse
    {
        $registration = DB::table('wp_evt_registrations')
            ->where('gateway_reference', $trackingId)
            ->first(['status']);
        $registrationData = $registration ? (array) $registration : [];

        if (! empty($registrationData)) {
            $registrationStatus = strtolower((string) ($registrationData['status'] ?? 'awaiting_payment'));
            return response()->json([
                'tracking_id' => $trackingId,
                'status' => $registrationStatus === 'paid' ? 'paid' : 'pending',
                'registration_status' => $registrationStatus,
            ]);
        }

        $result = $this->service->getTransactionStatus($trackingId);

        $registrationStatus = (string) ($registrationData['status'] ?? 'awaiting_payment');

        return response()->json([
            'tracking_id' => $trackingId,
            'status' => $result['status'] ?? 'unknown',
            'registration_status' => $registrationStatus,
            'response' => $result['response'] ?? [],
        ]);
    }

    private function firstPaymentValue(array $payload, array $keys): string
    {
        foreach ($keys as $key) {
            if (isset($payload[$key]) && is_scalar($payload[$key]) && (string) $payload[$key] !== '') {
                return (string) $payload[$key];
            }
        }

        foreach ($payload as $value) {
            if (is_array($value)) {
                $nested = $this->firstPaymentValue($value, $keys);
                if ($nested !== '') {
                    return $nested;
                }
            }
        }

        return '';
    }
}
