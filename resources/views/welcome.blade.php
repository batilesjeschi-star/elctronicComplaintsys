<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Barangay ECS') }} - Electronic Complaint System</title>
    @include('partials.styles')
    <style>
        .stamp-badge {
            border: 3px dashed var(--ecs-accent);
            border-radius: 1rem;
            padding: 1rem 1.75rem;
            display: inline-block;
            transform: rotate(-4deg);
            background: #fff;
        }
        .hero-shape {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 15%, var(--ecs-primary-tint) 0%, transparent 55%);
            z-index: 0;
        }
        .step-number {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--ecs-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold text-ecs" href="{{ route('home') }}">
                <i class="bi bi-megaphone-fill"></i> Barangay ECS
            </a>
            <div class="d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-ecs">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-ecs">Log In</a>
                    <a href="{{ route('register') }}" class="btn btn-ecs">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <header class="position-relative overflow-hidden">
        <div class="hero-shape"></div>
        <div class="container position-relative py-5">
            <div class="row align-items-center gy-5 py-4">
                <div class="col-lg-7">
                    <span class="badge bg-ecs-accent-tint text-dark px-3 py-2 mb-3 fw-normal">
                        For residents of the barangay
                    </span>
                    <h1 class="font-display fw-semibold display-5 mb-3">
                        Report it. Track it. Get it resolved.
                    </h1>
                    <p class="fs-5 text-muted mb-4" style="max-width: 42rem;">
                        Potholes, broken streetlights, overflowing garbage, flooding, drainage, safety concerns &mdash;
                        tell the barangay directly and follow every update online, from submission to resolution.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @auth
                            <a href="{{ route('complaints.create') }}" class="btn btn-ecs btn-lg px-4">
                                <i class="bi bi-megaphone me-1"></i> Report a Problem
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-ecs btn-lg px-4">Get Started</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-ecs btn-lg px-4">I already have an account</a>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-5 text-center">
                    <div class="stamp-badge">
                        <div class="small text-uppercase text-muted" style="letter-spacing: .08em;">Official Reference No.</div>
                        <div class="font-monospace fw-bold fs-4 text-ecs">ECS-{{ now()->format('Ymd') }}-4F2K9</div>
                        <div class="small text-muted"><i class="bi bi-check-circle text-success"></i> Every report is tracked</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-4">
            <h2 class="font-display fw-semibold text-center mb-5">How it works</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <div class="step-number">1</div>
                        <div>
                            <h3 class="h5 fw-semibold">Report</h3>
                            <p class="text-muted mb-0">Describe the problem, add its location, and attach a few photos. Optionally share your GPS pin.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <div class="step-number">2</div>
                        <div>
                            <h3 class="h5 fw-semibold">Track</h3>
                            <p class="text-muted mb-0">Get an official reference number instantly, and follow every status change from your dashboard.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-3">
                        <div class="step-number">3</div>
                        <div>
                            <h3 class="h5 fw-semibold">Resolve</h3>
                            <p class="text-muted mb-0">Barangay staff review, act, and mark it resolved &mdash; you'll be notified by email at every step.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-3">
            <h2 class="font-display fw-semibold text-center mb-5">What you can report</h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 text-center">
                <div class="col">
                    <div class="card card-ecs h-100 p-3"><i class="bi bi-cone-striped fs-2 text-ecs mb-2"></i><div class="small fw-semibold">Roads &amp; Potholes</div></div>
                </div>
                <div class="col">
                    <div class="card card-ecs h-100 p-3"><i class="bi bi-trash3 fs-2 text-ecs mb-2"></i><div class="small fw-semibold">Garbage Collection</div></div>
                </div>
                <div class="col">
                    <div class="card card-ecs h-100 p-3"><i class="bi bi-water fs-2 text-ecs mb-2"></i><div class="small fw-semibold">Drainage &amp; Flooding</div></div>
                </div>
                <div class="col">
                    <div class="card card-ecs h-100 p-3"><i class="bi bi-lightbulb fs-2 text-ecs mb-2"></i><div class="small fw-semibold">Street Lights</div></div>
                </div>
                <div class="col">
                    <div class="card card-ecs h-100 p-3"><i class="bi bi-shield-exclamation fs-2 text-ecs mb-2"></i><div class="small fw-semibold">Public Safety</div></div>
                </div>
                <div class="col">
                    <div class="card card-ecs h-100 p-3"><i class="bi bi-three-dots fs-2 text-ecs mb-2"></i><div class="small fw-semibold">Others</div></div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center text-muted small py-4 border-top">
        &copy; {{ date('Y') }} {{ config('app.name', 'Barangay ECS') }} &mdash; Electronic Complaint System
    </footer>

</body>
</html>
