<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * The single "/dashboard" route Breeze redirects everyone to after login.
     * We branch here based on role instead of touching Breeze's auth controllers.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $stats = [
            'total' => $user->complaints()->count(),
            'pending' => $user->complaints()->where('status', Complaint::STATUS_PENDING)->count(),
            'in_progress' => $user->complaints()->where('status', Complaint::STATUS_IN_PROGRESS)->count(),
            'resolved' => $user->complaints()->where('status', Complaint::STATUS_RESOLVED)->count(),
        ];

        $recentComplaints = $user->complaints()->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recentComplaints'));
    }
}
