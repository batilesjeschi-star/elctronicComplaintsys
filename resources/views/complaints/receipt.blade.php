@extends('layouts.app')

@section('title', 'Receipt - '.$complaint->reference_number)

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('complaints.show', $complaint) }}" class="small text-muted">
            <i class="bi bi-arrow-left"></i> Back to Complaint
        </a>
        <button onclick="window.print()" class="btn btn-ecs">
            <i class="bi bi-printer me-1"></i> Print / Save as PDF
        </button>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-ecs border-2" style="border-color: var(--ecs-primary) !important;">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <i class="bi bi-megaphone-fill fs-2 text-ecs"></i>
                        <h1 class="font-display h4 fw-semibold mt-2 mb-0">{{ config('app.name', 'Barangay ECS') }}</h1>
                        <div class="text-muted small">Electronic Complaint System &mdash; Official Receipt</div>
                    </div>

                    <hr>

                    <div class="text-center my-4">
                        <div class="text-muted small text-uppercase" style="letter-spacing:.08em;">Reference Number</div>
                        <div class="font-monospace fw-bold fs-3 text-ecs">{{ $complaint->reference_number }}</div>
                        <span class="badge {{ $complaint->status_badge_class }} mt-2">{{ $complaint->status_label }}</span>
                    </div>

                    <dl class="row">
                        <dt class="col-sm-4">Filed By</dt>
                        <dd class="col-sm-8">{{ $complaint->user->name }}</dd>

                        <dt class="col-sm-4">Title</dt>
                        <dd class="col-sm-8">{{ $complaint->title }}</dd>

                        <dt class="col-sm-4">Category</dt>
                        <dd class="col-sm-8">{{ $complaint->category_label }}</dd>

                        <dt class="col-sm-4">Location</dt>
                        <dd class="col-sm-8">{{ $complaint->location }}</dd>

                        <dt class="col-sm-4">Date Filed</dt>
                        <dd class="col-sm-8">{{ $complaint->created_at->format('F d, Y g:i A') }}</dd>
                    </dl>

                    <hr>

                    <p class="text-muted small text-center mb-0">
                        Please keep this reference number for follow-up. You may check the status of this report
                        anytime by logging in to {{ config('app.name', 'Barangay ECS') }}.
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection
