@extends('layouts.app')

@section('title', 'Multimedia Library — HD Trailers, Songs & Cinematics — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page Header & Overview -->
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="small text-danger fw-bold text-uppercase" style="letter-spacing: 0.08em;">
                    <i class="bi bi-youtube me-1"></i> Entertainment Video Library
                </span>
                <span class="badge bg-secondary bg-opacity-25 text-light">{{ $mediaItems->total() }} HD Videos</span>
            </div>
            <h1 class="h2 fw-bold text-light mb-1">Trailers, Songs & Cinematics</h1>
            <p class="text-muted small mb-0">Discover official 4K trailers, anime openings, gaming cinematics, and original soundtracks streamed via YouTube</p>
        </div>
        <div class="d-flex gap-2">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.media.create') }}" class="fh-btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add YouTube Media
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Featured Hero Trailer Spotlight (Only when not searching or applying specific filters) -->
    @if($featuredMedia->isNotEmpty() && !request()->hasAny(['search', 'type', 'media_type', 'category', 'quality', 'min_rating']))
        @php
            $hero = $featuredMedia->first();
        @endphp
        <div class="fh-card fh-card-glow p-0 mb-5 position-relative overflow-hidden rounded-4 border border-subtle" style="background: #0a0a0c;">
            <div class="row g-0 align-items-center">
                <!-- Video / Thumbnail Frame -->
                <div class="col-lg-7 position-relative" style="min-height: 360px;">
                    <img src="{{ $hero->thumbnail_url }}" alt="{{ $hero->title }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0" style="filter: brightness(0.85);" loading="eager">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, transparent 50%, #0a0a0c 100%), linear-gradient(180deg, rgba(10,10,12,0.2) 0%, rgba(10,10,12,0.85) 100%);"></div>
                    
                    <!-- Play Overlay Button -->
                    <a href="{{ route('multimedia.show', $hero->id) }}" class="position-absolute top-50 start-50 translate-middle text-decoration-none z-2" aria-label="Play Trailer">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 72px; height: 72px; background: rgba(229, 9, 20, 0.95); box-shadow: 0 0 32px rgba(229, 9, 20, 0.8);">
                            <i class="bi bi-play-fill fs-1 text-white" style="margin-left: 4px;"></i>
                        </div>
                    </a>

                    <div class="position-absolute top-0 start-0 p-3 z-2 d-flex gap-2">
                        <span class="badge bg-danger text-white fw-bold px-3 py-2">
                            ⭐ FEATURED SPOTLIGHT
                        </span>
                        <span class="badge bg-black bg-opacity-75 text-light px-2 py-1">
                            {{ $hero->quality }}
                        </span>
                    </div>
                </div>

                <!-- Hero Info Details -->
                <div class="col-lg-5 p-4 p-md-5">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="fh-badge fh-badge-primary">
                            {{ $hero->media_type_label }}
                        </span>
                        @if($hero->category)
                            <a href="{{ route('fandoms.show', $hero->category->slug) }}" class="fh-badge fh-badge-cyan text-decoration-none">
                                {{ $hero->category->name }}
                            </a>
                        @endif
                    </div>

                    <h2 class="h3 fw-bold text-light mb-2">
                        <a href="{{ route('multimedia.show', $hero->id) }}" class="text-light text-decoration-none hover-primary">
                            {{ $hero->title }}
                        </a>
                    </h2>

                    @if($hero->ratings_avg)
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="text-warning small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= round($hero->ratings_avg) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                            </div>
                            <span class="text-light fw-bold small">{{ number_format($hero->ratings_avg, 1) }}</span>
                            <span class="text-muted small">({{ $hero->ratings_count }} fan reviews)</span>
                        </div>
                    @endif

                    <p class="text-secondary small mb-4 line-clamp-3">
                        {{ $hero->description ?: 'Stream this official high-definition fandom video in the Fan Hub Plus multimedia theater.' }}
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('multimedia.show', $hero->id) }}" class="fh-btn-primary">
                            <i class="bi bi-play-circle-fill me-1"></i> Watch Video &rarr;
                        </a>
                        @if($hero->external_url)
                            <a href="{{ $hero->external_url }}" target="_blank" rel="noopener noreferrer" class="fh-btn-ghost">
                                <i class="bi bi-youtube text-danger me-1"></i> YouTube ↗
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Discovery Search & Multi-Filter Toolbar -->
    <div class="fh-card p-3 p-md-4 mb-5">
        <form method="GET" action="{{ route('multimedia.index') }}" class="row g-2 align-items-center">
            <!-- Keyword Search -->
            <div class="col-lg-3 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-subtle text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search title, song, trailer..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Fandom / Category -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Media Type -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="media_type" class="form-select form-select-sm">
                    <option value="">All Media Types</option>
                    @foreach(\App\Models\Media::MEDIA_TYPES as $k => $lbl)
                        <option value="{{ $k }}" {{ request('media_type') === $k ? 'selected' : '' }}>
                            {{ $lbl }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Quality -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="quality" class="form-select form-select-sm">
                    <option value="">All Qualities</option>
                    @foreach(\App\Models\Media::QUALITIES as $q)
                        <option value="{{ $q }}" {{ request('quality') === $q ? 'selected' : '' }}>
                            {{ $q }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Min Rating -->
            <div class="col-6 col-md-3 col-lg-1">
                <select name="min_rating" class="form-select form-select-sm">
                    <option value="">Rating</option>
                    <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>4.5+ ★</option>
                    <option value="4.0" {{ request('min_rating') == '4.0' ? 'selected' : '' }}>4.0+ ★</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="sort" class="form-select form-select-sm">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest Added</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Viewed</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Top Rated ★</option>
                    <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>A-Z Alphabetical</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 col-lg-auto ms-auto d-flex gap-1 mt-2 mt-lg-0">
                <button type="submit" class="fh-btn-primary btn-sm px-3 flex-fill">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('multimedia.index') }}" class="fh-btn-ghost btn-sm" title="Reset All Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Curated Category Rails (When browsing default state) -->
    @if(!request()->hasAny(['search', 'type', 'media_type', 'category', 'quality', 'min_rating']))
        <!-- Rail 1: Trending Videos -->
        @if($trendingVideos->isNotEmpty())
            <section class="fh-rail mb-5">
                <div class="fh-rail-header">
                    <h2 class="fh-rail-title">
                        <span class="fh-rail-accent"></span>
                        🔥 Trending in Multimedia
                    </h2>
                    <a href="{{ route('multimedia.index', ['sort' => 'popular']) }}" class="fh-rail-link">
                        See All Trending <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="fh-slider-wrapper position-relative">
                    <button class="fh-slider-arrow left" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <div class="fh-slider-track">
                        @foreach($trendingVideos as $vid)
                            <div class="fh-slider-item" style="flex: 0 0 calc(100% / 4 - 0.75rem);">
                                <x-media-card :media="$vid" />
                            </div>
                        @endforeach
                    </div>
                    <button class="fh-slider-arrow right" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </section>
        @endif

        <!-- Rail 2: Anime Trailers & Openings -->
        @if($animeTrailers->isNotEmpty())
            <section class="fh-rail mb-5">
                <div class="fh-rail-header">
                    <h2 class="fh-rail-title">
                        <span class="fh-rail-accent"></span>
                        🍥 Anime Trailers & Openings
                    </h2>
                    <a href="{{ route('multimedia.index', ['category' => 'anime']) }}" class="fh-rail-link">
                        All Anime Media <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="fh-slider-wrapper position-relative">
                    <button class="fh-slider-arrow left" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <div class="fh-slider-track">
                        @foreach($animeTrailers as $vid)
                            <div class="fh-slider-item" style="flex: 0 0 calc(100% / 4 - 0.75rem);">
                                <x-media-card :media="$vid" />
                            </div>
                        @endforeach
                    </div>
                    <button class="fh-slider-arrow right" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </section>
        @endif

        <!-- Rail 3: Gaming Trailers & Cinematics -->
        @if($gamingTrailers->isNotEmpty())
            <section class="fh-rail mb-5">
                <div class="fh-rail-header">
                    <h2 class="fh-rail-title">
                        <span class="fh-rail-accent"></span>
                        🎮 Gaming Trailers & Cinematics
                    </h2>
                    <a href="{{ route('multimedia.index', ['category' => 'gaming']) }}" class="fh-rail-link">
                        All Gaming Media <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="fh-slider-wrapper position-relative">
                    <button class="fh-slider-arrow left" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <div class="fh-slider-track">
                        @foreach($gamingTrailers as $vid)
                            <div class="fh-slider-item" style="flex: 0 0 calc(100% / 4 - 0.75rem);">
                                <x-media-card :media="$vid" />
                            </div>
                        @endforeach
                    </div>
                    <button class="fh-slider-arrow right" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </section>
        @endif

        <!-- Rail 4: Movie & TV Trailers -->
        @if($movieTrailers->isNotEmpty())
            <section class="fh-rail mb-5">
                <div class="fh-rail-header">
                    <h2 class="fh-rail-title">
                        <span class="fh-rail-accent"></span>
                        🎬 Movie & TV Theatrical Trailers
                    </h2>
                    <a href="{{ route('multimedia.index', ['category' => 'movies']) }}" class="fh-rail-link">
                        All Movies & TV <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="fh-slider-wrapper position-relative">
                    <button class="fh-slider-arrow left" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <div class="fh-slider-track">
                        @foreach($movieTrailers as $vid)
                            <div class="fh-slider-item" style="flex: 0 0 calc(100% / 4 - 0.75rem);">
                                <x-media-card :media="$vid" />
                            </div>
                        @endforeach
                    </div>
                    <button class="fh-slider-arrow right" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </section>
        @endif

        <!-- Rail 5: Songs, OSTs & Music Videos -->
        @if($musicAndOst->isNotEmpty())
            <section class="fh-rail mb-5">
                <div class="fh-rail-header">
                    <h2 class="fh-rail-title">
                        <span class="fh-rail-accent"></span>
                        🎵 Songs, Music Videos & OSTs
                    </h2>
                    <a href="{{ route('multimedia.index', ['media_type' => 'anime_song']) }}" class="fh-rail-link">
                        All Music & OSTs <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="fh-slider-wrapper position-relative">
                    <button class="fh-slider-arrow left" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                    <div class="fh-slider-track">
                        @foreach($musicAndOst as $vid)
                            <div class="fh-slider-item" style="flex: 0 0 calc(100% / 4 - 0.75rem);">
                                <x-media-card :media="$vid" />
                            </div>
                        @endforeach
                    </div>
                    <button class="fh-slider-arrow right" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
                </div>
            </section>
        @endif
    @endif

    <!-- All Media Video Grid Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-light mb-0">
                <i class="bi bi-grid-3x3-gap text-danger me-2"></i> All Videos & Media ({{ $mediaItems->total() }})
            </h2>
            <span class="small text-muted">Showing {{ $mediaItems->firstItem() ?? 0 }}–{{ $mediaItems->lastItem() ?? 0 }} of {{ $mediaItems->total() }}</span>
        </div>

        <div class="row g-4">
            @forelse($mediaItems as $media)
                <div class="col-sm-6 col-lg-3">
                    <x-media-card :media="$media" />
                </div>
            @empty
                <div class="col-12">
                    <x-empty-state 
                        icon="bi-film" 
                        title="No Multimedia Found" 
                        message="No video assets match your specified query or filters. Try adjusting your search term or clearing filters."
                        actionText="Reset All Filters"
                        :actionUrl="route('multimedia.index')"
                    />
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($mediaItems->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $mediaItems->links() }}
        </div>
    @endif
</div>
@endsection
