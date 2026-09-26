<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'franchise',
        'biography',
        'abilities',
        'profile_image',
        'cover_image',
        'is_featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the avatar / profile image URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_image && (str_starts_with($this->profile_image, 'http://') || str_starts_with($this->profile_image, 'https://'))) {
            return $this->profile_image;
        }

        if ($this->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image)) {
            return asset('storage/' . $this->profile_image);
        }

        if ($this->cover_image && (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://'))) {
            return $this->cover_image;
        }

        return 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&w=600&q=80';
    }
}
