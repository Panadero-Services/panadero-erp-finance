<?php

namespace App\Bots\Providers;

use App\Bots\Interfaces\ProviderInterface;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ApiProvider implements ProviderInterface
{
    private Client $httpClient;
    private string $apiUrl; // Changed from baseUrl
    private string $cacheKey;
    private string $httpMethod;
    private array $headers;
    private int $cacheTtlSeconds;


    public function __construct(array $config)
    {
        // Validate and extract required config values
        if (!isset($config['url'])) { // Expect 'url'
            throw new \InvalidArgumentException('ApiProvider config missing required key: url');
        }
        if (!isset($config['method'])) { // Expect 'method'
            throw new \InvalidArgumentException('ApiProvider config missing required key: method');
        }

        $this->httpClient = new Client(); // Or configure Guzzle via $config if needed
        $this->apiUrl = $config['url'];
        $this->httpMethod = strtoupper($config['method']); // Store method, typically uppercase
        $this->headers = $config['headers'] ?? []; // Get headers, default to empty array

        // Use 'cache_key_prefix' for the cache key, make it more unique
        $this->cacheKey = ($config['cache_key_prefix'] ?? 'api_provider_state') . '_' . md5($this->apiUrl . $this->httpMethod . serialize($this->headers));
        
        // Use 'cache_ttl_seconds'
        $this->cacheTtlSeconds = $config['cache_ttl_seconds'] ?? 3600; // Default to 1 hour (3600 seconds)
    }

    // Assuming your ProviderInterface defines getCurrentState, getPreviousState, saveState
    // And ProviderController@test calls fetchCurrentState.
    // Let's align with fetchCurrentState as it was in BscBuyEventProvider and controller.
    public function fetchCurrentState(): mixed
    {
        try {
            $response = $this->httpClient->request($this->httpMethod, $this->apiUrl, [
                'headers' => $this->headers,
                'timeout' => $config['rpc_timeout'] ?? 30, // Use rpc_timeout from config or default
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('API Provider Error fetching current state: ' . $e->getMessage(), [
                'url' => $this->apiUrl,
                'method' => $this->httpMethod,
                'headers' => $this->headers
            ]);
            return null;
        }
    }

    public function fetchPreviousState(): mixed // Aligned name
    {
        return Cache::get($this->cacheKey);
    }

    public function saveState(mixed $state): void
    {
        Cache::put($this->cacheKey, $state, now()->addSeconds($this->cacheTtlSeconds));
    }

    // If your interface strictly uses getCurrentState, getPreviousState:
    // public function getCurrentState(): mixed { return $this->fetchCurrentState(); }
    // public function getPreviousState(): mixed { return $this->fetchPreviousState(); }
}