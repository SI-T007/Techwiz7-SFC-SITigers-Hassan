<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'type',
        'description',
        'cover_image',
        'release_date',
        'platform',
        'developer_publisher',
        'external_url',
        'status',
        'tags',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'is_featured' => 'boolean',
            'tags' => 'array',
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

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    /**
     * Scope for upcoming releases (future dates).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('release_date', '>=', now()->toDateString())
                     ->orderBy('release_date', 'asc');
    }

    /**
     * Scope for past/released drops.
     */
    public function scopePast($query)
    {
        return $query->where('release_date', '<', now()->toDateString())
                     ->orderBy('release_date', 'desc');
    }

    /**
     * Scope for featured releases.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Filter by category.
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Filter by release format type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Filter by JSON tag.
     */
    public function scopeTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
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

        return 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=1200&auto=format&fit=crop&q=80';
    }

    /**
     * Check if release has already dropped.
     */
    public function isReleased(): bool
    {
        return $this->status === 'released' || ($this->release_date && $this->release_date->isPast());
    }

    /**
     * Days remaining until release date (null if in past).
     */
    public function getDaysUntilReleaseAttribute(): ?int
    {
        if (! $this->release_date || $this->release_date->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->release_date, false);
    }
}
