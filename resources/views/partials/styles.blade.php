{{-- Shared <head> assets + design tokens, included by every layout. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --ecs-primary: #1B4D3E;
        --ecs-primary-dark: #0F2E24;
        --ecs-primary-tint: #E7F0EC;
        --ecs-accent: #C98A2E;
        --ecs-accent-tint: #F7ECD9;
        --ecs-bg: #F7F5F1;
        --ecs-ink: #22302C;
        --ecs-muted: #5B6B65;
    }

    body {
        background-color: var(--ecs-bg);
        color: var(--ecs-ink);
        font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .font-display { font-family: 'Fraunces', Georgia, serif; }

    a { color: var(--ecs-primary); }
    a:hover { color: var(--ecs-primary-dark); }

    /* Brand navbar (resident-facing pages) */
    .navbar-ecs { background-color: var(--ecs-primary-dark); }
    .navbar-ecs .navbar-brand,
    .navbar-ecs .nav-link { color: #EDEBE2 !important; }
    .navbar-ecs .nav-link:hover,
    .navbar-ecs .nav-link.active { color: #ffffff !important; font-weight: 600; }
    .navbar-ecs .navbar-toggler { border-color: rgba(255,255,255,.35); }

    /* Brand buttons, used for the primary call to action on a page */
    .btn-ecs {
        background-color: var(--ecs-primary);
        border-color: var(--ecs-primary);
        color: #fff;
    }
    .btn-ecs:hover, .btn-ecs:focus {
        background-color: var(--ecs-primary-dark);
        border-color: var(--ecs-primary-dark);
        color: #fff;
    }
    .btn-outline-ecs {
        border-color: var(--ecs-primary);
        color: var(--ecs-primary);
    }
    .btn-outline-ecs:hover {
        background-color: var(--ecs-primary);
        color: #fff;
    }

    .text-ecs { color: var(--ecs-primary); }
    .bg-ecs-tint { background-color: var(--ecs-primary-tint); }
    .bg-ecs-accent-tint { background-color: var(--ecs-accent-tint); }

    .card-ecs {
        border: none;
        border-radius: .75rem;
        box-shadow: 0 1px 3px rgba(15, 46, 36, .1), 0 1px 2px rgba(15, 46, 36, .06);
    }

    /* Admin sidebar */
    .sidebar-ecs {
        background-color: var(--ecs-primary-dark);
        min-height: 100vh;
    }
    .sidebar-ecs .nav-link {
        color: #CFE0D8;
        border-radius: .5rem;
        padding: .55rem .9rem;
        margin-bottom: .15rem;
    }
    .sidebar-ecs .nav-link:hover { background-color: rgba(255,255,255,.08); color: #fff; }
    .sidebar-ecs .nav-link.active { background-color: var(--ecs-accent); color: #21170a; font-weight: 600; }
    .sidebar-ecs .sidebar-brand { color: #fff; }
    .sidebar-ecs hr { border-color: rgba(255,255,255,.15); }

    .stat-card { border-radius: .75rem; border: none; }

    /* Focus states stay visible for keyboard users */
    a:focus-visible, button:focus-visible, .btn:focus-visible,
    input:focus-visible, select:focus-visible, textarea:focus-visible {
        outline: 3px solid var(--ecs-accent);
        outline-offset: 2px;
    }

    @media print {
        .no-print { display: none !important; }
    }
</style>
