<?php

namespace App\Bots\Interfaces;

interface ExecutorInterface
{
    /**
     * Handle a detected event
     * @param string $eventType The type of event
     * @param array $eventData The event data
     * @return void
     */
    public function handleEvent(string $eventType, array $eventData): void;

    /**
     * Get supported notification channels
     * @return array Array of channel identifiers
     */
    public function getSupportedChannels(): array;

    /**
     * Send notification through a specific channel
     * @param string $channel The notification channel
     * @param array $data The notification data
     * @return void
     */
    public function sendNotification(string $channel, array $data): void;
}
