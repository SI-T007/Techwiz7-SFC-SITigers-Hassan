@extends('layouts.app')

@section('title', 'Explore the Universe — Fan Hub Plus')

@section('content')
<div class="container py-4">
   
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Discovery Hub</span>
            <h1 class="h2 fw-bold text-light mb-1">Explore Lore & Content</h1>
            <p class="text-muted small mb-0">Browse lore articles, character analyses, official trailers, and canon archives across every fandom</p>
        </div>
        <span class="badge bg-secondary bg-opacity-25 text-light px-3 py-2 fs-6">
            {{ $totalResultsCount ?? $contents->total() }} Entries Found
        </span>
    </div>

   
    <div class="fh-card p-3 p-md-4 mb-4">
        <form method="GET" action="{{ route('explore') }}" class="row g-2 align-items-center">
           
            <div class="col-lg-4 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-subtle text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search by title, lore, description..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Fandom / Category Filter -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="category" class="form-select">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Genre Filter -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="genre" class="form-select">
                    <option value="">All Genres</option>
                    @foreach($genres as $genre)
                        <option value="{{ $genre->slug }}" {{ request('genre') === $genre->slug ? 'selected' : '' }}>
                            {{ $genre->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Content Format / Type -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="type" class="form-select">
                    <option value="">All Formats</option>
                    <option value="article" {{ request('type') === 'article' ? 'selected' : '' }}>Article</option>
                    <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
                    <option value="trailer" {{ request('type') === 'trailer' ? 'selected' : '' }}>Trailer</option>
                    <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>Audio</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Image</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-6 col-md-3 col-lg-2">
                <select name="sort" class="form-select">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest Released</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="alpha" {{ request('sort') === 'alpha' ? 'selected' : '' }}>Alphabetical (A-Z)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 col-lg-auto ms-auto d-flex gap-2 mt-2 mt-lg-0">
                <button type="submit" class="fh-btn-primary flex-fill px-4">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('explore') }}" class="fh-btn-ghost" title="Reset all filters">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>

        <!-- Active Filter Chips -->
        @if(!empty($activeFilters))
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top border-subtle">
                <span class="small text-muted me-1">Active filters:</span>
                @foreach($activeFilters as $key => $filter)
                    @php
                        $queryParams = request()->except($filter['param'], 'page');
                        $removeUrl = route('explore', $queryParams);
                    @endphp
                    <span class="badge bg-primary bg-opacity-25 text-light border border-primary border-opacity-50 d-inline-flex align-items-center gap-1 py-1 px-2">
                        {{ $filter['label'] }}
                        <a href="{{ $removeUrl }}" class="text-light text-decoration-none ms-1" title="Remove filter">&times;</a>
                    </span>
                @endforeach
                <a href="{{ route('explore') }}" class="small text-danger text-decoration-none ms-2">Clear all</a>
            </div>
        @endif
    </div>

    {{-- Matched Releases Section --}}
    @if(isset($matchedReleases) && $matchedReleases->isNotEmpty())
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="h5 fw-bold text-light mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar2-event-fill text-warning"></i> Matching Releases & Premieres
                    <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 small">{{ $matchedReleases->count() }}</span>
                </h3>
                <a href="{{ route('releases.index', ['search' => request('search')]) }}" class="small text-danger text-decoration-none fw-semibold">
                    View Full Calendar &rarr;
                </a>
            </div>
            <div class="row g-3">
                @foreach($matchedReleases as $rel)
                    <div class="col-md-6 col-lg-4">
                        <div class="card bg-dark border-subtle h-100 overflow-hidden shadow-sm hover-elevate">
                            <div class="position-relative" style="height: 175px;">
                                <img src="{{ $rel->cover_image_url ?: 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80' }}"
                                     alt="{{ $rel->title }}"
                                     class="w-100 h-100"
                                     style="object-fit: cover;">
                                <span class="badge position-absolute top-0 end-0 m-2 px-2 py-1 text-uppercase fw-bold" style="background-color: #f59e0b; font-size: 0.7rem;">
                                    {{ $rel->type_label ?? 'Drop' }}
                                </span>
                                @if($rel->category)
                                    <span class="badge bg-dark bg-opacity-75 text-light position-absolute bottom-0 start-0 m-2 border border-secondary" style="font-size: 0.72rem;">
                                        {{ $rel->category->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column p-3">
                                <h5 class="card-title text-light fw-bold fs-6 mb-1 text-truncate" title="{{ $rel->title }}">
                                    {{ $rel->title }}
                                </h5>
                                <p class="card-text text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $rel->description }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-subtle">
                                    <span class="small text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1 text-info"></i>{{ $rel->release_date?->format('M d, Y') ?? 'Coming Soon' }}
                                    </span>
                                    <a href="{{ route('releases.show', $rel->slug) }}" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size: 0.75rem;">
                                        Details &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Matched Characters Section --}}
    @if(isset($matchedCharacters) && $matchedCharacters->isNotEmpty())
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="h5 fw-bold text-light mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-info"></i> Matching Characters
                    <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 small">{{ $matchedCharacters->count() }}</span>
                </h3>
                <a href="{{ route('characters.index', ['search' => request('search')]) }}" class="small text-danger text-decoration-none fw-semibold">
                    Character Archives &rarr;
                </a>
            </div>
            <div class="row g-3">
                @foreach($matchedCharacters as $char)
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card bg-dark border-subtle h-100 text-center p-3 hover-elevate">
                            <img src="{{ $char->avatar_url }}" alt="{{ $char->name }}" class="rounded-circle mx-auto mb-2 border border-info border-2" style="width: 68px; height: 68px; object-fit: cover;">
                            <h6 class="text-light fw-bold mb-1 text-truncate small" title="{{ $char->name }}">{{ $char->name }}</h6>
                            <span class="text-muted text-truncate d-block" style="font-size: 0.7rem;">{{ $char->category?->name ?? 'Canon Lore' }}</span>
                            <a href="{{ route('characters.show', $char->slug) }}" class="btn btn-sm btn-outline-info mt-2 py-0 px-2 w-100" style="font-size: 0.7rem;">Profile</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Matched Media Trailers Section --}}
    @if(isset($matchedMedia) && $matchedMedia->isNotEmpty())
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="h5 fw-bold text-light mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-play-circle-fill text-danger"></i> Matching Trailers & OSTs
                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50 small">{{ $matchedMedia->count() }}</span>
                </h3>
                <a href="{{ route('multimedia.index', ['search' => request('search')]) }}" class="small text-danger text-decoration-none fw-semibold">
                    Multimedia Hub &rarr;
                </a>
            </div>
            <div class="row g-3">
                @foreach($matchedMedia as $m)
                    <div class="col-md-6 col-lg-4">
                        <div class="card bg-dark border-subtle h-100 overflow-hidden shadow-sm hover-elevate">
                            <div class="position-relative" style="height: 160px;">
                                <img src="{{ $m->thumbnail_url }}" alt="{{ $m->title }}" class="w-100 h-100" style="object-fit: cover;" onerror="window.handleImageFallback ? window.handleImageFallback(this) : null">
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2" style="font-size: 0.68rem;">{{ $m->type_label }}</span>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="text-light fw-bold text-truncate mb-1" title="{{ $m->title }}">{{ $m->title }}</h6>
                                <p class="text-muted small mb-2 text-truncate">{{ $m->category?->name ?? 'Universe' }}</p>
                                <a href="{{ route('multimedia.show', $m->id) }}" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold" style="font-size: 0.75rem;">Watch Video &rarr;</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Content Grid Header -->
    @if(isset($matchedReleases) && $matchedReleases->isNotEmpty() || isset($matchedCharacters) && $matchedCharacters->isNotEmpty())
        <h3 class="h5 fw-bold text-light mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-journal-text text-primary"></i> Universe Lore Articles & Catalog
        </h3>
    @endif

    <!-- Content Grid -->
    <div class="row g-4">
        @forelse($contents as $content)
            <div class="col-md-6 col-lg-4">
                <x-content-card :content="$content" />
            </div>
        @empty
            <div class="col-12">
                @if((isset($matchedReleases) && $matchedReleases->isNotEmpty()) || (isset($matchedCharacters) && $matchedCharacters->isNotEmpty()) || (isset($matchedMedia) && $matchedMedia->isNotEmpty()))
                    <div class="alert alert-dark border-subtle text-muted text-center py-4 my-2">
                        <i class="bi bi-info-circle fs-4 d-block mb-2 text-info"></i>
                        No additional lore articles matched this exact query, but matching drops, characters, or media are listed above!
                    </div>
                @else
                    <x-empty-state 
                        icon="bi-journal-x" 
                        title="No Universe Content Found" 
                        message="No entries matched your specified criteria. Try clearing some filters or searching for alternative fandom keywords."
                        actionText="Reset All Filters"
                        :actionUrl="route('explore')"
                    />
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($contents->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $contents->links() }}
        </div>
    @endif
</div>
@endsection
