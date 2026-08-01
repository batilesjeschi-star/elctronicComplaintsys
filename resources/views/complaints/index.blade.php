@extends('layouts.app')

@section('title', 'My Complaints')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="h3 fw-semibold mb-0">My Complaints</h1>
        <a href="{{ route('complaints.create') }}" class="btn btn-ecs">
            <i class="bi bi-plus-circle me-1"></i> Report a Problem
        </a>
    </div>

    <div class="card card-ecs mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('complaints.index') }}" class="row g-2 align-items-end">
                <div class="col-md-7">
                    <label class="form-label small text-muted mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                           placeholder="Search by title, reference number, or location">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach (\App\Models\Complaint::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-ecs"><i class="bi bi-search"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-ecs">
        <div class="card-body p-0">
            @if ($complaints->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    No complaints match your search.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Reference #</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($complaints as $complaint)
                                <tr>
                                    <td class="font-monospace small">{{ $complaint->reference_number }}</td>
                                    <td>{{ $complaint->title }}</td>
                                    <td><i class="bi {{ $complaint->category_icon }} me-1"></i>{{ $complaint->category_label }}</td>
                                    <td class="small">{{ $complaint->location }}</td>
                                    <td><span class="badge {{ $complaint->status_badge_class }}">{{ $complaint->status_label }}</span></td>
                                    <td class="small text-muted">{{ $complaint->created_at->format('M d, Y') }}</td>
                                    <td><a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-ecs">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($complaints->hasPages())
            <div class="card-footer bg-white">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>

@endsection
