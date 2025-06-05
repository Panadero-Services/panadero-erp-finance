<?php

namespace App\Bots\Processors;

use App\Bots\Interfaces\ProcessorInterface;

class PatternMatchProcessor implements ProcessorInterface
{
    private string $eventType;
    private string $field;
    private string $pattern;

    public function __construct(string $eventType, string $field, string $pattern)
    {
        $this->eventType = $eventType;
        $this->field = $field;
        $this->pattern = $pattern;
    }

    public function processStates(mixed $currentState, mixed $previousState = null): array
    {
        if (!isset($currentState[$this->field])) return [];
        if (preg_match($this->pattern, $currentState[$this->field])) {
            return [[
                'type' => $this->eventType,
                'field' => $this->field,
                'value' => $currentState[$this->field],
                'pattern' => $this->pattern,
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
