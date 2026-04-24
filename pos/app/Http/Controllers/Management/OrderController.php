<?php

namespace App\Http\Controllers\Management;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
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
