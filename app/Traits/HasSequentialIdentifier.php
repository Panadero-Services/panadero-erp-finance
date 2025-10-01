<?php

namespace App\Traits;

trait HasSequentialIdentifier
{
    /**
     * Generate next sequential number for this model
     */
    public static function getNextNumber($prefix = '', $length = 5)
    {
        // Get the highest existing number for this prefix
        $lastRecord = static::where('identifier', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastRecord) {
            // Extract the number part and increment
            $numberPart = substr($lastRecord->identifier, strlen($prefix));
            $nextNumber = (int)$numberPart + 1;
        } else {
            $nextNumber = 1;
        }
        
        return str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Generate identifier with custom prefix and sequential number
     */
    public static function generateIdentifier($prefix, $length = 5)
    {
        $number = static::getNextNumber($prefix, $length);
        return $prefix . $number;
    }
}
