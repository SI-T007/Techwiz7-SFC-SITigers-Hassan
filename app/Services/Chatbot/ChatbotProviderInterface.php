<?php

namespace App\Services\Chatbot;

interface ChatbotProviderInterface
{
    /**
     * Generate an AI response from a prompt and conversation context.
     *
     * @param string $prompt
     * @param array $conversationHistory
     * @return string|null Returns null if provider fails, is unconfigured, or times out.
     */
    public function generateResponse(string $prompt, array $conversationHistory = []): ?string;

    /**
     * Check if the AI provider is properly configured with an API key.
     */
    public function isAvailable(): bool;
}
