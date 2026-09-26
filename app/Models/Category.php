<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'cover_image',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Users who favourited this category.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'category_user')->withTimestamps();
    }

    /**
     * Contents in this category.
     */
    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    /**
     * Characters in this category.
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    /**
     * Media items in this category.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Merchandise in this category.
     */
    public function merchandise(): HasMany
    {
        return $this->hasMany(Merchandise::class);
    }

    /**
     * Upcoming releases in this category.
     */
    public function releases(): HasMany
    {
        return $this->hasMany(Release::class);
    }

    /**
     * Events in this category.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Fan submissions in this category.
     */
    public function fanSubmissions(): HasMany
    {
        return $this->hasMany(FanSubmission::class);
    }

    /**
     * Scope active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orderBy('sort_order');
    }

    /**
     * Resolve valid Bootstrap Icon class.
     */
    public function getIconClassAttribute(): string
    {
        $icon = trim($this->icon ?? '');
        if (empty($icon)) {
            return match($this->slug) {
                'anime' => 'bi-tv',
                'gaming' => 'bi-controller',
                'movies' => 'bi-film',
                'tv-shows' => 'bi-display',
                'k-pop' => 'bi-music-note-beamed',
                'comics' => 'bi-book-half',
                'manga' => 'bi-journal-bookmark',
                'cosplay' => 'bi-mask',
                default => 'bi-stars',
            };
        }

        return str_starts_with($icon, 'bi-') ? $icon : 'bi-' . $icon;
    }
}
