<?php

namespace App\Http\Controllers\Management;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'table_id' => ['nullable', 'integer', 'exists:dining_tables,id'],
            'menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_channel' => ['required', Rule::in(collect(PaymentChannel::cases())->pluck('value')->all())],
        ]);

        $table = isset($data['table_id'])
            ? DiningTable::query()->find($data['table_id'])
            : null;

        $order = $this->orderService->create([
            'customer' => [
                'name' => $data['customer_name'] ?: 'Pelanggan',
                'phone' => 'POS-'.Str::upper(Str::random(8)),
                'email' => null,
            ],
            'payment_channel' => $data['payment_channel'],
            'notes' => null,
            'items' => [
                [
                    'menu_item_id' => $data['menu_item_id'],
                    'quantity' => $data['quantity'],
                    'notes' => null,
                ],
            ],
        ], $table, $request->user(), $request);

        return back()->with('success', 'Pesanan '.$order->public_id.' berhasil dibuat.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(collect(OrderStatus::cases())->pluck('value')->all())],
        ]);

        $this->orderService->updateStatus($order, OrderStatus::from($data['status']), $request->user(), $request);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
