<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'settings' => \App\Models\Setting::all()->pluck('value', 'key'),
            'midtrans_client_key' => config('services.midtrans.client_key'),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'activeOrder' => function () use ($request) {
                $phone = session('customer_phone') ?? $request->cookie('customer_phone');
                if (! $phone) {
                    return null;
                }

                return \App\Models\Order::query()
                    ->whereHas('customer', fn ($q) => $q->where('phone', $phone))
                    ->whereNotIn('status', [\App\Enums\OrderStatus::Completed->value, \App\Enums\OrderStatus::Cancelled->value])
                    ->latest()
                    ->first();
            },
        ];
    }
}
