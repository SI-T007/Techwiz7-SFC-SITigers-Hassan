@extends('layouts.app')

@section('title', $release->title . ' — Release Drops — Fan Hub Plus')

@section('content')
<div class="container py-4">
    
    <x-breadcrumb :items="$breadcrumbs" />

   
    <div class="position-relative rounded-4 overflow-hidden mb-4" style="min-height: 340px; background: rgba(0,0,0,0.6);">
        <img src="{{ $release->cover_image_url }}" 
             alt="{{ $release->title }}" 
             class="w-100 h-100 position-absolute top-0 start-0 object-fit-cover opacity-50"
             style="filter: blur(2px);">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0.3) 0%, rgba(15,23,42,0.95) 100%);"></div>

        <div class="position-relative p-4 p-md-5 d-flex flex-column justify-content-end h-100 text-light" style="min-height: 340px;">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="fh-badge fh-badge-primary">
                    {{ strtoupper($release->type) }}
                </span>
                @if($release->category)
                    <a href="{{ route('fandoms.show', $release->category->slug) }}" class="badge bg-secondary bg-opacity-75 text-light text-decoration-none">
                        {{ $release->category->name }}
                    </a>
                @endif
                @if($release->isReleased())
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Available Now</span>
                @else
                    <span class="badge bg-warning text-dark fw-bold"><i class="bi bi-hourglass-split me-1"></i> Upcoming Drop</span>
                @endif
            </div>

            <h1 class="display-6 fw-bold text-white mb-2">{{ $release->title }}</h1>

            <div class="d-flex flex-wrap align-items-center gap-3 text-light small mb-3">
                <span>
                    <i class="bi bi-calendar-event text-info me-1"></i> Release Date: 
                    <strong>{{ $release->release_date->format('F d, Y') }}</strong>
                </span>
                <span>
                    <i class="bi bi-cpu text-info me-1"></i> {{ $release->platform ?? 'All Platforms' }}
                </span>
                <span>
                    <i class="bi bi-building text-info me-1"></i> {{ $release->developer_publisher ?? 'Publisher TBA' }}
                </span>
            </div>

            <!-- Tags Bar -->
            @if(!empty($release->tags))
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach($release->tags as $tag)
                        <span class="badge bg-info bg-opacity-25 text-info">
                            <i class="bi bi-tag-fill me-1"></i>{{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Action Buttons Row -->
            <div class="d-flex flex-wrap gap-2 pt-1">
                @if($release->external_url)
                    <a href="{{ $release->external_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary px-4">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Official Product / Store Page
                    </a>
                @endif

                @auth
                    @php
                        $isBookmarked = auth()->user()->bookmarks()
                            ->where('bookmarkable_type', \App\Models\Release::class)
                            ->where('bookmarkable_id', $release->id)
                            ->exists();
                    @endphp
                    <button type="button" 
                            class="btn {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }}" 
                            id="btnBookmarkRelease"
                            onclick="toggleReleaseBookmark({{ $release->id }});">
                        <i class="bi {{ $isBookmarked ? 'bi-bookmark-star-fill' : 'bi-bookmark-star' }} me-1"></i>
                        <span id="bookmarkReleaseText">{{ $isBookmarked ? 'Bookmarked' : 'Add to Wishlist' }}</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-warning">
                        <i class="bi bi-bookmark-star me-1"></i> Sign In to Bookmark
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Grid: Info + Countdown + Related -->
    <div class="row g-4">
        <!-- Left: Release Details & Countdown -->
        <div class="col-lg-8">
            <!-- Visual Live Countdown Timer (When future release date exists) -->
            @if(!$release->isReleased())
                <div class="fh-card p-4 mb-4 text-center">
                    <span class="text-uppercase small text-info fw-bold" style="letter-spacing: 0.1em;">Countdown to Premiere</span>
                    <h2 class="h5 fw-bold text-light mb-3">Target Launch Window</h2>

                    <!-- Live Countdown Grid -->
                    <div class="d-flex justify-content-center gap-2 gap-md-4 my-2" id="countdownContainer" data-date="{{ $release->release_date->format('Y-m-d') }}">
                        <div class="p-3 rounded bg-dark border border-secondary text-center" style="min-width: 75px;">
                            <div class="h3 fw-bold text-primary mb-0" id="cdDays">{{ $release->days_until_release }}</div>
                            <div class="text-muted small text-uppercase" style="font-size: 0.7rem;">Days</div>
                        </div>
                        <div class="p-3 rounded bg-dark border border-secondary text-center" style="min-width: 75px;">
                            <div class="h3 fw-bold text-primary mb-0" id="cdHours">--</div>
                            <div class="text-muted small text-uppercase" style="font-size: 0.7rem;">Hours</div>
                        </div>
                        <div class="p-3 rounded bg-dark border border-secondary text-center" style="min-width: 75px;">
                            <div class="h3 fw-bold text-primary mb-0" id="cdMins">--</div>
                            <div class="text-muted small text-uppercase" style="font-size: 0.7rem;">Mins</div>
                        </div>
                        <div class="p-3 rounded bg-dark border border-secondary text-center" style="min-width: 75px;">
                            <div class="h3 fw-bold text-primary mb-0" id="cdSecs">--</div>
                            <div class="text-muted small text-uppercase" style="font-size: 0.7rem;">Secs</div>
                        </div>
                    </div>

                    <!-- Non-JS fallback message -->
                    <noscript>
                        <p class="text-warning small mt-2">
                            <i class="bi bi-clock me-1"></i> Releasing on {{ $release->release_date->format('F d, Y') }}.
                        </p>
                    </noscript>
                </div>
            @endif

            <!-- Description -->
            <div class="fh-card p-4 mb-4">
                <h2 class="h5 fw-bold text-light mb-3">
                    <i class="bi bi-info-circle me-2 text-info"></i> Release Synopsis & Features
                </h2>
                <div class="text-light fs-6" style="white-space: pre-wrap; line-height: 1.7;">{{ $release->description ?? 'Official description and drop details will be updated as distributor announcements arrive.' }}</div>
            </div>

            <!-- Related Canon Lore Articles -->
            @if($relatedLore->isNotEmpty())
                <div class="fh-card p-4 mb-4">
                    <h2 class="h5 fw-bold text-light mb-3">
                        <i class="bi bi-journal-richtext me-2 text-warning"></i> Canonical Lore in this Fandom
                    </h2>
                    <div class="row g-3">
                        @foreach($relatedLore as $lore)
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-dark border border-secondary h-100 d-flex flex-column">
                                    <h3 class="h6 fw-bold mb-1">
                                        <a href="{{ route('explore.show', $lore->slug) }}" class="text-light text-decoration-none hover-primary">
                                            {{ $lore->title }}
                                        </a>
                                    </h3>
                                    <span class="small text-muted mb-2 text-capitalize">{{ $lore->type }}</span>
                                    <p class="small text-muted flex-grow-1" style="font-size: 0.8rem;">
                                        {{ Str::limit($lore->summary ?? $lore->body, 70) }}
                                    </p>
                                    <a href="{{ route('explore.show', $lore->slug) }}" class="small text-info text-decoration-none mt-auto">
                                        Read Canon <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Release Specifications & Related Drops -->
        <div class="col-lg-4">
            <!-- Specifications Card -->
            <div class="fh-card p-4 mb-4">
                <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">Product Specifications</h3>
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Universe Realm</small>
                        <span class="fw-semibold text-info">{{ $release->category?->name ?? 'General' }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Media Format</small>
                        <span class="text-uppercase">{{ $release->type }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Platform / Medium</small>
                        <span>{{ $release->platform ?? 'Standard Edition' }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Publisher / Studio</small>
                        <span>{{ $release->developer_publisher ?? 'Licensed Production' }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Release Window</small>
                        <span>{{ $release->release_date->format('M d, Y') }}</span>
                    </li>
                </ul>
            </div>

            <!-- Related Releases -->
            @if($relatedReleases->isNotEmpty())
                <div class="fh-card p-4">
                    <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">More Upcoming in {{ $release->category?->name }}</h3>
                    <div class="d-flex flex-column gap-3">
                        @foreach($relatedReleases as $rel)
                            <div class="d-flex gap-3 align-items-center border-bottom border-secondary border-opacity-25 pb-2">
                                <div class="rounded overflow-hidden bg-dark flex-shrink-0" style="width: 50px; height: 50px;">
                                    <img src="{{ $rel->cover_image_url }}" alt="{{ $rel->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="flex-grow-1 text-truncate">
                                    <a href="{{ route('releases.show', $rel->slug) }}" class="text-light text-decoration-none fw-semibold small text-truncate d-block hover-primary">
                                        {{ $rel->title }}
                                    </a>
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        {{ $rel->release_date->format('M d, Y') }} &bull; {{ strtoupper($rel->type) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Countdown Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('countdownContainer');
    if (!container) return;

    const targetDateStr = container.getAttribute('data-date');
    const targetDate = new Date(targetDateStr + 'T00:00:00');

    function updateCountdown() {
        const now = new Date();
        const diff = targetDate - now;

        if (diff <= 0) {
            document.getElementById('cdDays').innerText = '0';
            document.getElementById('cdHours').innerText = '0';
            document.getElementById('cdMins').innerText = '0';
            document.getElementById('cdSecs').innerText = '0';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const mins = Math.floor((diff / 1000 / 60) % 60);
        const secs = Math.floor((diff / 1000) % 60);

        document.getElementById('cdDays').innerText = days;
        document.getElementById('cdHours').innerText = String(hours).padStart(2, '0');
        document.getElementById('cdMins').innerText = String(mins).padStart(2, '0');
        document.getElementById('cdSecs').innerText = String(secs).padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});

@auth
function toggleReleaseBookmark(releaseId) {
    const btn = document.getElementById('btnBookmarkRelease');
    const text = document.getElementById('bookmarkReleaseText');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch("{{ route('bookmarks.toggle') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            bookmarkable_type: "release",
            bookmarkable_id: releaseId
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (data.is_bookmarked) {
                btn.classList.remove('btn-outline-warning');
                btn.classList.add('btn-warning');
                btn.querySelector('i').className = 'bi bi-bookmark-star-fill me-1';
                text.innerText = 'Bookmarked';
            } else {
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-outline-warning');
                btn.querySelector('i').className = 'bi bi-bookmark-star me-1';
                text.innerText = 'Add to Wishlist';
            }
        }
    })
    .catch(err => console.error("Error toggling bookmark:", err));
}
@endauth
</script>
@endsection
