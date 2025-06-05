<?php

namespace App\Bots\Providers;

use App\Bots\Interfaces\ProviderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Eth; // For type hinting
use Web3\Contract;

class BscBuyEventProvider implements ProviderInterface
{
    private Eth $eth;
    private string $contractAddress;
    private string $eventTopic0;
    private array $eventAbi; // Minimal ABI for the event
    private int $startBlock;
    private int $blocksPerRequest;
    private string $cacheKey;
    private int $cacheTtlMinutes;
    private array $config;
    private ?int $lastBlockToSave = null;

    public function __construct(array $config)
    {
        $this->validateConfig($config);
        $this->config = $config;

        $this->contractAddress = $config['contract_address'];
        $this->eventTopic0 = $config['event_topic0'];
        $this->eventAbi = $config['event_abi']; // Expects an array containing the single event ABI definition
        $this->startBlock = $config['start_block'] ?? 0;
        $this->blocksPerRequest = $config['blocks_per_request'] ?? 2000;
        $this->cacheKey = $config['cache_key_prefix'] . '_last_block_web3p'; // Differentiate cache key
        $this->cacheTtlMinutes = $config['cache_ttl_minutes'] ?? 1440;

        $timeout = $config['rpc_timeout'] ?? 30;
        $httpProvider = new HttpProvider(new HttpRequestManager($config['rpc_url'], $timeout));
        $web3 = new Web3($httpProvider);
        $this->eth = $web3->eth;
    }

    private function validateConfig(array $config): void
    {
        $requiredKeys = ['rpc_url', 'contract_address', 'event_topic0', 'event_abi', 'cache_key_prefix'];
        foreach ($requiredKeys as $key) {
            if (!isset($config[$key])) {
                throw new \InvalidArgumentException("BscBuyEventProvider config missing required key: {$key}");
            }
        }
        if (!is_array($config['event_abi']) || empty($config['event_abi']) || !isset($config['event_abi'][0]['type']) || $config['event_abi'][0]['type'] !== 'event') {
            throw new \InvalidArgumentException("BscBuyEventProvider 'event_abi' must be a non-empty array containing a single event definition.");
        }
    }

    public function fetchCurrentState(): mixed
    {
        $latestBlock = $this->getLatestBlockNumber();
        if ($latestBlock === null) {
            Log::error("BscBuyEventProvider: Could not fetch latest block number from RPC: " . $this->config['rpc_url']);
            return [];
        }

        $fromBlock = (int) Cache::get($this->cacheKey, $this->startBlock);
        $fromBlock = min($fromBlock, $latestBlock);
        $fromBlock = $fromBlock > 0 ? $fromBlock + 1 : $this->startBlock;

        $events = [];
        $currentScanBlock = $fromBlock;

        while ($currentScanBlock <= $latestBlock) {
            $toBlock = min($currentScanBlock + $this->blocksPerRequest - 1, $latestBlock);
            if ($currentScanBlock > $toBlock) break;

            Log::info("BscBuyEventProvider: Scanning from block {$currentScanBlock} to {$toBlock} for contract {$this->contractAddress} (web3p)");

            $params = [
                'fromBlock' => '0x' . dechex($currentScanBlock),
                'toBlock' => '0x' . dechex($toBlock),
                'address' => $this->contractAddress,
                'topics' => [$this->eventTopic0],
            ];

            $logsData = null;
            $this->eth->getLogs($params, function ($err, $result) use (&$logsData) {
                if ($err) {
                    Log::error("BscBuyEventProvider: Error fetching logs (web3p): " . $err->getMessage());
                    return;
                }
                $logsData = $result;
            });

            if ($logsData === null) {
                Log::warning("BscBuyEventProvider: Failed to get logs for block range {$currentScanBlock}-{$toBlock} (web3p). Skipping this range.");
                $currentScanBlock = $toBlock + 1;
                continue;
            }

            if (!empty($logsData)) {
                // The event_abi passed to constructor should be an array containing the single event definition
                $contract = new Contract($this->config['rpc_url'], $this->eventAbi); 
                $eventDefinition = $this->eventAbi[0]; // Use the single event ABI from the config

                foreach ($logsData as $log) {
                    try {
                        // Ensure the log's topic0 matches the one we're looking for (already filtered by eth_getLogs, but good practice)
                        if (strtolower($log->topics[0]) !== strtolower($this->eventTopic0)) {
                            continue;
                        }

                        $decodedEventData = $contract->getEventMapper()->decode($eventDefinition, $log);
                        
                        $decodedEvent = (array) $decodedEventData; // Cast to array for easier access
                        $decodedEvent['transactionHash'] = $log->transactionHash;
                        $decodedEvent['blockNumber'] = Utils::hexToNumber($log->blockNumber);
                        $decodedEvent['logIndex'] = Utils::hexToNumber($log->logIndex);
                        // web3.php often returns BigNumber objects for uints, convert them to strings or numbers
                        foreach ($decodedEvent as $key => $value) {
                            if (is_object($value) && method_exists($value, 'toString')) { // Check for BigNumber
                                $decodedEvent[$key] = $value->toString();
                            }
                        }
                        $events[] = $decodedEvent;
                    } catch (\Exception $e) {
                        Log::error("BscBuyEventProvider: Error decoding event log (web3p): " . $e->getMessage(), ['log' => $log, 'trace' => $e->getTraceAsString()]);
                    }
                }
            }
            $currentScanBlock = $toBlock + 1;
        }
        
        if (!empty($events)) {
            $this->lastBlockToSave = max(array_column($events, 'blockNumber'));
        } elseif ($fromBlock <= $latestBlock) {
            $this->lastBlockToSave = $latestBlock;
        } else {
            $this->lastBlockToSave = $fromBlock - 1;
        }

        return $events;
    }

    public function saveState(): void
    {
        if ($this->lastBlockToSave !== null) {
            Cache::put($this->cacheKey, $this->lastBlockToSave, now()->addMinutes($this->cacheTtlMinutes));
            Log::info("BscBuyEventProvider: Saved last processed block: {$this->lastBlockToSave} for key {$this->cacheKey} (web3p)");
            $this->lastBlockToSave = null;
        }
    }

    public function fetchPreviousState(): mixed
    {
        return (int) Cache::get($this->cacheKey, $this->startBlock);
    }

    private function getLatestBlockNumber(): ?int
    {
        $blockNumber = null;
        $this->eth->blockNumber(function ($err, $result) use (&$blockNumber) {
            if ($err) {
                Log::error("BscBuyEventProvider: Error fetching block number (web3p): " . $err->getMessage());
                return;
            }
            $blockNumber = Utils::hexToNumber($result);
        });
        return $blockNumber;
    }
}