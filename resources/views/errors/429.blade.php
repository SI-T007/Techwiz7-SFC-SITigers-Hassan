@extends('layouts.app')

@section('title', '429 Rate Limit Exceeded — Fan Hub Plus')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="fh-card fh-card-glow p-5">
                <div class="fh-logo-icon mx-auto mb-4" style="width: 64px; height: 64px; font-size: 2rem; background: linear-gradient(135deg, var(--fh-accent-amber), var(--fh-accent-crimson));">
                    <i class="bi bi-speedometer"></i>
                </div>
                <h1 class="display-5 fw-bold text-light mb-2">429 Slow Down</h1>
                <h5 class="text-warning mb-3">Rate Limit Exceeded</h5>
                <p class="text-muted mb-4">
                    Too many portal requests received in a brief window. Please pause for a moment before retrying.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="fh-btn-primary">
                        <i class="bi bi-house me-1"></i> Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
