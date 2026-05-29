<?php

namespace App\Http\Controllers\Customer;

use App\Enums\PaymentChannel;
use App\Enums\PaymentStatus;
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
                ['value' => PaymentChannel::Qris->value, 'label' => 'E-wallet Midtrans'],
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

        session(['customer_phone' => $payload['customer']['phone']]);
        cookie()->queue('customer_phone', $payload['customer']['phone'], 60 * 24 * 365 * 5); // 5 years

        return to_route('customer.track', $order->public_id)
            ->with('success', 'Pesanan berhasil dibuat.');
    }

    public function track(string $publicId): Response
    {
        $order = Order::query()
            ->with('items', 'payments', 'customer', 'diningTable')
            ->where('public_id', $publicId)
            ->firstOrFail();

        // Ensure persistence if they visit via a direct link
        if ($order->customer) {
            session(['customer_phone' => $order->customer->phone]);
            cookie()->queue('customer_phone', $order->customer->phone, 60 * 24 * 365 * 5);
        }

        $payment = $order->payments()->latest()->first();
        if ($payment && $payment->provider === 'midtrans' && $payment->status === PaymentStatus::Pending) {
            $payment = app(\App\Services\MidtransPaymentService::class)->checkStatus($payment);
            if ($payment->status === PaymentStatus::Settlement) {
                $this->orderService->markPaymentSettled($payment);
                $order->refresh();
            }
        }

        return Inertia::render('Customer/Track', [
            'order' => $order,
            'payment' => $order->payments()->latest()->first(),
        ]);
    }

    public function history(Request $request): Response|RedirectResponse
    {
        $phone = session('customer_phone') ?? $request->cookie('customer_phone');

        if (! $phone) {
            return to_route('public.menu');
        }

        $orders = Order::query()
            ->with('items', 'payments')
            ->whereHas('customer', fn ($q) => $q->where('phone', $phone))
            ->latest()
            ->get();

        return Inertia::render('Customer/History', [
            'orders' => $orders,
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

        return to_route('public.home')->with('success', 'Terima kasih atas penilaian Anda. Silakan jelajahi website kami!');
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
