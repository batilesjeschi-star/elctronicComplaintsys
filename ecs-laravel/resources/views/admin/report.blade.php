@extends('layouts.app')
@section('title', 'Reports')

@section('content')
    <h4 class="mb-3">Complaint Reports</h4>

    <form method="GET" class="mb-3 no-print">
        <div class="btn-group">
            <a href="{{ route('admin.reports', ['period' => 'daily']) }}" class="btn btn-outline-success @if($period=='daily') active @endif">Daily</a>
            <a href="{{ route('admin.reports', ['period' => 'weekly']) }}" class="btn btn-outline-success @if($period=='weekly') active @endif">Weekly</a>
            <a href="{{ route('admin.reports', ['period' => 'monthly']) }}" class="btn btn-outline-success @if($period=='monthly') active @endif">Monthly</a>
        </div>
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary ms-2"><i class="bi bi-printer"></i> Print Report</button>
    </form>

    <div class="row g-3 mb-3">
        @foreach ($summary as $status => $count)
            <div class="col-6 col-md-2">
                <div class="card p-3 text-center">
                    <div class="fs-4 fw-bold">{{ $count }}</div>
                    <div class="text-muted small">{{ $status }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>Ref #</th><th>Title</th><th>Category</th><th>Resident</th><th>Status</th><th>Date Filed</th></tr></thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->reference_number }}</td>
                            <td>{{ $complaint->title }}</td>
                            <td>{{ $complaint->category }}</td>
                            <td>{{ $complaint->user->name }}</td>
                            <td><span class="badge {{ $complaint->statusBadgeClass() }}">{{ $complaint->status }}</span></td>
                            <td>{{ $complaint->created_at->format('M d, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No complaints in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
