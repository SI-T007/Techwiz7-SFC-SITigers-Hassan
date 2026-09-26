@extends('layouts.app')

@section('title', 'Events Management — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Events Management</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-light mb-1">Fandom Events & Conventions</h1>
        <p class="text-muted small mb-0">Oversee global gatherings, conventions, screenings, venue coordinates, and ticket links</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('events.index') }}" target="_blank" class="btn btn-outline-info btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Public Directory
        </a>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary-gradient btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Create New Event
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="fh-card p-3">
            <div class="small text-muted mb-1">Total Events</div>
            <div class="h4 fw-bold text-light mb-0">{{ $counts['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="fh-card p-3 border-primary border-opacity-50">
            <div class="small text-primary mb-1">Upcoming</div>
            <div class="h4 fw-bold text-primary mb-0">{{ $counts['upcoming'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="fh-card p-3 border-success border-opacity-50">
            <div class="small text-success mb-1">Ongoing Now</div>
            <div class="h4 fw-bold text-success mb-0">{{ $counts['ongoing'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="fh-card p-3">
            <div class="small text-muted mb-1">Completed</div>
            <div class="h4 fw-bold text-secondary mb-0">{{ $counts['completed'] }}</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="fh-card p-3 mb-4">
    <form method="GET" action="{{ route('admin.events.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm bg-dark border-secondary text-light" 
                   placeholder="Search title, city, venue..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">All Event Formats</option>
                <option value="convention" {{ request('type') === 'convention' ? 'selected' : '' }}>Convention</option>
                <option value="cosplay_meetup" {{ request('type') === 'cosplay_meetup' ? 'selected' : '' }}>Cosplay Meetup</option>
                <option value="screening" {{ request('type') === 'screening' ? 'selected' : '' }}>Screening</option>
                <option value="concert" {{ request('type') === 'concert' ? 'selected' : '' }}>Concert</option>
                <option value="livestream" {{ request('type') === 'livestream' ? 'selected' : '' }}>Livestream</option>
                <option value="release_event" {{ request('type') === 'release_event' ? 'selected' : '' }}>Release Event</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm bg-dark border-secondary text-light">
                <option value="">All Statuses</option>
                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Events Table -->
<div class="fh-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
            <thead>
                <tr class="text-muted small border-secondary border-opacity-25">
                    <th style="width: 50px;">Preview</th>
                    <th>Title & Category</th>
                    <th>Type</th>
                    <th>Location / City</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr class="border-secondary border-opacity-10">
                        <td>
                            <div class="rounded overflow-hidden bg-dark" style="width: 44px; height: 44px;">
                                <img src="{{ $event->cover_image_url }}" alt="{{ $event->title }}" class="w-100 h-100 object-fit-cover">
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-light text-truncate" style="max-width: 250px;">
                                {{ $event->title }}
                            </div>
                            <div class="small text-info">
                                {{ $event->category?->name ?? 'General' }}
                            </div>
                        </td>
                        <td>
                            <span class="fh-badge fh-badge-purple small">
                                {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                            </span>
                        </td>
                        <td>
                            <span class="small text-light">
                                <i class="bi bi-geo-alt text-danger me-1"></i> {{ $event->location_display }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ $event->starts_at->format('M d, Y H:i') }}
                        </td>
                        <td>
                            @if($event->isCancelled())
                                <span class="badge bg-danger small">Cancelled</span>
                            @elseif($event->isOngoing())
                                <span class="badge bg-success small">Ongoing</span>
                            @elseif($event->isCompleted())
                                <span class="badge bg-secondary small">Completed</span>
                            @else
                                <span class="badge bg-primary small">Upcoming</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="btn btn-sm btn-outline-info py-0 px-2 small" title="View Public Page">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary py-0 px-2 small" title="Edit Event">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event permanently?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 small" title="Delete Event">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No events currently match your search criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25 d-flex justify-content-center">
            {{ $events->links() }}
        </div>
    @endif
</div>
</div>
@endsection
