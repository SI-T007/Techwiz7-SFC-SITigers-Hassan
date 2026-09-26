@extends('layouts.app')

@section('title', 'Create Account — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="fh-card fh-card-glow p-4 p-md-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="fh-logo-icon mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Join the Universe</h2>
                    <p class="text-muted small">Create your Fan Hub Plus profile to unlock bookmarks, ratings, and fandom channels</p>
                </div>

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register.submit') }}" id="registerForm">
                    @csrf

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name or Handle</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted">
                                <i class="bi bi-person"></i>
                            </span>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus 
                                   placeholder="e.g. Maya Lin or NeoFighter">
                        </div>
                        @error('name')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email" 
                                   placeholder="you@domain.com">
                        </div>
                        @error('email')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password and Confirmation -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password" placeholder="Min. 8 characters"
                                       oninput="checkPasswordStrength(this.value)">
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block mt-1 small" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password-confirm" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted">
                                    <i class="bi bi-check2-circle"></i>
                                </span>
                                <input id="password-confirm" type="password" class="form-control" 
                                       name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    <!-- Password Strength Meter -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Security Strength:</span>
                            <span class="small fw-semibold" id="strengthText">Too short</span>
                        </div>
                        <div class="progress" style="height: 5px; background: rgba(255,255,255,0.1);">
                            <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Terms & Privacy Checkbox -->
                    <div class="mb-4 form-check">
                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }} required>
                        <label class="form-check-label small text-muted" for="terms">
                            I accept the <a href="#" class="text-info text-decoration-none">Terms of Service</a> & <a href="#" class="text-info text-decoration-none">Privacy Policy</a>
                        </label>
                        @error('terms')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary-gradient py-2">
                            <i class="bi bi-person-check me-1"></i> Complete Registration
                        </button>
                    </div>

                    <!-- Footer Link -->
                    <div class="text-center pt-2 border-top border-secondary border-opacity-25">
                        <p class="small text-muted mb-0">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-info text-decoration-none fw-semibold">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function checkPasswordStrength(password) {
        const bar = document.getElementById('strengthBar');
        const text = document.getElementById('strengthText');
        
        let score = 0;
        if (password.length >= 8) score += 25;
        if (/[A-Z]/.test(password)) score += 25;
        if (/[0-9]/.test(password)) score += 25;
        if (/[^A-Za-z0-9]/.test(password)) score += 25;

        bar.style.width = score + '%';

        if (score === 0 || password.length < 8) {
            bar.className = 'progress-bar bg-danger';
            text.textContent = 'Too short (min 8 chars)';
            text.className = 'small fw-semibold text-danger';
        } else if (score <= 50) {
            bar.className = 'progress-bar bg-warning';
            text.textContent = 'Fair';
            text.className = 'small fw-semibold text-warning';
        } else if (score <= 75) {
            bar.className = 'progress-bar bg-info';
            text.textContent = 'Good';
            text.className = 'small fw-semibold text-info';
        } else {
            bar.className = 'progress-bar bg-success';
            text.textContent = 'Strong';
            text.className = 'small fw-semibold text-success';
        }
    }
</script>
@endpush
