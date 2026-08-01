@extends('layouts.app')

@section('title', 'Complaint '.$complaint->reference_number)

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-2">
        <div>
            <a href="{{ route('complaints.index') }}" class="small text-muted d-inline-block mb-1">
                <i class="bi bi-arrow-left"></i> Back to My Complaints
            </a>
            <h1 class="h3 fw-semibold mb-1">{{ $complaint->title }}</h1>
            <span class="font-monospace text-muted">{{ $complaint->reference_number }}</span>
        </div>
        <div class="d-flex gap-2">
            <span class="badge {{ $complaint->status_badge_class }} fs-6 align-self-start py-2 px-3">{{ $complaint->status_label }}</span>
            <a href="{{ route('complaints.receipt', $complaint) }}" class="btn btn-outline-ecs">
                <i class="bi bi-printer me-1"></i> Print Receipt
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-ecs mb-4">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Report Details</h2>

                    <dl class="row mb-0">
                        <dt class="col-sm-3">Category</dt>
                        <dd class="col-sm-9"><i class="bi {{ $complaint->category_icon }} me-1"></i>{{ $complaint->category_label }}</dd>

                        <dt class="col-sm-3">Location</dt>
                        <dd class="col-sm-9">
                            {{ $complaint->location }}
                            @if ($complaint->has_location)
                                &mdash; <a href="{{ $complaint->map_url }}" target="_blank" rel="noopener">View on map <i class="bi bi-box-arrow-up-right small"></i></a>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Description</dt>
                        <dd class="col-sm-9">{{ $complaint->description }}</dd>

                        <dt class="col-sm-3">Submitted</dt>
                        <dd class="col-sm-9">{{ $complaint->created_at->format('F d, Y g:i A') }}</dd>

                        @if ($complaint->department)
                            <dt class="col-sm-3">Assigned Department</dt>
                            <dd class="col-sm-9">{{ $complaint->department->name }}</dd>
                        @endif

                        @if ($complaint->assigned_to)
                            <dt class="col-sm-3">Assigned To</dt>
                            <dd class="col-sm-9">{{ $complaint->assigned_to }}</dd>
                        @endif

                        @if ($complaint->admin_remarks)
                            <dt class="col-sm-3">Barangay Remarks</dt>
                            <dd class="col-sm-9">{{ $complaint->admin_remarks }}</dd>
                        @endif
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
                                        <img src="{{ $image->url }}" class="img-fluid rounded-3 img-thumbnail" style="aspect-ratio: 1/1; object-fit: cover;" alt="Submitted photo">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($complaint->resolution_photo)
                <div class="card card-ecs mb-4 border-success">
                    <div class="card-body">
                        <h2 class="h6 text-uppercase text-success mb-3"><i class="bi bi-check-circle-fill"></i> Resolution Photo</h2>
                        <a href="{{ $complaint->resolution_photo_url }}" target="_blank" rel="noopener">
                            <img src="{{ $complaint->resolution_photo_url }}" class="img-fluid rounded-3" style="max-height: 320px;" alt="Resolution photo">
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card card-ecs">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-muted mb-3">Status Timeline</h2>
                    @if ($complaint->updates->isEmpty())
                        <p class="text-muted small mb-0">No updates yet.</p>
                    @else
                        <ul class="list-unstyled mb-0">
                            @foreach ($complaint->updates as $update)
                                <li class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <span class="badge bg-secondary-subtle text-dark border">
                                        {{ $update->status_label }}
                                    </span>
                                    <div class="small text-muted mt-1">
                                        {{ $update->created_at->format('M d, Y g:i A') }}
                                        &mdash; {{ $update->admin?->name ?? 'System' }}
                                    </div>
                                    @if ($update->remarks)
                                        <div class="small mt-1">{{ $update->remarks }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
