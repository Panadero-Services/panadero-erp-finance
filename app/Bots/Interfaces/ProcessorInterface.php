<?php

namespace App\Bots\Interfaces;

interface ProcessorInterface
{
    /**
     * Process the current and previous states to detect events
     * @param mixed $currentState The current state
     * @param mixed|null $previousState The previous state
     * @return array Array of detected events
     */
    public function processStates(mixed $currentState, mixed $previousState = null): array;

    /**
     * Get the event type that this processor handles
     * @return string The event type identifier
     */
    public function getEventType(): string;
}
