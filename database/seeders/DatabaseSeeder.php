<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This file REPLACES the default DatabaseSeeder that ships with Laravel.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DepartmentSeeder::class,
            ComplaintSeeder::class,
        ]);
    }
}
