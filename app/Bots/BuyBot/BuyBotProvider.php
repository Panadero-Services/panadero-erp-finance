<?php

namespace App\Bots\BuyBot;

use App\Bots\Interfaces\ProviderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class BuyBotProvider implements ProviderInterface
{
    private Client $httpClient;
    private string $apiUrl;
    private string $cacheKey;

    public function __construct(Client $httpClient, string $apiUrl, string $cacheKey = 'buybot_last_block')
    {
        $this->httpClient = $httpClient;
        $this->apiUrl = $apiUrl;
        $this->cacheKey = $cacheKey;
    }

    public function getCurrentState(): mixed
    {
        try {
            $response = $this->httpClient->get($this->apiUrl);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('BuyBot API error: ' . $e->getMessage());
            return null;
        }
    }

    public function getPreviousState(): mixed|null
    {
        return Cache::get($this->cacheKey);
    }

    public function saveState(mixed $state): void
    {
        Cache::put($this->cacheKey, $state, now()->addMinutes(5));
    }
}
