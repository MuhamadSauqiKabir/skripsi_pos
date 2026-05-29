<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\MidtransPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(
        private readonly MidtransPaymentService $midtransPaymentService,
        private readonly OrderService $orderService,
    ) {
    }

    public function webhook(Request $request): Response
    {
        $payment = $this->midtransPaymentService->handleNotification($request->all());

        if ($payment && $payment->status === PaymentStatus::Settlement) {
            $this->orderService->markPaymentSettled($payment, $request);
        } elseif ($payment && $payment->status === PaymentStatus::Expired) {
            $payment->order->update([
                'payment_status' => PaymentStatus::Expired,
                'status' => \App\Enums\OrderStatus::Expired,
            ]);
        }

        return response()->noContent();
    }

    public function show(Order $order): Response
    {
        return response()->json([
            'payment' => $order->payments()->latest()->first(),
        ]);
    }

    /**
     * Verify payment status directly with Midtrans API.
     * Called from frontend after Snap popup success/close.
     */
    public function verify(Request $request, Order $order): \Illuminate\Http\JsonResponse
    {
        $payment = $order->payments()->latest()->first();

        if (! $payment || $payment->status === PaymentStatus::Settlement) {
            return response()->json([
                'status' => $payment?->status->value ?? 'not_found',
                'order_status' => $order->status->value,
            ]);
        }

        // Check status directly with Midtrans API
        $payment = $this->midtransPaymentService->checkStatus($payment);

        // If payment is now settled, update the order status too
        if ($payment->status === PaymentStatus::Settlement) {
            $this->orderService->markPaymentSettled($payment, $request);
            $order->refresh();
        }

        return response()->json([
            'status' => $payment->status->value,
            'order_status' => $order->status->value,
        ]);
    }

    /**
     * Regenerate Midtrans Snap token for orders where token creation failed.
     */
    public function regenerateToken(Order $order): \Illuminate\Http\RedirectResponse
    {
        $payment = $order->payments()->latest()->first();

        if ($payment) {
            $payment->delete();
        }

        $this->midtransPaymentService->createPayment($order);

        return back()->with('success', 'Token pembayaran berhasil diperbarui.');
    }
}
