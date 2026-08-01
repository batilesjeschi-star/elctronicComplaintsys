@extends('layouts.app')
@section('title', 'My Dashboard')

@section('content')
    <h3 class="mb-3">Welcome, {{ auth()->user()->name }} 👋</h3>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                <div class="text-muted">Total Complaints</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="fs-3 fw-bold text-secondary">{{ $stats['pending'] }}</div>
                <div class="text-muted">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="fs-3 fw-bold text-warning">{{ $stats['in_progress'] }}</div>
                <div class="text-muted">In Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card p-3 text-center">
                <div class="fs-3 fw-bold text-success">{{ $stats['resolved'] }}</div>
                <div class="text-muted">Resolved</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Recent Complaints</h5>
        <a href="{{ route('complaints.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-circle"></i> File a New Complaint</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Reference #</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Filed On</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->reference_number }}</td>
                            <td>{{ $complaint->title }}</td>
                            <td>{{ $complaint->category }}</td>
                            <td><span class="badge {{ $complaint->statusBadgeClass() }}">{{ $complaint->status }}</span></td>
                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                            <td><a href="{{ route('complaints.show', $complaint) }}" class="btn btn-sm btn-outline-success">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">You haven't filed any complaints yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('complaints.index') }}">View all my complaints &rarr;</a>
    </div>
@endsection
