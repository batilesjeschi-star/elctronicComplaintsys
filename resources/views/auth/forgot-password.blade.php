@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
    <h1 class="h4 fw-semibold mb-1">Forgot your password?</h1>
    <p class="text-muted mb-4">Enter your email and we'll send you a link to reset it.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-ecs w-100 py-2">Email Password Reset Link</button>

        <p class="text-center text-muted small mt-4 mb-0">
            <a href="{{ route('login') }}"><i class="bi bi-arrow-left"></i> Back to login</a>
        </p>
    </form>
@endsection
