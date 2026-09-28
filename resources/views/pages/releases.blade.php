@extends('layouts.app')

@section('title', 'Upcoming Fandom Releases & Drop Calendar — Fan Hub Plus')

@push('styles')
<style>
    .fh-release-chart-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        backdrop-filter: blur(12px);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    
    <x-breadcrumb :items="$breadcrumbs" />

    
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Upcoming Fandom Releases & Drop Schedule</span>
                <span class="badge bg-secondary bg-opacity-25 text-light">{{ $releases->total() }} Drops Scheduled</span>
            </div>
            <h1 class="h2 fw-bold text-light mb-1">Fandom Release Radar & Drops</h1>
            <p class="text-muted small mb-0">Track upcoming games, anime seasons, movie premieres, manga volumes, and collector merchandise</p>
        </div>
    </div>

    <!-- Release Ecosystem Graphical Radar Widget -->
    <div class="fh-release-chart-card p-4 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h3 class="h6 fw-bold text-light text-uppercase mb-2" style="letter-spacing: 0.05em;">
                    <i class="bi bi-disc text-primary me-2"></i>Release Format Ecosystem
                </h3>
                <p class="text-muted small mb-3">Live distribution across gaming launches, theatrical films, anime drops, and collector goods.</p>
                <div class="row g-2 text-center small">
                    @foreach($releaseStats as $fmt => $count)
                        <div class="col-4 col-md-2">
                            <div class="p-2 rounded-3 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                                <div class="fw-bold text-light fs-6">{{ $count }}</div>
                                <div class="text-muted" style="font-size: 0.68rem;">{{ $fmt }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-5">
                <div style="position: relative; height: 160px; width: 100%;">
                    <canvas id="releaseEcosystemChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeframe Navigation Pills -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('releases.index', array_merge(request()->except('timeframe', 'page'), ['timeframe' => 'upcoming'])) }}" 
           class="btn btn-sm {{ $timeframe === 'upcoming' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            <i class="bi bi-hourglass-split me-1"></i> Upcoming Drops
        </a>
        <a href="{{ route('releases.index', array_merge(request()->except('timeframe', 'page'), ['timeframe' => 'recent'])) }}" 
           class="btn btn-sm {{ $timeframe === 'recent' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            <i class="bi bi-check2-all me-1"></i> Already Released
        </a>
        <a href="{{ route('releases.index', array_merge(request()->except('timeframe', 'page'), ['timeframe' => 'featured'])) }}" 
           class="btn btn-sm {{ $timeframe === 'featured' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            <i class="bi bi-star-fill me-1"></i> Featured Spotlights
        </a>
        <a href="{{ route('releases.index', array_merge(request()->except('timeframe', 'page'), ['timeframe' => 'all'])) }}" 
           class="btn btn-sm {{ $timeframe === 'all' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            All Releases
        </a>
    </div>

    <!-- Popular Tag Filter Badges -->
    <div class="d-flex flex-wrap align-items-center gap-1 mb-4">
        <span class="text-muted small me-2"><i class="bi bi-tags me-1"></i> Tags:</span>
        <a href="{{ route('releases.index', request()->except('tag', 'page')) }}" 
           class="badge {{ !request('tag') ? 'bg-primary' : 'bg-secondary bg-opacity-25 text-light' }} text-decoration-none">
            All Tags
        </a>
        @foreach($allTags as $tag)
            <a href="{{ route('releases.index', array_merge(request()->except('page'), ['tag' => $tag])) }}" 
               class="badge {{ request('tag') === $tag ? 'bg-primary' : 'bg-secondary bg-opacity-25 text-light' }} text-decoration-none">
                {{ $tag }}
            </a>
        @endforeach
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="fh-card p-3 p-md-4 mb-4">
        <form method="GET" action="{{ route('releases.index') }}" class="row g-2 align-items-center">
            @if(request('timeframe'))
                <input type="hidden" name="timeframe" value="{{ request('timeframe') }}">
            @endif
            @if(request('tag'))
                <input type="hidden" name="tag" value="{{ request('tag') }}">
            @endif

            <!-- Search -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm bg-dark border-secondary text-light" 
                           placeholder="Search title, platform, publisher..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Fandom / Category -->
            <div class="col-6 col-md-4 col-lg-3">
                <select name="category" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Format / Type -->
            <div class="col-6 col-md-4 col-lg-2">
                <select name="type" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Formats</option>
                    <option value="game" {{ request('type') === 'game' ? 'selected' : '' }}>Video Game</option>
                    <option value="anime" {{ request('type') === 'anime' ? 'selected' : '' }}>Anime / Movie</option>
                    <option value="movie" {{ request('type') === 'movie' ? 'selected' : '' }}>Theatrical Film</option>
                    <option value="tv_series" {{ request('type') === 'tv_series' ? 'selected' : '' }}>TV / Streaming Series</option>
                    <option value="comic" {{ request('type') === 'comic' ? 'selected' : '' }}>Comic Book</option>
                    <option value="manga" {{ request('type') === 'manga' ? 'selected' : '' }}>Manga Volume</option>
                    <option value="merchandise" {{ request('type') === 'merchandise' ? 'selected' : '' }}>Merchandise / Figure</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-12 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary-gradient flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Apply Filters
                </button>
                <a href="{{ route('releases.index') }}" class="btn btn-sm btn-outline-secondary text-light" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Releases Grid -->
    <div class="row g-4">
        @forelse($releases as $release)
            <div class="col-md-6 col-lg-4">
                <x-release-card :release="$release" />
            </div>
        @empty
            <div class="col-12">
                <x-empty-state 
                    icon="bi-box-seam" 
                    title="No Releases Found" 
                    message="There are currently no releases matching the selected criteria or timeframe."
                    actionText="View All Upcoming Releases"
                    :actionUrl="route('releases.index')"
                />
            </div>
        @endforelse
    </div>

    @if($releases->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $releases->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('releaseEcosystemChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($releaseStats)),
                datasets: [{
                    data: @json(array_values($releaseStats)),
                    backgroundColor: ['#6366f1', '#ef4444', '#f59e0b', '#06b6d4', '#8b5cf6', '#10b981'],
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
