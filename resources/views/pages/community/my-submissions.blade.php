@extends('layouts.app')

@section('title', 'My Fan Submissions — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Creator Portfolio</span>
            <h1 class="h2 fw-bold text-light mb-1">My Fan Submissions</h1>
            <p class="text-muted small mb-0">Track the moderation status of your artwork, cosplay photography, and fan fiction</p>
        </div>
        <div>
            <a href="{{ route('community.submit') }}" class="fh-btn-primary">
                <i class="bi bi-plus-circle me-1"></i> New Submission
            </a>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="fh-card p-3 mb-4">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('community.my-submissions') }}" class="btn btn-sm {{ !request('status') ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                All Submissions
            </a>
            <a href="{{ route('community.my-submissions', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                <i class="bi bi-clock-history me-1"></i> Pending
            </a>
            <a href="{{ route('community.my-submissions', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') === 'approved' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                <i class="bi bi-check-circle me-1"></i> Approved
            </a>
            <a href="{{ route('community.my-submissions', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') === 'rejected' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                <i class="bi bi-x-circle me-1"></i> Needs Revision
            </a>
        </div>
    </div>

    <!-- Submissions Grid -->
    <div class="row g-4">
        @forelse($submissions as $sub)
            <div class="col-md-6 col-lg-4">
                <div class="fh-card h-100 d-flex flex-column p-4">
                    <!-- Top Status & Type -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fh-badge fh-badge-primary">
                            {{ ucwords(str_replace('_', ' ', $sub->type)) }}
                        </span>
                        @if($sub->status === 'approved')
                            <span class="badge bg-success bg-opacity-25 text-success small">
                                <i class="bi bi-check-circle-fill me-1"></i> Approved
                            </span>
                        @elseif($sub->status === 'pending')
                            <span class="badge bg-warning bg-opacity-25 text-warning small">
                                <i class="bi bi-clock-history me-1"></i> Pending Review
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-25 text-danger small">
                                <i class="bi bi-x-circle-fill me-1"></i> Rejected
                            </span>
                        @endif
                    </div>

                    <!-- Image Thumbnail Preview if available -->
                    @if($sub->featured_image)
                        <div class="mb-3 rounded-2 overflow-hidden border border-subtle" style="height: 160px; background: #000;">
                            <img src="{{ asset('storage/' . $sub->featured_image) }}" alt="{{ $sub->title }}" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                    @endif

                    <!-- Title -->
                    <h3 class="h6 fw-bold text-light mb-2">
                        @if($sub->status === 'approved')
                            <a href="{{ route('community.submissions.show', $sub->id) }}" class="text-light text-decoration-none">
                                {{ $sub->title }}
                            </a>
                        @else
                            {{ $sub->title }}
                        @endif
                    </h3>

                    <!-- Realm & Date -->
                    <div class="small text-muted mb-3">
                        <span class="text-info fw-semibold">{{ $sub->category?->name ?? 'General Fandom' }}</span> &bull;
                        <span>Submitted {{ $sub->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- Moderation Feedback Notice -->
                    @if($sub->status === 'rejected' && $sub->rejection_reason)
                        <div class="p-2 mb-3 rounded border border-danger border-opacity-50 small text-danger" style="background: rgba(239, 68, 68, 0.08);">
                            <strong>Moderator Feedback:</strong> {{ $sub->rejection_reason }}
                        </div>
                    @elseif($sub->status === 'pending')
                        <div class="small text-muted mb-3">
                            <i class="bi bi-hourglass-split me-1 text-warning"></i> Awaiting moderator review.
                        </div>
                    @endif

                    <!-- Bottom Actions -->
                    <div class="mt-auto pt-3 border-top border-subtle d-flex justify-content-between align-items-center">
                        @if($sub->status === 'approved')
                            <a href="{{ route('community.submissions.show', $sub->id) }}" class="small text-info text-decoration-none fw-semibold">
                                View Public Showcase &rarr;
                            </a>
                        @else
                            <span class="small text-muted">ID #{{ $sub->id }}</span>
                        @endif

                        <form method="POST" action="{{ route('community.submissions.destroy', $sub->id) }}" onsubmit="return confirm('Delete this submission?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0 small text-decoration-none">
                                <i class="bi bi-trash3"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <x-empty-state 
                    icon="bi-palette" 
                    title="No Submissions Found" 
                    message="You have not submitted any fan creations yet. Submit your fan art, cosplay photos, or creative stories to share with the universe."
                    actionText="Submit Your First Creation"
                    :actionUrl="route('community.submit')"
                />
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($submissions->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $submissions->links() }}
        </div>
    @endif
</div>
@endsection
