@extends('layouts.admin')

@section('title', 'All Complaints')

@section('content')

    <div class="mb-4">
        <h1 class="h3 fw-semibold mb-1">All Complaints</h1>
        <p class="text-muted mb-0">{{ $complaints->total() }} total complaint(s) match the current filters.</p>
    </div>

    <div class="card card-ecs mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.complaints.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                           placeholder="Title, reference #, or location">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All</option>
                        @foreach (\App\Models\Complaint::CATEGORIES as $value => $label)
                            <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach (\App\Models\Complaint::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-ecs"><i class="bi bi-search"></i> Filter</button>
                    <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-ecs">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference #</th>
                            <th>Resident</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($complaints as $complaint)
                            <tr>
                                <td class="font-monospace small">{{ $complaint->reference_number }}</td>
                                <td>{{ $complaint->user->name }}</td>
                                <td>{{ $complaint->title }}</td>
                                <td><i class="bi {{ $complaint->category_icon }} me-1"></i>{{ $complaint->category_label }}</td>
                                <td><span class="badge {{ $complaint->status_badge_class }}">{{ $complaint->status_label }}</span></td>
                                <td class="small text-muted">{{ $complaint->created_at->format('M d, Y') }}</td>
                                <td><a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-ecs">Review</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-5">No complaints match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($complaints->hasPages())
            <div class="card-footer bg-white">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>

@endsection
