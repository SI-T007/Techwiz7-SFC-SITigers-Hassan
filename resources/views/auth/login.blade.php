@extends('layouts.app')

@section('title', 'Sign In — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="fh-card fh-card-glow p-4 p-md-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="fh-logo-icon mx-auto mb-3" style="width: 48px; height: 48px; font-size: 1.5rem;">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Welcome Back</h2>
                    <p class="text-muted small">Sign in to your Fan Hub Plus account to continue</p>
                </div>

                <!-- One-Click Demo Credentials Quick Fill -->
                <div class="bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-3 p-3 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Test Access
                        </span>
                        <span class="badge bg-secondary bg-opacity-25 text-light small">Dev Helper</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-custom flex-fill py-1 small" onclick="fillCredentials('admin@fanhub.test', 'Password@123')">
                            <i class="bi bi-shield-fill text-danger me-1"></i> Admin
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-custom flex-fill py-1 small" onclick="fillCredentials('user@fanhub.test', 'Password@123')">
                            <i class="bi bi-person-fill text-info me-1"></i> Member
                        </button>
                    </div>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                    @csrf

                    <!-- Email Field -->
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

                    <!-- Password Field -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            @if (Route::has('password.request'))
                                <a class="small text-decoration-none text-info" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary border-opacity-25 text-muted">
                                <i class="bi bi-key"></i>
                            </span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required autocomplete="current-password" placeholder="••••••••">
                            <button class="btn btn-outline-secondary border-opacity-25" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block mt-1 small" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Remember my session on this device
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary-gradient py-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Universe
                        </button>
                    </div>

                    <!-- Footer Link -->
                    <div class="text-center pt-2 border-top border-secondary border-opacity-25">
                        <p class="small text-muted mb-0">
                            Don't have an account yet? 
                            <a href="{{ route('register') }}" class="text-info text-decoration-none fw-semibold">
                                Create an account
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
    // Password visibility toggle
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    toggleBtn.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    });

    // Helper for fast demo login
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endpush
