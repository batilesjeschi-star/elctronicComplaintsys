<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

/**
 * Admin landing page: high-level statistics + most recent complaints.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::where('status', 'Pending')->count(),
            'under_review' => Complaint::where('status', 'Under Review')->count(),
            'in_progress' => Complaint::where('status', 'In Progress')->count(),
            'resolved' => Complaint::where('status', 'Resolved')->count(),
            'rejected' => Complaint::where('status', 'Rejected')->count(),
        ];

        // Complaints filed per category, used for a simple breakdown table on the dashboard
        $byCategory = Complaint::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $recent = Complaint::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'byCategory', 'recent'));
    }
}
