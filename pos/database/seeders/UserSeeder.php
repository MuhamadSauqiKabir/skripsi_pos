<?php

namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'superadmin@nineties.id'],
            [
                'name' => 'Super Admin Nineties',
                'role' => Role::SuperAdmin,
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@nineties.id'],
            [
                'name' => 'Manager Senopati',
                'role' => Role::Admin,
                'phone' => '081290001990',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'employee@nineties.id'],
            [
                'name' => 'Staf Barista',
                'role' => Role::Employee,
                'phone' => '089988887777',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
