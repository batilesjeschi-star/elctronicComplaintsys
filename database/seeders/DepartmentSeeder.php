<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Barangay Engineering Office', 'description' => 'Handles roads, potholes, drainage, and infrastructure repairs.'],
            ['name' => 'Sanitation / Garbage Collection', 'description' => 'Handles garbage collection and general cleanliness concerns.'],
            ['name' => 'Barangay Tanod (Peace and Order)', 'description' => 'Handles public safety and peace-and-order concerns.'],
            ['name' => 'Barangay Health Office', 'description' => 'Handles health and sanitation-related concerns.'],
            ['name' => 'Disaster Risk Reduction Office', 'description' => 'Handles flooding and other disaster-related concerns.'],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['name' => $department['name']], $department);
        }
    }
}
