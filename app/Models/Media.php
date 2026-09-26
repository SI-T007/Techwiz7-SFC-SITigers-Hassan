<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    /**
     * Supported controlled media types.
     */
    public const MEDIA_TYPES = [
        'anime_trailer' => 'Anime Trailer',
        'anime_opening' => 'Anime Opening',
        'anime_ending' => 'Anime Ending',
        'anime_song' => 'Anime Song / OST',
        'gaming_trailer' => 'Gaming Trailer',
        'gaming_cinematic' => 'Gaming Cinematic',
        'gaming_gameplay' => 'Gaming Gameplay',
        'movie_trailer' => 'Movie Trailer',
        'movie_teaser' => 'Movie Teaser',
        'movie_song' => 'Movie Song / OST',
        'tv_trailer' => 'TV Trailer',
        'tv_featurette' => 'TV Featurette',
        'music_video' => 'K-Pop / Music Video',
        'character_video' => 'Character Video',
        'behind_scenes' => 'Behind the Scenes',
        'other' => 'Other',
    ];

    /**
     * Supported video quality metadata.
     */
    public const QUALITIES = ['SD', 'HD', 'Full HD', '4K', 'Unknown'];

    protected $fillable = [
        'category_id',
        'content_id',
        'user_id',
        'title',
        'type',
        'media_type',
        'quality',
        'duration',
        'language',
        'release_date',
        'file_path',
        'external_url',
        'youtube_video_id',
        'thumbnail',
        'description',
        'tags',
        'views',
        'is_featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'views' => 'integer',
            'release_date' => 'date',
            'tags' => 'array',
        ];
    }

    /**
     * Category / Fandom Relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Optional Content / Lore Anchor.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Uploader / Curator Admin.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Polymorphic User Ratings Relationship.
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    /**
     * Scopes.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeMediaType($query, string $mediaType)
    {
        return $query->where('media_type', $mediaType);
    }

    /**
     * Safe YouTube Video ID Extractor.
     * Supports watch?v=, youtu.be/, embed/, shorts/, and raw 11-character video IDs.
     */
    public static function extractYouTubeId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        // If it's already an 11-char alphanumeric ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        // Comprehensive YouTube URL regex
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Generate secure YouTube Embed URL.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtube_video_id ?: self::extractYouTubeId($this->external_url);
        if ($id) {
            return 'https://www.youtube.com/embed/' . urlencode($id) . '?rel=0&modestbranding=1';
        }

        return null;
    }

    /**
     * Get High-Definition YouTube Thumbnail with Fallbacks.
     */
    public function getYoutubeThumbnailUrlAttribute(): ?string
    {
        $id = $this->youtube_video_id ?: self::extractYouTubeId($this->external_url);
        if ($id) {
            return 'https://img.youtube.com/vi/' . urlencode($id) . '/hqdefault.jpg';
        }

        return null;
    }

    /**
     * Main Thumbnail URL resolution.
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://'))) {
            return $this->thumbnail;
        }

        if ($this->thumbnail && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }

        if ($this->youtube_thumbnail_url) {
            return $this->youtube_thumbnail_url;
        }

        if ($this->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/' . $this->file_path);
        }

        return 'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=800&q=80';
    }

    /**
     * Formatted Media Type Label.
     */
    public function getMediaTypeLabelAttribute(): string
    {
        return self::MEDIA_TYPES[$this->media_type] ?? ucfirst(str_replace('_', ' ', $this->media_type ?: $this->type));
    }

    /**
     * Average rating accessor.
     */
    public function getRatingsAvgAttribute(): ?float
    {
        if (array_key_exists('ratings_avg', $this->attributes)) {
            return $this->attributes['ratings_avg'] !== null ? (float) $this->attributes['ratings_avg'] : null;
        }

        $avg = $this->ratings()->avg('score');
        return $avg !== null ? round((float) $avg, 1) : null;
    }

    /**
     * Ratings count accessor.
     */
    public function getRatingsCountAttribute(): int
    {
        if (array_key_exists('ratings_count', $this->attributes)) {
            return (int) $this->attributes['ratings_count'];
        }

        return (int) $this->ratings()->count();
    }

    /**
     * Check if a specific user rated this media.
     */
    public function userRating(?User $user = null): ?int
    {
        if (! $user) {
            $user = auth()->user();
        }

        if (! $user) {
            return null;
        }

        return $this->ratings()->where('user_id', $user->id)->value('score');
    }

    /**
     * Fallback for legacy media source attribute.
     */
    public function getMediaSourceAttribute(): ?string
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return $this->external_url;
    }
}

