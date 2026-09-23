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

    public function askJson(string $prompt, array $schema): string
    {
        $key = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-2.5-flash');

        if (!$key) {
            return json_encode([
                'answer' => 'AI assistant is not configured yet. Please contact support.',
                'escalate' => true,
                'department' => 'technical',
                'priority' => 'normal',
                'reason' => 'Gemini API key is not configured.',
            ]);
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}";
        $response = Http::timeout(30)->post($url, [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 900,
                'responseMimeType' => 'application/json',
                'responseSchema' => $schema,
            ],
        ]);

        if (!$response->successful()) {
            report(new \RuntimeException('Gemini JSON API error: '.$response->body()));
            return json_encode([
                'answer' => 'I could not safely process this request right now, so I am transferring it to a support agent.',
                'escalate' => true,
                'department' => 'technical',
                'priority' => 'normal',
                'reason' => 'Gemini structured response failed.',
            ]);
        }

        return (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '{}');
    }
}
