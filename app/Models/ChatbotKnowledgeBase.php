<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotKnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'chatbot_knowledge';

    protected $fillable = [
        'category_id',
        'question',
        'answer',
        'keywords',
        'is_active',
        'helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'is_active' => 'boolean',
            'helpful_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope for active knowledge entries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search by question, answer, or keywords.
     */
    public function scopeSearch($query, string $term)
    {
        $term = trim($term);
        return $query->where(function ($q) use ($term) {
            $q->where('question', 'like', "%{$term}%")
              ->orWhere('answer', 'like', "%{$term}%")
              ->orWhere('keywords', 'like', "%{$term}%");
        });
    }

    /**
     * Increment helpful count.
     */
    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }
}
