<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use App\Enums\Role;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\WeeklyPrediction;
use App\Models\CustomerFeedback;
use App\Services\PredictiveStockService;
use Illuminate\Support\Facades\DB;

trait HasDashboardProps
{
    protected function baseProps(array $props, string $currentPage): array
    {
        // We might need to resolve services manually if not injected
        $predictiveStockService = app(PredictiveStockService::class);
        $predictiveStockService->generate();
        
        $user = request()->user();

        $nav = [
            ['label' => 'Dashboard / Dasbor', 'href' => route('dashboard.overview'), 'key' => 'overview'],
            ['label' => 'Orders / Pesanan', 'href' => route('dashboard.orders'), 'key' => 'orders'],
            ['label' => 'Inventory / Stok', 'href' => route('dashboard.inventory'), 'key' => 'inventory'],
        ];

        if (in_array($user->role, [Role::SuperAdmin, Role::Admin])) {
            $nav[] = ['label' => 'Tables / Meja', 'href' => route('dashboard.tables'), 'key' => 'tables'];
            $nav[] = ['label' => 'Reports / Laporan', 'href' => route('dashboard.reports'), 'key' => 'reports'];
        }

        $sidebarUtilities = [
            ['label' => 'Profile / Profil', 'href' => route('dashboard.profile'), 'key' => 'profile'],
        ];

        if ($user->role === Role::SuperAdmin) {
            $sidebarUtilities[] = ['label' => 'Settings / Pengaturan', 'href' => route('dashboard.settings'), 'key' => 'settings'];
        }

        return array_merge([
            'currentPage' => $currentPage,
            'roleScope' => $this->roleScope(),
            'analytics' => $this->analytics(),
            'dashboardNav' => $nav,
            'sidebarUtilities' => $sidebarUtilities,
        ], $props);
    }

    protected function roleScope(): array
    {
        return match (request()->user()->role) {
            Role::SuperAdmin => ['showAudit' => true, 'showStockManager' => true],
            Role::Admin => ['showAudit' => false, 'showStockManager' => true],
            Role::Employee => ['showAudit' => false, 'showStockManager' => false],
            default => ['showAudit' => false, 'showStockManager' => false],
        };
    }

    protected function analytics(): array
    {
        $report = $this->weeklyReport();

        return [
            'weeklyRevenue' => $report['revenue'],
            'grossProfit' => $report['gross_profit'],
            'activeOrders' => Order::query()->whereIn('status', [
                OrderStatus::AwaitingPayment->value,
                OrderStatus::Paid->value,
                OrderStatus::Brewing->value,
                OrderStatus::Ready->value,
            ])->count(),
            'averageRating' => $report['average_rating'],
        ];
    }

    protected function weeklyReport(): array
    {
        $weeklyOrders = Order::query()
            ->whereBetween('ordered_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        return [
            'gross_profit' => round((float) $weeklyOrders->sum('gross_profit'), 2),
            'revenue' => round((float) $weeklyOrders->sum('total_amount'), 2),
            'total_orders' => $weeklyOrders->count(),
            'average_rating' => round((float) CustomerFeedback::query()->avg('rating'), 2),
            'table_efficiency' => $this->tableEfficiency(),
        ];
    }

    protected function tableEfficiency()
    {
        return DiningTable::query()
            ->withCount([
                'orders as order_count' => fn($query) => $query->whereBetween('ordered_at', [now()->subDays(30), now()]),
            ])
            ->get()
            ->map(fn(DiningTable $table) => [
                'name' => $table->name,
                'capacity' => $table->capacity,
                'order_count' => $table->order_count,
                'efficiency' => round(($table->order_count / max($table->capacity, 1)) * 100, 2),
                'public_token' => $table->public_token,
                'coordinate_x' => $table->coordinate_x,
                'coordinate_y' => $table->coordinate_y,
            ]);
    }
}
