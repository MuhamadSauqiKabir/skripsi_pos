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

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:menu_categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        \App\Models\MenuCategory::create([
            ...$data,
            'slug' => Str::slug($data['name']),
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
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
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu-items', 'public');
            $imageUrl = '/storage/' . $path;
        }

        $menu = MenuItem::create([
            'menu_category_id' => $data['menu_category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'cost_of_goods' => $data['cost_of_goods'],
            'prep_time_minutes' => $data['prep_time_minutes'],
            'image_url' => $imageUrl,
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
        ]);

        $this->auditLogService->log($request->user()?->id, 'menu.created', 'menu_item', $menu->id, $data, $request);

        return back()->with('success', 'Menu baru berhasil ditambahkan.');
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validate([
            'menu_category_id' => ['required', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_of_goods' => ['required', 'numeric', 'min:0'],
            'prep_time_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $attributes = [
            'menu_category_id' => $data['menu_category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'cost_of_goods' => $data['cost_of_goods'],
            'prep_time_minutes' => $data['prep_time_minutes'],
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu-items', 'public');
            $attributes['image_url'] = '/storage/' . $path;
        }

        $menuItem->update($attributes);

        $this->auditLogService->log($request->user()?->id, 'menu.updated', 'menu_item', $menuItem->id, $attributes, $request);

        return back()->with('success', 'Menu berhasil diperbarui.');
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
