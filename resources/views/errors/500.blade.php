@extends('layouts.app')

@section('title', '500 Server Anomaly — Fan Hub Plus')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="fh-card fh-card-glow p-5">
                <div class="fh-logo-icon mx-auto mb-4" style="width: 64px; height: 64px; font-size: 2rem; background: linear-gradient(135deg, var(--fh-accent-crimson), var(--fh-accent-purple));">
                    <i class="bi bi-cpu"></i>
                </div>
                <h1 class="display-5 fw-bold text-light mb-2">500 Server Anomaly</h1>
                <h5 class="text-danger mb-3">Internal System Disturbance</h5>
                <p class="text-muted mb-4">
                    Our platform nexus encountered an unexpected glitch. System logs have been dispatched to the platform administrators.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="fh-btn-primary">
                        <i class="bi bi-house me-1"></i> Return to Safety
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
