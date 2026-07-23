@extends('layouts.app')
@section('title', 'All Complaints')

@section('content')
    <h4 class="mb-3">All Complaints</h4>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title, ref #, location">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') == $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') == $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" title="From date">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" title="To date">
        </div>
        <div class="col-md-1">
            <button class="btn btn-success w-100"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ref #</th><th>Title</th><th>Category</th><th>Resident</th><th>Status</th><th>Date Filed</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td>{{ $complaint->reference_number }}</td>
                            <td>{{ $complaint->title }}</td>
                            <td>{{ $complaint->category }}</td>
                            <td>{{ $complaint->user->name }}</td>
                            <td><span class="badge {{ $complaint->statusBadgeClass() }}">{{ $complaint->status }}</span></td>
                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                            <td><a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-success">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No complaints found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $complaints->links() }}</div>
@endsection
