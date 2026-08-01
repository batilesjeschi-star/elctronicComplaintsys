<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Electronic Complaint System')</title>

    <!-- Bootstrap 5 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .navbar-brand { font-weight: 700; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .badge { font-weight: 500; }
        footer { color: #6c757d; font-size: .875rem; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success no-print">
    <div class="container">
        <a class="navbar-brand" href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('dashboard') }}">
            <i class="bi bi-megaphone-fill"></i> ECS <small class="fw-normal">Barangay</small>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            @auth
                <ul class="navbar-nav me-auto ms-3">
                    @if (auth()->user()->is_admin)
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.complaints.index') }}">All Complaints</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.reports') }}">Reports</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('complaints.index') }}">My Complaints</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('complaints.create') }}">File a Complaint</a></li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i> {{ auth()->user()->name }}</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-light btn-sm ms-2" type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>

<div class="container my-4">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<footer class="text-center py-4 no-print">
    Electronic Complaint System &copy; {{ date('Y') }} — Barangay Capstone Project
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
