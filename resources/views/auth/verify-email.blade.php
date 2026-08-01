@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
    <h1 class="h4 fw-semibold mb-1">Verify your email</h1>
    <p class="text-muted mb-4">
        Thanks for signing up! Please verify your email address by clicking the link we just emailed you.
        If you didn't receive it, we can send another one.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-ecs">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Log Out</button>
        </form>
    </div>
@endsection
