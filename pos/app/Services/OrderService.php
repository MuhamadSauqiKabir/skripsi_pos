<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use App\Enums\PaymentStatus;
use App\Models\CustomerProfile;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OrderService
{
    public function __construct(
        private readonly DiscountService $discountService,
        private readonly InventoryService $inventoryService,
        private readonly XenditPaymentService $xenditPaymentService,
        private readonly ReceiptService $receiptService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function create(array $payload, ?DiningTable $table = null, ?User $actor = null, ?Request $request = null): Order
    {
        return DB::transaction(function () use ($payload, $table, $actor, $request) {
            $customer = CustomerProfile::query()->firstOrCreate(
                [
                    'phone' => Arr::get($payload, 'customer.phone'),
                ],
                [
                    'name' => Arr::get($payload, 'customer.name', 'Guest'),
                    'email' => Arr::get($payload, 'customer.email'),
                ],
            );

            if (! $customer->wasRecentlyCreated) {
                $customer->update([
                    'name' => Arr::get($payload, 'customer.name', $customer->name),
                    'email' => Arr::get($payload, 'customer.email', $customer->email),
                ]);
            }

            $menuItems = MenuItem::query()
                ->with('ingredients')
                ->whereIn('id', collect($payload['items'])->pluck('menu_item_id'))
                ->get()
                ->keyBy('id');

            $items = collect($payload['items'])->map(function (array $item) use ($menuItems) {
                $menuItem = $menuItems->get($item['menu_item_id']);
                $quantity = (int) $item['quantity'];
                $lineTotal = (float) $menuItem->price * $quantity;
                $lineProfit = ((float) $menuItem->price - (float) $menuItem->cost_of_goods) * $quantity;

                return [
                    'model' => $menuItem,
                    'quantity' => $quantity,
                    'notes' => $item['notes'] ?? null,
                    'line_total' => $lineTotal,
                    'line_profit' => $lineProfit,
                ];
            });

            $subtotal = $items->sum('line_total');
            $grossProfit = $items->sum('line_profit');
            $discount = $this->discountService->calculate($customer, $subtotal);
            $taxAmount = round(($subtotal - $discount['amount']) * 0.11, 2);
            $totalAmount = max(0, $subtotal - $discount['amount'] + $taxAmount);
            $paymentChannel = PaymentChannel::from($payload['payment_channel']);

            $order = Order::create([
                'public_id' => 'ORD-'.Str::upper(Str::random(10)),
                'customer_profile_id' => $customer->id,
                'dining_table_id' => $table?->id,
                'created_by' => $actor?->id,
                'status' => $paymentChannel === PaymentChannel::Qris ? OrderStatus::AwaitingPayment : OrderStatus::Paid,
                'payment_status' => $paymentChannel === PaymentChannel::Qris ? PaymentStatus::Pending : PaymentStatus::Settlement,
                'payment_channel' => $paymentChannel,
                'subtotal' => $subtotal,
                'discount_amount' => $discount['amount'],
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'gross_profit' => $grossProfit,
                'discount_reason' => $discount['reason'],
                'discount_breakdown' => $discount['breakdown'],
                'notes' => $payload['notes'] ?? null,
                'ordered_at' => now(),
                'paid_at' => $paymentChannel === PaymentChannel::Cash ? now() : null,
            ]);

            $items->each(function (array $item) use ($order): void {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['model']->id,
                    'menu_name_snapshot' => $item['model']->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['model']->price,
                    'unit_cost' => $item['model']->cost_of_goods,
                    'line_total' => $item['line_total'],
                    'line_profit' => $item['line_profit'],
                    'notes' => $item['notes'],
                ]);
            });

            $this->inventoryService->deductForOrder($order);

            if ($paymentChannel === PaymentChannel::Qris) {
                $this->xenditPaymentService->createQrisPayment($order);
            } else {
                Payment::create([
                    'order_id' => $order->id,
                    'channel' => PaymentChannel::Cash->value,
                    'status' => PaymentStatus::Settlement->value,
                    'provider' => 'cashier',
                    'reference_id' => 'CASH-'.Str::upper(Str::random(10)),
                    'settled_at' => now(),
                ]);

                $this->receiptService->generateEscPos($order);
                $this->receiptService->sendDigitalReceipt($order);
            }

            $customer->increment('visit_count');

            $this->auditLogService->log(
                $actor?->id,
                'order.created',
                'order',
                $order->id,
                ['public_id' => $order->public_id],
                $request,
            );

            return $order->load('items', 'payments', 'customer', 'diningTable');
        });
    }

    public function markPaymentSettled(Payment $payment, ?Request $request = null): void
    {
        DB::transaction(function () use ($payment, $request): void {
            $order = $payment->order;

            $order->update([
                'payment_status' => PaymentStatus::Settlement,
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
            ]);

            $this->receiptService->generateEscPos($order);
            $this->receiptService->sendDigitalReceipt($order);

            $this->auditLogService->log(
                null,
                'payment.settled',
                'payment',
                $payment->id,
                ['order_public_id' => $order->public_id],
                $request,
            );
        });
    }

    public function updateStatus(Order $order, OrderStatus $status, ?User $actor = null, ?Request $request = null): Order
    {
        $attributes = ['status' => $status];

        if ($status === OrderStatus::Completed) {
            $attributes['completed_at'] = now();
        }

        $order->update($attributes);

        $this->auditLogService->log(
            $actor?->id,
            'order.status_updated',
            'order',
            $order->id,
            ['status' => $status->value],
            $request,
        );

        return $order->refresh();
    }
}
