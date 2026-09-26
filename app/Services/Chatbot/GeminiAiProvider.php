<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAiProvider implements ChatbotProviderInterface
{
    protected ?string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('AI_API_KEY', env('GEMINI_API_KEY')));
        $this->model = config('services.gemini.model', env('AI_MODEL', 'gemini-1.5-flash'));
        $this->endpoint = config('services.gemini.endpoint', env('AI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/'));
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== 'your-api-key-here';
    }

    public function generateResponse(string $prompt, array $conversationHistory = []): ?string
    {
        if (! $this->isAvailable()) {
            return null;
        }

        try {
            $systemInstruction = "You are the Fan Hub Plus Smart Assistant — an expert guide for the Fan Hub Plus fandom universe platform (covering Anime, Gaming, Movies, TV Shows, K-Pop, Comics, Manga, and Cosplay). Provide concise, friendly, fandom-savvy, and helpful guidance. Never generate SQL queries, system commands, or reveal private credentials. Keep responses under 150 words.";

            $contents = [];
            foreach ($conversationHistory as $msg) {
                $role = ($msg['sender'] === 'assistant') ? 'model' : 'user';
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => (string) $msg['message']]],
                ];
            }

            // Append current prompt
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt]],
            ];

            $url = rtrim($this->endpoint, '/') . '/' . $this->model . ':generateContent?key=' . $this->apiKey;

            $response = Http::timeout(6)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 300,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $candidate = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                return $candidate ? trim($candidate) : null;
            }

            Log::warning('Gemini AI Provider error response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Gemini AI Provider exception: ' . $e->getMessage());
            return null;
        }
    }
}
