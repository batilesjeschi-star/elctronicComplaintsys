<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates (or updates) the default barangay staff login.
     *
     * IMPORTANT: change this password before deploying anywhere public.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ecs.test'],
            [
                'name' => 'Barangay Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '09171234567',
                'address' => 'Barangay Hall',
                'email_verified_at' => now(),
            ]
        );
    }
}
