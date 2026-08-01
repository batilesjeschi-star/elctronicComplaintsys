<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * The authenticated resident's own complaint history, with search + status filter.
     */
    public function index(Request $request): View
    {
        $query = Auth::user()->complaints()->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $complaints = $query->paginate(10)->withQueryString();

        return view('complaints.index', compact('complaints'));
    }

    /**
     * Show the "report a problem" form.
     */
    public function create(): View
    {
        return view('complaints.create');
    }

    /**
     * Save a new complaint, store any uploaded photos, and start its audit trail.
     */
    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $complaint = Auth::user()->complaints()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'location' => $validated['location'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status' => Complaint::STATUS_PENDING,
        ]);

        // Each photo is stored on the "public" disk under complaints/{id}/...
        // so it can later be served through the public/storage symlink.
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('complaints/'.$complaint->id, 'public');

                $complaint->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        ComplaintUpdate::create([
            'complaint_id' => $complaint->id,
            'user_id' => null,
            'status' => Complaint::STATUS_PENDING,
            'remarks' => 'Complaint submitted by resident.',
        ]);

        return redirect()
            ->route('complaints.show', $complaint)
            ->with('success', "Your complaint was submitted! Your reference number is {$complaint->reference_number}.");
    }

    /**
     * View a single complaint. Residents may only view their own.
     */
    public function show(Complaint $complaint): View
    {
        $this->authorizeAccess($complaint);

        $complaint->load(['images', 'updates.admin', 'department']);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Printable receipt showing the reference number, used as proof of filing.
     */
    public function receipt(Complaint $complaint): View
    {
        $this->authorizeAccess($complaint);

        return view('complaints.receipt', compact('complaint'));
    }

    /**
     * Residents can only see their own complaints; admins can see everything.
     */
    private function authorizeAccess(Complaint $complaint): void
    {
        if ($complaint->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'You are not allowed to view this complaint.');
        }
    }
}
