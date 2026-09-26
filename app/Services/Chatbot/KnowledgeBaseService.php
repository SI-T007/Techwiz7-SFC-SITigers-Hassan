<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotKnowledgeBase;
use Illuminate\Support\Str;

class KnowledgeBaseService
{
    /**
     * Find best matching knowledge base entry for a user question.
     *
     * @param string $query
     * @return array|null Returns ['text' => string, 'kb_id' => int] or null
     */
    public function findMatch(string $query): ?array
    {
        $normalized = strtolower(trim($query));
        $words = array_filter(explode(' ', preg_replace('/[^\w\s]/', '', $normalized)), fn($w) => strlen($w) > 2);

        $entries = ChatbotKnowledgeBase::active()->with('category')->get();
        if ($entries->isEmpty()) {
            return null;
        }

        $bestEntry = null;
        $highestScore = 0;

        foreach ($entries as $entry) {
            $score = 0;
            $qLower = strtolower($entry->question);

            // Exact match or substring match in question
            if ($normalized === $qLower) {
                $score += 100;
            } elseif (Str::contains($qLower, $normalized) || Str::contains($normalized, $qLower)) {
                $score += 50;
            }

            // Keyword matches
            if (!empty($entry->keywords) && is_array($entry->keywords)) {
                foreach ($entry->keywords as $kw) {
                    $kwLower = strtolower(trim($kw));
                    if (Str::contains($normalized, $kwLower)) {
                        $score += 25;
                    }
                }
            }

            // Word overlap
            foreach ($words as $w) {
                if (Str::contains($qLower, $w)) {
                    $score += 5;
                }
            }

            if ($score > $highestScore && $score >= 20) {
                $highestScore = $score;
                $bestEntry = $entry;
            }
        }

        if ($bestEntry) {
            return [
                'text' => $bestEntry->answer,
                'kb_id' => $bestEntry->id,
                'source' => 'knowledge_base',
            ];
        }

        return null;
    }
}
