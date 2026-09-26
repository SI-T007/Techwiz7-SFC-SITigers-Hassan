@extends('layouts.app')

@section('title', 'Platform Feedback & Bug Reports — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="fh-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Community Voice</span>
                    <h1 class="h2 fw-bold text-light mb-1">Platform Feedback & Bug Reports</h1>
                    <p class="text-muted small">Help us improve Fan Hub Plus. Report bugs, propose new fandoms, or submit general inquiries</p>
                </div>

                <form method="POST" action="{{ route('feedback.store') }}">
                    @csrf

                    <div class="row g-3 mb-3">
                        <!-- Name (pre-filled if logged in) -->
                        <div class="col-md-6">
                            <label for="name" class="form-label text-light fw-semibold">Your Name</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', auth()->user()?->name) }}" 
                                   placeholder="Your name or handle">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email (pre-filled if logged in) -->
                        <div class="col-md-6">
                            <label for="email" class="form-label text-light fw-semibold">Email Address</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', auth()->user()?->email) }}" 
                                   placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Feedback Type -->
                    <div class="mb-3">
                        <label for="type" class="form-label text-light fw-semibold">Feedback Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="suggestion" {{ old('type') === 'suggestion' ? 'selected' : '' }}>Feature Suggestion / Fandom Request</option>
                            <option value="bug" {{ old('type') === 'bug' ? 'selected' : '' }}>Technical Bug Report</option>
                            <option value="query" {{ old('type') === 'query' ? 'selected' : '' }}>General Query</option>
                            <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="mb-3">
                        <label for="subject" class="form-label text-light fw-semibold">Subject / Headline <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="subject" 
                               id="subject" 
                               class="form-control @error('subject') is-invalid @enderror" 
                               value="{{ old('subject') }}" 
                               placeholder="e.g., Suggestion: Add Webtoon reader mode or Dark mode contrast issue" 
                               required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="mb-4">
                        <label for="message" class="form-label text-light fw-semibold">Message / Details <span class="text-danger">*</span></label>
                        <textarea name="message" 
                                  id="message" 
                                  class="form-control @error('message') is-invalid @enderror" 
                                  rows="6" 
                                  placeholder="Provide as much context as possible. If reporting a bug, describe what occurred and the browser used..." 
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-subtle">
                        <a href="{{ route('home') }}" class="fh-btn-ghost">Back to Home</a>
                        <button type="submit" class="fh-btn-primary px-4">
                            <i class="bi bi-send me-1"></i> Send Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
