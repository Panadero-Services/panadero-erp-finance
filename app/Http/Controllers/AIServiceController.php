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
        // Use custom system prompt if provided, otherwise default
        $systemPrompt = $config['systemPrompt'] ?? 'You are a helpful AI assistant. Provide clear, accurate, and helpful responses.';
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $config['model'],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt
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
        $systemPrompt = $config['systemPrompt'] ?? 'You are a helpful AI assistant.';
        
        // Optimize prompt length - use shorter, more focused prompts
        $optimizedPrompt = $this->optimizePrompt($systemPrompt, $prompt, $context);
        
        $startTime = microtime(true);
        
        try {
            $response = Http::timeout(30) // Reduced timeout for faster failure detection
                ->retry(2, 1000) // Retry twice with 1 second delay
                ->post("{$baseUrl}/api/generate", [
                    'model' => $config['model'],
                    'prompt' => $optimizedPrompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => $config['temperature'] ?? 0.7,
                        'num_predict' => 500, // Limit response length
                        'stop' => ['\n\n\n', 'User Question:', 'Context:'] // Stop tokens
                    ]
                ]);

            $responseTime = (microtime(true) - $startTime) * 1000;

            if (!$response->successful()) {
                $this->logError('Ollama API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'model' => $config['model'],
                    'response_time' => $responseTime
                ]);
                throw new \Exception('Ollama API error: ' . $response->status() . ' - ' . $response->body());
            }

            $data = $response->json();
            
            // Log successful request
            $this->logSuccess([
                'model' => $config['model'],
                'response_time' => $responseTime,
                'tokens_generated' => strlen($data['response'] ?? '')
            ]);

            return response()->json(['response' => $data['response']]);
            
        } catch (\Exception $e) {
            $responseTime = (microtime(true) - $startTime) * 1000;
            $this->logError('Ollama Request Failed', [
                'error' => $e->getMessage(),
                'model' => $config['model'],
                'response_time' => $responseTime,
                'timeout' => $responseTime > 29000
            ]);
            throw $e;
        }
    }

    private function optimizePrompt(string $systemPrompt, string $prompt, array $context): string
    {
        // Use shorter, more focused system prompts
        $shortSystemPrompt = $this->getShortSystemPrompt($systemPrompt);
        
        // Limit context to essential information only
        $essentialContext = [
            'module' => $context['module'] ?? 'general',
            'timestamp' => $context['timestamp'] ?? date('Y-m-d H:i:s')
        ];
        
        return "{$shortSystemPrompt}\n\nQ: {$prompt}\nA:";
    }

    private function getShortSystemPrompt(string $fullPrompt): string
    {
        // Extract key role from full prompt
        if (strpos($fullPrompt, 'ERP Finance') !== false) {
            return "You are an ERP Finance expert. Answer concisely.";
        } elseif (strpos($fullPrompt, 'ERP HR') !== false) {
            return "You are an ERP HR expert. Answer concisely.";
        } elseif (strpos($fullPrompt, 'ERP Inventory') !== false) {
            return "You are an ERP Inventory expert. Answer concisely.";
        } elseif (strpos($fullPrompt, 'ERP Compliance') !== false) {
            return "You are an ERP Compliance expert. Answer concisely.";
        } elseif (strpos($fullPrompt, 'calculator') !== false) {
            return "You are a business calculator. Show step-by-step math.";
        }
        
        return "You are an ERP expert. Answer concisely.";
    }

    private function logError(string $type, array $data): void
    {
        \Log::error("AI Service Error: {$type}", $data);
        
        // Store in database for dashboard
        \App\Models\AIErrorLog::create([
            'error_type' => $type,
            'error_data' => $data,
            'created_at' => now()
        ]);
    }

    private function logSuccess(array $data): void
    {
        \Log::info("AI Service Success", $data);
        
        // Store in database for dashboard
        \App\Models\AISuccessLog::create([
            'success_data' => $data,
            'created_at' => now()
        ]);
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