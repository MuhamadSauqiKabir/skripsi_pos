<?php

namespace App\Services;

use App\Enums\PaymentChannel;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;
use Throwable;

class XenditPaymentService
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {
    }

    public function createQrisPayment(Order $order): Payment
    {
        $referenceId = 'NCF-'.Str::upper(Str::random(12));
        $secret = config('services.xendit.secret_key');

        $payment = Payment::create([
            'order_id' => $order->id,
            'channel' => PaymentChannel::Qris->value,
            'status' => PaymentStatus::Pending->value,
            'provider' => 'xendit',
            'reference_id' => $referenceId,
            'expires_at' => now()->addMinutes(20),
        ]);

        if (! $secret) {
            return tap($payment)->update([
                'provider' => 'demo',
                'qr_string' => "DEMO-QRIS|{$order->public_id}|{$order->total_amount}",
                'payload' => ['mode' => 'demo'],
            ]);
        }

        try {
            $response = $this->http
                ->withBasicAuth($secret, '')
                ->acceptJson()
                ->post(config('services.xendit.base_url').'/payment_requests', [
                    'reference_id' => $referenceId,
                    'currency' => 'IDR',
                    'amount' => (float) $order->total_amount,
                    'country' => 'ID',
                    'capture_method' => 'AUTOMATIC',
                    'metadata' => [
                        'order_public_id' => $order->public_id,
                        'table' => $order->diningTable?->name,
                    ],
                    'payment_method' => [
                        'type' => 'QR_CODE',
                        'reusability' => 'ONE_TIME_USE',
                        'qr_code' => [
                            'channel_code' => 'QRIS',
                        ],
                    ],
                ])
                ->throw()
                ->json();

            $payment->update([
                'provider_payment_id' => data_get($response, 'id'),
                'checkout_url' => data_get($response, 'actions.desktop_web_checkout_url')
                    ?? data_get($response, 'payment_method.qr_code.channel_properties.checkout_url'),
                'qr_string' => data_get($response, 'payment_method.qr_code.channel_properties.qr_string')
                    ?? data_get($response, 'payment_method.qr_string')
                    ?? data_get($response, 'qr_string'),
                'payload' => $response,
                'expires_at' => data_get($response, 'payment_method.expires_at', $payment->expires_at),
            ]);
        } catch (RequestException|Throwable $exception) {
            report($exception);

            $payment->update([
                'provider' => 'xendit-fallback',
                'payload' => [
                    'error' => $exception->getMessage(),
                ],
                'qr_string' => "FALLBACK-QRIS|{$order->public_id}|{$order->total_amount}",
            ]);
        }

        return $payment->refresh();
    }

    /**
     * Webhook Xendit diverifikasi dengan callback token agar status settlement,
     * pending, dan expired tidak bisa dipalsukan dari luar sistem.
     */
    public function handleWebhook(array $payload, ?string $callbackToken): ?Payment
    {
        $expectedToken = config('services.xendit.webhook_token');

        if ($expectedToken && $callbackToken !== $expectedToken) {
            abort(403, 'Webhook token tidak valid.');
        }

        $providerPaymentId = data_get($payload, 'data.id')
            ?? data_get($payload, 'id');
        $event = data_get($payload, 'event');
        $status = data_get($payload, 'data.status')
            ?? data_get($payload, 'status');

        $payment = Payment::query()
            ->where('provider_payment_id', $providerPaymentId)
            ->orWhere('reference_id', data_get($payload, 'data.reference_id'))
            ->first();

        if (! $payment) {
            return null;
        }

        $normalizedStatus = match (true) {
            in_array($status, ['SUCCEEDED', 'SETTLED']) || $event === 'payment.succeeded' => PaymentStatus::Settlement,
            in_array($status, ['EXPIRED']) || $event === 'payment.expired' => PaymentStatus::Expired,
            in_array($status, ['FAILED']) || $event === 'payment.failed' => PaymentStatus::Failed,
            default => PaymentStatus::Pending,
        };

        $payment->update([
            'status' => $normalizedStatus->value,
            'webhook_payload' => $payload,
            'settled_at' => $normalizedStatus === PaymentStatus::Settlement ? now() : null,
        ]);

        return $payment->refresh();
    }
}
