@extends('layouts.app')

@section('title', 'Edit Event: ' . $event->title . ' — Admin Console')

@section('content')
<div class="container py-4">
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit #{{ $event->id }}</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-light mb-0">Edit Fandom Event</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="btn btn-outline-info btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Public Page
        </a>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="fh-card p-4 p-md-5">
    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Title -->
            <div class="col-md-8">
                <label for="title" class="form-label text-light fw-semibold">Event Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control bg-dark border-secondary text-light @error('title') is-invalid @enderror" 
                       value="{{ old('title', $event->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Category -->
            <div class="col-md-4">
                <label for="category_id" class="form-label text-light fw-semibold">Fandom Category</label>
                <select name="category_id" id="category_id" class="form-select bg-dark border-secondary text-light @error('category_id') is-invalid @enderror">
                    <option value="">General / Multi-Fandom</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $event->category_id) == $cat->id ? 'selected' : '' }}>
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
                    <option value="convention" {{ old('event_type', $event->event_type) === 'convention' ? 'selected' : '' }}>Convention</option>
                    <option value="cosplay_meetup" {{ old('event_type', $event->event_type) === 'cosplay_meetup' ? 'selected' : '' }}>Cosplay Meetup</option>
                    <option value="screening" {{ old('event_type', $event->event_type) === 'screening' ? 'selected' : '' }}>Screening</option>
                    <option value="concert" {{ old('event_type', $event->event_type) === 'concert' ? 'selected' : '' }}>Concert</option>
                    <option value="livestream" {{ old('event_type', $event->event_type) === 'livestream' ? 'selected' : '' }}>Livestream</option>
                    <option value="release_event" {{ old('event_type', $event->event_type) === 'release_event' ? 'selected' : '' }}>Release Event</option>
                    <option value="other" {{ old('event_type', $event->event_type) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('event_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="starts_at" class="form-label text-light fw-semibold">Starts At <span class="text-danger">*</span></label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control bg-dark border-secondary text-light @error('starts_at') is-invalid @enderror" 
                       value="{{ old('starts_at', $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '') }}" required>
                @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="ends_at" class="form-label text-light fw-semibold">Ends At (Optional)</label>
                <input type="datetime-local" name="ends_at" id="ends_at" class="form-control bg-dark border-secondary text-light @error('ends_at') is-invalid @enderror" 
                       value="{{ old('ends_at', $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '') }}">
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
                       value="{{ old('venue_name', $event->venue_name) }}">
                @error('venue_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="address" class="form-label text-light">Street Address</label>
                <input type="text" name="address" id="address" class="form-control bg-dark border-secondary text-light @error('address') is-invalid @enderror" 
                       value="{{ old('address', $event->address) }}">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="city" class="form-label text-light">City</label>
                <input type="text" name="city" id="city" class="form-control bg-dark border-secondary text-light @error('city') is-invalid @enderror" 
                       value="{{ old('city', $event->city) }}">
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="country" class="form-label text-light">Country</label>
                <input type="text" name="country" id="country" class="form-control bg-dark border-secondary text-light @error('country') is-invalid @enderror" 
                       value="{{ old('country', $event->country) }}">
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="latitude" class="form-label text-light">Latitude</label>
                <input type="number" step="0.00000001" name="latitude" id="latitude" class="form-control bg-dark border-secondary text-light @error('latitude') is-invalid @enderror" 
                       value="{{ old('latitude', $event->latitude) }}">
                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="longitude" class="form-label text-light">Longitude</label>
                <input type="number" step="0.00000001" name="longitude" id="longitude" class="form-control bg-dark border-secondary text-light @error('longitude') is-invalid @enderror" 
                       value="{{ old('longitude', $event->longitude) }}">
                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- External Ticket & Media -->
            <div class="col-12 mt-4 pt-2 border-top border-secondary border-opacity-25">
                <h5 class="text-info mb-1"><i class="bi bi-ticket-perforated me-1"></i> Tickets & Media</h5>
            </div>

            <div class="col-md-6">
                <label for="ticket_url" class="form-label text-light">Official Ticket / Registration URL</label>
                <input type="url" name="ticket_url" id="ticket_url" class="form-control bg-dark border-secondary text-light @error('ticket_url') is-invalid @enderror" 
                       value="{{ old('ticket_url', $event->ticket_url) }}">
                @error('ticket_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label text-light fw-semibold">Event Status</label>
                <select name="status" id="status" class="form-select bg-dark border-secondary text-light @error('status') is-invalid @enderror">
                    <option value="upcoming" {{ old('status', $event->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status', $event->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-12">
                <label for="cover_image" class="form-label text-light">Event Cover Image (Leave blank to keep existing)</label>
                @if($event->cover_image)
                    <div class="mb-2">
                        <img src="{{ $event->cover_image_url }}" alt="Preview" class="rounded" style="max-height: 80px;">
                    </div>
                @endif
                <input type="file" name="cover_image" id="cover_image" class="form-control bg-dark border-secondary text-light @error('cover_image') is-invalid @enderror">
                @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label text-light">Event Description</label>
                <textarea name="description" id="description" rows="5" class="form-control bg-dark border-secondary text-light @error('description') is-invalid @enderror">{{ old('description', $event->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Flags -->
            <div class="col-md-6">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label text-light" for="is_featured">Feature on Event Highlights & Homepage</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="is_online" id="is_online" value="1" {{ old('is_online', $event->is_online) ? 'checked' : '' }}>
                    <label class="form-check-label text-light" for="is_online">Virtual / Online Livestream Event</label>
                </div>
            </div>

            <div class="col-12 mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
            </div>
        </div>
    </form>
</div>
</div>
@endsection
