<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * A handful of sample complaints in different statuses/categories so the
 * dashboard, filters, and tables have something to display right away.
 */
class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $resident = User::where('email', 'juan@example.com')->first();

        if (! $resident) {
            return;
        }

        $samples = [
            [
                'title' => 'Large pothole along Rizal Street',
                'description' => 'A deep pothole near the barangay hall is causing traffic and is dangerous at night.',
                'category' => 'Road',
                'location' => 'Rizal Street, near Barangay Hall',
                'status' => 'Pending',
            ],
            [
                'title' => 'Uncollected garbage for 3 days',
                'description' => 'Garbage bins along Mabini St. have not been collected since Monday and are starting to smell.',
                'category' => 'Garbage',
                'location' => 'Mabini Street, Purok 2',
                'status' => 'Under Review',
            ],
            [
                'title' => 'Broken street light at the basketball court',
                'description' => 'The street light near the covered court has been flickering and is now completely out.',
                'category' => 'Street Light',
                'location' => 'Barangay Covered Court',
                'status' => 'In Progress',
            ],
            [
                'title' => 'Flooding after heavy rain',
                'description' => 'The intersection floods knee-deep every time it rains hard due to a clogged drainage canal.',
                'category' => 'Drainage',
                'location' => 'Corner of Bonifacio and Luna St.',
                'status' => 'Resolved',
            ],
            [
                'title' => 'Stray dogs causing safety concerns',
                'description' => 'A pack of stray dogs has been chasing children walking to school in the morning.',
                'category' => 'Safety',
                'location' => 'Near Sample Elementary School',
                'status' => 'Rejected',
            ],
        ];

        foreach ($samples as $sample) {
            $complaint = Complaint::updateOrCreate(
                ['title' => $sample['title']],
                [
                    'reference_number' => Complaint::generateReferenceNumber(),
                    'user_id' => $resident->id,
                    'description' => $sample['description'],
                    'category' => $sample['category'],
                    'location' => $sample['location'],
                    'status' => $sample['status'],
                    'admin_remarks' => $sample['status'] === 'Rejected' ? 'Duplicate report / outside barangay jurisdiction.' : null,
                ]
            );

            if ($complaint->updates()->count() === 0) {
                ComplaintUpdate::create([
                    'complaint_id' => $complaint->id,
                    'status' => $sample['status'],
                    'remarks' => 'Seeded sample record.',
                    'updated_by' => null,
                ]);
            }
        }
    }
}
