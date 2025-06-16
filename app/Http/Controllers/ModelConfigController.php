<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ModelConfigController extends Controller
{
    /**
     * Get the model configuration for a specific module and table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $module
     * @param  string  $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModelConfig(Request $request, $module, $table)
    {
        try { 
            // Check if user is authenticated
            if (!Auth::check()) {
                Log::warning('Unauthorized access attempt to model config', [
                    'module' => $module,
                    'table' => $table,
                    'ip' => $request->ip()
                ]);
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Please log in to continue'
                ], 401);
            }

            // Convert table name to StudlyCase Model name
            $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
            
            // Check if the class exists
            if (!class_exists($modelClass)) {
                Log::warning('Model not found', [
                    'model' => $modelClass,
                    'module' => $module,
                    'table' => $table
                ]);
                return response()->json([
                    'error' => 'Model not found',
                    'message' => "Model {$modelClass} does not exist"
                ], 404);
            }

            // Get the model instance
            $modelInstance = new $modelClass();

            // Get form fields configuration if available
            $formFields = method_exists($modelInstance, 'formFields') 
                ? $modelInstance::formFields() 
                : [];

            // Get validation rules if available
            $validationRules = method_exists($modelInstance, 'validationRules') 
                ? $modelInstance::validationRules() 
                : [];

            // Get searchable columns if available
            $searchableColumns = method_exists($modelInstance, 'getSearchableColumns') 
                ? $modelInstance->getSearchableColumns() 
                : [];

            // Get links table options if available
            $linksTable = method_exists($modelInstance, 'linksTable') 
                ? $modelInstance::linksTable() 
                : [];

            // Build the configuration object
            $config = [
                'title' => 'title', // Default title field
                'description' => 'description', // Default description field
                'body' => 'body', // Default body field
                'status' => 'status', // Default status field
                'color' => 'color', // Default color field
                'version' => 'version', // Default version field
                'icon' => 'icon', // Default icon field
                'flags' => [], // Will be populated with boolean fields
                'tabs' => ['content', 'status', 'author'], // Default tabs
                'showUser' => true, // Default show user info
                'showTags' => true, // Default show tags
                'formFields' => $formFields,
                'validationRules' => $validationRules,
                'searchableColumns' => $searchableColumns,
                'linksTable' => $linksTable
            ];

            // Find boolean fields (flags) from the model's fillable attributes
            if (property_exists($modelInstance, 'fillable') && is_array($modelInstance->fillable)) {
                foreach ($modelInstance->fillable as $field) {
                    if (Str::startsWith($field, 'is_')) {
                        $config['flags'][] = $field;
                    }
                }
            }

            // If no flags were found, add some common ones
            if (empty($config['flags'])) {
                $config['flags'] = [
                    'is_active',
                    'is_locked',
                    'is_featured',
                    'is_public',
                    'is_published'
                ];
            }

            Log::info('Model config retrieved successfully', [
                'module' => $module,
                'table' => $table,
                'user_id' => Auth::id()
            ]);

            return response()->json($config);

        } catch (\Exception $e) {
            Log::error('Error getting model configuration', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'module' => $module,
                'table' => $table,
                'user_id' => Auth::id(),
                'model_class' => $modelClass ?? null
            ]);

            return response()->json([
                'error' => 'Error getting model configuration',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 