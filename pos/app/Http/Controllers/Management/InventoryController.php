<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Services\AuditLogService;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function adjust(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['restock', 'waste'])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $this->inventoryService->adjust(
            $ingredient,
            (float) $data['quantity'],
            $data['type'],
            $request->user(),
            $data['notes'] ?? null,
        );

        $this->auditLogService->log($request->user()?->id, 'inventory.adjusted', 'ingredient', $ingredient->id, $data, $request);

        return back()->with('success', 'Stok bahan berhasil diperbarui.');
    }
}
