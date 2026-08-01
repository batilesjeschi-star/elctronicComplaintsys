<?php

namespace App\Http\Controllers;

use App\Mail\ComplaintStatusUpdated;
use App\Models\Complaint;
use App\Models\ComplaintImage;
use App\Models\ComplaintUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Resident-facing complaint actions: list, create, view, print receipt.
 */
class ComplaintController extends Controller
{
    /**
     * List the logged-in resident's own complaints, with search + pagination.
     */
    public function index(Request $request)
    {
        $query = $request->user()->complaints()->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $complaints = $query->paginate(10)->withQueryString();

        return view('complaints.index', compact('complaints'));
    }

    public function create()
    {
        return view('complaints.create');
    }

    /**
     * Validate the form, store the complaint, save uploaded images,
     * and create the very first entry in the audit trail (complaint_updates).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'in:' . implode(',', Complaint::CATEGORIES)],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png', 'max:4096'], // 4MB each
        ]);

        $complaint = DB::transaction(function () use ($validated, $request) {
            $complaint = Complaint::create([
                'reference_number' => Complaint::generateReferenceNumber(),
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'location' => $validated['location'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'Pending',
            ]);

            // Save each uploaded photo to storage/app/public/complaints
            foreach ($request->file('images', []) as $file) {
                $path = $file->store('complaints', 'public');
                ComplaintImage::create([
                    'complaint_id' => $complaint->id,
                    'image_path' => $path,
                ]);
            }

            // First row of the audit trail
            ComplaintUpdate::create([
                'complaint_id' => $complaint->id,
                'status' => 'Pending',
                'remarks' => 'Complaint submitted by resident.',
                'updated_by' => Auth::id(),
            ]);

            return $complaint;
        });

        return redirect()
            ->route('complaints.show', $complaint)
            ->with('success', "Complaint submitted! Your reference number is {$complaint->reference_number}.");
    }

    /**
     * Show a single complaint (only the owner or an admin may view it).
     */
    public function show(Complaint $complaint)
    {
        $this->authorizeOwnerOrAdmin($complaint);

        $complaint->load(['images', 'updates.updatedBy', 'department']);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Printable receipt with the reference number, useful as proof of filing.
     */
    public function receipt(Complaint $complaint)
    {
        $this->authorizeOwnerOrAdmin($complaint);

        return view('complaints.receipt', compact('complaint'));
    }

    private function authorizeOwnerOrAdmin(Complaint $complaint): void
    {
        $user = Auth::user();
        abort_unless($user->is_admin || $complaint->user_id === $user->id, 403);
    }
}
