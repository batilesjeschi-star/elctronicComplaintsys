<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * System-wide statistics for the barangay staff dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::where('status', Complaint::STATUS_PENDING)->count(),
            'under_review' => Complaint::where('status', Complaint::STATUS_UNDER_REVIEW)->count(),
            'in_progress' => Complaint::where('status', Complaint::STATUS_IN_PROGRESS)->count(),
            'resolved' => Complaint::where('status', Complaint::STATUS_RESOLVED)->count(),
            'rejected' => Complaint::where('status', Complaint::STATUS_REJECTED)->count(),
        ];

        // Powers the category breakdown chart on the dashboard.
        $byCategory = Complaint::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $recentComplaints = Complaint::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'byCategory', 'recentComplaints'));
    }
}
