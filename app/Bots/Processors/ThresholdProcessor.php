<?php

namespace App\Bots\Processors;

use App\Bots\Interfaces\ProcessorInterface;

class ThresholdProcessor implements ProcessorInterface
{
    private string $eventType;
    private string $field;
    private float $threshold;
    private string $direction; // 'above' or 'below'

    public function __construct(string $eventType, string $field, float $threshold, string $direction = 'above')
    {
        $this->eventType = $eventType;
        $this->field = $field;
        $this->threshold = $threshold;
        $this->direction = $direction;
    }

    public function processStates(mixed $currentState, mixed $previousState = null): array
    {
        if (!isset($currentState[$this->field])) return [];
        $value = $currentState[$this->field];

        if ($this->direction === 'above' && $value > $this->threshold) {
            return [[
                'type' => $this->eventType,
                'field' => $this->field,
                'value' => $value,
                'threshold' => $this->threshold,
                'direction' => 'above',
                'timestamp' => now()
            ]];
        }
        if ($this->direction === 'below' && $value < $this->threshold) {
            return [[
                'type' => $this->eventType,
                'field' => $this->field,
                'value' => $value,
                'threshold' => $this->threshold,
                'direction' => 'below',
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
