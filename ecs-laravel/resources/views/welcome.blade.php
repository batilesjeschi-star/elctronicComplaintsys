@extends('layouts.app')
@section('title', 'Welcome')

@section('content')
    <div class="text-center py-5">
        <i class="bi bi-megaphone-fill text-success" style="font-size:4rem;"></i>
        <h1 class="mt-3">Electronic Complaint System</h1>
        <p class="lead text-muted">Report roads, garbage, drainage, street light, and safety issues in your barangay — track them until they're resolved.</p>

        @guest
            <a href="{{ route('login') }}" class="btn btn-success me-2">Login</a>
            <a href="{{ route('register') }}" class="btn btn-outline-success">Register</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn btn-success">Go to Dashboard</a>
        @endguest
    </div>
@endsection
