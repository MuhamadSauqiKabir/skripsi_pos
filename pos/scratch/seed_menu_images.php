<?php

use App\Models\MenuItem;

$images = [
    'Coffee' => 'https://images.unsplash.com/photo-1541167760496-16295508e1f3?auto=format&fit=crop&q=80&w=800',
    'Latte' => 'https://images.unsplash.com/photo-1570968015848-7117dd02da74?auto=format&fit=crop&q=80&w=800',
    'Cappuccino' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&q=80&w=800',
    'Espresso' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?auto=format&fit=crop&q=80&w=800',
    'Cake' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&q=80&w=800',
    'Pastry' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&q=80&w=800',
    'Bread' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&q=80&w=800',
    'Tea' => 'https://images.unsplash.com/photo-1544787210-2213d64ac9f9?auto=format&fit=crop&q=80&w=800',
];

$menuItems = MenuItem::all();

foreach ($menuItems as $item) {
    foreach ($images as $keyword => $url) {
        if (stripos($item->name, $keyword) !== false) {
            $item->update(['image_url' => $url]);
            echo "Updated {$item->name} with image.\n";
            continue 2;
        }
    }
    // Fallback image if no keyword matches
    $item->update(['image_url' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=800']);
}
