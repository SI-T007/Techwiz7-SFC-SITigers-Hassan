@extends('layouts.app')

@section('title', 'Create Event — Admin Console — Fan Hub Plus')

@section('content')
<div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-light mb-0">Create Fandom Event</h1>
    </div>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Events
    </a>
</div>

<div class="fh-card p-4 p-md-5">
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <!-- Title -->
            <div class="col-md-8">
                <label for="title" class="form-label text-light fw-semibold">Event Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control bg-dark border-secondary text-light @error('title') is-invalid @enderror" 
                       value="{{ old('title') }}" placeholder="e.g. Anime Expo 2026 Summit" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Category -->
            <div class="col-md-4">
                <label for="category_id" class="form-label text-light fw-semibold">Fandom Category</label>
                <select name="category_id" id="category_id" class="form-select bg-dark border-secondary text-light @error('category_id') is-invalid @enderror">
                    <option value="">General / Multi-Fandom</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Type & Status -->
            <div class="col-md-4">
                <label for="event_type" class="form-label text-light fw-semibold">Event Format <span class="text-danger">*</span></label>
                <select name="event_type" id="event_type" class="form-select bg-dark border-secondary text-light @error('event_type') is-invalid @enderror" required>
                    <option value="convention" {{ old('event_type') === 'convention' ? 'selected' : '' }}>Convention</option>
                    <option value="cosplay_meetup" {{ old('event_type') === 'cosplay_meetup' ? 'selected' : '' }}>Cosplay Meetup</option>
                    <option value="screening" {{ old('event_type') === 'screening' ? 'selected' : '' }}>Screening</option>
                    <option value="concert" {{ old('event_type') === 'concert' ? 'selected' : '' }}>Concert</option>
                    <option value="livestream" {{ old('event_type') === 'livestream' ? 'selected' : '' }}>Livestream</option>
                    <option value="release_event" {{ old('event_type') === 'release_event' ? 'selected' : '' }}>Release Event</option>
                    <option value="other" {{ old('event_type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('event_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="starts_at" class="form-label text-light fw-semibold">Starts At <span class="text-danger">*</span></label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control bg-dark border-secondary text-light @error('starts_at') is-invalid @enderror" 
                       value="{{ old('starts_at') }}" required>
                @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="ends_at" class="form-label text-light fw-semibold">Ends At (Optional)</label>
                <input type="datetime-local" name="ends_at" id="ends_at" class="form-control bg-dark border-secondary text-light @error('ends_at') is-invalid @enderror" 
                       value="{{ old('ends_at') }}">
                @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Location Section Header -->
            <div class="col-12 mt-4 pt-2 border-top border-secondary border-opacity-25">
                <h5 class="text-info mb-1"><i class="bi bi-geo-alt me-1"></i> Venue & Geolocation Information</h5>
                <p class="text-muted small">Provide physical address and GPS coordinates for interactive map rendering and nearby discovery.</p>
            </div>

            <div class="col-md-6">
                <label for="venue_name" class="form-label text-light">Venue Name</label>
                <input type="text" name="venue_name" id="venue_name" class="form-control bg-dark border-secondary text-light @error('venue_name') is-invalid @enderror" 
                       value="{{ old('venue_name') }}" placeholder="e.g. Makuhari Messe Convention Center">
                @error('venue_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="address" class="form-label text-light">Street Address</label>
                <input type="text" name="address" id="address" class="form-control bg-dark border-secondary text-light @error('address') is-invalid @enderror" 
                       value="{{ old('address') }}" placeholder="e.g. 2-1 Nakase, Mihama-ku">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="city" class="form-label text-light">City</label>
                <input type="text" name="city" id="city" class="form-control bg-dark border-secondary text-light @error('city') is-invalid @enderror" 
                       value="{{ old('city') }}" placeholder="e.g. Tokyo">
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="country" class="form-label text-light">Country</label>
                <input type="text" name="country" id="country" class="form-control bg-dark border-secondary text-light @error('country') is-invalid @enderror" 
                       value="{{ old('country') }}" placeholder="e.g. Japan">
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="latitude" class="form-label text-light">Latitude (Decimal Coordinates)</label>
                <input type="number" step="0.00000001" name="latitude" id="latitude" class="form-control bg-dark border-secondary text-light @error('latitude') is-invalid @enderror" 
                       value="{{ old('latitude') }}" placeholder="e.g. 35.6482">
                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="longitude" class="form-label text-light">Longitude (Decimal Coordinates)</label>
                <input type="number" step="0.00000001" name="longitude" id="longitude" class="form-control bg-dark border-secondary text-light @error('longitude') is-invalid @enderror" 
                       value="{{ old('longitude') }}" placeholder="e.g. 140.0347">
                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- External Ticket & Media -->
            <div class="col-12 mt-4 pt-2 border-top border-secondary border-opacity-25">
                <h5 class="text-info mb-1"><i class="bi bi-ticket-perforated me-1"></i> Tickets & Presentation</h5>
            </div>

            <div class="col-md-6">
                <label for="ticket_url" class="form-label text-light">Official Ticket / Registration URL</label>
                <input type="url" name="ticket_url" id="ticket_url" class="form-control bg-dark border-secondary text-light @error('ticket_url') is-invalid @enderror" 
                       value="{{ old('ticket_url') }}" placeholder="https://example.com/tickets">
                @error('ticket_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label text-light fw-semibold">Event Status</label>
                <select name="status" id="status" class="form-select bg-dark border-secondary text-light @error('status') is-invalid @enderror">
                    <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12">
                <label for="cover_image" class="form-label text-light">Event Cover Image (JPEG, PNG, WebP &bull; Max 5MB)</label>
                <input type="file" name="cover_image" id="cover_image" class="form-control bg-dark border-secondary text-light @error('cover_image') is-invalid @enderror">
                @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label text-light">Event Description</label>
                <textarea name="description" id="description" rows="5" class="form-control bg-dark border-secondary text-light @error('description') is-invalid @enderror" 
                          placeholder="Comprehensive details, panel schedules, guest appearances...">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Flags -->
            <div class="col-md-6">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                    <label class="form-check-label text-light" for="is_featured">Feature on Event Highlights & Homepage</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_online" id="is_online" value="1" {{ old('is_online') ? 'checked' : '' }}>
                    <label class="form-check-label text-light" for="is_online">Virtual / Online Livestream Event</label>
                </div>
            </div>

            <div class="col-12 mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Create Event</button>
            </div>
        </div>
    </form>
</div>
</div>
@endsection
