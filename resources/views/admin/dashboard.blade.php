@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 fw-semibold mb-1">Barangay Staff Dashboard</h1>
            <p class="text-muted mb-0">Overview of all resident reports across the system.</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-ecs">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> View Full Reports
        </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 mb-4">
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Total</div>
                    <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Pending</div>
                    <div class="fs-3 fw-bold text-secondary">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Under Review</div>
                    <div class="fs-3 fw-bold text-info">{{ $stats['under_review'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">In Progress</div>
                    <div class="fs-3 fw-bold text-primary">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Resolved</div>
                    <div class="fs-3 fw-bold text-success">{{ $stats['resolved'] }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-ecs stat-card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Rejected</div>
                    <div class="fs-3 fw-bold text-danger">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card card-ecs h-100">
                <div class="card-header bg-white fw-semibold py-3">Reports by Category</div>
                <div class="card-body">
                    <canvas id="categoryChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card card-ecs h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-semibold">Recent Complaints</span>
                    <a href="{{ route('admin.complaints.index') }}" class="small">View all &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference #</th>
                                    <th>Resident</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentComplaints as $complaint)
                                    <tr>
                                        <td class="font-monospace small">{{ $complaint->reference_number }}</td>
                                        <td>{{ $complaint->user->name }}</td>
                                        <td>{{ $complaint->category_label }}</td>
                                        <td><span class="badge {{ $complaint->status_badge_class }}">{{ $complaint->status_label }}</span></td>
                                        <td><a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-ecs">View</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No complaints yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const categoryLabels = @json(collect(\App\Models\Complaint::CATEGORIES));
    const byCategory = @json($byCategory);

    const labels = Object.keys(categoryLabels).map(key => categoryLabels[key]);
    const data = Object.keys(categoryLabels).map(key => byCategory[key] ?? 0);

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#1B4D3E', '#C98A2E', '#2F6F5E', '#8FBBA9', '#E0C088', '#5B6B65'],
                borderWidth: 2,
                borderColor: '#ffffff',
            }],
        },
        options: {
            plugins: { legend: { position: 'bottom' } },
        },
    });
</script>
@endsection
