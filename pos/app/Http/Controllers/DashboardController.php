<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentChannel;
use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\CustomerFeedback;
use App\Models\DiningTable;
use App\Models\Ingredient;
use App\Models\InventoryAlert;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\WeeklyPrediction;
use App\Services\PredictiveStockService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly PredictiveStockService $predictiveStockService,
    ) {
    }

    public function index()
    {
        return redirect()->route('dashboard.overview');
    }

    public function overview(): Response
    {
        return Inertia::render('Dashboard/Overview', $this->baseProps([
            'topMenus' => $this->topMenus(),
            'predictions' => $this->predictions(),
            'salesByDay' => $this->salesByDay(),
            'bentoCards' => [
                ['title' => 'Online Orders / Pesanan Online', 'value' => $this->onlineOrders()->count(), 'meta' => 'Digital payment ready'],
                ['title' => 'Low Stock / Stok Menipis', 'value' => Ingredient::query()->whereColumn('stock', '<=', 'par_stock')->count(), 'meta' => 'Ingredient alert'],
                ['title' => 'Table Efficiency / Efisiensi Meja', 'value' => $this->tableEfficiency()->avg('efficiency'), 'meta' => 'Rata-rata utilisasi'],
                ['title' => 'CSAT / Kepuasan', 'value' => round((float) CustomerFeedback::query()->avg('rating'), 1), 'meta' => 'Customer satisfaction'],
            ],
        ], 'overview'));
    }

    public function orders(): Response
    {
        return Inertia::render('Dashboard/Orders', $this->baseProps([
            'recentOrders' => Order::query()
                ->with('customer', 'diningTable', 'payments')
                ->latest('ordered_at')
                ->paginate(12),
            'onlineOrders' => $this->onlineOrders()->values(),
            'statusOptions' => collect(OrderStatus::cases())->map(fn(OrderStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ]),
        ], 'orders'));
    }

    public function inventory(): Response
    {
        return Inertia::render('Dashboard/Inventory', $this->baseProps([
            'categories' => MenuCategory::query()->orderBy('sort_order')->get(),
            'menuItems' => MenuItem::query()->with('category')->orderBy('name')->get(),
            'ingredients' => Ingredient::query()->orderBy('name')->get(),
            'alerts' => InventoryAlert::query()->with('ingredient')->where('is_resolved', false)->latest()->get(),
        ], 'inventory'));
    }

    public function tables(): Response
    {
        return Inertia::render('Dashboard/Tables', $this->baseProps([
            'tables' => DiningTable::query()->orderBy('name')->get(),
            'tableEfficiency' => $this->tableEfficiency(),
        ], 'tables'));
    }

    public function reports(): Response
    {
        return Inertia::render('Dashboard/Reports', $this->baseProps([
            'report' => $this->weeklyReport(),
            'predictions' => $this->predictions(),
            'auditLogs' => $this->roleScope()['showAudit']
                ? AuditLog::query()->with('user')->latest()->take(12)->get()
                : [],
        ], 'reports'));
    }

    public function profile(): Response
    {
        return Inertia::render('Dashboard/Profile', $this->baseProps([], 'profile'));
    }

    public function settings(): Response
    {
        return Inertia::render('Dashboard/Settings', $this->baseProps([
            'integrations' => [
                'midtrans' => ['label' => 'Midtrans / Pembayaran', 'status' => config('services.midtrans.server_key') ? 'Connected' : 'Demo Mode'],
            ],
        ], 'settings'));
    }

    public function webContent(): Response
    {
        return Inertia::render('Dashboard/WebContent', $this->baseProps([
            'contentSettings' => \App\Models\Setting::query()
                ->whereIn('key', ['shop_address', 'shop_phone', 'shop_email', 'shop_hours', 'landing_hero_title', 'landing_hero_subtitle', 'about_story_timeline'])
                ->pluck('value', 'key'),
        ], 'web-content'));
    }

    private function baseProps(array $props, string $currentPage): array
    {
        $this->predictiveStockService->generate();
        $user = request()->user();

        $nav = [
            ['label' => 'Dashboard / Dasbor', 'href' => route('dashboard.overview'), 'key' => 'overview'],
            ['label' => 'Orders / Pesanan', 'href' => route('dashboard.orders'), 'key' => 'orders'],
            ['label' => 'Inventory / Stok', 'href' => route('dashboard.inventory'), 'key' => 'inventory'],
        ];

        // Meja & Laporan untuk Admin ke atas
        if (in_array($user->role, [Role::SuperAdmin, Role::Admin])) {
            $nav[] = ['label' => 'Tables / Meja', 'href' => route('dashboard.tables'), 'key' => 'tables'];
            $nav[] = ['label' => 'Reports / Laporan', 'href' => route('dashboard.reports'), 'key' => 'reports'];
        }

        $sidebarUtilities = [
            ['label' => 'Profile / Profil', 'href' => route('dashboard.profile'), 'key' => 'profile'],
        ];

        if (in_array($user->role, [Role::SuperAdmin, Role::Admin])) {
            $sidebarUtilities[] = ['label' => 'Web Content / Konten', 'href' => route('dashboard.web-content'), 'key' => 'web-content'];
        }

        // Settings & Staff Management hanya untuk SuperAdmin
        if ($user->role === Role::SuperAdmin) {
            $sidebarUtilities[] = ['label' => 'Staff / Pegawai', 'href' => route('dashboard.users'), 'key' => 'users'];
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

    private function weeklyReport(): array
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

    private function analytics(): array
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

    private function roleScope(): array
    {
        return match (request()->user()->role) {
            Role::SuperAdmin => ['showAudit' => true, 'showStockManager' => true],
            Role::Admin => ['showAudit' => false, 'showStockManager' => true],
            Role::Employee => ['showAudit' => false, 'showStockManager' => false],
            default => ['showAudit' => false, 'showStockManager' => false],
        };
    }

    private function topMenus()
    {
        return MenuItem::query()
            ->withCount([
                'orderItems as sold_qty' => fn($query) => $query
                    ->whereHas('order', fn($orderQuery) => $orderQuery->whereBetween('ordered_at', [now()->subDays(30), now()])),
            ])
            ->orderByDesc('sold_qty')
            ->take(6)
            ->get(['id', 'name', 'price']);
    }

    private function predictions()
    {
        return WeeklyPrediction::query()->with('menuItem')->latest('trend_score')->take(6)->get();
    }

    private function tableEfficiency()
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

    private function salesByDay()
    {
        return Order::query()
            ->selectRaw('DATE(ordered_at) as day, SUM(total_amount) as total')
            ->whereBetween('ordered_at', [now()->subDays(6), now()->endOfDay()])
            ->groupBy(DB::raw('DATE(ordered_at)'))
            ->orderBy('day')
            ->get();
    }

    private function onlineOrders()
    {
        return Order::query()
            ->with('customer', 'payments')
            ->where('payment_channel', PaymentChannel::Qris->value)
            ->latest('ordered_at')
            ->take(8)
            ->get()
            ->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'public_id' => $order->public_id,
                    'customer' => $order->customer?->name ?? 'Guest',
                    'payment_status' => $order->payment_status->value,
                    'total_amount' => $order->total_amount,
                    'shipping_status' => 'Self Pickup / Dine-in',
                ];
            });
    }
}
