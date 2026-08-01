@extends('layouts.admin')

@section('title', 'Reports')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 no-print">
        <div>
            <h1 class="h3 fw-semibold mb-1">Reports</h1>
            <p class="text-muted mb-0">
                Showing complaints from <strong>{{ $from->format('M d, Y') }}</strong> to <strong>{{ $to->format('M d, Y') }}</strong>.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-ecs"><i class="bi bi-printer me-1"></i> Print</button>
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn btn-ecs">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>
    </div>

    <div class="card card-ecs mb-4 no-print">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Period</label>
                    <select name="period" id="period" class="form-select" onchange="document.getElementById('customRange').classList.toggle('d-none', this.value !== 'custom')">
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div id="customRange" class="col-md-6 d-flex gap-2 {{ $period === 'custom' ? '' : 'd-none' }}">
                    <div class="flex-fill">
                        <label class="form-label small text-muted mb-1">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>
                    <div class="flex-fill">
                        <label class="form-label small text-muted mb-1">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-ecs"><i class="bi bi-funnel"></i> Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 mb-4">
        <div class="col">
            <div class="card card-ecs stat-card h-100"><div class="card-body"><div class="text-muted small text-uppercase mb-1">Total</div><div class="fs-4 fw-bold">{{ $summary['total'] }}</div></div></div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100"><div class="card-body"><div class="text-muted small text-uppercase mb-1">Pending</div><div class="fs-4 fw-bold text-secondary">{{ $summary['pending'] }}</div></div></div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100"><div class="card-body"><div class="text-muted small text-uppercase mb-1">Under Review</div><div class="fs-4 fw-bold text-info">{{ $summary['under_review'] }}</div></div></div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100"><div class="card-body"><div class="text-muted small text-uppercase mb-1">In Progress</div><div class="fs-4 fw-bold text-primary">{{ $summary['in_progress'] }}</div></div></div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100"><div class="card-body"><div class="text-muted small text-uppercase mb-1">Resolved</div><div class="fs-4 fw-bold text-success">{{ $summary['resolved'] }}</div></div></div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100"><div class="card-body"><div class="text-muted small text-uppercase mb-1">Rejected</div><div class="fs-4 fw-bold text-danger">{{ $summary['rejected'] }}</div></div></div>
        </div>
    </div>

    <div class="card card-ecs mb-4">
        <div class="card-header bg-white fw-semibold py-3">Breakdown by Category</div>
        <div class="card-body">
            @if ($byCategory->isEmpty())
                <p class="text-muted small mb-0">No complaints in this period.</p>
            @else
                @foreach (\App\Models\Complaint::CATEGORIES as $value => $label)
                    @php($count = $byCategory[$value] ?? 0)
                    @if ($count > 0)
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $label }}</span>
                            <span class="text-muted">{{ $count }}</span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;" role="progressbar" aria-valuenow="{{ $count }}" aria-valuemin="0" aria-valuemax="{{ $summary['total'] }}">
                            <div class="progress-bar" style="width: {{ $summary['total'] ? ($count / $summary['total']) * 100 : 0 }}%; background-color: var(--ecs-primary);"></div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>

    <div class="card card-ecs">
        <div class="card-header bg-white fw-semibold py-3">Complaint List</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference #</th>
                            <th>Resident</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Resolved</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($complaints as $complaint)
                            <tr>
                                <td class="font-monospace small">{{ $complaint->reference_number }}</td>
                                <td>{{ $complaint->user->name ?? 'N/A' }}</td>
                                <td>{{ $complaint->category_label }}</td>
                                <td><span class="badge {{ $complaint->status_badge_class }}">{{ $complaint->status_label }}</span></td>
                                <td class="small text-muted">{{ $complaint->created_at->format('M d, Y') }}</td>
                                <td class="small text-muted">{{ $complaint->resolved_at?->format('M d, Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No complaints in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
