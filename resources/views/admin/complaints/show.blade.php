@extends('layouts.admin')

@section('title', 'Complaint '.$complaint->reference_number)

@section('content')

    <a href="{{ route('admin.complaints.index') }}" class="small text-muted d-inline-block mb-2">
        <i class="bi bi-arrow-left"></i> Back to All Complaints
    </a>

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-2">
        <div>
            <h1 class="h3 fw-semibold mb-1">{{ $complaint->title }}</h1>
            <span class="font-monospace text-muted">{{ $complaint->reference_number }}</span>
        </div>
        <span class="badge {{ $complaint->status_badge_class }} fs-6 py-2 px-3">{{ $complaint->status_label }}</span>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">

            <div class="card card-ecs mb-4">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Report Details</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Filed By</dt>
                        <dd class="col-sm-8">
                            {{ $complaint->user->name }}
                            @if ($complaint->user->phone)<br><span class="text-muted small">{{ $complaint->user->phone }}</span>@endif
                        </dd>

                        <dt class="col-sm-4">Category</dt>
                        <dd class="col-sm-8"><i class="bi {{ $complaint->category_icon }} me-1"></i>{{ $complaint->category_label }}</dd>

                        <dt class="col-sm-4">Location</dt>
                        <dd class="col-sm-8">
                            {{ $complaint->location }}
                            @if ($complaint->has_location)
                                &mdash; <a href="{{ $complaint->map_url }}" target="_blank" rel="noopener">View on map <i class="bi bi-box-arrow-up-right small"></i></a>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $complaint->description }}</dd>

                        <dt class="col-sm-4">Submitted</dt>
                        <dd class="col-sm-8">{{ $complaint->created_at->format('F d, Y g:i A') }}</dd>
                    </dl>
                </div>
            </div>

            @if ($complaint->images->isNotEmpty())
                <div class="card card-ecs mb-4">
                    <div class="card-body">
                        <h2 class="h6 text-uppercase text-muted mb-3">Submitted Photos</h2>
                        <div class="row row-cols-2 row-cols-md-3 g-2">
                            @foreach ($complaint->images as $image)
                                <div class="col">
                                    <a href="{{ $image->url }}" target="_blank" rel="noopener">
                                        <img src="{{ $image->url }}" class="img-fluid rounded-3 img-thumbnail" style="aspect-ratio:1/1; object-fit:cover;" alt="Submitted photo">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="card card-ecs">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Status Timeline</h2>
                    <ul class="list-unstyled mb-0">
                        @foreach ($complaint->updates as $update)
                            <li class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <span class="badge bg-secondary-subtle text-dark border">{{ $update->status_label }}</span>
                                <div class="small text-muted mt-1">
                                    {{ $update->created_at->format('M d, Y g:i A') }} &mdash; {{ $update->admin?->name ?? 'System' }}
                                </div>
                                @if ($update->remarks)
                                    <div class="small mt-1">{{ $update->remarks }}</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-ecs">
                <div class="card-header bg-white fw-semibold py-3">Update This Complaint</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach (\App\Models\Complaint::STATUSES as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $complaint->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Assign to Department</label>
                            <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">&mdash; None &mdash;</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $complaint->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assigned Personnel <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="assigned_to" id="assigned_to" value="{{ old('assigned_to', $complaint->assigned_to) }}"
                                   class="form-control @error('assigned_to') is-invalid @enderror" placeholder="e.g. Juan Dela Cruz">
                            @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="admin_remarks" class="form-label">Remarks / Action Taken</label>
                            <textarea name="admin_remarks" id="admin_remarks" rows="3"
                                      class="form-control @error('admin_remarks') is-invalid @enderror"
                                      placeholder="Describe what was done or why the status changed.">{{ old('admin_remarks', $complaint->admin_remarks) }}</textarea>
                            @error('admin_remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="resolution_photo" class="form-label">Resolution Photo <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="file" name="resolution_photo" id="resolution_photo" accept="image/png, image/jpeg"
                                   class="form-control @error('resolution_photo') is-invalid @enderror">
                            @error('resolution_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if ($complaint->resolution_photo)
                                <div class="form-text">A resolution photo is already on file. Uploading a new one will replace it.</div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-ecs w-100 py-2">
                            <i class="bi bi-check-circle me-1"></i> Save Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
