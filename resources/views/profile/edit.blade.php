@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')

    <h1 class="h3 fw-semibold mb-4">Profile Settings</h1>

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Update name / email --}}
            <div class="card card-ecs mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Profile Information</h2>

                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success">Profile updated successfully.</div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-ecs">Save Changes</button>
                    </form>
                </div>
            </div>

            {{-- Update password --}}
            <div class="card card-ecs mb-4">
                <div class="card-body p-4">
                    <h2 class="h6 text-uppercase text-muted mb-3">Update Password</h2>

                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success">Password updated successfully.</div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
                            @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
                            @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control" autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-ecs">Update Password</button>
                    </form>
                </div>
            </div>

            {{-- Delete account --}}
            <div class="card border-danger">
                <div class="card-body p-4">
                    <h2 class="h6 text-uppercase text-danger mb-2">Delete Account</h2>
                    <p class="text-muted small">Once your account is deleted, all of its data will be permanently removed. This cannot be undone.</p>

                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        Delete Account
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Account Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Please enter your password to confirm you want to permanently delete your account.</p>
                        <input type="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="Password" autocomplete="current-password">
                        @error('password', 'userDeletion')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete My Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@if ($errors->userDeletion->isNotEmpty())
    @section('scripts')
        <script>
            // If the "delete account" form failed validation, re-open its modal
            // so the error message (wrong password) is actually visible.
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
            });
        </script>
    @endsection
@endif
