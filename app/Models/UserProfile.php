<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'display_name',
        'bio',
        'location',
        'website',
        'display_preferences',
        'accessibility_preferences',
    ];

    protected function casts(): array
    {
        return [
            'display_preferences' => 'array',
            'accessibility_preferences' => 'array',
        ];
    }

    /**
     * User owning this profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Avatar URL helper.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        $name = urlencode($this->display_name ?: ($this->user->name ?? 'User'));
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=ffffff&bold=true&rounded=true";
    }

    /**
     * Default preferences helper.
     */
    public static function defaultDisplayPreferences(): array
    {
        return [
            'theme' => 'dark',
            'language' => 'en',
            'animations' => true,
        ];
    }

    public static function defaultAccessibilityPreferences(): array
    {
        return [
            'reduced_motion' => false,
            'high_contrast' => false,
            'font_size' => 'normal',
        ];
    }
}
