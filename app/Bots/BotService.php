<?php

namespace App\Bots;

use App\Bots\Interfaces\ProviderInterface;
use App\Bots\Interfaces\ProcessorInterface;
use App\Bots\Interfaces\ExecutorInterface;
use Illuminate\Support\Facades\Log;

class BotService
{
    private ProviderInterface $provider;
    private ProcessorInterface $processor;
    private ExecutorInterface $executor;

    public function __construct(
        ProviderInterface $provider,
        ProcessorInterface $processor,
        ExecutorInterface $executor
    ) {
        $this->provider = $provider;
        $this->processor = $processor;
        $this->executor = $executor;
    }

    public function process(): void
    {
        try {
            // Get current state from provider
            $currentState = $this->provider->getCurrentState();
            if ($currentState === null) {
                Log::warning('Provider returned null state');
                return;
            }

            // Get previous state
            $previousState = $this->provider->getPreviousState();

            // Process states to detect events
            $events = $this->processor->processStates($currentState, $previousState);

            // Handle each detected event
            foreach ($events as $event) {
                $this->executor->handleEvent(
                    $this->processor->getEventType(),
                    $event
                );
            }

            // Save current state
            $this->provider->saveState($currentState);

        } catch (\Exception $e) {
            Log::error('Bot processing error: ' . $e->getMessage());
        }
    }
}
