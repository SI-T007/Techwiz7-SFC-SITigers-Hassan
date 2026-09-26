<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user is an Administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a standard Registered User.
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * User profile (one-to-one).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * User's favorite categories/fandoms (many-to-many).
     */
    public function favoriteCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_user')->withTimestamps();
    }

    /**
     * Ratings submitted by user.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Bookmarks created by user.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Fan submissions created by user.
     */
    public function fanSubmissions(): HasMany
    {
        return $this->hasMany(FanSubmission::class);
    }

    /**
     * Feedback submitted by user.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Activity log for this user.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class)->orderByDesc('created_at');
    }

    /**
     * Watch history for this user.
     */
    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class, 'user_id')->orderByDesc('watched_at');
    }

    /**
     * Chatbot conversations for this user.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(ChatbotConversation::class);
    }

    /**
     * Helper to get avatar URL (from profile or UI avatar fallback).
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->relationLoaded('profile') && $this->profile && $this->profile->avatar) {
            return asset('storage/' . $this->profile->avatar);
        }
        
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=ffffff&bold=true&rounded=true";
    }

    /**
     * Helper to get display name (or full name fallback).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('profile') && $this->profile && !empty($this->profile->display_name)) {
            return $this->profile->display_name;
        }

        return $this->name;
    }
}
