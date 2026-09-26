@extends('layouts.app')

@section('title', 'The Fan Collective — Creative Community Hub — Fan Hub Plus')

@push('styles')
<style>
    .fh-community-stat-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        backdrop-filter: blur(12px);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Hero Banner with Creative Telemetry -->
    <div class="fh-card fh-card-glow p-4 p-md-5 mb-4 position-relative overflow-hidden">
        <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Creative Universe</span>
                <h1 class="display-5 fw-bold text-light mb-2">The Fan Collective</h1>
                <p class="lead text-muted mb-4" style="max-width: 650px;">
                    Discover curated fan art, cosplay photo shoots, original fiction stories, and theories created by our global fandom community.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('community.submit') }}" class="fh-btn-primary">
                        <i class="bi bi-palette-fill me-1"></i> Submit Fan Creation
                    </a>
                    @auth
                        <a href="{{ route('community.my-submissions') }}" class="fh-btn-ghost">
                            <i class="bi bi-journal-text me-1"></i> My Submissions
                        </a>
                    @endauth
                    <a href="{{ route('feedback.create') }}" class="fh-btn-ghost">
                        <i class="bi bi-chat-dots me-1"></i> Send Feedback
                    </a>
                </div>
            </div>

            <!-- Mini Creative Medium Donut Chart -->
            <div class="col-lg-5">
                <div class="p-3 rounded-4 border border-secondary border-opacity-25" style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                            <i class="bi bi-palette text-warning me-2"></i>Creative Mediums
                        </h3>
                        <span class="badge bg-warning bg-opacity-25 text-warning small">Community Spectrum</span>
                    </div>
                    <div style="position: relative; height: 160px; width: 100%;">
                        <canvas id="communityMediumChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('community.index') }}" class="row g-2 align-items-center">
            <!-- Medium Type Buttons -->
            <div class="col-lg-5 col-md-12 d-flex flex-wrap gap-1">
                <a href="{{ route('community.index') }}" 
                   class="btn btn-sm {{ !request('type') ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    All Works
                </a>
                <a href="{{ route('community.index', array_merge(request()->except('type', 'page'), ['type' => 'fan_art'])) }}" 
                   class="btn btn-sm {{ request('type') === 'fan_art' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Fan Art
                </a>
                <a href="{{ route('community.index', array_merge(request()->except('type', 'page'), ['type' => 'cosplay'])) }}" 
                   class="btn btn-sm {{ request('type') === 'cosplay' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Cosplay
                </a>
                <a href="{{ route('community.index', array_merge(request()->except('type', 'page'), ['type' => 'fan_fiction'])) }}" 
                   class="btn btn-sm {{ request('type') === 'fan_fiction' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Fiction
                </a>
                <a href="{{ route('community.index', array_merge(request()->except('type', 'page'), ['type' => 'fan_video'])) }}" 
                   class="btn btn-sm {{ request('type') === 'fan_video' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
                    Videos
                </a>
            </div>

            <!-- Fandom Filter Dropdown -->
            <div class="col-6 col-md-4 col-lg-3">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Fandom Realms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Input -->
            <div class="col-6 col-md-5 col-lg-3">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search creations..." value="{{ request('search') }}">
                    <button type="submit" class="fh-btn-primary btn-sm"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <!-- Reset Button -->
            <div class="col-auto col-md-3 col-lg-1">
                <a href="{{ route('community.index') }}" class="fh-btn-ghost btn-sm w-100 text-center" title="Reset filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Approved Fan Creations Showcase Grid -->
    <div class="row g-4 mb-5">
        @forelse($submissions as $sub)
            <div class="col-md-6 col-lg-4">
                <div class="fh-card fh-card-interactive h-100 d-flex flex-column">
                    <!-- Artwork Thumbnail / Placeholder -->
                    <div class="fh-thumb-wrap position-relative">
                        @if($sub->featured_image)
                            <img src="{{ asset('storage/' . $sub->featured_image) }}" alt="{{ $sub->title }}" loading="lazy">
                        @else
                            <div class="fh-thumb-placeholder">
                                <i class="bi {{ $sub->type === 'cosplay' ? 'bi-camera' : ($sub->type === 'fan_video' ? 'bi-play-btn' : 'bi-brush') }}"></i>
                            </div>
                        @endif

                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="fh-badge fh-badge-primary">
                                {{ ucwords(str_replace('_', ' ', $sub->type)) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3 p-md-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small text-info fw-semibold">{{ $sub->category?->name ?? 'General' }}</span>
                            <span class="small text-muted">{{ $sub->published_at ? $sub->published_at->format('M d') : $sub->created_at->format('M d') }}</span>
                        </div>

                        <h3 class="h6 fw-bold text-light mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.5rem;">
                            <a href="{{ route('community.submissions.show', $sub->id) }}" class="text-light text-decoration-none">
                                {{ $sub->title }}
                            </a>
                        </h3>

                        <p class="small text-muted mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $sub->body ?: 'Community fan project created and shared on Fan Hub Plus.' }}
                        </p>

                        <!-- Creator Footer -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-subtle mt-auto">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $sub->user?->avatar_url }}" alt="{{ $sub->user?->name }}" class="avatar-img" width="24" height="24">
                                <span class="small text-light fw-semibold">{{ $sub->user?->name ?? 'Creator' }}</span>
                            </div>
                            <a href="{{ route('community.submissions.show', $sub->id) }}" class="small fw-semibold text-info text-decoration-none">
                                View Work &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <x-empty-state 
                    icon="bi-palette" 
                    title="No Fan Creations Found" 
                    message="No verified community creations match your filters. Be the first to publish your artwork, cosplay, or stories to this realm!"
                    actionText="Submit Your Creation"
                    :actionUrl="route('community.submit')"
                />
            </div>
        @endforelse
    </div>

    @if($submissions->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $submissions->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('communityMediumChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($communityStats)),
                datasets: [{
                    data: [
                        {{ max(1, $communityStats['Fan Art'] ?? 0) }},
                        {{ max(1, $communityStats['Cosplay'] ?? 0) }},
                        {{ max(1, $communityStats['Fiction'] ?? 0) }},
                        {{ max(1, $communityStats['Videos'] ?? 0) }}
                    ],
                    backgroundColor: ['#f59e0b', '#ec4899', '#6366f1', '#06b6d4'],
                    borderColor: 'rgba(15, 23, 42, 0.95)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 8, font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endpush
