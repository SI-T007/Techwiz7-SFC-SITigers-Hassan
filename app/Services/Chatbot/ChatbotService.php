<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\User;
use Illuminate\Support\Str;

class ChatbotService
{
    public function __construct(
        protected KnowledgeBaseService $kbService,
        protected DatabaseQueryService $dbService,
        protected ChatbotProviderInterface $aiProvider
    ) {}

    /**
     * Process a user message in a conversation and return the assistant response.
     *
     * @param ChatbotConversation $conversation
     * @param string $message
     * @return ChatbotMessage
     */
    public function handleUserMessage(ChatbotConversation $conversation, string $message): ChatbotMessage
    {
        $sanitizedMessage = strip_tags(trim($message));
        $sanitizedMessage = Str::limit($sanitizedMessage, 1000, '');

        // 1. Record User Message
        $userMessage = ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => $sanitizedMessage,
            'source' => 'user',
        ]);

        // Auto update conversation title if first message
        $conversation->updateTitleFromQuery($sanitizedMessage);

        // 2. Step 1: Check Database Query Service (High Priority verified facts)
        $dbMatch = $this->dbService->queryDatabase($sanitizedMessage);
        if ($dbMatch) {
            return ChatbotMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'assistant',
                'message' => $dbMatch['text'],
                'source' => 'database',
                'metadata' => $dbMatch['metadata'] ?? null,
            ]);
        }

        // 3. Step 2: Check Admin Knowledge Base (FAQ matching)
        $kbMatch = $this->kbService->findMatch($sanitizedMessage);
        if ($kbMatch) {
            return ChatbotMessage::create([
                'conversation_id' => $conversation->id,
                'sender' => 'assistant',
                'message' => $kbMatch['text'],
                'source' => 'knowledge_base',
                'matched_kb_id' => $kbMatch['kb_id'],
            ]);
        }

        // 4. Step 3: Check AI Provider (If configured and online)
        if ($this->aiProvider->isAvailable()) {
            $history = $conversation->messages()
                ->latest('id')
                ->take(6)
                ->get()
                ->reverse()
                ->toArray();

            $aiResponse = $this->aiProvider->generateResponse($sanitizedMessage, $history);
            if ($aiResponse) {
                return ChatbotMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender' => 'assistant',
                    'message' => $aiResponse,
                    'source' => 'ai',
                ]);
            }
        }

        // 5. Step 4: Intelligent Platform Fallback
        $fallbackText = $this->generateFallbackResponse($sanitizedMessage);
        return ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'assistant',
            'message' => $fallbackText,
            'source' => 'fallback',
        ]);
    }

    /**
     * Generate structured friendly fallback when no exact match is found.
     */
    protected function generateFallbackResponse(string $query): string
    {
        return "I couldn't find a direct match for your question in our database, but I can guide you to explore Fan Hub Plus:\n\n" .
            "• **[Fandom Hubs](" . route('fandoms.index') . ")** — Browse Anime, Gaming, Movies, TV Shows, K-Pop, Comics, Manga, and Cosplay.\n" .
            "• **[Upcoming Events](" . route('events.index') . ")** — Global conventions, tournaments, and location-aware search.\n" .
            "• **[Releases Radar](" . route('releases.index') . ")** — Target launch dates and collector drops.\n" .
            "• **[Multimedia Center](" . route('multimedia.index') . ")** — Video reels, audio playlists, and gallery art.\n" .
            "• **[Character Lore](" . route('characters.index') . ")** — Canonical hero profiles and abilities.\n\n" .
            "Try asking: *\"What fandoms are available?\"*, *\"Show upcoming events\"*, or *\"How do I bookmark content?\"*";
    }

    /**
     * Get or create active conversation for a user or guest session.
     */
    public function getOrCreateConversation(?User $user, ?string $sessionId = null): ChatbotConversation
    {
        if ($user) {
            $conversation = ChatbotConversation::where('user_id', $user->id)
                ->latest('updated_at')
                ->first();

            if (! $conversation) {
                $conversation = ChatbotConversation::create([
                    'user_id' => $user->id,
                    'title' => 'New Conversation',
                ]);
            }

            return $conversation;
        }

        // Guest Session
        $sessionId = $sessionId ?: session()->getId();
        $conversation = ChatbotConversation::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->latest('updated_at')
            ->first();

        if (! $conversation) {
            $conversation = ChatbotConversation::create([
                'session_id' => $sessionId,
                'title' => 'Guest Conversation',
            ]);
        }

        return $conversation;
    }
}
