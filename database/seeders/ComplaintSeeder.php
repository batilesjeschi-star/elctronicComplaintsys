<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComplaintSeeder extends Seeder
{
    /**
     * Creates a handful of sample residents and complaints so the app
     * has realistic data to demo immediately after installation.
     */
    public function run(): void
    {
        $residents = User::factory()->count(5)->create([
            'role' => 'resident',
            'password' => Hash::make('password123'),
        ]);

        $categories = array_keys(Complaint::CATEGORIES);
        $statuses = array_keys(Complaint::STATUSES);
        $departmentIds = Department::pluck('id')->all();
        $adminId = User::where('role', 'admin')->value('id');

        foreach (range(1, 15) as $i) {
            $resident = $residents->random();
            $status = $statuses[array_rand($statuses)];

            $complaint = Complaint::create([
                'user_id' => $resident->id,
                'title' => $this->sampleTitle(),
                'description' => 'This is a sample complaint generated for demonstration purposes. Feel free to delete these once you have real resident reports.',
                'category' => $categories[array_rand($categories)],
                'location' => 'Purok '.rand(1, 7).', Sample Street',
                'latitude' => 11.2400 + (rand(-100, 100) / 10000),
                'longitude' => 125.0000 + (rand(-100, 100) / 10000),
                'status' => $status,
                'assigned_to' => $status !== Complaint::STATUS_PENDING ? 'Juan Dela Cruz' : null,
                'department_id' => $status !== Complaint::STATUS_PENDING && $departmentIds
                    ? $departmentIds[array_rand($departmentIds)]
                    : null,
                'admin_remarks' => $status === Complaint::STATUS_RESOLVED ? 'Issue has been addressed by the assigned team.' : null,
                'resolved_at' => $status === Complaint::STATUS_RESOLVED ? now()->subDays(rand(1, 10)) : null,
            ]);

            // Every complaint starts with a "submitted" entry in its audit trail.
            ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'user_id' => null,
                'status' => Complaint::STATUS_PENDING,
                'remarks' => 'Complaint submitted by resident.',
            ]);

            // Complaints that moved past "pending" get a follow-up audit entry too.
            if ($status !== Complaint::STATUS_PENDING && $adminId) {
                ComplaintUpdate::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => $adminId,
                    'status' => $status,
                    'remarks' => 'Status updated by barangay staff.',
                ]);
            }
        }
    }

    private function sampleTitle(): string
    {
        $titles = [
            'Large pothole on main road',
            'Streetlight not working for a week',
            'Uncollected garbage piling up',
            'Clogged drainage causing flooding',
            'Loose electrical wires near school',
            'Overgrown grass blocking sidewalk',
            'Stray animals roaming the area',
            'Broken water pipe on the street',
        ];

        return $titles[array_rand($titles)];
    }
}
