<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates one admin account and one sample resident account so the
 * grader / adviser can log in immediately after seeding.
 *
 * Admin login:    admin@ecs.gov.ph / password
 * Resident login: juan@example.com / password
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ecs.gov.ph'],
            [
                'name' => 'Barangay Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'juan@example.com'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'phone' => '09171234567',
                'address' => 'Purok 3, Barangay Sample',
                'email_verified_at' => now(),
            ]
        );
    }
}
