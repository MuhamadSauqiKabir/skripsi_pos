<?php

namespace App\Services;

use App\Enums\PaymentChannel;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Throwable;

class MidtransPaymentService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
        
        // Fix Midtrans SDK CURLOPT_HTTPHEADER bug and bypass SSL verification locally
        Config::$curlOptions[CURLOPT_HTTPHEADER] = [];
        if (! Config::$isProduction) {
            Config::$curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            Config::$curlOptions[CURLOPT_SSL_VERIFYHOST] = false;
        }
    }

    public function createPayment(Order $order): Payment
    {
        $referenceId = 'NCF-'.Str::upper(Str::random(12));

        $payment = Payment::create([
            'order_id' => $order->id,
            'channel' => PaymentChannel::Qris->value, // Reusing Qris for Digital/E-wallet
            'status' => PaymentStatus::Pending->value,
            'provider' => 'midtrans',
            'reference_id' => $referenceId,
            'expires_at' => now()->addMinutes(20),
        ]);

        if (! Config::$serverKey) {
            return tap($payment)->update([
                'provider' => 'demo',
                'checkout_url' => "https://demo.midtrans.com/checkout/{$order->public_id}",
                'payload' => ['mode' => 'demo'],
            ]);
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $referenceId,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $order->customer->name,
                    'email' => $order->customer->email,
                    'phone' => $order->customer->phone,
                ],
                'callbacks' => [
                    'finish' => route('customer.track', $order->public_id),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $checkoutUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken;
            if (Config::$isProduction) {
                $checkoutUrl = "https://app.midtrans.com/snap/v2/vtweb/" . $snapToken;
            }

            $payment->update([
                'provider_payment_id' => $snapToken,
                'checkout_url' => $checkoutUrl,
                'payload' => $params,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            $payment->update([
                'provider' => 'midtrans-fallback',
                'payload' => [
                    'error' => $exception->getMessage(),
                ],
            ]);
        }

        return $payment->refresh();
    }

    public function handleNotification(array $payload): ?Payment
    {
        $orderId = data_get($payload, 'order_id');
        $status = data_get($payload, 'transaction_status');
        $type = data_get($payload, 'payment_type');
        $fraud = data_get($payload, 'fraud_status');

        $payment = Payment::where('reference_id', $orderId)->first();

        if (! $payment) {
            return null;
        }

        $normalizedStatus = match ($status) {
            'capture' => $fraud == 'challenge' ? PaymentStatus::Pending : PaymentStatus::Settlement,
            'settlement' => PaymentStatus::Settlement,
            'pending' => PaymentStatus::Pending,
            'deny', 'expire', 'cancel' => PaymentStatus::Expired,
            default => PaymentStatus::Pending,
        };

        $payment->update([
            'status' => $normalizedStatus->value,
            'webhook_payload' => $payload,
            'settled_at' => $normalizedStatus === PaymentStatus::Settlement ? now() : null,
        ]);

        return $payment->refresh();
    }

    /**
     * Directly check transaction status from Midtrans API.
     * Used when webhook can't reach localhost or as a manual verification.
     */
    public function checkStatus(Payment $payment): Payment
    {
        try {
            $response = Transaction::status($payment->reference_id);

            $status = $response->transaction_status ?? 'pending';
            $fraud = $response->fraud_status ?? null;

            $normalizedStatus = match ($status) {
                'capture' => $fraud == 'challenge' ? PaymentStatus::Pending : PaymentStatus::Settlement,
                'settlement' => PaymentStatus::Settlement,
                'pending' => PaymentStatus::Pending,
                'deny', 'expire', 'cancel' => PaymentStatus::Expired,
                default => PaymentStatus::Pending,
            };

            $payment->update([
                'status' => $normalizedStatus->value,
                'settled_at' => $normalizedStatus === PaymentStatus::Settlement ? now() : null,
            ]);
        } catch (Throwable $e) {
            report($e);
        }

        return $payment->refresh();
    }
}
