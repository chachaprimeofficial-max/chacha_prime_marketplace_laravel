<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    public function ask(string $prompt): string
    {
        $key = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-2.5-flash');

        if (!$key) {
            return 'AI assistant is not configured yet. Please add GEMINI_API_KEY to the server .env file.';
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";
        $response = Http::timeout(30)->post($url, [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.4, 'maxOutputTokens' => 700],
        ]);

        if (!$response->successful()) {
            report(new \RuntimeException('Gemini API error: '.$response->body()));
            return 'Sorry, the AI assistant is temporarily unavailable.';
        }

        return (string) data_get($response->json(), 'candidates.0.content.parts.0.text', 'Sorry, I could not generate a response.');
    }
}
