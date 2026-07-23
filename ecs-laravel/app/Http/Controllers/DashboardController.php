<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Resident dashboard: quick summary of "my complaints" grouped by status.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $complaints = $user->complaints()->latest()->take(5)->get();

        $stats = [
            'total' => $user->complaints()->count(),
            'pending' => $user->complaints()->where('status', 'Pending')->count(),
            'in_progress' => $user->complaints()->where('status', 'In Progress')->count(),
            'resolved' => $user->complaints()->where('status', 'Resolved')->count(),
        ];

        return view('dashboard', compact('complaints', 'stats'));
    }
}
