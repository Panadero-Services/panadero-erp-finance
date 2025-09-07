<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIServiceController extends Controller
{
    public function callAI(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'provider' => 'required|string|in:openai,claude,ollama,custom',
                'prompt' => 'required|string',
                'context' => 'array',
                'config' => 'required|array'
            ]);

            $config = $validated['config'];
            $prompt = $validated['prompt'];
            $context = $validated['context'] ?? [];

            switch ($validated['provider']) {
                case 'openai':
                    return $this->callOpenAI($prompt, $context, $config);
                case 'claude':
                    return $this->callClaude($prompt, $context, $config);
                case 'ollama':
                    return $this->callOllama($prompt, $context, $config);
                case 'custom':
                    return $this->callCustomAPI($prompt, $context, $config);
                default:
                    return response()->json(['error' => 'Unknown provider'], 400);
            }
        } catch (\Exception $e) {
            Log::error('AI Service Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function callOpenAI(string $prompt, array $context, array $config): JsonResponse
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $config['apiKey'],
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $config['model'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an inventory management AI assistant. Provide helpful insights about inventory data, stock optimization, and supply chain management.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt . "\n\nContext: " . json_encode($context)
                ]
            ],
            'max_tokens' => $config['maxTokens'] ?? 500,
            'temperature' => $config['temperature'] ?? 0.7
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        return response()->json(['response' => $data['choices'][0]['message']['content']]);
    }

    private function callClaude(string $prompt, array $context, array $config): JsonResponse
    {
        $response = Http::withHeaders([
            'x-api-key' => $config['apiKey'],
            'Content-Type' => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => $config['model'],
            'max_tokens' => $config['maxTokens'] ?? 500,
            'messages' => [[
                'role' => 'user',
                'content' => "You are an inventory management AI assistant. {$prompt}\n\nContext: " . json_encode($context)
            ]]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        return response()->json(['response' => $data['content'][0]['text']]);
    }

    private function callOllama(string $prompt, array $context, array $config): JsonResponse
    {
        $baseUrl = $config['baseUrl'] ?? 'http://localhost:11434';
        
        $response = Http::post("{$baseUrl}/api/generate", [
            'model' => $config['model'],
            'prompt' => "You are an inventory management AI assistant. {$prompt}\n\nContext: " . json_encode($context),
            'stream' => false,
            'options' => [
                'temperature' => $config['temperature'] ?? 0.7
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Ollama API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        return response()->json(['response' => $data['response']]);
    }

    private function callCustomAPI(string $prompt, array $context, array $config): JsonResponse
    {
        $response = Http::post($config['baseUrl'], [
            'prompt' => $prompt,
            'context' => $context,
            'model' => $config['model'],
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['maxTokens'] ?? 500
        ]);

        if (!$response->successful()) {
            throw new \Exception('Custom API error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        return response()->json(['response' => $data['response'] ?? $data['content'] ?? $data['message']]);
    }
}