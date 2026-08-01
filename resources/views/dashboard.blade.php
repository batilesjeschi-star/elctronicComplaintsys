@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 fw-semibold mb-1">Hello, {{ Auth::user()->name }} 👋</h1>
            <p class="text-muted mb-0">Here's a summary of your reports to the barangay.</p>
        </div>
        <a href="{{ route('complaints.create') }}" class="btn btn-ecs">
            <i class="bi bi-plus-circle me-1"></i> Report a Problem
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Total Reports</div>
                    <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Pending</div>
                    <div class="fs-3 fw-bold text-secondary">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">In Progress</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Resolved</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['resolved'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-ecs">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <span class="fw-semibold">Recent Reports</span>
            <a href="{{ route('complaints.index') }}" class="small">View all &rarr;</a>
        </div>
        <div class="card-body p-0">
            @if ($recentComplaints->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    You haven't submitted any reports yet.
                    <div class="mt-3"><a href="{{ route('complaints.create') }}" class="btn btn-ecs btn-sm">Report your first problem</a></div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Reference #</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentComplaints as $complaint)
                                <tr>
                                    <td class="font-monospace small">{{ $complaint->reference_number }}</td>
                                    <td>{{ $complaint->title }}</td>
                                    <td><i class="bi {{ $complaint->category_icon }} me-1"></i>{{ $complaint->category_label }}</td>
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
    </div>

@endsection
