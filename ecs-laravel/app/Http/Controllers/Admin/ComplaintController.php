<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ComplaintStatusUpdated;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Admin-facing complaint management: listing/filtering, detail view,
 * status updates, assignment, and resolution photo uploads.
 */
class ComplaintController extends Controller
{
    /**
     * All complaints, with filters for category / status / date range and a search box.
     */
    public function index(Request $request)
    {
        $query = Complaint::with('user')->latest();

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(15)->withQueryString();

        return view('admin.complaints.index', [
            'complaints' => $complaints,
            'categories' => Complaint::CATEGORIES,
            'statuses' => Complaint::STATUSES,
        ]);
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['images', 'updates.updatedBy', 'user', 'department']);
        $departments = Department::orderBy('name')->get();

        return view('admin.complaints.show', [
            'complaint' => $complaint,
            'departments' => $departments,
            'statuses' => Complaint::STATUSES,
        ]);
    }

    /**
     * Update status/remarks/assignment. Creates an audit-trail row and
     * emails the resident about the change. If marked Resolved, an
     * optional resolution photo can be uploaded.
     */
    public function update(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', Complaint::STATUSES)],
            'admin_remarks' => ['nullable', 'string', 'max:5000'],
            'assigned_to' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'resolution_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:4096'],
        ]);

        if ($request->hasFile('resolution_photo')) {
            $validated['resolution_photo'] = $request->file('resolution_photo')->store('resolutions', 'public');
        }

        if ($validated['status'] === 'Resolved') {
            $validated['resolved_at'] = now();
        }

        $complaint->update($validated);

        // Audit trail entry
        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'status' => $validated['status'],
            'remarks' => $validated['admin_remarks'] ?? null,
            'updated_by' => Auth::id(),
        ]);

        // Notify the resident by email (fails silently in local/dev if mail isn't configured)
        try {
            Mail::to($complaint->user->email)->send(new ComplaintStatusUpdated($complaint->fresh()));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Complaint updated and resident notified.');
    }

    /**
     * Simple report screen: filter complaints by a date range (daily/weekly/monthly
     * presets just adjust the "from" date before hitting this same route).
     */
    public function report(Request $request)
    {
        $period = $request->get('period', 'daily');

        $from = match ($period) {
            'weekly' => now()->subWeek(),
            'monthly' => now()->subMonth(),
            default => now()->startOfDay(),
        };

        $complaints = Complaint::with('user')
            ->where('created_at', '>=', $from)
            ->latest()
            ->get();

        $summary = $complaints->groupBy('status')->map->count();

        return view('admin.report', compact('complaints', 'summary', 'period'));
    }
}
