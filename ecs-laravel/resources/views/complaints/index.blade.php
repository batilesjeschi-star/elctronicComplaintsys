@extends('layouts.app')
@section('title', 'My Complaints')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Complaints</h4>
        <a href="{{ route('complaints.create') }}" class="btn btn-success btn-sm"><i class="bi bi-plus-circle"></i> File a New Complaint</a>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by title, reference #, or location">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach (\App\Models\Complaint::STATUSES as $status)
                    <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-success w-100"><i class="bi bi-search"></i> Filter</button>
        </div>
    </form>

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
                        <tr><td colspan="6" class="text-center text-muted py-4">No complaints found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $complaints->links() }}</div>
@endsection
