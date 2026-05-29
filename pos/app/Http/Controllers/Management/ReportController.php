<?php

namespace App\Http\Controllers\Management;

use App\Exports\WeeklySalesExport;
use App\Http\Controllers\Concerns\HasDashboardProps;
use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use App\Models\AuditLog;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\WeeklyPrediction;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    use HasDashboardProps;

    public function index(Request $request): Response
    {
        $start = $request->query('start', now()->startOfWeek()->format('Y-m-d'));
        $end = $request->query('end', now()->endOfWeek()->format('Y-m-d'));

        $startDate = CarbonImmutable::parse($start);
        $endDate = CarbonImmutable::parse($end)->endOfDay();

        $orders = Order::query()
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->get();

        $analytics = [
            'gross_profit' => round((float) $orders->sum('gross_profit'), 2),
            'revenue' => round((float) $orders->sum('total_amount'), 2),
            'total_orders' => $orders->count(),
            'customer_rating' => round((float) CustomerFeedback::query()->whereBetween('created_at', [$startDate, $endDate])->avg('rating'), 2),
            
            // Data untuk Line Chart (Tren Pendapatan Harian)
            'daily_trends' => Order::query()
                ->select(DB::raw('DATE(ordered_at) as date'), DB::raw('SUM(total_amount) as amount'))
                ->whereBetween('ordered_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            // Data untuk Pie Chart (Kategori Terlaris)
            'category_share' => DB::table('menu_categories')
                ->select('menu_categories.name', DB::raw('COUNT(order_items.id) as count'))
                ->join('menu_items', 'menu_items.menu_category_id', '=', 'menu_categories.id')
                ->join('order_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereBetween('orders.ordered_at', [$startDate, $endDate])
                ->groupBy('menu_categories.id', 'menu_categories.name')
                ->get(),

            'top_menus' => MenuItem::query()
                ->select('menu_items.name', DB::raw('SUM(order_items.quantity) as sold_qty'), DB::raw('SUM(order_items.line_total) as sales'))
                ->join('order_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereBetween('orders.ordered_at', [$startDate, $endDate])
                ->groupBy('menu_items.id', 'menu_items.name')
                ->orderByDesc('sold_qty')
                ->take(5)
                ->get(),
        ];

        $base = $this->baseProps([], 'reports');

        return Inertia::render('Dashboard/Reports', [
            ...$base,
            'analytics' => $analytics,
            'filters' => [
                'start' => $start,
                'end' => $end,
            ],
            'predictions' => WeeklyPrediction::query()->with('menuItem')->latest('trend_score')->take(6)->get(),
            'auditLogs' => $base['roleScope']['showAudit']
                ? AuditLog::query()->with('user')->latest()->take(12)->get()
                : [],
        ]);
    }

    public function exportPdf(Request $request)
    {
        $start = $request->query('start', now()->startOfWeek()->format('Y-m-d'));
        $end = $request->query('end', now()->endOfWeek()->format('Y-m-d'));
        $startDate = CarbonImmutable::parse($start);
        $endDate = CarbonImmutable::parse($end)->endOfDay();

        $orders = Order::query()->whereBetween('ordered_at', [$startDate, $endDate])->get();
        
        $data = [
            'title' => 'Laporan Penjualan Nineties Coffee',
            'date_range' => $start . ' s/d ' . $end,
            'revenue' => $orders->sum('total_amount'),
            'profit' => $orders->sum('gross_profit'),
            'orders_count' => $orders->count(),
            'orders' => $orders->load('diningTable', 'customer'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sales_pdf', $data);
        return $pdf->download('report-'.$start.'-to-'.$end.'.pdf');
    }

    public function exportWeekly(Request $request): BinaryFileResponse
    {
        $start = $request->query('start', now()->startOfWeek()->format('Y-m-d'));
        $end = $request->query('end', now()->endOfWeek()->format('Y-m-d'));
        $startDate = CarbonImmutable::parse($start);
        $endDate = CarbonImmutable::parse($end)->endOfDay();

        $orders = Order::query()->whereBetween('ordered_at', [$startDate, $endDate])->get();

        $report = [
            'gross_profit' => round((float) $orders->sum('gross_profit'), 2),
            'revenue' => round((float) $orders->sum('total_amount'), 2),
            'total_orders' => $orders->count(),
            'average_rating' => round((float) CustomerFeedback::query()->whereBetween('created_at', [$startDate, $endDate])->avg('rating'), 2),
            'top_menus' => MenuItem::query()
                ->select('menu_items.name', DB::raw('SUM(order_items.quantity) as sold_qty'), DB::raw('SUM(order_items.line_total) as sales'))
                ->join('order_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereBetween('orders.ordered_at', [$startDate, $endDate])
                ->groupBy('menu_items.id', 'menu_items.name')
                ->orderByDesc('sold_qty')
                ->take(10)
                ->get(),
        ];

        return Excel::download(
            new WeeklySalesExport($report, $startDate, $endDate),
            'report-'.$start.'-to-'.$end.'.xlsx',
        );
    }
}
