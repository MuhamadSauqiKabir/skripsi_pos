<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\DiningTable;
use App\Models\Ingredient;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use App\Services\TableQrCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PosSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'superadmin@ninetiescoffee.test'],
            [
                'name' => 'Nineties Super Admin',
                'role' => Role::SuperAdmin,
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'owner@ninetiescoffee.test'],
            [
                'name' => 'Nineties Owner',
                'role' => Role::Admin,
                'phone' => '081111111111',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'kasir@ninetiescoffee.test'],
            [
                'name' => 'Kasir Nineties',
                'role' => Role::Employee,
                'phone' => '082222222222',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $coffee = MenuCategory::query()->firstOrCreate(
            ['slug' => 'coffee-signature'],
            ['name' => 'Coffee Signature', 'description' => 'Kopi khas Nineties', 'sort_order' => 1],
        );
        $nonCoffee = MenuCategory::query()->firstOrCreate(
            ['slug' => 'non-coffee'],
            ['name' => 'Non Coffee', 'description' => 'Minuman creamy dan segar', 'sort_order' => 2],
        );
        $snack = MenuCategory::query()->firstOrCreate(
            ['slug' => 'artisan-bites'],
            ['name' => 'Artisan Bites', 'description' => 'Teman ngopi favorit', 'sort_order' => 3],
        );

        $ingredients = collect([
            ['name' => 'Arabica Beans', 'unit' => 'gram', 'stock' => 4500, 'par_stock' => 1500, 'cost_per_unit' => 0.9],
            ['name' => 'Fresh Milk', 'unit' => 'ml', 'stock' => 10000, 'par_stock' => 3000, 'cost_per_unit' => 0.05],
            ['name' => 'Caramel Syrup', 'unit' => 'ml', 'stock' => 2500, 'par_stock' => 700, 'cost_per_unit' => 0.08],
            ['name' => 'Chocolate Syrup', 'unit' => 'ml', 'stock' => 2200, 'par_stock' => 700, 'cost_per_unit' => 0.08],
            ['name' => 'Mineral Water', 'unit' => 'ml', 'stock' => 12000, 'par_stock' => 3000, 'cost_per_unit' => 0.01],
        ])->mapWithKeys(function (array $ingredient) {
            $model = Ingredient::query()->updateOrCreate(['name' => $ingredient['name']], $ingredient);

            return [$model->name => $model];
        });

        $menus = [
            [
                'category' => $coffee,
                'name' => 'Nineties Gold Latte',
                'description' => 'Espresso creamy dengan karakter vintage.',
                'price' => 32000,
                'cost_of_goods' => 13000,
                'prep_time_minutes' => 7,
                'ingredients' => ['Arabica Beans' => 18, 'Fresh Milk' => 180, 'Caramel Syrup' => 20],
            ],
            [
                'category' => $coffee,
                'name' => 'Retro Americano',
                'description' => 'Espresso bold dengan aftertaste clean.',
                'price' => 24000,
                'cost_of_goods' => 9000,
                'prep_time_minutes' => 5,
                'ingredients' => ['Arabica Beans' => 18, 'Mineral Water' => 150],
            ],
            [
                'category' => $nonCoffee,
                'name' => 'Creamy 90s Choco',
                'description' => 'Cokelat hangat dengan sentuhan nostalgia.',
                'price' => 28000,
                'cost_of_goods' => 11000,
                'prep_time_minutes' => 6,
                'ingredients' => ['Fresh Milk' => 170, 'Chocolate Syrup' => 25],
            ],
            [
                'category' => $snack,
                'name' => 'Butter Croffle',
                'description' => 'Croffle renyah untuk teman meeting.',
                'price' => 26000,
                'cost_of_goods' => 10000,
                'prep_time_minutes' => 8,
                'ingredients' => [],
            ],
        ];

        foreach ($menus as $menuData) {
            $menu = MenuItem::query()->updateOrCreate(
                ['slug' => Str::slug($menuData['name'])],
                [
                    'menu_category_id' => $menuData['category']->id,
                    'name' => $menuData['name'],
                    'description' => $menuData['description'],
                    'price' => $menuData['price'],
                    'cost_of_goods' => $menuData['cost_of_goods'],
                    'prep_time_minutes' => $menuData['prep_time_minutes'],
                    'is_featured' => true,
                    'is_available' => true,
                ],
            );

            $syncData = collect($menuData['ingredients'])
                ->mapWithKeys(fn ($quantity, $name) => [$ingredients[$name]->id => ['quantity' => $quantity]])
                ->all();

            $menu->ingredients()->sync($syncData);
        }

        $tables = [
            ['name' => 'Meja A1', 'capacity' => 2, 'coordinate_x' => 12, 'coordinate_y' => 25],
            ['name' => 'Meja A2', 'capacity' => 4, 'coordinate_x' => 36, 'coordinate_y' => 25],
            ['name' => 'Meja B1', 'capacity' => 4, 'coordinate_x' => 12, 'coordinate_y' => 54],
            ['name' => 'Meja B2', 'capacity' => 6, 'coordinate_x' => 36, 'coordinate_y' => 54],
        ];

        $qrService = app(TableQrCodeService::class);

        foreach ($tables as $tableData) {
            $table = DiningTable::query()->firstOrCreate(
                ['name' => $tableData['name']],
                [...$tableData, 'public_token' => Str::lower(Str::random(14))],
            );

            $qrService->refresh($table);
        }
    }
}
