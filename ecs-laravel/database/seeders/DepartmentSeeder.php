<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Public Works & Infrastructure', 'contact_person' => 'Engr. Santos', 'contact_number' => '09170000001'],
            ['name' => 'Sanitation & Waste Management', 'contact_person' => 'Mr. Reyes', 'contact_number' => '09170000002'],
            ['name' => 'Barangay Tanod (Public Safety)', 'contact_person' => 'Tanod Chief Cruz', 'contact_number' => '09170000003'],
            ['name' => 'Drainage & Flood Control', 'contact_person' => 'Engr. Bautista', 'contact_number' => '09170000004'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['name' => $dept['name']], $dept);
        }
    }
}
