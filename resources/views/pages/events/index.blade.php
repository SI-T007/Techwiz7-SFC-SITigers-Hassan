@extends('layouts.app')

@section('title', 'Multiverse Events Map, Calendar & Nearby Conventions — Fan Hub Plus')

@push('styles')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
   
    @keyframes radarSweep {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes radarPulse {
        0% { transform: scale(0.85); opacity: 0.8; }
        50% { transform: scale(1.4); opacity: 0.15; }
        100% { transform: scale(0.85); opacity: 0.8; }
    }
    @keyframes pinBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    .radar-scanner-box {
        position: relative;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 2px solid rgba(6, 182, 212, 0.4);
        background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, rgba(15, 23, 42, 0.6) 70%);
        overflow: hidden;
        margin: 0 auto;
        box-shadow: 0 0 25px rgba(6, 182, 212, 0.25);
    }
    .radar-scanner-box::before {
        content: '';
        position: absolute;
        inset: 20px;
        border-radius: 50%;
        border: 1px dashed rgba(6, 182, 212, 0.3);
    }
    .radar-scanner-box::after {
        content: '';
        position: absolute;
        inset: 45px;
        border-radius: 50%;
        border: 1px solid rgba(6, 182, 212, 0.25);
    }
    .radar-sweep-beam {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 70px;
        height: 70px;
        transform-origin: 0 0;
        background: conic-gradient(from 0deg, rgba(6, 182, 212, 0.6) 0deg, transparent 65deg);
        animation: radarSweep 3.5s linear infinite;
        pointer-events: none;
    }
    .radar-center-blip {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 10px;
        height: 10px;
        background: #06b6d4;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 12px #06b6d4;
    }

    /* Map Styling */
    #eventLeafletMap {
        height: 540px;
        width: 100%;
        border-radius: 1rem;
        z-index: 10;
        background: #090d16;
    }
    .leaflet-container {
        font-family: 'Inter', system-ui, sans-serif !important;
        background: #090d16 !important;
    }
    .leaflet-popup-content-wrapper {
        background: rgba(15, 23, 42, 0.95) !important;
        color: #f8fafc !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 12px !important;
        backdrop-filter: blur(14px) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.6) !important;
        padding: 4px !important;
    }
    .leaflet-popup-tip {
        background: rgba(15, 23, 42, 0.95) !important;
    }

    /* Custom Map Marker Pins */
    .custom-event-pin {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
    }
    .custom-event-pin .pin-core {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 14px;
        box-shadow: 0 0 15px rgba(0,0,0,0.6);
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 2;
    }
    .custom-event-pin:hover .pin-core {
        transform: scale(1.25);
    }
    .custom-event-pin .pin-pulse {
        position: absolute;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        animation: radarPulse 2.2s infinite ease-out;
        z-index: 1;
    }

    /* View Switcher Segmented Control */
    .view-switcher-pill {
        background: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 8px 18px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .view-switcher-pill:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.2);
    }
    .view-switcher-pill.active {
        background: linear-gradient(135deg, #e50914 0%, #6366f1 100%);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 20px rgba(229, 9, 20, 0.35);
    }

    /* City Quick Jump Pills */
    .city-jump-btn {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
        font-size: 0.78rem;
        border-radius: 20px;
        padding: 4px 12px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .city-jump-btn:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border-color: #06b6d4;
    }

    /* Event Card Hover */
    .fh-event-card {
        background: rgba(15, 23, 42, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        overflow: hidden;
        backdrop-filter: blur(12px);
        transition: transform 0.28s ease, border-color 0.28s ease, box-shadow 0.28s ease;
    }
    .fh-event-card:hover {
        transform: translateY(-4px);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 16px 36px -12px rgba(0, 0, 0, 0.7);
    }

    /* Calendar Grid Specifics */
    .cal-day-cell {
        min-height: 95px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 8px;
        padding: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .cal-day-cell:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(6, 182, 212, 0.4);
        transform: scale(1.02);
    }
    .cal-day-cell.is-today {
        border-color: #e50914 !important;
        background: rgba(229, 9, 20, 0.08) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-lg-5 py-4">
    <!-- Breadcrumbs -->
    <x-breadcrumb :items="$breadcrumbs" />

    <!-- Animated Hero Spotlight Banner -->
    <div class="fh-card p-4 p-lg-5 mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(30,27,75,0.85) 100%);">
        <div class="position-absolute top-0 end-0 p-3 opacity-15 pe-none d-none d-md-block" style="font-size: 14rem; line-height: 1; transform: translate(15%, -15%);">
            <i class="bi bi-geo-alt-fill text-danger"></i>
        </div>

        <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="badge bg-danger bg-opacity-25 text-danger px-3 py-1.5 border border-danger border-opacity-25 fw-bold">
                        <i class="bi bi-broadcast me-1 pulse-dot"></i> Live Multiverse Radar
                    </span>
                    <span class="badge bg-info bg-opacity-25 text-info px-3 py-1.5 border border-info border-opacity-25">
                        <i class="bi bi-globe-americas me-1"></i> 25+ Global Fandom Conventions
                    </span>
                    <span class="badge bg-success bg-opacity-25 text-success px-3 py-1.5 border border-success border-opacity-25">
                        <i class="bi bi-pin-map-fill me-1"></i> Interactive Map & Calendar
                    </span>
                </div>

                <h1 class="display-5 fw-bold text-white mb-2">
                    Multiverse Events, Conventions & Meetups
                </h1>
                <p class="text-light text-opacity-75 lead mb-4" style="max-width: 680px;">
                    Explore Comic-Cons, anime summits, world cosplay championships, and esports tournaments across Tokyo, Los Angeles, London, Seoul, Cologne, and your local area.
                </p>

                <!-- Four Interactive View Mode Switches -->
                <div class="d-flex flex-wrap align-items-center gap-2" role="tablist">
                    <button type="button" class="btn view-switcher-pill active" id="btnViewMap" onclick="switchEventsView('map')">
                        <i class="bi bi-map-fill"></i> Interactive Map
                    </button>
                    <button type="button" class="btn view-switcher-pill" id="btnViewCalendar" onclick="switchEventsView('calendar')">
                        <i class="bi bi-calendar-week-fill"></i> Calendar View
                    </button>
                    <button type="button" class="btn view-switcher-pill" id="btnViewNearby" onclick="switchEventsView('nearby')">
                        <i class="bi bi-compass-fill text-warning"></i> Nearby Events Radar
                    </button>
                    <button type="button" class="btn view-switcher-pill" id="btnViewList" onclick="switchEventsView('list')">
                        <i class="bi bi-grid-3x3-gap-fill"></i> All Events ({{ $events->total() }})
                    </button>
                </div>
            </div>

            <!-- Spotlight Card -->
            @if($featuredEvent)
                <div class="col-lg-4">
                    <div class="fh-fan-stat-card p-3 p-xl-4 h-100 position-relative overflow-hidden border-primary border-opacity-50">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-warning text-dark fw-bold small">
                                <i class="bi bi-star-fill me-1"></i> FEATURED GATHERING
                            </span>
                            <span class="text-muted small">
                                {{ $featuredEvent->starts_at->format('M d, Y') }}
                            </span>
                        </div>
                        <h5 class="fw-bold text-light mb-2 text-truncate" title="{{ $featuredEvent->title }}">
                            <a href="{{ route('events.show', $featuredEvent->slug) }}" class="text-light text-decoration-none hover-primary">
                                {{ $featuredEvent->title }}
                            </a>
                        </h5>
                        <div class="small text-muted mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $featuredEvent->location_display }}
                        </div>
                        <p class="small text-light text-opacity-75 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $featuredEvent->description }}
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('events.show', $featuredEvent->slug) }}" class="btn btn-sm btn-primary flex-fill">
                                View Dossier &rarr;
                            </a>
                            @if($featuredEvent->ticket_url)
                                <a href="{{ $featuredEvent->ticket_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-ticket-perforated"></i> Tickets
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- VIEW 1: INTERACTIVE MULTIVERSE MAP -->
    <div id="sectionMapView" class="event-view-section mb-5">
        <div class="fh-fan-stat-card p-3 p-md-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h4 class="fw-bold text-light mb-1">
                        <i class="bi bi-globe-americas text-info me-2"></i>Global Multiverse Conventions Map
                    </h4>
                    <p class="text-muted small mb-0">Click any pulsing pin to inspect venue coordinates, schedules, and book official passes.</p>
                </div>

                <!-- Map Action Controls -->
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-info" id="mapLocateMeBtn" onclick="locateUserOnMap()">
                        <i class="bi bi-crosshair me-1"></i> Locate Me
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetMapGlobalView()">
                        <i class="bi bi-arrows-fullscreen me-1"></i> Global View
                    </button>
                    <a href="{{ route('events.calendar') }}" class="btn btn-sm btn-primary-gradient">
                        <i class="bi bi-calendar3 me-1"></i> Calendar Schedule
                    </a>
                </div>
            </div>

            <!-- Quick Jump City Pills -->
            <div class="d-flex align-items-center gap-1 overflow-x-auto pb-2 mb-3">
                <span class="small text-muted me-2 flex-shrink-0"><i class="bi bi-airplane-engines me-1"></i>Fly To Hub:</span>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(35.6762, 139.6503, 11, 'Tokyo Hub')">🇯🇵 Tokyo</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(34.0522, -118.2437, 11, 'Los Angeles')">🇺🇸 Los Angeles</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(51.5074, -0.1278, 11, 'London')">🇬🇧 London</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(40.7128, -74.0060, 11, 'New York')">🇺🇸 New York</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(37.5665, 126.9780, 11, 'Seoul')">🇰🇷 Seoul</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(50.9375, 6.9603, 11, 'Cologne')">🇩🇪 Cologne</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(-33.8688, 151.2093, 11, 'Sydney')">🇦🇺 Sydney</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(48.8566, 2.3522, 11, 'Paris')">🇫🇷 Paris</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(1.3521, 103.8198, 12, 'Singapore')">🇸🇬 Singapore</button>
                <button type="button" class="city-jump-btn" onclick="flyToLocation(32.7157, -117.1611, 12, 'San Diego')">🇺🇸 San Diego</button>
            </div>

            <!-- Leaflet Map Container -->
            <div class="position-relative overflow-hidden rounded-3 border border-secondary border-opacity-25">
                <div id="eventLeafletMap"></div>

                <!-- Live Map Legend Overlay -->
                <div class="position-absolute bottom-0 start-0 m-3 p-2 px-3 rounded-3 border border-secondary border-opacity-25"
                     style="background: rgba(15,23,42,0.85); backdrop-filter: blur(10px); z-index: 400; font-size: 0.75rem;">
                    <div class="fw-bold text-light mb-1">Fandom Pin Types:</div>
                    <div class="d-flex flex-wrap gap-2 text-muted">
                        <span><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #e50914;"></span>Anime</span>
                        <span><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #6366f1;"></span>Gaming</span>
                        <span><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #10b981;"></span>Cosplay</span>
                        <span><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #f59e0b;"></span>Comics</span>
                        <span><span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #ec4899;"></span>K-Pop</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 2: NEARBY EVENTS RADAR -->
    <div id="sectionNearbyView" class="event-view-section mb-5 d-none">
        <div class="fh-fan-stat-card p-4">
            <div class="row align-items-center g-4 mb-4">
                <div class="col-md-auto text-center">
                    <div class="radar-scanner-box">
                        <div class="radar-sweep-beam"></div>
                        <div class="radar-center-blip"></div>
                    </div>
                </div>
                <div class="col-md">
                    <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-1 mb-2 fw-bold">
                        <i class="bi bi-radar me-1"></i> GEOLOCATION RADAR
                    </span>
                    <h3 class="fw-bold text-light mb-1">Find Fandom Gatherings Near You</h3>
                    <p class="text-muted small mb-3">
                        Use your device's GPS or choose a global hub to instantly discover anime conventions, cosplay summits, and game tournaments closest to you.
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="button" class="btn btn-primary" id="btnDetectNearbyGps" onclick="startNearbyRadarScan()">
                            <i class="bi bi-geo-alt-fill me-1"></i> Detect My Coordinates
                        </button>

                        <!-- Radius Selector Pills -->
                        <div class="btn-group btn-group-sm" role="group" id="radarRadiusGroup">
                            <input type="radio" class="btn-check" name="radarRadius" id="rad50" value="50" onchange="changeRadarRadius(50)">
                            <label class="btn btn-outline-secondary" for="rad50">50 km</label>

                            <input type="radio" class="btn-check" name="radarRadius" id="rad250" value="250" onchange="changeRadarRadius(250)">
                            <label class="btn btn-outline-secondary" for="rad250">250 km</label>

                            <input type="radio" class="btn-check" name="radarRadius" id="rad500" value="500" checked onchange="changeRadarRadius(500)">
                            <label class="btn btn-outline-secondary" for="rad500">500 km</label>

                            <input type="radio" class="btn-check" name="radarRadius" id="rad1000" value="1000" onchange="changeRadarRadius(1000)">
                            <label class="btn btn-outline-secondary" for="rad1000">1000 km</label>

                            <input type="radio" class="btn-check" name="radarRadius" id="radGlobal" value="20000" onchange="changeRadarRadius(20000)">
                            <label class="btn btn-outline-secondary" for="radGlobal">Global</label>
                        </div>

                        <!-- Manual City Selector -->
                        <select class="form-select form-select-sm bg-dark text-light border-secondary" style="width: auto;" id="radarCitySelector" onchange="selectManualCity(this.value)">
                            <option value="">Or Pick Nearest Hub...</option>
                            <option value="35.6762,139.6503">Tokyo, Japan</option>
                            <option value="34.0522,-118.2437">Los Angeles, USA</option>
                            <option value="51.5074,-0.1278">London, UK</option>
                            <option value="40.7128,-74.0060">New York, USA</option>
                            <option value="37.5665,126.9780">Seoul, South Korea</option>
                            <option value="50.9375,6.9603">Cologne, Germany</option>
                            <option value="-33.8688,151.2093">Sydney, Australia</option>
                            <option value="48.8566,2.3522">Paris, France</option>
                            <option value="32.7157,-117.1611">San Diego, USA</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Scanner Status Banner -->
            <div id="radarStatusAlert" class="alert alert-secondary bg-opacity-25 border-secondary border-opacity-25 d-flex align-items-center justify-content-between p-2 px-3 rounded-3 mb-4">
                <div class="small text-light">
                    <i class="bi bi-info-circle-fill text-info me-2"></i>
                    <span id="radarStatusText">Click "Detect My Coordinates" or select a hub above to sort events by distance.</span>
                </div>
                <span class="badge bg-secondary" id="radarResultCount">Showing all locations</span>
            </div>

            <!-- Nearby Events Cards Grid (Populated via JS) -->
            <div class="row g-4" id="nearbyEventsGrid">
                <!-- Dynamically rendered nearby event cards -->
            </div>
        </div>
    </div>

    <!-- VIEW 3: INTERACTIVE MONTHLY CALENDAR -->
    <div id="sectionCalendarView" class="event-view-section mb-5 d-none">
        <div class="fh-fan-stat-card p-3 p-md-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold text-light mb-1">
                        <i class="bi bi-calendar-check-fill text-danger me-2"></i>
                        {{ $currentMonth->format('F Y') }} Multiverse Schedule
                    </h4>
                    <p class="text-muted small mb-0">Browse day-by-day anime gatherings, convention weekends, and broadcast dates.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('events.calendar', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-chevron-left me-1"></i> Prev Month
                    </a>
                    <a href="{{ route('events.calendar', ['month' => now()->format('Y-m')]) }}" class="btn btn-sm btn-outline-info">
                        This Month
                    </a>
                    <a href="{{ route('events.calendar', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
                        Next Month <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                    <a href="{{ route('events.calendar') }}" class="btn btn-sm btn-primary ms-2">
                        <i class="bi bi-arrows-angle-expand me-1"></i> Fullscreen Calendar
                    </a>
                </div>
            </div>

            <!-- Calendar Days Header -->
            <div class="row g-1 text-center fw-bold text-muted small pb-2 border-bottom border-secondary border-opacity-50">
                <div class="col">SUN</div>
                <div class="col">MON</div>
                <div class="col">TUE</div>
                <div class="col">WED</div>
                <div class="col">THU</div>
                <div class="col">FRI</div>
                <div class="col">SAT</div>
            </div>

            @php
                $daysInMonth = $currentMonth->daysInMonth;
                $firstDayOfWeek = $currentMonth->copy()->startOfMonth()->dayOfWeek;
                $todayDay = now()->isSameMonth($currentMonth) ? (int) now()->format('j') : null;
            @endphp

            <!-- Calendar Month Grid -->
            <div class="row g-1 pt-1">
                @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="col p-2 text-muted" style="min-height: 95px; opacity: 0.2; background: rgba(255,255,255,0.01); border-radius: 8px;"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dayEvents = $eventsByDay[$day] ?? [];
                        $isToday = ($todayDay === $day);
                    @endphp
                    <div class="col">
                        <div class="cal-day-cell {{ $isToday ? 'is-today' : '' }}" onclick="openCalendarDayDrawer({{ $day }})" id="calCell-{{ $day }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold {{ $isToday ? 'text-danger' : 'text-light' }}" style="font-size: 0.85rem;">{{ $day }}</span>
                                @if(count($dayEvents) > 0)
                                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">{{ count($dayEvents) }}</span>
                                @endif
                            </div>

                            @foreach(array_slice($dayEvents, 0, 2) as $dev)
                                <div class="text-truncate px-1 py-0.5 rounded mb-1 text-light small" 
                                     style="font-size: 0.68rem; background: rgba(99, 102, 241, 0.25); border-left: 2px solid #6366f1;" 
                                     title="{{ $dev->title }}">
                                    {{ $dev->title }}
                                </div>
                            @endforeach

                            @if(count($dayEvents) > 2)
                                <div class="text-muted" style="font-size: 0.62rem;">+{{ count($dayEvents) - 2 }} more</div>
                            @endif
                        </div>
                    </div>

                    @if(($firstDayOfWeek + $day) % 7 === 0 && $day < $daysInMonth)
                        </div><div class="row g-1 pt-1">
                    @endif
                @endfor
            </div>

            <!-- Day Preview Drawer (Pops open on day click) -->
            <div id="calendarDayDrawer" class="mt-4 p-3 rounded-3 border border-secondary border-opacity-25 d-none" style="background: rgba(255,255,255,0.03);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-light mb-0" id="drawerTitle">Events on This Day</h5>
                    <button type="button" class="btn btn-sm btn-link text-muted" onclick="document.getElementById('calendarDayDrawer').classList.add('d-none')">
                        <i class="bi bi-x-lg"></i> Close
                    </button>
                </div>
                <div id="drawerContent" class="row g-3"></div>
            </div>
        </div>
    </div>

    <!-- VIEW 4: FILTERABLE GRID OF ALL EVENTS -->
    <div id="sectionListView" class="event-view-section">
        <!-- Filter & Search Bar -->
        <div class="fh-fan-stat-card p-3 mb-4">
            <form method="GET" action="{{ route('events.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-dark border-secondary text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-dark border-secondary text-light" placeholder="Search event, city, venue..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="category" class="form-select form-select-sm bg-dark border-secondary text-light">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="type" class="form-select form-select-sm bg-dark border-secondary text-light">
                        <option value="">All Types</option>
                        <option value="convention" {{ request('type') === 'convention' ? 'selected' : '' }}>Conventions</option>
                        <option value="cosplay_meetup" {{ request('type') === 'cosplay_meetup' ? 'selected' : '' }}>Cosplay Meetups</option>
                        <option value="screening" {{ request('type') === 'screening' ? 'selected' : '' }}>Screenings</option>
                        <option value="concert" {{ request('type') === 'concert' ? 'selected' : '' }}>Concerts</option>
                        <option value="release_event" {{ request('type') === 'release_event' ? 'selected' : '' }}>Release Drops</option>
                        <option value="livestream" {{ request('type') === 'livestream' ? 'selected' : '' }}>Virtual Livestreams</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="city" class="form-select form-select-sm bg-dark border-secondary text-light">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="format" class="form-select form-select-sm bg-dark border-secondary text-light">
                        <option value="">All Formats</option>
                        <option value="in_person" {{ request('format') === 'in_person' ? 'selected' : '' }}>In-Person</option>
                        <option value="online" {{ request('format') === 'online' ? 'selected' : '' }}>Virtual / Online</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Events Grid -->
        <div class="row g-4">
            @forelse($events as $event)
                <div class="col-md-6 col-lg-4">
                    <div class="fh-event-card h-100 d-flex flex-column">
                        <div class="position-relative" style="height: 190px; overflow: hidden;">
                            <img src="{{ $event->cover_image_url }}" alt="{{ $event->title }}" class="w-100 h-100 object-fit-cover">
                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(15,23,42,0.85) 100%);"></div>

                            <!-- Badges -->
                            <div class="position-absolute top-0 start-0 m-3 d-flex flex-wrap gap-1">
                                <span class="badge bg-dark bg-opacity-75 text-light border border-secondary border-opacity-50 small">
                                    {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                </span>
                                @if($event->category)
                                    <span class="badge bg-primary bg-opacity-75 text-light small">
                                        {{ $event->category->name }}
                                    </span>
                                @endif
                            </div>

                            <div class="position-absolute bottom-0 start-0 m-3 text-light">
                                <span class="badge bg-danger bg-opacity-90 fw-bold small">
                                    <i class="bi bi-calendar-event me-1"></i> {{ $event->starts_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="p-3 p-xl-4 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold text-light mb-2 text-truncate" title="{{ $event->title }}">
                                <a href="{{ route('events.show', $event->slug) }}" class="text-light text-decoration-none hover-primary">
                                    {{ $event->title }}
                                </a>
                            </h5>

                            <div class="small text-muted mb-2">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $event->location_display }}
                            </div>

                            <p class="small text-muted mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $event->description }}
                            </p>

                            <div class="pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                                <a href="{{ route('events.show', $event->slug) }}" class="small text-info text-decoration-none fw-semibold">
                                    Full Dossier &rarr;
                                </a>

                                <div class="d-flex align-items-center gap-2">
                                    @if($event->ticket_url)
                                        <a href="{{ $event->ticket_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success py-1 px-2.5">
                                            <i class="bi bi-ticket-perforated"></i> Tickets
                                        </a>
                                    @endif
                                    <a href="{{ route('events.ics', $event) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Download .ics Calendar">
                                        <i class="bi bi-calendar-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <x-empty-state
                        icon="bi-calendar-x"
                        title="No Conventions Found"
                        message="No fandom events match your search criteria. Try removing filters or check nearby hubs."
                        actionText="Reset All Filters"
                        :actionUrl="route('events.index')"
                    />
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Raw Map Events JSON from Backend
const rawMapEvents = @json($mapEvents);
const rawMonthEvents = @json($monthEvents);

let leafletMap = null;
let markerLayerGroup = null;
let userRadarMarker = null;
let userRadarCircle = null;
let currentRadarRadiusKm = 500;
let userCurrentCoords = null;

document.addEventListener('DOMContentLoaded', function() {
    initLeafletMap();
    renderNearbyEvents(rawMapEvents);
});

/**
 * 1. Initialize Dark Leaflet Map with CartoDB DarkMatter Tiles
 */
function initLeafletMap() {
    const mapContainer = document.getElementById('eventLeafletMap');
    if (!mapContainer) return;

    // Center on global view
    leafletMap = L.map('eventLeafletMap', {
        center: [28.0, 15.0],
        zoom: 2,
        minZoom: 2,
        maxZoom: 18,
        scrollWheelZoom: true
    });

    // Dark Matter tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://carto.com/">CARTO</a> &copy; OpenStreetMap',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(leafletMap);

    markerLayerGroup = L.layerGroup().addTo(leafletMap);
    populateMapMarkers(rawMapEvents);
}

/**
 * 2. Populate Neon SVG Pins on Map
 */
function populateMapMarkers(events) {
    if (!markerLayerGroup) return;
    markerLayerGroup.clearLayers();

    const categoryColors = {
        'anime': '#e50914',
        'gaming': '#6366f1',
        'cosplay': '#10b981',
        'comics': '#f59e0b',
        'k-pop': '#ec4899',
        'movies': '#06b6d4',
        'all': '#8b5cf6'
    };

    events.forEach(ev => {
        if (!ev.lat || !ev.lng) return;

        const color = categoryColors[ev.category_slug] || '#6366f1';
        const iconHtml = `
            <div class="custom-event-pin">
                <div class="pin-pulse" style="background: ${color};"></div>
                <div class="pin-core" style="background: ${color};">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
            </div>
        `;

        const customIcon = L.divIcon({
            html: iconHtml,
            className: 'custom-leaflet-pin',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
            popupAnchor: [0, -18]
        });

        const popupContent = `
            <div style="width: 260px; font-family: 'Inter', sans-serif;">
                <img src="${ev.cover_image}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                <div style="display: flex; gap: 4px; margin-bottom: 4px;">
                    <span style="background: ${color}; color: #fff; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">${ev.category_name}</span>
                    <span style="background: rgba(255,255,255,0.1); color: #cbd5e1; font-size: 10px; padding: 2px 6px; border-radius: 4px;">${ev.type_label}</span>
                </div>
                <div style="font-weight: 700; font-size: 14px; color: #fff; line-height: 1.25; margin-bottom: 4px;">${ev.title}</div>
                <div style="font-size: 11px; color: #94a3b8; margin-bottom: 2px;">
                    <i class="bi bi-geo-alt" style="color: #06b6d4;"></i> ${ev.venue_name ? ev.venue_name + ', ' : ''}${ev.city}, ${ev.country}
                </div>
                <div style="font-size: 11px; color: #94a3b8; margin-bottom: 8px;">
                    <i class="bi bi-calendar3" style="color: #f59e0b;"></i> ${ev.starts_at} at ${ev.starts_time}
                </div>
                <div style="display: flex; gap: 6px;">
                    <a href="${ev.url}" class="btn btn-sm btn-primary" style="flex: 1; font-size: 11px; padding: 4px 8px;">View Dossier</a>
                    ${ev.ticket_url ? `<a href="${ev.ticket_url}" target="_blank" class="btn btn-sm btn-outline-success" style="font-size: 11px; padding: 4px 8px;">Tickets</a>` : ''}
                </div>
            </div>
        `;

        const marker = L.marker([ev.lat, ev.lng], { icon: customIcon }).bindPopup(popupContent);
        markerLayerGroup.addLayer(marker);
    });
}

/**
 * 3. Animated View Switching
 */
function switchEventsView(viewName) {
    const sections = ['map', 'calendar', 'nearby', 'list'];
    sections.forEach(s => {
        const secEl = document.getElementById(`section${s.charAt(0).toUpperCase() + s.slice(1)}View`);
        const btnEl = document.getElementById(`btnView${s.charAt(0).toUpperCase() + s.slice(1)}`);
        if (secEl) {
            if (s === viewName) {
                secEl.classList.remove('d-none');
            } else {
                secEl.classList.add('d-none');
            }
        }
        if (btnEl) {
            if (s === viewName) {
                btnEl.classList.add('active');
            } else {
                btnEl.classList.remove('active');
            }
        }
    });

    if (viewName === 'map' && leafletMap) {
        setTimeout(() => leafletMap.invalidateSize(), 150);
    }

    if (viewName === 'nearby' && !userCurrentCoords) {
        // Automatically start scanning if nearby view is activated
        startNearbyRadarScan();
    }
}

/**
 * 4. Fly Map smoothly to city
 */
function flyToLocation(lat, lng, zoom = 11, label = '') {
    switchEventsView('map');
    if (leafletMap) {
        leafletMap.flyTo([lat, lng], zoom, {
            animate: true,
            duration: 1.8
        });
    }
}

function resetMapGlobalView() {
    if (leafletMap) {
        leafletMap.flyTo([28.0, 15.0], 2, {
            animate: true,
            duration: 1.5
        });
    }
}

/**
 * 5. Nearby Events Radar & Geolocation
 */
function startNearbyRadarScan() {
    const statusText = document.getElementById('radarStatusText');
    const detectBtn = document.getElementById('btnDetectNearbyGps');
    if (detectBtn) detectBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Locating...';

    if (statusText) statusText.textContent = 'Contacting GPS satellites & determining nearby coordinates...';

    if (!navigator.geolocation) {
        if (statusText) statusText.textContent = 'Geolocation not supported by your browser. Please select a city hub from the dropdown.';
        if (detectBtn) detectBtn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i> Detect My Coordinates';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            userCurrentCoords = { lat, lng };

            if (statusText) statusText.innerHTML = `<strong>Coordinates locked:</strong> Lat ${lat.toFixed(4)}, Lng ${lng.toFixed(4)}. Searching within ${currentRadarRadiusKm} km...`;
            if (detectBtn) detectBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Location Locked';

            fetchNearbyEvents(lat, lng, currentRadarRadiusKm);
            updateUserRadarOnMap(lat, lng, currentRadarRadiusKm);
        },
        err => {
            console.warn('Geolocation error:', err);
            if (statusText) statusText.textContent = 'Location access denied or unavailable. Fallback: select nearest hub from dropdown.';
            if (detectBtn) detectBtn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i> Detect My Coordinates';
            // Fallback to Los Angeles / Tokyo default
            selectManualCity('34.0522,-118.2437');
        },
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

function selectManualCity(coordsStr) {
    if (!coordsStr) return;
    const parts = coordsStr.split(',');
    const lat = parseFloat(parts[0]);
    const lng = parseFloat(parts[1]);
    userCurrentCoords = { lat, lng };

    const statusText = document.getElementById('radarStatusText');
    if (statusText) statusText.innerHTML = `<strong>Hub Selected:</strong> Lat ${lat.toFixed(4)}, Lng ${lng.toFixed(4)}. Scanning radius ${currentRadarRadiusKm} km...`;

    fetchNearbyEvents(lat, lng, currentRadarRadiusKm);
    updateUserRadarOnMap(lat, lng, currentRadarRadiusKm);
}

function changeRadarRadius(radiusKm) {
    currentRadarRadiusKm = radiusKm;
    if (userCurrentCoords) {
        fetchNearbyEvents(userCurrentCoords.lat, userCurrentCoords.lng, currentRadarRadiusKm);
        updateUserRadarOnMap(userCurrentCoords.lat, userCurrentCoords.lng, currentRadarRadiusKm);
    }
}

async function fetchNearbyEvents(lat, lng, radiusKm) {
    try {
        const res = await fetch(`/events/nearby-data?lat=${lat}&lng=${lng}&radius=${radiusKm}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            renderNearbyEvents(data.events);
            const countBadge = document.getElementById('radarResultCount');
            if (countBadge) countBadge.textContent = `${data.count} conventions detected in radius`;
        }
    } catch (e) {
        console.error('Nearby error:', e);
        // Fallback: client-side distance calculate
        renderNearbyEvents(computeDistancesClientSide(lat, lng, radiusKm));
    }
}

function computeDistancesClientSide(userLat, userLng, radiusKm) {
    return rawMapEvents.map(ev => {
        let dist = null;
        if (ev.lat && ev.lng) {
            const R = 6371;
            const dLat = (ev.lat - userLat) * Math.PI / 180;
            const dLon = (ev.lng - userLng) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(userLat * Math.PI / 180) * Math.cos(ev.lat * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            dist = Math.round(R * c);
        }
        return { ...ev, distance_km: dist };
    })
    .filter(ev => ev.distance_km === null || ev.distance_km <= radiusKm)
    .sort((a, b) => (a.distance_km || 99999) - (b.distance_km || 99999));
}

function renderNearbyEvents(events) {
    const grid = document.getElementById('nearbyEventsGrid');
    if (!grid) return;

    if (!events.length) {
        grid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-broadcast fs-1 text-warning mb-2 d-block"></i>
                <h5 class="text-light fw-bold">No Conventions in Current Radius</h5>
                <p class="text-muted small">Try increasing your search radius to 1,000 km or Global, or pick a different hub.</p>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="changeRadarRadius(20000)">Expand to Global</button>
            </div>
        `;
        return;
    }

    grid.innerHTML = events.map(ev => `
        <div class="col-md-6 col-lg-4">
            <div class="fh-event-card h-100 d-flex flex-column">
                <div class="position-relative" style="height: 180px; overflow: hidden;">
                    <img src="${ev.cover_image}" alt="${ev.title}" class="w-100 h-100 object-fit-cover">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(15,23,42,0.85) 100%);"></div>
                    
                    <div class="position-absolute top-0 start-0 m-3 d-flex gap-1">
                        <span class="badge bg-dark bg-opacity-75 text-light border border-secondary border-opacity-50 small">${ev.type_label}</span>
                        <span class="badge bg-primary bg-opacity-75 text-light small">${ev.category_name}</span>
                    </div>

                    ${ev.distance_km !== null ? `
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning text-dark fw-bold" style="box-shadow: 0 4px 12px rgba(245,158,11,0.4);">
                                <i class="bi bi-cursor-fill me-1"></i> ${ev.distance_km} km away
                            </span>
                        </div>
                    ` : ''}

                    <div class="position-absolute bottom-0 start-0 m-3 text-light">
                        <span class="badge bg-danger bg-opacity-90 fw-bold small">
                            <i class="bi bi-calendar-event me-1"></i> ${ev.starts_at}
                        </span>
                    </div>
                </div>

                <div class="p-3 d-flex flex-column flex-grow-1">
                    <h6 class="fw-bold text-light mb-1 text-truncate" title="${ev.title}">
                        <a href="${ev.url}" class="text-light text-decoration-none hover-primary">${ev.title}</a>
                    </h6>
                    <div class="small text-muted mb-3">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> ${ev.venue_name ? ev.venue_name + ', ' : ''}${ev.city}, ${ev.country}
                    </div>

                    <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                        <a href="${ev.url}" class="small text-info text-decoration-none fw-semibold">View Dossier &rarr;</a>
                        <div class="d-flex gap-1">
                            ${ev.ticket_url ? `<a href="${ev.ticket_url}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2.5">Passes</a>` : ''}
                            <button type="button" class="btn btn-sm btn-outline-info py-1 px-2" onclick="flyToLocation(${ev.lat}, ${ev.lng}, 13, '${ev.title}')" title="Show on Map">
                                <i class="bi bi-map"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function updateUserRadarOnMap(lat, lng, radiusKm) {
    if (!leafletMap) return;

    if (userRadarMarker) leafletMap.removeLayer(userRadarMarker);
    if (userRadarCircle) leafletMap.removeLayer(userRadarCircle);

    // Glowing User Location Marker
    const userPinHtml = `
        <div style="position: relative; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
            <div style="position: absolute; width: 24px; height: 24px; background: rgba(6, 182, 212, 0.4); border-radius: 50%; animation: radarPulse 1.8s infinite;"></div>
            <div style="width: 14px; height: 14px; background: #06b6d4; border: 2px solid #ffffff; border-radius: 50%; box-shadow: 0 0 10px #06b6d4;"></div>
        </div>
    `;
    const userIcon = L.divIcon({
        html: userPinHtml,
        className: 'user-radar-pin',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    userRadarMarker = L.marker([lat, lng], { icon: userIcon })
        .addTo(leafletMap)
        .bindPopup('<strong style="color: #06b6d4;">Your Current Location</strong>')
        .openPopup();

    if (radiusKm && radiusKm < 20000) {
        userRadarCircle = L.circle([lat, lng], {
            radius: radiusKm * 1000,
            color: '#06b6d4',
            weight: 1.5,
            fillColor: '#06b6d4',
            fillOpacity: 0.08
        }).addTo(leafletMap);
    }

    leafletMap.flyTo([lat, lng], 6, { animate: true, duration: 1.5 });
}

function locateUserOnMap() {
    startNearbyRadarScan();
    switchEventsView('map');
}

/**
 * 6. Interactive Day Preview Drawer for Calendar
 */
function openCalendarDayDrawer(day) {
    const drawer = document.getElementById('calendarDayDrawer');
    const drawerTitle = document.getElementById('drawerTitle');
    const drawerContent = document.getElementById('drawerContent');
    if (!drawer || !drawerTitle || !drawerContent) return;

    drawer.classList.remove('d-none');
    drawerTitle.innerHTML = `<i class="bi bi-calendar-event text-danger me-2"></i> Events on Day ${day} of this Month`;

    // Filter rawMonthEvents for this day
    const matching = rawMonthEvents.filter(ev => {
        const d = new Date(ev.starts_at);
        return d.getDate() === day;
    });

    if (!matching.length) {
        drawerContent.innerHTML = `
            <div class="col-12 text-muted small py-2">
                No major conventions scheduled on day ${day}. Check adjacent dates or view the full month schedule.
            </div>
        `;
        drawer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return;
    }

    drawerContent.innerHTML = matching.map(ev => `
        <div class="col-md-6">
            <div class="p-3 rounded-3 border border-secondary border-opacity-25" style="background: rgba(255,255,255,0.02);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-danger small">${ev.category ? ev.category.name : 'Gathering'}</span>
                    <span class="small text-muted">${new Date(ev.starts_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                </div>
                <h6 class="fw-bold text-light mb-1">${ev.title}</h6>
                <div class="small text-muted mb-2">
                    <i class="bi bi-geo-alt text-danger me-1"></i> ${ev.venue_name || ''} ${ev.city ? '(' + ev.city + ')' : ''}
                </div>
                <div class="d-flex gap-2">
                    <a href="/events/${ev.slug}" class="btn btn-sm btn-primary py-1 px-3">View Details</a>
                    ${ev.ticket_url ? `<a href="${ev.ticket_url}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-3">Tickets</a>` : ''}
                </div>
            </div>
        </div>
    `).join('');

    drawer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
@endpush
