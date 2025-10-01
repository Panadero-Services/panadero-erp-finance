<?php

namespace App\Services;

class ModelRegistry
{
    private static $registry = [];

    /**
     * Register a model for a specific table name
     */
    public static function register($tableName, $modelClass)
    {
        self::$registry[$tableName] = $modelClass;
    }

    /**
     * Get the model class for a table name
     */
    public static function getModelForTable($tableName)
    {
        // 1. Check explicit registry first
        if (isset(self::$registry[$tableName])) {
            return self::$registry[$tableName];
        }

        // 2. Try to find model by convention
        $possibleModels = [];

        // Handle erp_ prefix tables
        if (str_starts_with($tableName, 'erp_')) {
            $modelName = 'Erp' . \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular(substr($tableName, 4)));
            $possibleModels[] = 'App\\Models\\' . $modelName;
        }

        // Standard naming convention
        $standardModel = \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($tableName));
        $possibleModels[] = 'App\\Models\\' . $standardModel;

        // Try with common prefixes
        $possibleModels[] = 'App\\Models\\Erp' . $standardModel;
        $possibleModels[] = 'App\\Models\\Inventory' . $standardModel;

        // Find the first existing model
        foreach ($possibleModels as $possibleModel) {
            if (class_exists($possibleModel)) {
                return $possibleModel;
            }
        }

        return null;
    }

    /**
     * Get all registered models
     */
    public static function getRegistry()
    {
        return self::$registry;
    }

    /**
     * Auto-register models that have getTableName() method
     */
    public static function autoRegister()
    {
        $modelsPath = app_path('Models');
        $files = glob($modelsPath . '/*.php');

        foreach ($files as $file) {
            $className = 'App\\Models\\' . basename($file, '.php');
            
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                if ($reflection->hasMethod('getTableName')) {
                    $instance = new $className();
                    $tableName = $instance->getTableName();
                    self::register($tableName, $className);
                }
            }
        }
    }
}
