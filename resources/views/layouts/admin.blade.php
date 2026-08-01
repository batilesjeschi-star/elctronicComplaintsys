<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name', 'Barangay ECS') }} Admin</title>
    @include('partials.styles')
</head>
<body>

<div class="d-flex">

    {{-- Sidebar: static column on large screens, offcanvas drawer on mobile --}}
    <nav id="adminSidebar" class="offcanvas-lg offcanvas-start sidebar-ecs text-white" tabindex="-1" style="width: 250px;" aria-label="Admin navigation">
        <div class="offcanvas-header d-lg-none">
            <span class="sidebar-brand fw-semibold">Barangay ECS</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-3">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand d-none d-lg-flex align-items-center gap-2 text-decoration-none mb-4 fs-5 fw-semibold">
                <i class="bi bi-megaphone-fill"></i> Barangay ECS
            </a>

            <ul class="nav nav-pills flex-column mb-auto gap-1">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.complaints.index') }}" class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard2-data me-2"></i> Complaints
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3 me-2"></i> Departments
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
                    </a>
                </li>
            </ul>

            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <strong>{{ Auth::user()->name }}</strong>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Profile Settings</a></li>
                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-house me-2"></i>Visit Public Site</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Log Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- Main content --}}
    <div class="flex-grow-1" style="min-width: 0;">
        <div class="d-lg-none d-flex align-items-center justify-content-between p-3 border-bottom bg-white no-print">
            <button class="btn btn-outline-ecs" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                <i class="bi bi-list fs-5"></i> Menu
            </button>
            <span class="fw-semibold text-ecs"><i class="bi bi-megaphone-fill me-1"></i> Barangay ECS</span>
        </div>

        <main class="container-fluid p-3 p-md-4">
            @include('partials.flash-messages')

            @if (isset($header))
                <div class="mb-4">{{ $header }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
