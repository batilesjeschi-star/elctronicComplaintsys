<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Barangay ECS') }}</title>
    @include('partials.styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-ecs no-print" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <i class="bi bi-megaphone-fill"></i>
                <span>Barangay ECS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('complaints.index') ? 'active' : '' }}" href="{{ route('complaints.index') }}">My Complaints</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('complaints.create') ? 'active' : '' }}" href="{{ route('complaints.create') }}">Report a Problem</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Profile Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @include('partials.flash-messages')

        @if (isset($header))
            <div class="mb-4">{{ $header }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="no-print text-center text-muted small py-4">
        &copy; {{ date('Y') }} {{ config('app.name', 'Barangay ECS') }} &mdash; Electronic Complaint System
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
