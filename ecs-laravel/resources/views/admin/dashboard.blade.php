@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
    <h4 class="mb-3">Admin Dashboard</h4>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card p-3 text-center">
                <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                <div class="text-muted small">Total</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card p-3 text-center">
                <div class="fs-4 fw-bold text-secondary">{{ $stats['pending'] }}</div>
                <div class="text-muted small">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card p-3 text-center">
                <div class="fs-4 fw-bold text-info">{{ $stats['under_review'] }}</div>
                <div class="text-muted small">Under Review</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card p-3 text-center">
                <div class="fs-4 fw-bold text-warning">{{ $stats['in_progress'] }}</div>
                <div class="text-muted small">In Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card p-3 text-center">
                <div class="fs-4 fw-bold text-success">{{ $stats['resolved'] }}</div>
                <div class="text-muted small">Resolved</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card p-3 text-center">
                <div class="fs-4 fw-bold text-danger">{{ $stats['rejected'] }}</div>
                <div class="text-muted small">Rejected</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card p-3">
                <h6 class="text-muted">Complaints by Category</h6>
                <ul class="list-group list-group-flush">
                    @foreach ($byCategory as $category => $count)
                        <li class="list-group-item d-flex justify-content-between">
                            {{ $category }} <span class="badge bg-success rounded-pill">{{ $count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted mb-0">Recent Complaints</h6>
                    <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Ref #</th><th>Title</th><th>Resident</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($recent as $complaint)
                                <tr>
                                    <td>{{ $complaint->reference_number }}</td>
                                    <td>{{ $complaint->title }}</td>
                                    <td>{{ $complaint->user->name }}</td>
                                    <td><span class="badge {{ $complaint->statusBadgeClass() }}">{{ $complaint->status }}</span></td>
                                    <td><a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-success">Open</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
