<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DynamicController extends Controller
{
    protected function getModelClass($table)
    {
        // Convert table name to singular studly case
        // e.g., 'blog_posts' -> 'BlogPost'
        $modelName = Str::studly(Str::singular($table));
        $modelClass = "App\\Models\\{$modelName}";

        // Check if the model exists
        if (!class_exists($modelClass)) {
            Log::error("Model not found", [
                'table' => $table,
                'modelClass' => $modelClass
            ]);
            return null;
        }

        return $modelClass;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $module, $table)
    {
        // Convert table name to model class
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            abort(404, "Model {$modelClass} not found");
        }
        
        $query = $modelClass::query();
        
        // Add relationships if they exist
        if (method_exists($modelClass, 'user')) {
            $query->with('user');
        }
        if (method_exists($modelClass, 'project')) {
            $query->with('project');
        }
        
        // Get allowed searchable columns from the model
        $allowedSearchFields = method_exists($modelClass, 'getSearchableColumns') 
            ? $modelClass::getSearchableColumns() 
            : [];

        // Handle search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $searchFields = $request->get('search_fields', implode(',', $allowedSearchFields));
            $fieldsArray = array_filter(array_map('trim', explode(',', $searchFields)));
            $fieldsArray = array_intersect($fieldsArray, $allowedSearchFields);

            $query->where(function ($q) use ($searchTerm, $fieldsArray) {
                foreach ($fieldsArray as $field) {
                    $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                }
            });
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc');

        // Get per_page from request, default to 12
        $perPage = $request->get('per_page', 12);

        // Create resource collection
        $resourceClass = "App\\Http\\Resources\\" . Str::studly(Str::singular($table)) . "Resource";
        
        // Get meta data
        $meta = [];

        // Add options format if available
        if (method_exists($modelClass, 'optionsFormat')) {
            $meta['options_format'] = $modelClass::optionsFormat();
        }
        
        if (method_exists($modelClass, 'validationRules')) {
            $meta['validation_rules'] = $modelClass::validationRules();
        }
        
        if (method_exists($modelClass, 'formFields')) {
            $meta['form_fields'] = $modelClass::formFields();
        }
        
        if (method_exists($modelClass, 'linksTable')) {
            $meta['links_table'] = $modelClass::linksTable();
        }
        
        if (method_exists($modelClass, 'getSearchableColumns')) {
            $meta['searchable_columns'] = $modelClass::getSearchableColumns();
        }
        
        if (method_exists($modelClass, 'getTableColumns')) {
            $meta['table_columns'] = $modelClass::getTableColumns();
        }
        
        if (method_exists($modelClass, 'getStatusMapping')) {
            $meta['status_mapping'] = $modelClass::getStatusMapping();
        }

        if (method_exists($modelClass, 'getContentFields')) {
            $meta['content_fields'] = $modelClass::getContentFields();
        }

        // Add boolean fields from the model using an instance
        $modelInstance = new $modelClass();
        if (method_exists($modelInstance, 'getCasts')) {
            $casts = $modelInstance->getCasts();
            $meta['boolean_fields'] = collect($casts)
                ->filter(function ($cast, $field) {
                    return $cast === 'boolean';
                })
                ->keys()
                ->toArray();
        }

        // Create the collection with meta data
        $paginator = $query->paginate($perPage)->appends($request->query());
        if (class_exists($resourceClass)) {
            $records = $resourceClass::collection($paginator);
        } else {
            $records = \App\Http\Resources\DynamicResource::collection($paginator);
        }

        // Add meta data to the collection level
        $records->additional(['meta' => $meta]);

        // Get page configuration
        $page = \App\Models\Page::with('sections')->where('title', "{$module}/{$table}")->first();
        $baseSections = \App\Models\Section::where('page_id', '0')->get();

        $vueComponent = Str::studly($table);
        return Inertia::render("{$module}/{$vueComponent}", [
            'records' => $records,
            'page' => $page,
            'baseSections' => $baseSections
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $path = $request->path();
        $pathParts = explode('/', $path);
        $table = $pathParts[1] ?? null;
        
        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => "Could not determine table name from path: {$path}"
            ], 400);
        }
        
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            return response()->json([
                'success' => false,
                'message' => "Model {$modelClass} not found"
            ], 404);
        }
        
        $model = $modelClass::findOrFail($id);
        $fillableFields = $model->getFillable();
        $data = $request->only($fillableFields);
        $data['id'] = $id;

        // Handle type casting
        $casts = $model->getCasts();
        foreach ($casts as $field => $cast) {
            if (isset($data[$field])) {
                switch ($cast) {
                    case 'boolean':
                        $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'integer':
                        $data[$field] = (int) $data[$field];
                        break;
                    case 'array':
                    case 'json':
                        if (is_string($data[$field])) {
                            $data[$field] = json_decode($data[$field], true);
                        }
                        break;
                }
            }
        }
        
        try {
            if (method_exists($modelClass, 'validationRules')) {
                \Validator::make($data, $modelClass::validationRules())->validate();
            }
            
            $model->update($data);
            
            return response()->json([
                'success' => true,
                'message' => Str::studly(Str::singular($table)) . ' updated successfully',
                'data' => $model->fresh()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating ' . Str::studly(Str::singular($table)) . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get API data for a specific table (for dropdowns, etc.)
     */
    public function api(Request $request, $table)
    {
        // Convert table name to model class
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            return response()->json([
                'success' => false,
                'message' => "Model {$modelClass} not found"
            ], 404);
        }
        
        $query = $modelClass::query();
        
        // Get display fields from model metadata
        $displayFields = ['id'];
        if (method_exists($modelClass, 'formFields')) {
            $formFields = $modelClass::formFields();
            foreach ($formFields as $field) {
                if (isset($field['type']) && in_array($field['type'], ['text', 'string', 'title', 'name'])) {
                    $displayFields[] = $field['name'];
                    break; // Use first text field as display
                }
            }
        }
        
        // If no specific display field found, use common ones
        if (count($displayFields) === 1) {
            $commonFields = ['title', 'name', 'label', 'description'];
            foreach ($commonFields as $field) {
                if (in_array($field, $modelClass::getFillable())) {
                    $displayFields[] = $field;
                    break;
                }
            }
        }
        
        // Select only the needed fields
        $query->select($displayFields);
        
        // Order by the display field
        if (count($displayFields) > 1) {
            $query->orderBy($displayFields[1], 'asc');
        }
        
        $records = $query->get();
        
        return response()->json($records);
    }

    /**
     * Update a single field value for any model
     * Used primarily for quick updates like icons, status, etc.
     */
    public function updateField(Request $request, $table, $id)
    {
        try {
            // Convert table name to model class
            $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
            
            if (!class_exists($modelClass)) {
                return response()->json([
                    'success' => false,
                    'message' => "Model {$modelClass} not found"
                ], 404);
            }

            // Validate basic required fields
            $request->validate([
                'field' => 'required|string',
                'value' => 'required'
            ]);

            $field = $request->field;
            $value = $request->value;

            // Get model instance
            $model = $modelClass::findOrFail($id);

            // Check if field is fillable
            if (!in_array($field, $model->getFillable())) {
                return response()->json([
                    'success' => false,
                    'message' => "Field {$field} is not fillable in {$table}"
                ], 422);
            }

            // Handle type casting based on model's cast definitions
            $casts = $model->getCasts();
            if (isset($casts[$field])) {
                switch ($casts[$field]) {
                    case 'boolean':
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'integer':
                        $value = (int) $value;
                        break;
                    case 'array':
                    case 'json':
                        if (is_string($value)) {
                            $value = json_decode($value, true);
                        }
                        break;
                }
            }

            // Update the field
            $model->update([$field => $value]);

            // Optional: Add a small delay if needed for UI feedback
            if ($request->has('delay')) {
                sleep(1);
            }

            return response()->json([
                'success' => true,
                'message' => "{$table}: {$field} updated to {$value} for ID: {$id}",
                'data' => [
                    'id' => $id,
                    'field' => $field,
                    'value' => $value,
                    'table' => $table
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Record not found in {$table} with ID: {$id}"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($table, $id)
    {
        try {
            $modelClass = $this->getModelClass($table);
            if (!$modelClass) {
                return response()->json([
                    'success' => false,
                    'message' => "Model for table '{$table}' not found"
                ], 404);
            }
    
            $model = $modelClass::findOrFail($id);
            
            try {
                $model->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Record deleted successfully'
                ]);
            } catch (\PDOException $e) {
                // Handle foreign key constraint violation
                if ($e->getCode() == 23000) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete this record because it is referenced by other records'
                    ], 422);
                }
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ], 500);
        }
    }
} 