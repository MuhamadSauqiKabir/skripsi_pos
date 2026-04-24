<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Models\WeeklyPrediction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PredictiveStockService
{
    /**
     * Prediksi dibuat dari bobot tren 4 minggu terbaru vs 4 minggu sebelumnya.
     * Rumus ini sederhana, transparan, dan cocok untuk pendekatan prototype evolutionary.
     */
    public function generate(?CarbonImmutable $weekStart = null): Collection
    {
        $weekStart ??= now()->startOfWeek()->addWeek()->toImmutable();
        $recentStart = $weekStart->subWeeks(4);
        $olderStart = $weekStart->subWeeks(8);

        $recent = OrderItem::query()
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', fn ($query) => $query->whereBetween('ordered_at', [$recentStart, $weekStart]))
            ->groupBy('menu_item_id')
            ->pluck('total_qty', 'menu_item_id');

        $older = OrderItem::query()
            ->select('menu_item_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', fn ($query) => $query->whereBetween('ordered_at', [$olderStart, $recentStart]))
            ->groupBy('menu_item_id')
            ->pluck('total_qty', 'menu_item_id');

        return MenuItem::query()
            ->where('is_available', true)
            ->get()
            ->map(function (MenuItem $menuItem) use ($recent, $older, $weekStart) {
                $recentQty = (int) ($recent[$menuItem->id] ?? 0);
                $olderQty = (int) ($older[$menuItem->id] ?? 0);
                $trendScore = $olderQty > 0 ? (($recentQty - $olderQty) / $olderQty) * 100 : $recentQty * 10;
                $predictedQty = max(1, (int) round(($recentQty * 0.7) + (max($olderQty, 1) * 0.3)));

                return WeeklyPrediction::updateOrCreate(
                    [
                        'week_start' => $weekStart->toDateString(),
                        'menu_item_id' => $menuItem->id,
                    ],
                    [
                        'predicted_qty' => $predictedQty,
                        'trend_score' => round($trendScore, 2),
                        'meta' => [
                            'recent_4_weeks' => $recentQty,
                            'older_4_weeks' => $olderQty,
                        ],
                    ],
                );
            })
            ->sortByDesc('trend_score')
            ->values();
    }
}
