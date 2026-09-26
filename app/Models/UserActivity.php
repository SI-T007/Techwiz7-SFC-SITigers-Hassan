<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    use HasFactory;

    // The user_activities table only has created_at, no updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'activity_type',
        'subject_type',
        'subject_id',
        'description',
        'created_at',
    ];

    /**
     * User owning this activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity.
     */
    public static function record(?int $userId, string $type, ?string $description = null, ?string $subjectType = null, ?int $subjectId = null): ?self
    {
        if (!$userId) {
            return null;
        }

        // Map general action types to allowed enum values
        $validTypes = [
            'viewed_content',
            'bookmarked',
            'rated',
            'submitted_feedback',
            'submitted_fan_content',
            'updated_profile',
        ];

        $enumType = in_array($type, $validTypes) ? $type : 'updated_profile';

        return self::create([
            'user_id' => $userId,
            'activity_type' => $enumType,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description ?? 'User activity recorded',
            'created_at' => now(),
        ]);
    }
}
