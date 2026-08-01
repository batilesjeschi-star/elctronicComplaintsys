<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') - {{ config('app.name', 'Barangay ECS') }}</title>
    @include('partials.styles')
</head>
<body class="d-flex align-items-center" style="min-height: 100vh;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-9 col-md-7 col-lg-5">

                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:56px;height:56px;background-color: var(--ecs-primary);">
                            <i class="bi bi-megaphone-fill fs-4 text-white"></i>
                        </div>
                        <div class="font-display fs-4 fw-semibold text-ecs">Barangay ECS</div>
                        <div class="text-muted small">Electronic Complaint System</div>
                    </a>
                </div>

                <div class="card card-ecs">
                    <div class="card-body p-4 p-md-5">
                        @include('partials.flash-messages')
                        @yield('content')
                    </div>
                </div>

                <p class="text-center text-muted small mt-3 mb-0">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Barangay ECS') }}
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
