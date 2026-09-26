@extends('layouts.app')

@section('title', '404 Page Not Found — Fan Hub Plus')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="fh-card fh-card-glow p-5">
                <div class="fh-logo-icon mx-auto mb-4" style="width: 64px; height: 64px; font-size: 2rem; background: linear-gradient(135deg, var(--fh-primary), var(--fh-accent-cyan));">
                    <i class="bi bi-compass"></i>
                </div>
                <h1 class="display-5 fw-bold text-light mb-2">404 Lost in Space</h1>
                <h5 class="text-info mb-3">Sector Not Found</h5>
                <p class="text-muted mb-4">
                    The fandom coordinate or lore page you are attempting to access does not exist or has shifted timelines.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="fh-btn-secondary">
                        <i class="bi bi-house me-1"></i> Return to Base
                    </a>
                    <a href="{{ route('explore') }}" class="fh-btn-primary">
                        <i class="bi bi-compass me-1"></i> Explore Lore
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
