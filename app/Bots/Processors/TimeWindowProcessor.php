<?php

namespace App\Bots\Processors;

use App\Bots\Interfaces\ProcessorInterface;

class TimeWindowProcessor implements ProcessorInterface
{
    private string $eventType;
    private string $field;
    private int $windowMinutes;

    public function __construct(string $eventType, string $field, int $windowMinutes)
    {
        $this->eventType = $eventType;
        $this->field = $field;
        $this->windowMinutes = $windowMinutes;
    }

    public function processStates(mixed $currentState, mixed $previousState = null): array
    {
        if (!isset($currentState[$this->field])) return [];
        $eventTime = strtotime($currentState[$this->field]);
        if ($eventTime === false) return [];

        $now = time();
        if ($now - $eventTime <= $this->windowMinutes * 60) {
            return [[
                'type' => $this->eventType,
                'field' => $this->field,
                'event_time' => $currentState[$this->field],
                'window_minutes' => $this->windowMinutes,
                'timestamp' => now()
            ]];
        }
        return [];
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }
}
