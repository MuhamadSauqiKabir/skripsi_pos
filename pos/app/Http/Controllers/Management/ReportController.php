<?php

namespace App\Http\Controllers\Management;

use App\Exports\WeeklySalesExport;
use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use App\Models\MenuItem;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function exportWeekly(): BinaryFileResponse
    {
        $startDate = now()->startOfWeek()->toImmutable();
        $endDate = now()->endOfWeek()->toImmutable();

        $orders = Order::query()
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->get();

        $report = [
            'gross_profit' => round((float) $orders->sum('gross_profit'), 2),
            'revenue' => round((float) $orders->sum('total_amount'), 2),
            'top_menus' => MenuItem::query()
                ->select('menu_items.name', DB::raw('SUM(order_items.quantity) as sold_qty'))
                ->join('order_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->whereBetween('orders.ordered_at', [$startDate, $endDate])
                ->groupBy('menu_items.id', 'menu_items.name')
                ->orderByDesc('sold_qty')
                ->take(5)
                ->get(),
            'customer_rating' => round((float) CustomerFeedback::query()->avg('rating'), 2),
            'table_efficiency' => Order::query()
                ->select('dining_table_id', DB::raw('COUNT(*) as total_orders'))
                ->whereBetween('ordered_at', [$startDate, $endDate])
                ->groupBy('dining_table_id')
                ->get(),
        ];

        return Excel::download(
            new WeeklySalesExport($report, $startDate, $endDate),
            'nineties-coffee-weekly-report-'.$startDate->format('Y-m-d').'.xlsx',
        );
    }
}
