<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateComplaintStatusRequest;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\Department;
use App\Notifications\ComplaintStatusUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Every complaint in the system, with search + category/status/date filters.
     */
    public function index(Request $request): View
    {
        $query = Complaint::with(['user', 'department'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $complaints = $query->paginate(15)->withQueryString();

        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Full detail view for one complaint, including the status update form.
     */
    public function show(Complaint $complaint): View
    {
        $complaint->load(['images', 'updates.admin', 'user', 'department']);
        $departments = Department::orderBy('name')->get();

        return view('admin.complaints.show', compact('complaint', 'departments'));
    }

    /**
     * Update status, remarks, assignment, and optionally attach a resolution photo.
     * Every change is recorded in complaint_updates and emailed to the resident.
     */
    public function update(UpdateComplaintStatusRequest $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validated();

        $complaint->status = $validated['status'];
        $complaint->admin_remarks = $validated['admin_remarks'] ?? $complaint->admin_remarks;
        $complaint->assigned_to = $validated['assigned_to'] ?? $complaint->assigned_to;
        $complaint->department_id = $validated['department_id'] ?? $complaint->department_id;

        if ($complaint->status === Complaint::STATUS_RESOLVED && ! $complaint->resolved_at) {
            $complaint->resolved_at = now();
        }

        if ($request->hasFile('resolution_photo')) {
            $path = $request->file('resolution_photo')->store('complaints/'.$complaint->id.'/resolution', 'public');
            $complaint->resolution_photo = $path;
        }

        $complaint->save();

        // Permanent audit trail entry - this is what powers the timeline in the UI.
        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'user_id' => $request->user()->id,
            'status' => $complaint->status,
            'remarks' => $validated['admin_remarks'] ?? null,
        ]);

        // Let the resident know their complaint changed status.
        $complaint->user->notify(new ComplaintStatusUpdated($complaint));

        return redirect()
            ->route('admin.complaints.show', $complaint)
            ->with('success', 'Complaint updated. The resident has been notified by email.');
    }
}
