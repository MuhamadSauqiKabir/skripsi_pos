<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\InventoryAlert;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;

class InventoryService
{
    public function deductForOrder(Order $order): void
    {
        $order->loadMissing('items.menuItem.ingredients');

        $order->items->each(function ($item) use ($order): void {
            $item->menuItem->ingredients->each(function (Ingredient $ingredient) use ($item, $order): void {
                $deduction = (float) $ingredient->pivot->quantity * $item->quantity;
                $before = (float) $ingredient->stock;
                $after = max(0, $before - $deduction);

                $ingredient->update(['stock' => $after]);

                InventoryMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'order_id' => $order->id,
                    'user_id' => $order->created_by,
                    'type' => 'deduction',
                    'quantity' => $deduction,
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'notes' => "Pemakaian otomatis untuk order {$order->public_id}",
                ]);
            });
        });

        $this->syncAlerts();
    }

    public function adjust(Ingredient $ingredient, float $quantity, string $type, ?User $user = null, ?string $notes = null): Ingredient
    {
        $before = (float) $ingredient->stock;
        $after = $type === 'restock' ? $before + $quantity : max(0, $before - $quantity);

        $ingredient->update(['stock' => $after]);

        InventoryMovement::create([
            'ingredient_id' => $ingredient->id,
            'user_id' => $user?->id,
            'type' => $type,
            'quantity' => $quantity,
            'stock_before' => $before,
            'stock_after' => $after,
            'notes' => $notes,
        ]);

        $this->syncAlerts();

        return $ingredient->refresh();
    }

    public function syncAlerts(): Collection
    {
        return Ingredient::query()
            ->where('is_active', true)
            ->get()
            ->map(function (Ingredient $ingredient) {
                if ((float) $ingredient->stock <= (float) $ingredient->par_stock) {
                    return InventoryAlert::updateOrCreate(
                        [
                            'ingredient_id' => $ingredient->id,
                            'is_resolved' => false,
                        ],
                        [
                            'severity' => (float) $ingredient->stock <= ((float) $ingredient->par_stock / 2) ? 'high' : 'medium',
                            'message' => "{$ingredient->name} berada di bawah par stock.",
                        ],
                    );
                }

                InventoryAlert::where('ingredient_id', $ingredient->id)
                    ->where('is_resolved', false)
                    ->update([
                        'is_resolved' => true,
                        'resolved_at' => now(),
                    ]);

                return null;
            })
            ->filter();
    }
}
