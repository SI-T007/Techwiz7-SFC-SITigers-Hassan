@extends('layouts.app')

@section('title', 'Forgot Password — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="fh-card fh-card-glow p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="fh-logo-icon mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-question-circle"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Reset Password</h2>
                    <p class="text-muted small">Enter your email address and we will dispatch a password recovery link to your inbox</p>
                </div>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                   placeholder="name@fandom.com">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary-gradient py-2">
                            <i class="bi bi-send me-1"></i> Send Recovery Link
                        </button>
                    </div>

                    <div class="text-center pt-2 border-top border-secondary border-opacity-25">
                        <p class="small text-muted mb-0">
                            Remembered your password? 
                            <a href="{{ route('login') }}" class="text-info text-decoration-none fw-semibold">
                                Return to Sign In
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
