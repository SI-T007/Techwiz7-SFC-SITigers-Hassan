@extends('layouts.app')

@section('title', $event->title . ' — Fan Hub Plus Events')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Hero Header Banner -->
    <div class="position-relative rounded-4 overflow-hidden mb-4" style="min-height: 340px; background: rgba(0,0,0,0.6);">
        <img src="{{ $event->cover_image_url }}" 
             alt="{{ $event->title }}" 
             class="w-100 h-100 position-absolute top-0 start-0 object-fit-cover opacity-50"
             style="filter: blur(2px);">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(15,23,42,0.3) 0%, rgba(15,23,42,0.95) 100%);"></div>

        <div class="position-relative p-4 p-md-5 d-flex flex-column justify-content-end h-100 text-light" style="min-height: 340px;">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="fh-badge fh-badge-purple">
                    <i class="bi bi-tag-fill me-1"></i> {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                </span>
                @if($event->category)
                    <a href="{{ route('fandoms.show', $event->category->slug) }}" class="badge bg-secondary bg-opacity-75 text-light text-decoration-none">
                        {{ $event->category->name }}
                    </a>
                @endif

                @if($event->isCancelled())
                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Cancelled</span>
                @elseif($event->isOngoing())
                    <span class="badge bg-success pulse-dot"><i class="bi bi-broadcast me-1"></i> Ongoing Now</span>
                @elseif($event->isCompleted())
                    <span class="badge bg-secondary"><i class="bi bi-check2-circle me-1"></i> Completed</span>
                @else
                    <span class="badge bg-primary"><i class="bi bi-calendar3 me-1"></i> Upcoming Event</span>
                @endif
            </div>

            <h1 class="display-6 fw-bold text-white mb-2">{{ $event->title }}</h1>

            <div class="d-flex flex-wrap align-items-center gap-3 text-light small mb-3">
                <span>
                    <i class="bi bi-calendar-event text-info me-1"></i> 
                    {{ $event->starts_at->format('l, F d, Y') }}
                    @if($event->ends_at && $event->ends_at->format('Y-m-d') !== $event->starts_at->format('Y-m-d'))
                        &ndash; {{ $event->ends_at->format('l, F d, Y') }}
                    @endif
                </span>
                <span>
                    <i class="bi bi-clock text-info me-1"></i> 
                    {{ $event->starts_at->format('H:i') }}
                    @if($event->ends_at) &ndash; {{ $event->ends_at->format('H:i') }} @endif
                </span>
                <span>
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $event->location_display }}
                </span>
            </div>

            <!-- Action Buttons Row -->
            <div class="d-flex flex-wrap gap-2 pt-2">
                @if($event->ticket_url)
                    <a href="{{ $event->ticket_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-success px-4">
                        <i class="bi bi-ticket-perforated-fill me-1"></i> Official Tickets & Registration
                    </a>
                @endif

                <a href="{{ route('events.ics', $event) }}" class="btn btn-outline-light">
                    <i class="bi bi-calendar-plus me-1"></i> Add to Calendar (.ics)
                </a>

                @auth
                    @php
                        $isBookmarked = auth()->user()->bookmarks()
                            ->where('bookmarkable_type', \App\Models\Event::class)
                            ->where('bookmarkable_id', $event->id)
                            ->exists();
                    @endphp
                    <button type="button" 
                            class="btn {{ $isBookmarked ? 'btn-warning' : 'btn-outline-warning' }}" 
                            id="btnBookmarkEvent"
                            onclick="toggleEventBookmark({{ $event->id }});">
                        <i class="bi {{ $isBookmarked ? 'bi-bookmark-star-fill' : 'bi-bookmark-star' }} me-1"></i>
                        <span id="bookmarkText">{{ $isBookmarked ? 'Bookmarked' : 'Save Event' }}</span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-warning">
                        <i class="bi bi-bookmark-star me-1"></i> Sign In to Save
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Content & Sidebar -->
    <div class="row g-4">
        <!-- Left: Event Information & Description -->
        <div class="col-lg-8">
            <div class="fh-card p-4 mb-4">
                <h2 class="h5 fw-bold text-light mb-3">
                    <i class="bi bi-info-circle me-2 text-info"></i> Event Overview & Schedule
                </h2>
                <div class="text-light fs-6" style="white-space: pre-wrap; line-height: 1.7;">{{ $event->description ?? 'No detailed description provided for this fandom gathering.' }}</div>
            </div>

            <!-- Map View Section -->
            @if($event->latitude && $event->longitude)
                <div class="fh-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold text-light mb-0">
                            <i class="bi bi-map me-2 text-danger"></i> Venue Location & Navigation
                        </h2>
                        <span class="small text-muted">
                            Coordinates: {{ number_format($event->latitude, 4) }}, {{ number_format($event->longitude, 4) }}
                        </span>
                    </div>

                    <!-- Leaflet OpenStreetMap Container -->
                    <div id="eventMap" class="rounded-3 border border-secondary" style="height: 320px; z-index: 1;"></div>
                    <div class="small text-muted mt-2 d-flex justify-content-between">
                        <span><i class="bi bi-building me-1"></i> {{ $event->venue_name ?? 'Convention Hall' }} &bull; {{ $event->address }}</span>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $event->latitude }},{{ $event->longitude }}" target="_blank" rel="noopener noreferrer" class="text-info text-decoration-none">
                            Open in Google Maps <i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </div>
                </div>

                <!-- Leaflet JS & CSS (Free OpenStreetMap Integration) -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const lat = {{ $event->latitude }};
                    const lng = {{ $event->longitude }};
                    const map = L.map('eventMap').setView([lat, lng], 14);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    const marker = L.marker([lat, lng]).addTo(map);
                    marker.bindPopup("<b>{{ addslashes($event->title) }}</b><br>{{ addslashes($event->venue_name ?? '') }}<br>{{ addslashes($event->city ?? '') }}").openPopup();
                });
                </script>
            @elseif(!$event->is_online)
                <div class="fh-card p-4 mb-4">
                    <h2 class="h5 fw-bold text-light mb-2">
                        <i class="bi bi-pin-map text-warning me-2"></i> Venue Information
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $event->venue_name ?? 'Venue details to be announced.' }} &bull; {{ $event->city }}, {{ $event->country }}.
                    </p>
                </div>
            @endif
        </div>

        <!-- Right: Event Quick Specs & Related -->
        <div class="col-lg-4">
            <!-- Event Specifications Card -->
            <div class="fh-card p-4 mb-4">
                <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">Event Metadata</h3>
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Fandom Universe</small>
                        <span class="fw-semibold text-info">{{ $event->category?->name ?? 'Multi-Fandom' }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Format / Style</small>
                        <span class="text-capitalize">{{ str_replace('_', ' ', $event->event_type) }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">City & Country</small>
                        <span>{{ $event->city ?? 'TBA' }}, {{ $event->country ?? '' }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Venue Name</small>
                        <span>{{ $event->venue_name ?? 'Announced on official site' }}</span>
                    </li>
                    <li class="list-group-item bg-transparent text-light border-secondary px-0 py-2">
                        <small class="text-secondary d-block">Format Classification</small>
                        <span>{{ $event->is_online ? 'Virtual / Livestream Gathering' : 'In-Person Convention' }}</span>
                    </li>
                </ul>
            </div>

            <!-- Related Events in Category -->
            @if($relatedEvents->isNotEmpty())
                <div class="fh-card p-4">
                    <h3 class="h6 fw-bold text-light mb-3 text-uppercase" style="letter-spacing: 0.05em;">Related Gatherings</h3>
                    <div class="d-flex flex-column gap-3">
                        @foreach($relatedEvents as $rel)
                            <div class="d-flex gap-3 align-items-center border-bottom border-secondary border-opacity-25 pb-2">
                                <div class="rounded overflow-hidden bg-dark flex-shrink-0" style="width: 50px; height: 50px;">
                                    <img src="{{ $rel->cover_image_url }}" alt="{{ $rel->title }}" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="flex-grow-1 text-truncate">
                                    <a href="{{ route('events.show', $rel->slug) }}" class="text-light text-decoration-none fw-semibold small text-truncate d-block hover-primary">
                                        {{ $rel->title }}
                                    </a>
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        {{ $rel->starts_at->format('M d, Y') }} &bull; {{ $rel->city }}
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

@auth
<script>
function toggleEventBookmark(eventId) {
    const btn = document.getElementById('btnBookmarkEvent');
    const text = document.getElementById('bookmarkText');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch("{{ route('bookmarks.toggle') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            bookmarkable_type: "event",
            bookmarkable_id: eventId
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
                text.innerText = 'Save Event';
            }
        }
    })
    .catch(err => console.error("Error toggling bookmark:", err));
}
</script>
@endauth
@endsection
