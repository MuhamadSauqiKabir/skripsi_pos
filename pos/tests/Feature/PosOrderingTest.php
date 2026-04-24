<?php

use App\Models\DiningTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;

it('can render customer qr ordering page', function () {
    $category = MenuCategory::create([
        'name' => 'Coffee',
        'slug' => 'coffee',
        'sort_order' => 1,
    ]);

    MenuItem::create([
        'menu_category_id' => $category->id,
        'name' => 'Retro Latte',
        'slug' => 'retro-latte',
        'price' => 25000,
        'cost_of_goods' => 10000,
        'prep_time_minutes' => 5,
        'is_available' => true,
    ]);

    $table = DiningTable::create([
        'name' => 'A1',
        'capacity' => 2,
        'coordinate_x' => 10,
        'coordinate_y' => 20,
        'public_token' => 'table-a1',
    ]);

    $this->get("/qr/{$table->public_token}")
        ->assertOk()
        ->assertSee('Retro Latte');
});
