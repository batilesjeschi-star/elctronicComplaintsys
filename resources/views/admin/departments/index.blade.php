@extends('layouts.admin')

@section('title', 'Departments')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 fw-semibold mb-1">Departments</h1>
            <p class="text-muted mb-0">Teams that complaints can be assigned to for resolution.</p>
        </div>
        <button class="btn btn-ecs" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
            <i class="bi bi-plus-circle me-1"></i> Add Department
        </button>
    </div>

    <div class="card card-ecs">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Assigned Complaints</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                            <tr>
                                <td class="fw-semibold">{{ $department->name }}</td>
                                <td class="text-muted small">{{ $department->description ?: '—' }}</td>
                                <td>{{ $department->complaints_count }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-ecs" data-bs-toggle="modal" data-bs-target="#editDepartmentModal{{ $department->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" class="d-inline"
                                          onsubmit="return confirm('Remove this department? Complaints assigned to it will be unassigned.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-5">No departments yet. Add one to get started.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($departments->hasPages())
            <div class="card-footer bg-white">{{ $departments->links() }}</div>
        @endif
    </div>

    {{-- Edit modals live outside the table (a <div> is not valid directly inside <tbody>) --}}
    @foreach ($departments as $department)
        <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Department</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" value="{{ $department->name }}" class="form-control" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="2" class="form-control">{{ $department->description }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-ecs">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Add department modal --}}
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.departments.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-ecs">Add Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@if ($errors->any())
    @section('scripts')
        <script>
            // Re-open the "add department" modal if that form failed validation.
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('addDepartmentModal')).show();
            });
        </script>
    @endsection
@endif
