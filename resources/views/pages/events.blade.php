@extends('layouts.app')

@section('title', 'Fandom Events & Conventions — Fan Hub Plus')

@section('content')
<div class="container py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Global Fandom & Event Calendar</span>
                <span class="badge bg-secondary bg-opacity-25 text-light">{{ $events->total() }} Events Listed</span>
            </div>
            <h1 class="h2 fw-bold text-light mb-1">Fandom Gatherings, Expos & Summits</h1>
            <p class="text-muted small mb-0">Discover comic conventions, anime expos, gaming showcases, and cosplay gatherings worldwide</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Location-Aware Near Me Button -->
            <button type="button" class="btn btn-outline-info btn-sm" id="btnNearMe" onclick="requestUserLocation();">
                <i class="bi bi-crosshair me-1"></i> Events Near Me
            </button>
            <!-- Calendar View Link -->
            <a href="{{ route('events.calendar') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-calendar3 me-1"></i> Monthly Calendar
            </a>
        </div>
    </div>

    <!-- Active Location Banner (if GPS nearby active) -->
    @if($isNearbySearch)
        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 d-flex justify-content-between align-items-center py-2 px-3 mb-4 rounded">
            <span class="small text-info">
                <i class="bi bi-geo-alt-fill me-1"></i> Showing fandom gatherings sorted by distance from your current location coordinates.
            </span>
            <a href="{{ route('events.index') }}" class="btn btn-sm btn-link text-info text-decoration-none p-0 small">
                <i class="bi bi-x-circle me-1"></i> Clear GPS Filter
            </a>
        </div>
    @endif

    <!-- Status Filter Navigation Pills -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('events.index', array_merge(request()->except('status', 'page'), ['status' => 'upcoming'])) }}" 
           class="btn btn-sm {{ $status === 'upcoming' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            <i class="bi bi-calendar-check me-1"></i> Upcoming
        </a>
        <a href="{{ route('events.index', array_merge(request()->except('status', 'page'), ['status' => 'ongoing'])) }}" 
           class="btn btn-sm {{ $status === 'ongoing' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            <i class="bi bi-broadcast me-1"></i> Ongoing Now
        </a>
        <a href="{{ route('events.index', array_merge(request()->except('status', 'page'), ['status' => 'completed'])) }}" 
           class="btn btn-sm {{ $status === 'completed' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            <i class="bi bi-clock-history me-1"></i> Past Events
        </a>
        <a href="{{ route('events.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
           class="btn btn-sm {{ $status === 'all' ? 'fh-btn-primary' : 'fh-btn-ghost' }}">
            All Events
        </a>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="fh-card p-3 p-md-4 mb-4">
        <form method="GET" action="{{ route('events.index') }}" id="eventsFilterForm" class="row g-2 align-items-center">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('near_lat') && request('near_lng'))
                <input type="hidden" name="near_lat" value="{{ request('near_lat') }}">
                <input type="hidden" name="near_lng" value="{{ request('near_lng') }}">
            @endif

            <!-- Search Field -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm bg-dark border-secondary text-light" 
                           placeholder="Search title, venue..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Fandom / Category Filter -->
            <div class="col-6 col-md-4 col-lg-2">
                <select name="category" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Fandoms</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- City Filter -->
            <div class="col-6 col-md-4 col-lg-2">
                <select name="city" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                            {{ $city }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Event Type Filter -->
            <div class="col-6 col-md-4 col-lg-2">
                <select name="type" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Types</option>
                    <option value="convention" {{ request('type') === 'convention' ? 'selected' : '' }}>Convention</option>
                    <option value="cosplay_meetup" {{ request('type') === 'cosplay_meetup' ? 'selected' : '' }}>Cosplay Meetup</option>
                    <option value="screening" {{ request('type') === 'screening' ? 'selected' : '' }}>Screening</option>
                    <option value="concert" {{ request('type') === 'concert' ? 'selected' : '' }}>Concert</option>
                    <option value="livestream" {{ request('type') === 'livestream' ? 'selected' : '' }}>Livestream</option>
                    <option value="release_event" {{ request('type') === 'release_event' ? 'selected' : '' }}>Release Event</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-6 col-md-4 col-lg-2">
                <select name="sort" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="upcoming" {{ request('sort') === 'upcoming' ? 'selected' : '' }}>Upcoming First</option>
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest Added</option>
                    <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Alphabetical (A-Z)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-12 col-lg-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill" title="Apply Filters">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Events Grid -->
    <div class="row g-4" id="eventsContainer">
        @forelse($events as $event)
            <div class="col-md-6 col-lg-4">
                <x-event-card :event="$event" />
            </div>
        @empty
            <div class="col-12">
                <x-empty-state 
                    icon="bi-geo-alt" 
                    title="No Events Found" 
                    message="No gatherings or conventions match your current location or filter criteria."
                    actionText="View All Upcoming Events"
                    :actionUrl="route('events.index')"
                />
            </div>
        @endforelse
    </div>

    @if($events->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $events->links() }}
        </div>
    @endif
</div>

<!-- Geolocation Script with Graceful Error Handling -->
<script>
function requestUserLocation() {
    const btn = document.getElementById('btnNearMe');
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your current browser.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Locating...';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const url = new URL(window.location.href);
            url.searchParams.set('near_lat', lat.toFixed(6));
            url.searchParams.set('near_lng', lng.toFixed(6));
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        function(error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-crosshair me-1"></i> Events Near Me';
            let msg = 'Unable to retrieve location.';
            if (error.code === error.PERMISSION_DENIED) {
                msg = 'Location permission was denied. You can still filter events manually by selecting a city from the filter dropdown.';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                msg = 'Location information is currently unavailable.';
            } else if (error.code === error.TIMEOUT) {
                msg = 'Location request timed out. Please try again.';
            }
            alert(msg);
        },
        { timeout: 8000, enableHighAccuracy: false }
    );
}
</script>
@endsection
