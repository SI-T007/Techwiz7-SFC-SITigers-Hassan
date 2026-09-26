@extends('layouts.app')

@section('title', 'Anime Universe — Streaming Hub, Shonen Lore & Simulcasts — Fan Hub Plus')

@push('styles')
<style>
    .fh-anime-hero {
        position: relative;
        border-radius: 1.25rem;
        overflow: hidden;
        background: radial-gradient(circle at 80% 20%, rgba(239, 68, 68, 0.25) 0%, rgba(15, 23, 42, 0) 50%),
                    radial-gradient(circle at 20% 80%, rgba(139, 92, 246, 0.2) 0%, rgba(15, 23, 42, 0) 50%),
                    #0b0f19;
        border: 1px solid rgba(255, 255, 255, 0.1);
        min-height: 420px;
    }
    .fh-anime-hero-backdrop {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0.35;
        filter: blur(2px);
    }
    .fh-anime-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, #0b0f19 0%, rgba(11, 15, 25, 0.85) 45%, rgba(11, 15, 25, 0.3) 100%);
    }

    .fh-anime-badge {
        background: linear-gradient(135deg, #ef4444, #f97316);
        color: #fff;
        font-weight: 800;
        font-size: 0.72rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .genre-filter-chip {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #cbd5e1;
        padding: 0.4rem 0.9rem;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .genre-filter-chip:hover, .genre-filter-chip.active {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    }

    .anime-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .anime-card:hover {
        transform: translateY(-6px);
        border-color: rgba(239, 68, 68, 0.4);
        box-shadow: 0 15px 30px -10px rgba(239, 68, 68, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5 py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- 1. Spotlight Hero Banner -->
    <div class="fh-anime-hero p-4 p-md-5 mb-5 position-relative">
        @if($spotlight)
            <div class="fh-anime-hero-backdrop" style="background-image: url('{{ $spotlight->hero_thumbnail_url ?: $spotlight->thumbnail_url }}');"></div>
        @endif
        <div class="fh-anime-hero-overlay"></div>

        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="fh-anime-badge">
                        <i class="bi bi-fire"></i> ANIME SPOTLIGHT
                    </span>
                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25">
                        <i class="bi bi-broadcast me-1"></i> SIMULCAST 2026
                    </span>
                    @if($spotlight && $spotlight->ratings_avg)
                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25">
                            <i class="bi bi-star-fill me-1"></i> {{ number_format($spotlight->ratings_avg, 1) }} ★ ({{ $spotlight->ratings_count }} reviews)
                        </span>
                    @endif
                </div>

                <h1 class="display-5 fw-bold text-light mb-3">
                    {{ $spotlight ? $spotlight->title : 'Shonen Lore & Legendary Anime' }}
                </h1>
                <p class="text-secondary lead mb-4" style="max-width: 650px; line-height: 1.7;">
                    {{ $spotlight ? Str::limit($spotlight->description, 200) : 'Explore world-class Japanese animation, canonical character arcs, seasonal simulcast drops, and rich fandom theories.' }}
                </p>

                <div class="d-flex flex-wrap align-items-center gap-3">
                    @if($spotlight)
                        <a href="{{ route('explore.show', $spotlight->slug) }}" class="btn btn-primary-gradient px-4 py-2.5 fw-bold">
                            <i class="bi bi-play-circle-fill me-2"></i> Explore Lore & Media
                        </a>
                        <a href="{{ route('characters.index', ['category' => 'anime']) }}" class="btn btn-outline-light px-4 py-2.5">
                            <i class="bi bi-people me-2"></i> Anime Characters
                        </a>
                    @else
                        <a href="{{ route('explore', ['category' => 'anime']) }}" class="btn btn-primary-gradient px-4 py-2.5 fw-bold">
                            <i class="bi bi-collection-play me-2"></i> Browse All Anime
                        </a>
                    @endif
                </div>
            </div>

            <!-- Mini Quick Telemetry Widget -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="p-4 rounded-4 border border-secondary border-opacity-25 text-light" style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-bold text-light mb-0 text-uppercase" style="letter-spacing: 0.05em;">
                            <i class="bi bi-pie-chart-fill text-danger me-2"></i>Anime Realm Composition
                        </h3>
                        <span class="badge bg-danger bg-opacity-25 text-danger small">Live Radar</span>
                    </div>
                    <div style="position: relative; height: 180px; width: 100%;">
                        <canvas id="animeGenreDonut"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Interactive Anime Sub-genres & Search Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <!-- Genre Filter Pills -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('anime.index') }}" class="genre-filter-chip {{ !request('genre') ? 'active' : '' }}">
                🌟 All Anime
            </a>
            @foreach($genres as $genre)
                <a href="{{ route('anime.index', ['genre' => $genre->slug]) }}" class="genre-filter-chip {{ request('genre') === $genre->slug ? 'active' : '' }}">
                    {{ $genre->name }}
                </a>
            @endforeach
        </div>

        <!-- Quick Anime Search & Sort Form -->
        <form action="{{ route('anime.index') }}" method="GET" class="d-flex align-items-center gap-2">
            @if(request('genre'))
                <input type="hidden" name="genre" value="{{ request('genre') }}">
            @endif
            <div class="input-group input-group-sm" style="max-width: 240px;">
                <input type="text" name="search" class="form-control bg-dark text-light border-secondary" placeholder="Search anime series..." value="{{ request('search') }}">
                <button class="btn btn-outline-danger" type="submit"><i class="bi bi-search"></i></button>
            </div>
            <select name="sort" class="form-select form-select-sm bg-dark text-light border-secondary" style="width: 130px;" onchange="this.form.submit()">
                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Top Rated</option>
                <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>A-Z</option>
            </select>
        </form>
    </div>

    <!-- 3. Trending & High-Velocity Anime Rail -->
    @if($trendingAnime->count() > 0)
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 fw-bold text-light mb-0">
                    <i class="bi bi-fire text-danger me-2"></i> Trending Anime Series
                </h2>
                <span class="text-muted small">High Community Velocity</span>
            </div>
            <div class="row g-4">
                @foreach($trendingAnime as $animeItem)
                    <div class="col-md-6 col-lg-3">
                        <x-content-card :content="$animeItem" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. Filtered Anime Catalog Grid -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 fw-bold text-light mb-0">
                <i class="bi bi-collection-play text-primary me-2"></i> Anime Lore Catalog
            </h2>
            <span class="text-muted small">{{ $catalog->total() }} Total Entries</span>
        </div>

        <div class="row g-4">
            @forelse($catalog as $item)
                <div class="col-md-6 col-lg-3">
                    <x-content-card :content="$item" />
                </div>
            @empty
                <div class="col-12">
                    <x-empty-state 
                        icon="bi-tv" 
                        title="No Anime Matches Found" 
                        message="Try clearing your search query or selecting a different anime genre."
                    />
                </div>
            @endforelse
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $catalog->links() }}
        </div>
    </div>

    <!-- 5. Iconic Anime Characters Showcase -->
    @if($characters->count() > 0)
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h4 fw-bold text-light mb-0">
                        <i class="bi bi-people text-purple me-2"></i> Iconic Anime Legends & Characters
                    </h2>
                    <p class="text-muted small mb-0">Explore canonical powers, abilities, and 360° lore dossiers.</p>
                </div>
                <a href="{{ route('characters.index', ['category' => 'anime']) }}" class="small text-info text-decoration-none">
                    View all legends &rarr;
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

    <!-- 6. Seasonal Releases & Community Works Grid -->
    <div class="row g-4">
        <!-- Upcoming Seasonal Drops -->
        <div class="col-lg-6">
            <div class="p-4 rounded-4 border border-secondary border-opacity-25 h-100" style="background: rgba(15, 23, 42, 0.75);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-light mb-0">
                        <i class="bi bi-calendar-event text-danger me-2"></i> Seasonal Simulcast Drops
                    </h2>
                    <a href="{{ route('releases.index') }}" class="small text-danger text-decoration-none">Schedule &rarr;</a>
                </div>
                <div class="d-flex flex-column gap-3">
                    @forelse($upcomingReleases as $rel)
                        <x-release-card :release="$rel" />
                    @empty
                        <div class="p-3 text-center text-muted small">
                            No upcoming anime releases scheduled for this cycle.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Anime Community Fan Creations -->
        <div class="col-lg-6">
            <div class="p-4 rounded-4 border border-secondary border-opacity-25 h-100" style="background: rgba(15, 23, 42, 0.75);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold text-light mb-0">
                        <i class="bi bi-palette text-warning me-2"></i> Fan Works & Creations
                    </h2>
                    <a href="{{ route('community.submit') }}" class="btn btn-sm btn-outline-warning">Submit Art</a>
                </div>
                <div class="d-flex flex-column gap-3">
                    @forelse($fanSubmissions as $sub)
                        <div class="p-3 rounded-3 border border-secondary border-opacity-25 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02);">
                            <div>
                                <a href="{{ route('community.submissions.show', $sub) }}" class="fw-bold text-light text-decoration-none hover-primary">
                                    {{ $sub->title }}
                                </a>
                                <div class="text-muted small" style="font-size: 0.72rem;">By {{ $sub->user->name }} &bull; {{ $sub->submission_type }}</div>
                            </div>
                            <span class="badge bg-warning bg-opacity-25 text-warning small">Approved</span>
                        </div>
                    @empty
                        <div class="p-3 text-center text-muted small">
                            No community submissions for Anime yet. Be the first to share your fan art or theory!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('animeGenreDonut');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($genreCounts)),
                datasets: [{
                    data: @json(array_values($genreCounts)),
                    backgroundColor: ['#ef4444', '#f97316', '#8b5cf6', '#06b6d4', '#10b981'],
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
