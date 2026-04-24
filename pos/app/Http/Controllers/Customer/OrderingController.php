<?php

namespace App\Http\Controllers\Customer;

use App\Enums\PaymentChannel;
use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use App\Models\DiningTable;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class OrderingController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {
    }

    public function showMenu(string $token): Response
    {
        return Inertia::render('Customer/Menu', [
            'table' => $this->table($token),
            'categories' => $this->categories(),
        ]);
    }

    public function showCheckout(string $token): Response
    {
        return Inertia::render('Customer/Checkout', [
            'table' => $this->table($token),
            'paymentChannels' => [
                ['value' => PaymentChannel::Qris->value, 'label' => 'QRIS Xendit'],
                ['value' => PaymentChannel::Cash->value, 'label' => 'Bayar di kasir'],
            ],
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $table = $this->table($token);

        $payload = $request->validate([
            'customer.name' => ['required', 'string', 'max:100'],
            'customer.email' => ['nullable', 'email', 'max:100'],
            'customer.phone' => ['required', 'string', 'max:30'],
            'payment_channel' => ['required', Rule::in([PaymentChannel::Cash->value, PaymentChannel::Qris->value])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $order = $this->orderService->create($payload, $table, null, $request);

        return to_route('customer.track', $order->public_id)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function track(string $publicId): Response
    {
        $order = Order::query()
            ->with('items', 'payments', 'customer', 'diningTable')
            ->where('public_id', $publicId)
            ->firstOrFail();

        return Inertia::render('Customer/Track', [
            'order' => $order,
            'payment' => $order->payments()->latest()->first(),
        ]);
    }

    public function feedback(Request $request, string $publicId): RedirectResponse
    {
        $order = Order::query()->where('public_id', $publicId)->firstOrFail();

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        CustomerFeedback::updateOrCreate(
            ['order_id' => $order->id],
            [
                'customer_profile_id' => $order->customer_profile_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ],
        );

        if ($order->customer) {
            $order->customer->update(['satisfaction_rating' => $data['rating']]);
        }

        return back()->with('success', 'Terima kasih atas penilaian Anda.');
    }

    private function table(string $token): DiningTable
    {
        return DiningTable::query()->where('public_token', $token)->firstOrFail();
    }

    private function categories()
    {
        return MenuCategory::query()
            ->with(['menuItems' => fn ($query) => $query->where('is_available', true)->orderBy('name')])
            ->orderBy('sort_order')
            ->get();
    }
}
