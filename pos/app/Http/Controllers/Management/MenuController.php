<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'menu_category_id' => ['required', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_of_goods' => ['required', 'numeric', 'min:0'],
            'prep_time_minutes' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $menu = MenuItem::create([
            ...$data,
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
        ]);

        $this->auditLogService->log($request->user()?->id, 'menu.created', 'menu_item', $menu->id, $data, $request);

        return back()->with('success', 'Menu baru berhasil ditambahkan.');
    }

    public function toggleAvailability(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        $this->auditLogService->log(
            $request->user()?->id,
            'menu.availability_toggled',
            'menu_item',
            $menuItem->id,
            ['is_available' => $menuItem->is_available],
            $request,
        );

        return back()->with('success', 'Status ketersediaan menu diperbarui.');
    }
}
