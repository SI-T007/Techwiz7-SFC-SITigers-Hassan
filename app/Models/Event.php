<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'event_type',
        'starts_at',
        'ends_at',
        'venue_name',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'ticket_url',
        'is_featured',
        'is_online',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'is_featured' => 'boolean',
            'is_online' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    /**
     * Scope for upcoming events (start date in future, not cancelled).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', '!=', 'cancelled')
                     ->where('starts_at', '>=', now())
                     ->orderBy('starts_at', 'asc');
    }

    /**
     * Scope for ongoing events.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', '!=', 'cancelled')
                     ->where('starts_at', '<=', now())
                     ->where(function ($q) {
                         $q->whereNull('ends_at')
                           ->orWhere('ends_at', '>=', now());
                     })
                     ->orderBy('starts_at', 'desc');
    }

    /**
     * Scope for completed past events.
     */
    public function scopeCompleted($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'completed')
              ->orWhere(function ($sub) {
                  $sub->whereNotNull('ends_at')
                      ->where('ends_at', '<', now());
              });
        })->orderBy('starts_at', 'desc');
    }

    /**
     * Scope for featured events.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Filter by category/fandom slug or id.
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Filter by city.
     */
    public function scopeCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Filter by event type.
     */
    public function scopeEventType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Location-aware discovery: Query events within radius (in KM) using Haversine formula.
     */
    public function scopeNearby($query, float $latitude, float $longitude, float $radiusKm = 100)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance", [$latitude, $longitude, $latitude])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance', 'asc');
    }

    /**
     * Derived dynamic status based on dates & manual override.
     */
    public function getDerivedStatusAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        $now = now();
        if ($this->starts_at > $now) {
            return 'upcoming';
        }

        if ($this->ends_at && $this->ends_at < $now) {
            return 'completed';
        }

        return 'ongoing';
    }

    public function isUpcoming(): bool
    {
        return $this->derived_status === 'upcoming';
    }

    public function isOngoing(): bool
    {
        return $this->derived_status === 'ongoing';
    }

    public function isCompleted(): bool
    {
        return $this->derived_status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cover image URL helper.
     */
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->cover_image)) {
            return asset('storage/' . $this->cover_image);
        }

        if ($this->cover_image && filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            return $this->cover_image;
        }

        return 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&auto=format&fit=crop&q=80';
    }

    /**
     * Formatted location string.
     */
    public function getLocationDisplayAttribute(): string
    {
        if ($this->is_online) {
            return 'Online Event';
        }

        $parts = array_filter([$this->venue_name, $this->city, $this->country]);
        return count($parts) > 0 ? implode(', ', $parts) : 'Venue TBA';
    }

    /**
     * Generate standard RFC 5545 iCalendar (.ics) string.
     */
    public function generateIcs(): string
    {
        $dtStart = $this->starts_at ? $this->starts_at->utc()->format('Ymd\THis\Z') : now()->utc()->format('Ymd\THis\Z');
        $dtEnd = $this->ends_at ? $this->ends_at->utc()->format('Ymd\THis\Z') : ($this->starts_at ? $this->starts_at->addHours(3)->utc()->format('Ymd\THis\Z') : now()->addHours(3)->utc()->format('Ymd\THis\Z'));
        $dtStamp = now()->utc()->format('Ymd\THis\Z');
        $uid = 'event-' . $this->id . '@fanhubplus.test';
        $summary = addcslashes($this->title, ",;\\");
        $description = addcslashes(strip_tags($this->description ?? ''), ",;\\");
        $location = addcslashes($this->location_display, ",;\\");

        return "BEGIN:VCALENDAR\r\n" .
            "VERSION:2.0\r\n" .
            "PRODID:-//Fan Hub Plus//Events Engine//EN\r\n" .
            "CALSCALE:GREGORIAN\r\n" .
            "METHOD:PUBLISH\r\n" .
            "BEGIN:VEVENT\r\n" .
            "UID:{$uid}\r\n" .
            "DTSTAMP:{$dtStamp}\r\n" .
            "DTSTART:{$dtStart}\r\n" .
            "DTEND:{$dtEnd}\r\n" .
            "SUMMARY:{$summary}\r\n" .
            "DESCRIPTION:{$description}\r\n" .
            "LOCATION:{$location}\r\n" .
            "STATUS:CONFIRMED\r\n" .
            "END:VEVENT\r\n" .
            "END:VCALENDAR\r\n";
    }
}
