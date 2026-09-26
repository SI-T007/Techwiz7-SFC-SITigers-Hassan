<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'type',
        'description',
        'body',
        'thumbnail',
        'backdrop_image',
        'media_url',
        'release_date',
        'views',
        'popularity',
        'is_featured',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'release_date' => 'date',
            'published_at' => 'datetime',
            'views' => 'integer',
            'popularity' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'content_genre');
    }

    public function media(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->ratings()->avg('score');
        return $avg ? round((float) $avg, 1) : null;
    }

    public function getRatingsCountAttribute(): int
    {
        return $this->ratings()->count();
    }

    public function isBookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function userRating(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        return $this->ratings()->where('user_id', $user->id)->value('score');
    }

    public function userBookmark(?User $user): ?Bookmark
    {
        if (! $user) {
            return null;
        }

        return $this->bookmarks()->where('user_id', $user->id)->first();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Resolves the primary thumbnail URL for cards and media items.
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://'))) {
            // Prefer hqdefault for reliability if it's a YouTube link, or return external URL
            return $this->thumbnail;
        }

        if ($this->thumbnail && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }

        if ($this->media_url) {
            $ytId = \App\Models\Media::extractYouTubeId($this->media_url);
            if ($ytId) {
                return 'https://img.youtube.com/vi/' . urlencode($ytId) . '/hqdefault.jpg';
            }
        }

        return 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=800&q=85';
    }

    /**
     * Ultra High-Definition backdrop for cinematic Hero Banner (2K / 4K / 1080p).
     */
    public function getHeroThumbnailUrlAttribute(): string
    {
        if ($this->backdrop_image && (str_starts_with($this->backdrop_image, 'http://') || str_starts_with($this->backdrop_image, 'https://'))) {
            return $this->backdrop_image;
        }

        if ($this->backdrop_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->backdrop_image)) {
            return asset('storage/' . $this->backdrop_image);
        }

        if ($this->thumbnail && (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://'))) {
            return $this->thumbnail;
        }

        if ($this->thumbnail && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }

        if ($this->media_url) {
            $ytId = \App\Models\Media::extractYouTubeId($this->media_url);
            if ($ytId) {
                return 'https://img.youtube.com/vi/' . urlencode($ytId) . '/hqdefault.jpg';
            }
        }

        return 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=2560&q=95';
    }
}
