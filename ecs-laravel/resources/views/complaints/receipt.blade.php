@extends('layouts.app')
@section('title', 'Complaint Receipt')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 text-center">
                <i class="bi bi-check2-circle text-success" style="font-size: 3rem;"></i>
                <h4 class="mt-2">Complaint Receipt</h4>
                <p class="text-muted">Please keep this reference number for tracking your complaint.</p>

                <h2 class="fw-bold my-3">{{ $complaint->reference_number }}</h2>

                <table class="table table-sm text-start">
                    <tr><th>Title</th><td>{{ $complaint->title }}</td></tr>
                    <tr><th>Category</th><td>{{ $complaint->category }}</td></tr>
                    <tr><th>Location</th><td>{{ $complaint->location }}</td></tr>
                    <tr><th>Filed By</th><td>{{ $complaint->user->name }}</td></tr>
                    <tr><th>Date Filed</th><td>{{ $complaint->created_at->format('F d, Y g:i A') }}</td></tr>
                    <tr><th>Status</th><td><span class="badge {{ $complaint->statusBadgeClass() }}">{{ $complaint->status }}</span></td></tr>
                </table>

                <button onclick="window.print()" class="btn btn-success no-print">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
@endsection
