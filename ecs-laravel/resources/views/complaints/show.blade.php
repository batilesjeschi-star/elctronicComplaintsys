@extends('layouts.app')
@section('title', 'Complaint Details')

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-0">{{ $complaint->title }}</h4>
            <small class="text-muted">Reference #: <strong>{{ $complaint->reference_number }}</strong></small>
        </div>
        <div class="text-end">
            <span class="badge fs-6 {{ $complaint->statusBadgeClass() }}">{{ $complaint->status }}</span>
            <div class="mt-2 no-print">
                <a href="{{ route('complaints.receipt', $complaint) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-printer"></i> Print / Download Receipt
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card p-3 mb-3">
                <h6 class="text-muted">Description</h6>
                <p>{{ $complaint->description }}</p>

                <h6 class="text-muted">Category</h6>
                <p>{{ $complaint->category }}</p>

                <h6 class="text-muted">Location</h6>
                <p>{{ $complaint->location }}
                    @if ($complaint->latitude && $complaint->longitude)
                        <br><small class="text-muted">GPS: {{ $complaint->latitude }}, {{ $complaint->longitude }}</small>
                    @endif
                </p>

                @if ($complaint->admin_remarks)
                    <h6 class="text-muted">Admin Remarks</h6>
                    <p>{{ $complaint->admin_remarks }}</p>
                @endif

                @if ($complaint->department || $complaint->assigned_to)
                    <h6 class="text-muted">Assigned To</h6>
                    <p>{{ $complaint->assigned_to }} {{ $complaint->department ? '('.$complaint->department->name.')' : '' }}</p>
                @endif
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

            @if ($complaint->resolution_photo)
                <div class="card p-3 mb-3">
                    <h6 class="text-muted">Resolution Photo</h6>
                    <a href="{{ asset('storage/'.$complaint->resolution_photo) }}" target="_blank">
                        <img src="{{ asset('storage/'.$complaint->resolution_photo) }}" class="rounded border" style="width:200px;height:200px;object-fit:cover;">
                    </a>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card p-3">
                <h6 class="text-muted">Status History</h6>
                <ul class="list-unstyled mb-0">
                    @foreach ($complaint->updates as $update)
                        <li class="mb-3 border-start border-3 ps-2 border-success">
                            <span class="badge {{ (new \App\Models\Complaint(['status' => $update->status]))->statusBadgeClass() }}">{{ $update->status }}</span>
                            <div class="small text-muted">{{ $update->created_at->format('M d, Y g:i A') }}</div>
                            @if ($update->remarks)
                                <div class="small">{{ $update->remarks }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
