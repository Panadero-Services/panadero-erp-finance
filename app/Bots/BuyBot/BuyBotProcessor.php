<?php

namespace App\Bots\BuyBot;

use App\Bots\Interfaces\ProcessorInterface;

class BuyBotProcessor implements ProcessorInterface
{
    public function processStates(mixed $currentState, mixed $previousState = null): array
    {
        if (!$currentState || !isset($currentState['buys'])) {
            return [];
        }
        $newBuys = [];
        $prevTxs = $previousState['buys'] ?? [];
        $prevHashes = array_column($prevTxs, 'txHash');

        foreach ($currentState['buys'] as $buy) {
            if (!in_array($buy['txHash'], $prevHashes)) {
                $newBuys[] = [
                    'type' => 'buy',
                    'buyer' => $buy['buyer'],
                    'amount' => $buy['amount'],
                    'txHash' => $buy['txHash'],
                    'timestamp' => $buy['timestamp'],
                    'value' => $buy['value'] ?? null,
                ];
            }
        }
        return $newBuys;
    }

    public function getEventType(): string
    {
        return 'buy';
    }
}
