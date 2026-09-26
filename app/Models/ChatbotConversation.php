<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $table = 'chatbot_conversations';

    protected $fillable = [
        'user_id',
        'title',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id')->orderBy('id', 'asc');
    }

    /**
     * Get the latest message in this conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(ChatbotMessage::class, 'conversation_id')->latestOfMany('id');
    }

    /**
     * Auto-generate title from first user query if still default.
     */
    public function updateTitleFromQuery(string $query): void
    {
        if ($this->title === 'New Conversation' || empty($this->title)) {
            $this->update([
                'title' => Str::limit(trim($query), 45, '...'),
            ]);
        }
    }
}
