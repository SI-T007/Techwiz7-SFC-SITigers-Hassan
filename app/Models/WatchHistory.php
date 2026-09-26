<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchHistory extends Model
{
    use HasFactory;

    protected $table = 'watch_history';

    // No updated_at — history entries are append-only
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'watchable_type',
        'watchable_id',
        'title',
        'thumbnail',
        'type',
        'url',
        'watched_at',
    ];

    protected function casts(): array
    {
        return [
            'watched_at' => 'datetime',
        ];
    }

    /**
     * The user who watched this item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic: returns either Content or Media instance.
     */
    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record a watch history entry for an authenticated user.
     * Upserts — updates watched_at if the same item was already viewed today.
     */
    public static function record(
        int    $userId,
        string $watchableType,
        int    $watchableId,
        string $title,
        string $url,
        ?string $thumbnail = null,
        ?string $type = null
    ): void {
        // Check if user already watched this item in the last 30 minutes → skip duplicate
        $recent = static::where('user_id', $userId)
            ->where('watchable_type', $watchableType)
            ->where('watchable_id', $watchableId)
            ->where('watched_at', '>=', now()->subMinutes(30))
            ->exists();

        if ($recent) {
            return;
        }

        static::create([
            'user_id'        => $userId,
            'watchable_type' => $watchableType,
            'watchable_id'   => $watchableId,
            'title'          => mb_substr($title, 0, 490),
            'thumbnail'      => $thumbnail,
            'type'           => $type,
            'url'            => $url,
            'watched_at'     => now(),
        ]);
    }

    /**
     * Type badge label for display.
     */
    public function getTypeLabelAttribute(): string
    {
        return match (true) {
            str_contains($this->type, 'anime')   => 'Anime',
            str_contains($this->type, 'gaming')  => 'Gaming',
            str_contains($this->type, 'movie')   => 'Movie',
            str_contains($this->type, 'tv')      => 'TV',
            str_contains($this->type, 'trailer') => 'Trailer',
            str_contains($this->type, 'video')   => 'Video',
            str_contains($this->type, 'audio')   => 'Music',
            str_contains($this->type, 'article') => 'Article',
            $this->type === 'content'             => 'Lore',
            $this->type === 'media'               => 'Media',
            default                               => ucfirst($this->type ?? 'View'),
        };
    }

    /**
     * Bootstrap: always append 'created_at' as timestamp name alias.
     */
    public function getCreatedAtColumn(): string
    {
        return 'watched_at';
    }
}
