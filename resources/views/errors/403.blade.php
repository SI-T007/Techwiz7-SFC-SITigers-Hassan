@extends('layouts.app')

@section('title', '403 Forbidden — Access Denied')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="fh-card fh-card-glow p-5">
                <div class="fh-logo-icon mx-auto mb-4" style="width: 64px; height: 64px; font-size: 2rem; background: linear-gradient(135deg, var(--fh-accent-crimson), var(--fh-accent-pink));">
                    <i class="bi bi-shield-x"></i>
                </div>
                <h1 class="display-5 fw-bold text-light mb-2">403 Forbidden</h1>
                <h5 class="text-danger mb-3">Clearance Level Insufficient</h5>
                <p class="text-muted mb-4">
                    {{ $exception->getMessage() ?: 'You do not have administrative clearance to access this sector of the Fan Hub Plus universe.' }}
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('home') }}" class="fh-btn-secondary">
                        <i class="bi bi-house me-1"></i> Universe Home
                    </a>
                    @auth
                        <a href="{{ route('user.dashboard') }}" class="fh-btn-primary">
                            <i class="bi bi-speedometer2 me-1"></i> My Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="fh-btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
