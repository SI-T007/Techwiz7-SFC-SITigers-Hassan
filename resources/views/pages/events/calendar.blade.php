@extends('layouts.app')

@section('title', 'Monthly Events Calendar (' . $currentMonth->format('F Y') . ') — Fan Hub Plus')

@section('content')
<div class="container py-4">
   
    <x-breadcrumb :items="$breadcrumbs" />

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <span class="small text-info fw-bold text-uppercase" style="letter-spacing: 0.08em;">Interactive Calendar</span>
            <h1 class="h2 fw-bold text-light mb-1">{{ $currentMonth->format('F Y') }} Fandom Gatherings</h1>
            <p class="text-muted small mb-0">Browse conventions, cosplay championships, and livestream dates across the month</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('events.calendar', ['month' => $prevMonth, 'category' => request('category'), 'city' => request('city')]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-chevron-left me-1"></i> {{ \Carbon\Carbon::createFromFormat('Y-m', $prevMonth)->format('M Y') }}
            </a>
            <a href="{{ route('events.calendar', ['month' => now()->format('Y-m')]) }}" class="btn btn-outline-info btn-sm">
                Today
            </a>
            <a href="{{ route('events.calendar', ['month' => $nextMonth, 'category' => request('category'), 'city' => request('city')]) }}" class="btn btn-outline-secondary btn-sm">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('M Y') }} <i class="bi bi-chevron-right ms-1"></i>
            </a>
            <a href="{{ route('events.index') }}" class="btn btn-outline-info btn-sm ms-2">
                <i class="bi bi-map-fill me-1"></i> Map & Radar
            </a>
            <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-list-ul me-1"></i> List View
            </a>
        </div>
    </div>

    <!-- Filter Bar for Calendar -->
    <div class="fh-card p-3 mb-4">
        <form method="GET" action="{{ route('events.calendar') }}" class="row g-2 align-items-center">
            <input type="hidden" name="month" value="{{ $currentMonth->format('Y-m') }}">
            <div class="col-md-5">
                <select name="category" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Fandom Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <select name="city" class="form-select form-select-sm bg-dark border-secondary text-light">
                    <option value="">All Cities</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>
                            {{ $c }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Apply</button>
                <a href="{{ route('events.calendar', ['month' => $currentMonth->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    @php
        $daysInMonth = $currentMonth->daysInMonth;
        $firstDayOfWeek = $currentMonth->copy()->startOfMonth()->dayOfWeek; // 0 = Sunday, 6 = Saturday
        $todayDay = now()->isSameMonth($currentMonth) ? (int) now()->format('j') : null;
    @endphp

    <!-- Desktop & Tablet Calendar Grid -->
    <div class="fh-card p-3 p-md-4 mb-4 overflow-hidden">
        <!-- Day of Week Header -->
        <div class="row g-1 text-center fw-bold text-muted small pb-2 border-bottom border-secondary border-opacity-50">
            <div class="col">SUN</div>
            <div class="col">MON</div>
            <div class="col">TUE</div>
            <div class="col">WED</div>
            <div class="col">THU</div>
            <div class="col">FRI</div>
            <div class="col">SAT</div>
        </div>

        <!-- Calendar Cells Grid -->
        <div class="row g-1 pt-1">
            <!-- Empty cells before first day of month -->
            @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div class="col border border-secondary border-opacity-10 p-2 text-muted" style="min-height: 110px; opacity: 0.3;">
                </div>
            @endfor

            <!-- Days of Month -->
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $colIndex = ($firstDayOfWeek + $day - 1) % 7;
                    $dayEvents = $eventsByDay[$day] ?? [];
                    $isToday = ($todayDay === $day);
                @endphp

                <div class="col border border-secondary border-opacity-25 p-2 rounded {{ $isToday ? 'bg-primary bg-opacity-10 border-primary' : '' }}" 
                     style="min-height: 110px;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-bold {{ $isToday ? 'badge bg-primary' : 'text-light' }}">{{ $day }}</span>
                        @if(count($dayEvents) > 0)
                            <span class="badge bg-secondary bg-opacity-50" style="font-size: 0.65rem;">{{ count($dayEvents) }}</span>
                        @endif
                    </div>

                    <!-- Events on this day -->
                    <div class="d-flex flex-column gap-1 overflow-hidden" style="max-height: 80px;">
                        @foreach($dayEvents as $ev)
                            <a href="{{ route('events.show', $ev->slug) }}" 
                               class="badge bg-info bg-opacity-25 text-info text-truncate text-decoration-none text-start p-1 small" 
                               style="font-size: 0.7rem;"
                               title="{{ $ev->title }} ({{ $ev->city }})">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.4rem;"></i> {{ $ev->title }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Wrap rows every Saturday -->
                @if($colIndex === 6 && $day < $daysInMonth)
                    </div><div class="row g-1 pt-1">
                @endif
            @endfor

            <!-- Trailing empty cells -->
            @php
                $remainingCells = (7 - (($firstDayOfWeek + $daysInMonth) % 7)) % 7;
            @endphp
            @for($i = 0; $i < $remainingCells; $i++)
                <div class="col border border-secondary border-opacity-10 p-2 text-muted" style="min-height: 110px; opacity: 0.3;">
                </div>
            @endfor
        </div>
    </div>

    <!-- Monthly Events Summary List -->
    <div class="fh-card p-4">
        <h3 class="h5 fw-bold text-light mb-3">
            <i class="bi bi-card-checklist me-2 text-primary"></i> All Gatherings in {{ $currentMonth->format('F Y') }} ({{ $events->count() }})
        </h3>

        @if($events->isEmpty())
            <p class="text-muted mb-0">No gatherings currently scheduled for this month. Check upcoming months or submit an event inquiry.</p>
        @else
            <div class="list-group list-group-flush bg-transparent">
                @foreach($events as $event)
                    <div class="list-group-item bg-transparent text-light border-secondary border-opacity-25 px-0 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <span class="badge bg-secondary bg-opacity-50 me-2">{{ $event->starts_at->format('M d, Y') }}</span>
                            <a href="{{ route('events.show', $event->slug) }}" class="fw-semibold text-light text-decoration-none hover-primary">
                                {{ $event->title }}
                            </a>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $event->location_display }} &bull;
                                <span class="text-info">{{ $event->category?->name ?? 'Multi-Fandom' }}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-outline-info">View Details</a>
                            <a href="{{ route('events.ics', $event) }}" class="btn btn-sm btn-outline-secondary" title="Export .ics"><i class="bi bi-calendar-plus"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
