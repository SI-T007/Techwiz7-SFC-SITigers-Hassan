@extends('layouts.app')

@section('title', 'Review Submission: ' . $submission->title . ' — Admin Console')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.submissions.index') }}" class="text-muted text-decoration-none small">&larr; Back to Moderation Queue</a>
            </div>
            <h1 class="h2 fw-bold text-light mb-0">Review Fan Submission</h1>
            <p class="text-muted small mb-0">Submission ID: #{{ $submission->id }} &bull; Submitted by {{ $submission->user?->name ?? 'User' }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($submission->isApproved())
                <a href="{{ route('community.submissions.show', $submission->id) }}" target="_blank" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Public Showcase View
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Content Details Column -->
        <div class="col-lg-8">
            <div class="fh-card p-4 p-md-5 mb-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fh-badge fh-badge-primary">{{ ucwords(str_replace('_', ' ', $submission->type)) }}</span>
                        <span class="fh-badge fh-badge-cyan">{{ $submission->category?->name ?? 'General' }}</span>
                        <span class="small text-muted">{{ $submission->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        @if($submission->status === 'approved')
                            <span class="badge bg-success bg-opacity-25 text-success">Approved</span>
                        @elseif($submission->status === 'pending')
                            <span class="badge bg-warning bg-opacity-25 text-warning">Pending Review</span>
                        @else
                            <span class="badge bg-danger bg-opacity-25 text-danger">Rejected</span>
                        @endif
                    </div>
                </div>

                <h2 class="h3 fw-bold text-light mb-3">{{ $submission->title }}</h2>

                <!-- Submitter Profile Card -->
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 border border-subtle mb-4" style="background: rgba(255,255,255,0.02);">
                    <img src="{{ $submission->user?->avatar_url }}" alt="{{ $submission->user?->name }}" class="avatar-img" width="40" height="40">
                    <div>
                        <div class="fw-semibold text-light">{{ $submission->user?->name }}</div>
                        <div class="small text-muted">{{ $submission->user?->email }}</div>
                    </div>
                </div>

                <!-- Showcase Media / Image -->
                @if($submission->featured_image)
                    <div class="mb-4 rounded-3 overflow-hidden border border-subtle text-center" style="background: #000;">
                        <img src="{{ asset('storage/' . $submission->featured_image) }}" alt="{{ $submission->title }}" class="img-fluid" style="max-height: 500px; object-fit: contain;">
                    </div>
                @endif

                <!-- External URL Link -->
                @if($submission->external_url)
                    <div class="p-3 rounded-3 border border-info border-opacity-25 mb-4" style="background: rgba(6, 182, 212, 0.05);">
                        <span class="small text-muted d-block">External Project URL:</span>
                        <a href="{{ $submission->external_url }}" target="_blank" class="fw-semibold text-info text-decoration-none">
                            <i class="bi bi-box-arrow-up-right me-1"></i> {{ $submission->external_url }}
                        </a>
                    </div>
                @endif

                <!-- Body Text -->
                @if($submission->body)
                    <div class="p-4 rounded-3 border border-subtle mb-4 text-light" style="background: rgba(255,255,255,0.02); line-height: 1.8;">
                        {!! nl2br(e($submission->body)) !!}
                    </div>
                @endif

                <!-- Prior Review Log if applicable -->
                @if($submission->reviewed_by)
                    <div class="p-3 rounded-3 border border-subtle small text-muted">
                        <div><strong>Reviewed by:</strong> {{ $submission->reviewer?->name ?? 'Moderator' }} on {{ $submission->reviewed_at?->format('M d, Y H:i') }}</div>
                        @if($submission->rejection_reason)
                            <div class="text-danger mt-1"><strong>Feedback / Rejection Reason:</strong> {{ $submission->rejection_reason }}</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Moderation Action Panel -->
        <div class="col-lg-4">
            <div class="fh-card p-4 sticky-top" style="top: 90px;">
                <h3 class="h6 fw-bold text-light mb-3">
                    <i class="bi bi-shield-check text-primary me-2"></i> Moderation Actions
                </h3>

                <!-- Approve Action -->
                <form method="POST" action="{{ route('admin.submissions.approve', $submission->id) }}" class="mb-4">
                    @csrf
                    <p class="small text-muted mb-2">
                        Approving will immediately publish this fan creation to the community showcase.
                    </p>
                    <button type="submit" class="btn btn-success w-100 {{ $submission->isApproved() ? 'disabled' : '' }}">
                        <i class="bi bi-check-circle-fill me-1"></i> Approve & Publish
                    </button>
                </form>

                <hr class="border-subtle">

                <!-- Reject Action -->
                <form method="POST" action="{{ route('admin.submissions.reject', $submission->id) }}">
                    @csrf
                    <label for="rejection_reason" class="form-label text-light small fw-semibold">
                        Rejection Reason / Creator Feedback
                    </label>
                    <textarea name="rejection_reason" 
                              id="rejection_reason" 
                              class="form-control form-control-sm mb-2" 
                              rows="3" 
                              placeholder="Explain why this content was rejected (e.g. copyright issue, offensive language, poor resolution)...">{{ old('rejection_reason', $submission->rejection_reason) }}</textarea>
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                        <i class="bi bi-x-circle-fill me-1"></i> Reject Submission
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
