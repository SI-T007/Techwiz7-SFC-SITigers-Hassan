<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $table = 'chatbot_messages';

    protected $fillable = [
        'conversation_id',
        'sender',
        'message',
        'source',
        'matched_kb_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(ChatbotKnowledgeBase::class, 'matched_kb_id');
    }

    public function isFromUser(): bool
    {
        return $this->sender === 'user';
    }

    public function isFromAssistant(): bool
    {
        return $this->sender === 'assistant';
    }
}
