<?php

namespace App\Bots\Interfaces;

interface ProviderInterface
{
    // ... other method signatures ...

    // Corrected example for line 17:
    public function fetchPreviousState(): mixed; 

    // Or if it was a parameter:
    // public function someMethod(mixed $param): void;

    // Ensure other methods are also correct:
    public function __construct(array $config); // Constructor signature often not in interfaces, but if it is...
    public function fetchCurrentState(): mixed;
    public function saveState(mixed $state): void;
    // Add any other methods your providers must implement
}