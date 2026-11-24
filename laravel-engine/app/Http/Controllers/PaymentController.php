<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Payment\PaystackService;
use App\Services\WordPress\RegistrationCrypto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PaymentController extends Controller
{
    public function __construct(private readonly PaystackService $service)
    {
    }

    private function wordpress(): Connection
    {
        return DB::connection('wordpress');
    }

    private function wordpressTable(string $table): string
    {
        return $table;
    }

    private function checkoutRegistrations(object $registration, bool $lock = false): \Illuminate\Support\Collection
    {
        if (empty($registration->checkout_uuid)) {
            return collect([$registration]);
        }
        $query = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
            ->where('checkout_uuid', $registration->checkout_uuid)
            ->whereNull('parent_registration_id')
            ->orderBy('id');
        return ($lock ? $query->lockForUpdate() : $query)->get();
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

        $registration = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
            ->where('registration_uuid', $payload['registration_uuid'])
            ->where('payment_uuid', $payload['payment_uuid'])
            ->whereIn('status', ['pending', 'awaiting_payment'])
            ->first();
        $checkout = $registration ? $this->checkoutRegistrations($registration) : collect();
        $amount = (float) $checkout->sum('amount');
        if (! $registration || $checkout->first()->id !== $registration->id
            || $checkout->contains(fn ($row) => ! empty($row->deleted_at) || ! in_array($row->status, ['pending', 'awaiting_payment'], true) || $row->event_id !== $registration->event_id)
            || abs($amount - (float) $payload['amount']) > 0.01) {
            return response()->json(['status' => 'failed', 'message' => 'The payment amount does not match the registration.'], 422);
        }
        $payload['amount'] = $amount;

        if ($payload['payment_method'] === 'mpesa') {
            if (empty($payload['full_name']) || empty($payload['phone'])) {
                return response()->json(['status' => 'failed', 'message' => 'Full name and phone number are required for M-Pesa.'], 422);
            }
        }

        $fullName = trim((string) ($payload['full_name'] ?? ''));
        $metadata = [
            'payment_uuid' => $payload['payment_uuid'],
            'registration_uuid' => $payload['registration_uuid'],
            'event_id' => $payload['event_id'] ?? null,
            'ticket_type_id' => $payload['ticket_type_id'] ?? null,
            'full_name' => $fullName,
            'phone' => $payload['phone'],
            'payment_method' => $payload['payment_method'],
        ];
        $result = $payload['payment_method'] === 'mpesa'
            ? $this->service->chargeWithMobileMoney(
                $payload['email'],
                (float) $payload['amount'],
                (string) $payload['phone'],
                $payload['payment_uuid'],
                $metadata
            )
            : $this->service->initializeTransaction($payload['email'], (float) $payload['amount'], $metadata, ['card']);

        if (($result['ok'] ?? false) !== true) {
            return response()->json([
                'status' => 'failed',
                'message' => $result['message'] ?? 'Unable to initiate payment.',
            ], 422);
        }

        return response()->json([
            'status' => 'pending',
            'payment_uuid' => $payload['payment_uuid'],
            'access_code' => $result['access_code'] ?? null,
            'authorization_url' => $result['authorization_url'] ?? null,
            'reference' => $result['reference'] ?? null,
            'display_text' => $result['display_text'] ?? null,
            'message' => 'Payment initiated successfully.',
        ], 200);
    }

    public function ipn(Request $request): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signature = (string) $request->header('X-Paystack-Signature', '');
        if (! $this->service->verifyWebhookSignature($rawPayload, $signature)) {
            return response()->json(['status' => 'invalid_signature'], 400);
        }

        $payload = $request->all();
        if (($payload['event'] ?? '') !== 'charge.success') {
            DB::table('payment_logs')->insert([
                'payment_uuid' => data_get($payload, 'data.metadata.payment_uuid'),
                'event' => (string) ($payload['event'] ?? 'paystack_event'),
                'payload' => json_encode($payload, JSON_UNESCAPED_SLASHES),
                'status' => 'received',
                'message' => 'Paystack webhook received without a successful charge.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['status' => 'ok']);
        }

        $data = (array) ($payload['data'] ?? []);
        $metadata = (array) ($data['metadata'] ?? []);
        $paymentUuid = (string) ($metadata['payment_uuid'] ?? '');
        $reference = (string) ($data['reference'] ?? '');
        $paymentStatus = strtolower((string) ($data['status'] ?? 'success'));
        $payload = $data;
        $payload['metadata'] = $metadata;

        $paidStatuses = ['success'];

        DB::table('payment_logs')->insert([
            'payment_uuid' => $paymentUuid ?: null,
            'event' => 'webhook_received',
            'payload' => json_encode($payload, JSON_UNESCAPED_SLASHES),
            'status' => $paymentStatus ?: 'received',
            'message' => 'Paystack charge.success webhook received.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! in_array($paymentStatus, $paidStatuses, true)) {
            return response()->json(['status' => 'ok', 'payment_uuid' => $paymentUuid, 'message' => 'IPN logged without payment confirmation.']);
        }

        $paymentChannel = strtolower((string) ($data['channel'] ?? ''));
        $payerName = PaystackService::extractPayerName($payload);
        if ('card' === $paymentChannel && '' === $payerName && '' !== $reference) {
            $verification = $this->service->verifyTransaction($reference);
            if (($verification['ok'] ?? false) === true && 'success' === strtolower((string) ($verification['status'] ?? ''))) {
                $payerName = PaystackService::extractPayerName((array) ($verification['response'] ?? []));
            }
            logger()->info('IPN Processing: Cardholder name lookup completed', [
                'reference' => $reference,
                'name_found' => '' !== $payerName,
            ]);
        }
        $receiptNumber = (string) ($data['receipt_number'] ?? '');
        $serialNumber = '';
        $confirmationCode = (string) data_get($payload, 'authorization.authorization_code', data_get($payload, 'authorization.code', ''));
        $confirmedAmount = isset($data['amount']) ? ((float) $data['amount'] / 100) : '';
        $updates = [
            'status' => 'paid',
            'updated_at' => now(),
        ];
        if ($this->wordpress()->getSchemaBuilder()->hasColumn('evt_registrations', 'payment_error')) {
            $updates['payment_error'] = null;
        }
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
        logger()->info('IPN Processing: Starting transaction', ['payment_uuid' => $paymentUuid, 'reference' => $reference]);
        $registrationUuid = (string) ($metadata['registration_uuid'] ?? '');
        $confirmation = $this->wordpress()->transaction(function () use ($paymentUuid, $reference, $registrationUuid, $confirmedAmount, $updates): array {
            $registration = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
                ->where(function ($query) use ($paymentUuid, $reference): void {
                    $query->where('payment_uuid', $paymentUuid);
                    if ($reference !== '') {
                        $query->orWhere('gateway_reference', $reference);
                    }
                })
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (! $registration && $registrationUuid !== '') {
                $registration = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
                    ->where('registration_uuid', $registrationUuid)
                    ->lockForUpdate()
                    ->first();
            }
            if (! $registration) {
                return ['state' => 'missing'];
            }
            if (! empty($registration->parent_registration_id)) {
                return ['state' => 'unavailable'];
            }
            $checkout = $this->checkoutRegistrations($registration, true);
            if ($checkout->isEmpty() || $checkout->contains(fn ($row) => ! empty($row->deleted_at) || $row->event_id !== $registration->event_id)) {
                return ['state' => 'unavailable'];
            }
            $registration = $checkout->first();
            if ($checkout->every(fn ($row) => $row->status === 'paid')) {
                return [
                    'state' => 'duplicate',
                    'registration_id' => (int) $registration->id,
                    'ticket_pending' => $checkout->contains(fn ($row) => (int) ($row->admission_quantity ?? 1) > 0
                        && (empty($row->ticket_generated_at) || empty($row->ticket_sent_at))),
                    'different_reference' => $reference !== '' && $reference !== (string) $registration->gateway_reference,
                ];
            }
            $checkoutAmount = (float) $checkout->sum('amount');
            $expectedAmount = PaystackService::isSandboxEnvironment()
                ? 1.00
                : $checkoutAmount;
            if ($checkoutAmount <= 0 || $confirmedAmount === '' || abs($expectedAmount - (float) $confirmedAmount) > 0.01) {
                return ['state' => 'amount_mismatch'];
            }

            $event = $this->wordpress()->table($this->wordpressTable('evt_events'))->where('id', $registration->event_id)->lockForUpdate()->first(['id', 'max_attendees']);
            if (! $event || $checkout->contains(fn ($row) => ! in_array($row->status, ['pending', 'awaiting_payment', 'failed', 'expired'], true))) {
                return ['state' => 'unavailable'];
            }
            $quantity = (int) $checkout->sum(fn ($row) => (int) ($row->ticket_quantity ?? 1));
            if ($quantity < 1 || $quantity > 20 || $checkout->contains(fn ($row) => (int) ($row->ticket_quantity ?? 1) < 1)) {
                return ['state' => 'unavailable'];
            }
            $bulkSchema = $this->wordpress()->getSchemaBuilder()->hasColumn('evt_registrations', 'ticket_quantity');
            $paidQuery = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
                ->where('event_id', $event->id)
                ->whereNotIn('id', $checkout->pluck('id')->all())
                ->where(function ($query) {
                    $query->where('status', 'paid')
                        ->orWhere(function ($query) {
                            $query->where('status', 'confirmed')->where('amount', '<=', 0);
                        })
                        ->orWhere(function ($query) {
                            $query->whereIn('status', ['pending', 'awaiting_payment', 'failed'])->whereNotNull('reserved_until');
                        });
                });
            $paidCount = $bulkSchema
                ? $paidQuery->whereNull('parent_registration_id')->sum('ticket_quantity')
                : $paidQuery->count();
            if ($event->max_attendees !== null && (int) $event->max_attendees > 0 && $paidCount + $quantity > (int) $event->max_attendees) {
                return ['state' => 'event_full'];
            }
            $tickets = [];
            foreach ($checkout->sortBy('ticket_type_id') as $line) {
                $ticket = $this->wordpress()->table($this->wordpressTable('evt_ticket_types'))
                    ->where('id', $line->ticket_type_id)->where('event_id', $event->id)
                    ->lockForUpdate()->first(['id', 'quantity_available', 'quantity_sold']);
                if (! $ticket) {
                    return ['state' => 'unavailable'];
                }
                $lineQuantity = (int) ($line->ticket_quantity ?? 1);
                $held = ! empty($line->reserved_until) && in_array($line->status, ['pending', 'awaiting_payment', 'failed'], true);
                if (! $held && $ticket->quantity_available !== null && (int) $ticket->quantity_sold + $lineQuantity > (int) $ticket->quantity_available) {
                    return ['state' => 'ticket_sold_out'];
                }
                $tickets[] = [$line, $ticket, $lineQuantity, $held];
            }
            // A reservation remains in quantity_sold until the expiry worker releases it.
            foreach ($tickets as [$line, $ticket, $lineQuantity, $held]) {
                if (! $held) {
                    $this->wordpress()->table($this->wordpressTable('evt_ticket_types'))->where('id', $ticket->id)->update([
                        'quantity_sold' => $this->wordpress()->raw('quantity_sold + ' . $lineQuantity),
                        'updated_at' => now(),
                    ]);
                }
                $registrationUpdates = $updates;
                $registrationUpdates['gateway_reference'] = $reference ?: $line->gateway_reference;
                if ($checkout->count() > 1) {
                    $registrationUpdates['confirmed_amount'] = round((float) $confirmedAmount * (float) $line->amount / $checkoutAmount, 2);
                }
                if (isset($line->reserved_until)) {
                    $registrationUpdates['reserved_until'] = null;
                }
                $this->wordpress()->table($this->wordpressTable('evt_registrations'))->where('id', $line->id)->update($registrationUpdates);
            }

            return ['state' => 'confirmed', 'registration_id' => (int) $registration->id];
        }, 3);

        logger()->info('IPN Processing: Transaction completed', ['payment_uuid' => $paymentUuid, 'confirmation_state' => $confirmation['state'] ?? 'unknown', 'registration_id' => $confirmation['registration_id'] ?? null]);

        if ($confirmation['state'] === 'missing') {
            return response()->json(['status' => 'ok', 'payment_uuid' => $paymentUuid, 'message' => 'Registration not found.']);
        }
        if ($confirmation['state'] === 'duplicate') {
            if (! empty($confirmation['different_reference'])) {
                logger()->critical('A second successful payment needs manual review.', ['registration_id' => $confirmation['registration_id'], 'reference' => $reference]);
                if ($this->wordpress()->getSchemaBuilder()->hasColumn('evt_registrations', 'payment_error')) {
                    $leader = $this->wordpress()->table($this->wordpressTable('evt_registrations'))->where('id', $confirmation['registration_id'])->first();
                    foreach ($this->checkoutRegistrations($leader) as $line) {
                        $this->wordpress()->table($this->wordpressTable('evt_registrations'))->where('id', $line->id)->update([
                            'payment_error' => 'A different successful payment was received for this registration. Review the gateway transactions for a possible duplicate payment.',
                        ]);
                    }
                }
                DB::table('payment_logs')->insert([
                    'payment_uuid' => $paymentUuid ?: null,
                    'event' => 'possible_duplicate_payment',
                    'payload' => json_encode(['registration_id' => $confirmation['registration_id'], 'reference' => $reference]),
                    'status' => 'review',
                    'message' => 'A different successful payment was received for an already paid registration.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if (($confirmation['ticket_pending'] ?? false) === true) {
                $wordpressUrl = (string) config('services.wordpress.url', 'http://localhost/icrhk');
                $ticketTimestamp = (string) time();
                $ticketSecret = (string) config('services.wordpress.ticket_callback_secret', '');
                $ticketResponse = Http::timeout(30)
                    ->asForm()
                    ->withHeaders([
                        'X-CER-Ticket-Timestamp' => $ticketTimestamp,
                        'X-CER-Ticket-Signature' => hash_hmac('sha256', $confirmation['registration_id'] . '|' . $ticketTimestamp, $ticketSecret),
                    ])->post(rtrim($wordpressUrl, '/') . '/', ['cer_process_ticket' => $confirmation['registration_id']]);

                $ticketState = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
                    ->where('id', $confirmation['registration_id'])
                    ->first(['ticket_generated_at', 'ticket_sent_at']);
                if (! $ticketResponse->successful() || ! $ticketState || empty($ticketState->ticket_generated_at) || empty($ticketState->ticket_sent_at)) {
                    $retryTimestamp = (string) time();
                    $ticketResponse = Http::timeout(30)
                        ->asForm()
                        ->withHeaders([
                            'X-CER-Ticket-Timestamp' => $retryTimestamp,
                            'X-CER-Ticket-Signature' => hash_hmac('sha256', $confirmation['registration_id'] . '|' . $retryTimestamp, $ticketSecret),
                        ])->post(rtrim($wordpressUrl, '/') . '/', ['cer_process_ticket' => $confirmation['registration_id']]);
                }
                if (! $ticketResponse->successful()) {
                    logger()->error('WordPress ticket retry callback failed', ['registration_id' => $confirmation['registration_id'], 'status' => $ticketResponse->status()]);
                }
            }
            DB::table('payment_logs')->insert([
                'payment_uuid' => $paymentUuid ?: null,
                'event' => 'webhook_duplicate_ignored',
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

        $wordpressUrl = (string) config('services.wordpress.url', 'http://localhost/icrhk');
        logger()->info('IPN Processing: Making WordPress ticket callback', ['registration_id' => $confirmation['registration_id'], 'wordpress_url' => $wordpressUrl]);
        $ticketTimestamp = (string) time();
        $ticketSecret = (string) config('services.wordpress.ticket_callback_secret', '');
        try {
            $ticketResponse = Http::timeout(30)
                ->asForm()
                ->withHeaders([
                    'X-CER-Ticket-Timestamp' => $ticketTimestamp,
                    'X-CER-Ticket-Signature' => hash_hmac('sha256', $confirmation['registration_id'] . '|' . $ticketTimestamp, $ticketSecret),
                ])->post(rtrim($wordpressUrl, '/') . '/', ['cer_process_ticket' => $confirmation['registration_id']]);
            
            logger()->info('IPN Processing: Callback response received', ['registration_id' => $confirmation['registration_id'], 'status' => $ticketResponse->status(), 'successful' => $ticketResponse->successful()]);
        } catch (\Exception $e) {
            logger()->error('IPN Processing: Callback exception', ['registration_id' => $confirmation['registration_id'], 'error' => $e->getMessage()]);
            $ticketResponse = null;
        }

        $ticketState = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
            ->where('id', $confirmation['registration_id'])
            ->first(['ticket_generated_at', 'ticket_sent_at']);
        if (! $ticketResponse || ! $ticketResponse->successful() || ! $ticketState || empty($ticketState->ticket_generated_at) || empty($ticketState->ticket_sent_at)) {
            $retryTimestamp = (string) time();
            try {
                $ticketResponse = Http::timeout(30)
                    ->asForm()
                    ->withHeaders([
                        'X-CER-Ticket-Timestamp' => $retryTimestamp,
                        'X-CER-Ticket-Signature' => hash_hmac('sha256', $confirmation['registration_id'] . '|' . $retryTimestamp, $ticketSecret),
                    ])->post(rtrim($wordpressUrl, '/') . '/', ['cer_process_ticket' => $confirmation['registration_id']]);
                logger()->info('IPN Processing: Retry callback response received', ['registration_id' => $confirmation['registration_id'], 'status' => $ticketResponse->status()]);
            } catch (\Exception $e) {
                logger()->error('IPN Processing: Retry callback exception', ['registration_id' => $confirmation['registration_id'], 'error' => $e->getMessage()]);
                $ticketResponse = null;
            }
        }
        if (! $ticketResponse || ! $ticketResponse->successful()) {
            logger()->error('WordPress ticket delivery callback failed', ['registration_id' => $confirmation['registration_id'], 'status' => $ticketResponse ? $ticketResponse->status() : 'no_response']);
        }

        return response()->json([
            'status' => 'ok',
            'payment_uuid' => $paymentUuid,
            'message' => 'Paystack webhook processed.',
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $reference = (string) $request->input('reference', '');
        if ($reference === '') {
            return response()->json(['status' => 'failed', 'message' => 'A Paystack reference is required.'], 422);
        }

        $result = $this->service->verifyTransaction($reference);
        if (($result['ok'] ?? false) !== true) {
            return response()->json(['status' => 'failed', 'message' => $result['message'] ?? 'Unable to verify payment.'], 422);
        }

        $existingRegistration = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
            ->where(fn ($query) => $query->where('gateway_reference', $reference)->orWhere('payment_uuid', $reference))
            ->orderBy('id')->first();
        $needsReconciliation = ! $existingRegistration
            || $this->checkoutRegistrations($existingRegistration)->contains(fn ($row) => $row->status !== 'paid'
                || ((int) ($row->admission_quantity ?? 1) > 0 && (empty($row->ticket_generated_at) || empty($row->ticket_sent_at))));

        if (($result['status'] ?? '') === 'success' && $needsReconciliation) {
            $webhookPayload = json_encode([
                'event' => 'charge.success',
                'data' => $result['response'] ?? [],
            ], JSON_UNESCAPED_SLASHES);
            $reconciliationRequest = Request::create('/api/paystack-webhook', 'POST', [], [], [], [], $webhookPayload);
            $reconciliationRequest->headers->set('Content-Type', 'application/json');
            $reconciliationRequest->headers->set('X-Paystack-Signature', hash_hmac('sha512', $webhookPayload, (string) config('services.paystack.secret_key', '')));
            $this->ipn($reconciliationRequest);
        }

        $registration = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
            ->where(fn ($query) => $query->where('gateway_reference', $reference)->orWhere('payment_uuid', $reference))
            ->first(['status']);
        $verifiedRegistrationUuid = (string) data_get($result, 'response.metadata.registration_uuid', '');
        if (! $registration && $verifiedRegistrationUuid !== '') {
            $registration = $this->wordpress()->table($this->wordpressTable('evt_registrations'))
                ->where('registration_uuid', $verifiedRegistrationUuid)
                ->first(['status']);
        }
        return response()->json([
            'status' => $result['status'] ?? 'unknown',
            'registration_status' => $registration ? $registration->status : 'awaiting_payment',
            'message' => (string) data_get($result, 'response.gateway_response', data_get($result, 'response.message', '')),
        ]);
    }
}
