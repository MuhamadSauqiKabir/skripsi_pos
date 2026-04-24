<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\XenditPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(
        private readonly XenditPaymentService $xenditPaymentService,
        private readonly OrderService $orderService,
    ) {
    }

    public function webhook(Request $request): Response
    {
        $payment = $this->xenditPaymentService->handleWebhook(
            $request->all(),
            $request->header('x-callback-token'),
        );

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
}
