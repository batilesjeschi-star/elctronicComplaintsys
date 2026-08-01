@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('content')
    <h1 class="h4 fw-semibold mb-1">Confirm your password</h1>
    <p class="text-muted mb-4">This is a secure area. Please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autofocus autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-ecs w-100 py-2">Confirm</button>
    </form>
@endsection
