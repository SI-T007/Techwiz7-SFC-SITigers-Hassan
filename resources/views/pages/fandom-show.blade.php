@extends('layouts.app')

@section('title', $category->name . ' Universe Hub — Fan Hub Plus')

@push('styles')
<style>
    .fh-fandom-telemetry-card {
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

    <!-- Fandom Hero Banner -->
    <div class="fh-card fh-card-glow p-4 p-md-5 mb-4 position-relative overflow-hidden">
        <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
            <div class="col-auto">
                <div class="fh-category-icon-box" style="width: 72px; height: 72px; font-size: 2.2rem;">
                    <i class="bi {{ $category->icon ?: 'bi-stars' }}"></i>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="fh-badge fh-badge-cyan">Verified Fandom Universe</span>
                    <span class="badge bg-warning bg-opacity-25 text-warning">
                        <i class="bi bi-star-fill me-1"></i> {{ number_format($fandomMetrics['avg_rating'], 1) }} Sentiment
                    </span>
                </div>
                <h1 class="h2 fw-bold text-light mb-1">{{ $category->name }}</h1>
                <p class="text-secondary mb-0" style="max-width: 600px;">
                    {{ $category->description }}
                </p>
            </div>
            <div class="col-md-auto d-flex gap-2">
                @auth
                    <a href="{{ route('profile.edit') }}" class="fh-btn-primary">
                        <i class="bi bi-heart-fill text-danger me-1"></i> Favorite Realm
                    </a>
                @else
                    <a href="{{ route('register') }}" class="fh-btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Join Fandom
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Universe Lore Telemetry & Graphical Index -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="fh-fandom-telemetry-card p-4 h-100">
                <h2 class="h6 fw-bold text-light text-uppercase mb-3" style="letter-spacing: 0.05em;">
                    <i class="bi bi-speedometer2 text-info me-2"></i>Universe Lore Telemetry
                </h2>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border border-secondary border-opacity-25 text-center" style="background: rgba(255,255,255,0.02);">
                            <div class="h4 fw-bold text-light mb-0">{{ $fandomMetrics['contents_count'] }}</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Lore Articles</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border border-secondary border-opacity-25 text-center" style="background: rgba(255,255,255,0.02);">
                            <div class="h4 fw-bold text-info mb-0">{{ $fandomMetrics['characters_count'] }}</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Characters</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border border-secondary border-opacity-25 text-center" style="background: rgba(255,255,255,0.02);">
                            <div class="h4 fw-bold text-warning mb-0">{{ $fandomMetrics['submissions_count'] }}</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Fan Submissions</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border border-secondary border-opacity-25 text-center" style="background: rgba(255,255,255,0.02);">
                            <div class="h4 fw-bold text-success mb-0">{{ $fandomMetrics['media_count'] }}</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">Media Streams</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top border-secondary border-opacity-25 text-muted small">
                    <span><i class="bi bi-eye me-1 text-info"></i> {{ number_format($fandomMetrics['total_views']) }} Total Views</span>
                    <span><i class="bi bi-calendar-event me-1 text-primary"></i> {{ $fandomMetrics['events_count'] }} Gatherings</span>
                    <span><i class="bi bi-disc me-1 text-purple"></i> {{ $fandomMetrics['releases_count'] }} Drops</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="fh-fandom-telemetry-card p-4 h-100">
                <h2 class="h6 fw-bold text-light text-uppercase mb-2" style="letter-spacing: 0.05em;">
                    <i class="bi bi-pie-chart text-primary me-2"></i>Lore Distribution
                </h2>
                <div style="position: relative; height: 160px; width: 100%;">
                    <canvas id="fandomLoreDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Content & Lore -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-light mb-0">
                <i class="bi bi-journal-richtext text-primary me-2"></i> Featured Content & Lore
            </h2>
            <span class="small text-muted">{{ $contents->count() }} items</span>
        </div>

        <div class="row g-4">
            @forelse($contents as $content)
                <div class="col-md-6 col-lg-4">
                    <x-content-card :content="$content" />
                </div>
            @empty
                <div class="col-12">
                    <x-empty-state 
                        icon="bi-journal-x" 
                        title="No Articles Published Yet" 
                        message="Lore entries for {{ $category->name }} are currently in review."
                    />
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section 2: Characters in this Fandom -->
    @if($characters->count() > 0)
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold text-light mb-0">
                    <i class="bi bi-people text-purple me-2"></i> Notable Characters
                </h2>
                <a href="{{ route('characters.index', ['category' => $category->slug]) }}" class="small text-info text-decoration-none">
                    View all &rarr;
                </a>
            </div>

            <div class="row g-4">
                @foreach($characters as $char)
                    <div class="col-md-6 col-lg-3">
                        <x-character-card :character="$char" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Section 3: Upcoming Releases & Events -->
    <div class="row g-4">
        <div class="col-lg-6">
            <h2 class="h5 fw-bold text-light mb-3">
                <i class="bi bi-calendar-check text-info me-2"></i> Upcoming Drops
            </h2>
            <div class="d-flex flex-column gap-3">
                @forelse($releases as $rel)
                    <x-release-card :release="$rel" />
                @empty
                    <div class="fh-card p-3 text-center text-muted small">
                        No upcoming release drops currently announced for this realm.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-6">
            <h2 class="h5 fw-bold text-light mb-3">
                <i class="bi bi-geo-alt text-success me-2"></i> Fandom Gatherings & Events
            </h2>
            <div class="d-flex flex-column gap-3">
                @forelse($events as $ev)
                    <x-event-card :event="$ev" />
                @empty
                    <div class="fh-card p-3 text-center text-muted small">
                        No upcoming gatherings scheduled for this realm.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('fandomLoreDonutChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Articles', 'Characters', 'Fan Submissions', 'Media', 'Events'],
                datasets: [{
                    data: [
                        {{ max(1, $fandomMetrics['contents_count']) }},
                        {{ max(1, $fandomMetrics['characters_count']) }},
                        {{ max(0, $fandomMetrics['submissions_count']) }},
                        {{ max(0, $fandomMetrics['media_count']) }},
                        {{ max(0, $fandomMetrics['events_count']) }}
                    ],
                    backgroundColor: ['#6366f1', '#06b6d4', '#f59e0b', '#10b981', '#ec4899'],
                    borderColor: 'rgba(15, 23, 42, 0.95)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 8, font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endpush
