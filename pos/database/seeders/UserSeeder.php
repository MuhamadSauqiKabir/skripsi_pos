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
            ['email' => 'admin@pos.com'],
            [
                'name' => 'Legacy Admin',
                'role' => Role::Admin,
                'phone' => '080000000000',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'kasir@pos.com'],
            [
                'name' => 'Legacy Kasir',
                'role' => Role::Employee,
                'phone' => '089999999999',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
