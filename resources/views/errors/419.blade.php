@extends('layouts.app')

@section('title', '419 Session Expired — Fan Hub Plus')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="fh-card fh-card-glow p-5">
                <div class="fh-logo-icon mx-auto mb-4" style="width: 64px; height: 64px; font-size: 2rem; background: linear-gradient(135deg, var(--fh-accent-amber), var(--fh-accent-pink));">
                    <i class="bi bi-hourglass-bottom"></i>
                </div>
                <h1 class="display-5 fw-bold text-light mb-2">419 Session Expired</h1>
                <h5 class="text-warning mb-3">Security Token Out of Sync</h5>
                <p class="text-muted mb-4">
                    Your security session has timed out due to inactivity. Please refresh the page and re-submit your form.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ url()->previous() }}" class="fh-btn-primary">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Page
                    </a>
                    <a href="{{ route('home') }}" class="fh-btn-secondary">
                        Universe Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
