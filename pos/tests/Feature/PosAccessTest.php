<?php

use App\Enums\Role;
use App\Models\User;

it('allows staff roles to access dashboard', function () {
    $user = User::factory()->create([
        'role' => Role::Admin,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/dashboard/overview');
});

it('blocks employee from admin menu management endpoints', function () {
    $user = User::factory()->create([
        'role' => Role::Employee,
    ]);

    $this->actingAs($user)
        ->post('/management/menu-items', [
            'menu_category_id' => 1,
            'name' => 'Blocked Menu',
            'price' => 1000,
            'cost_of_goods' => 500,
            'prep_time_minutes' => 5,
        ])
        ->assertForbidden();
});
