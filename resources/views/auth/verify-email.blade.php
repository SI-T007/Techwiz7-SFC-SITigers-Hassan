@extends('layouts.app')

@section('title', 'Verify Your Email — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="fh-card fh-card-glow p-4 p-md-5 text-center">
                <div class="fh-logo-icon mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.5rem;">
                    <i class="bi bi-envelope-check"></i>
                </div>
                <h2 class="fw-bold mb-2">Verify Your Email</h2>
                <p class="text-muted small mb-4">
                    Thanks for joining Fan Hub Plus! Before proceeding to all features, please verify your email address by clicking on the link we just emailed to you.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-25 text-success small mb-4" role="alert">
                        A new verification link has been sent to the email address provided during registration.
                    </div>
                @endif

                <div class="d-flex flex-column gap-2 mb-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary-gradient w-100 py-2">
                            <i class="bi bi-arrow-repeat me-1"></i> Resend Verification Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-custom w-100 py-2">
                            <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
