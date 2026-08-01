@extends('layouts.app')
@section('title', 'Manage Complaint')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-0">{{ $complaint->title }}</h4>
            <small class="text-muted">Ref #: <strong>{{ $complaint->reference_number }}</strong> &middot; Filed by {{ $complaint->user->name }} ({{ $complaint->user->email }})</small>
        </div>
        <span class="badge fs-6 {{ $complaint->statusBadgeClass() }}">{{ $complaint->status }}</span>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card p-3 mb-3">
                <h6 class="text-muted">Description</h6>
                <p>{{ $complaint->description }}</p>
                <h6 class="text-muted">Category</h6>
                <p>{{ $complaint->category }}</p>
                <h6 class="text-muted">Location</h6>
                <p>{{ $complaint->location }}
                    @if ($complaint->latitude && $complaint->longitude)
                        <br><small class="text-muted">GPS: {{ $complaint->latitude }}, {{ $complaint->longitude }}
                        (<a href="https://www.google.com/maps?q={{ $complaint->latitude }},{{ $complaint->longitude }}" target="_blank">view on map</a>)</small>
                    @endif
                </p>
            </div>

            @if ($complaint->images->count())
                <div class="card p-3 mb-3">
                    <h6 class="text-muted">Uploaded Photos</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($complaint->images as $image)
                            <a href="{{ $image->url }}" target="_blank">
                                <img src="{{ $image->url }}" class="rounded border" style="width:140px;height:140px;object-fit:cover;">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="card p-3">
                <h6 class="text-muted">Status History</h6>
                <ul class="list-unstyled mb-0">
                    @foreach ($complaint->updates as $update)
                        <li class="mb-3 border-start border-3 ps-2 border-success">
                            <span class="badge {{ (new \App\Models\Complaint(['status' => $update->status]))->statusBadgeClass() }}">{{ $update->status }}</span>
                            <span class="small text-muted">by {{ $update->updatedBy->name ?? 'System' }} — {{ $update->created_at->format('M d, Y g:i A') }}</span>
                            @if ($update->remarks)
                                <div class="small">{{ $update->remarks }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-3">
                <h6 class="text-muted mb-3">Update Complaint</h6>
                <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected($complaint->status == $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks / Action Taken</label>
                        <textarea name="admin_remarks" class="form-control" rows="3">{{ old('admin_remarks', $complaint->admin_remarks) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign to Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- None --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected($complaint->department_id == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign to Personnel (name)</label>
                        <input type="text" name="assigned_to" value="{{ old('assigned_to', $complaint->assigned_to) }}" class="form-control" placeholder="e.g. Engr. Dela Cruz">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Resolution Photo (upload when marking as Resolved)</label>
                        <input type="file" name="resolution_photo" class="form-control" accept="image/png, image/jpeg">
                    </div>

                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-check2"></i> Save Update</button>
                </form>
            </div>
        </div>
    </div>
@endsection
