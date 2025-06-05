<?php

namespace App\Bots\Processors;

use App\Bots\Interfaces\ProcessorInterface;

class ChangeProcessor implements ProcessorInterface
{
    private string $eventType;
    private array $comparisonFields;

    public function __construct(
        string $eventType,
        array $comparisonFields
    ) {
        $this->eventType = $eventType;
        $this->comparisonFields = $comparisonFields;
    }

    public function processStates(mixed $currentState, mixed $previousState = null): array
    {
        if (!$previousState || !$currentState) {
            return [];
        }

        $events = [];
        foreach ($this->comparisonFields as $field) {
            if ($currentState[$field] !== $previousState[$field]) {
                $events[] = [
                    'type' => $this->eventType,
                    'field' => $field,
                    'old_value' => $previousState[$field] ?? null,
                    'new_value' => $currentState[$field],
                    'timestamp' => now()
                ];
            }
        }

        return $events;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }
}
