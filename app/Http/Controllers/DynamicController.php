<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DynamicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $module, $table)
    {
        // Check permissions
        $validPermission = $this->checkPermissions();
        
        // Get and validate model class
        $modelClass = $this->getModelClass($table);
        
        // Build query with relationships and search
        $query = $this->buildQuery($modelClass, $request);
        
        // Get metadata from model
        $meta = $this->getModelMetadata($modelClass);
        
        // Get records with pagination if permitted
        $records = $this->getRecords($query, $modelClass, $validPermission, $request);
        
        // Get page configuration
        $pageConfig = $this->getPageConfiguration($module, $table);
        
        // Return Inertia view
        return $this->renderView($module, $table, $records, $pageConfig);
    }

    /**
     * Check user permissions
     */
    private function checkPermissions(): bool
    {
        return auth()->check();  // Only check if user is logged in
    }

    /**
     * Get and validate model class
     */
    private function getModelClass(string $table): string
    {
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            abort(404, "Model {$modelClass} not found");
        }
        
        return $modelClass;
    }

    /**
     * Build query with relationships and search
     */
    private function buildQuery(string $modelClass, Request $request)
    {
        $query = $modelClass::query();
        
        // Get all roles and their permissions for the current user
        $userRolePermissions = auth()->user()
            ->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique();
        
        // Always apply readable scope if it exists
        if (method_exists($modelClass, 'scopeReadable')) {
            $query->readable($userRolePermissions);
        }
        
        // Add relationships
        $this->addRelationships($query, $modelClass);
        
        // Add search conditions
        $this->addSearchConditions($query, $modelClass, $request);
        
        // Add default ordering
        $query->orderBy('created_at', 'desc');
        
        return $query;
    }

    /**
     * Add relationships to query
     */
    private function addRelationships($query, string $modelClass): void
    {
        if (method_exists($modelClass, 'user')) {
            $query->with('user');
        }
        if (method_exists($modelClass, 'project')) {
            $query->with('project');
        }
    }

    /**
     * Add search conditions to query
     */
    private function addSearchConditions($query, string $modelClass, Request $request): void
    {
        $allowedSearchFields = method_exists($modelClass, 'getSearchableColumns') 
            ? $modelClass::getSearchableColumns() 
            : [];

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
    }

    /**
     * Get all metadata from model
     */
    private function getModelMetadata(string $modelClass): array
    {
        $meta = [];
        $metaMethods = [
            'optionsFormat' => 'options_format',
            'validationRules' => 'validation_rules',
            'formFields' => 'form_fields',
            'linksTable' => 'links_table',
            'getSearchableColumns' => 'searchable_columns',
            'getTableColumns' => 'table_columns',
            'getStatusMapping' => 'status_mapping',
            'getContentFields' => 'content_fields'
        ];

        foreach ($metaMethods as $method => $key) {
            if (method_exists($modelClass, $method)) {
                $meta[$key] = $modelClass::$method();
            }
        }

        // Add boolean fields
        $modelInstance = new $modelClass();
        if (method_exists($modelInstance, 'getCasts')) {
            $meta['boolean_fields'] = collect($modelInstance->getCasts())
                ->filter(fn($cast) => $cast === 'boolean')
                ->keys()
                ->toArray();
        }

        return $meta;
    }

    /**
     * Get records with pagination if permitted
     */
    private function getRecords($query, string $modelClass, bool $validPermission, Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $resourceClass = "App\\Http\\Resources\\" . Str::studly(Str::singular($modelClass)) . "Resource";
        
        if ($validPermission) {
            // Get paginated data when permission is valid
            $paginator = $query->paginate($perPage)->appends($request->query());
            $records = class_exists($resourceClass)
                ? $resourceClass::collection($paginator)
                : \App\Http\Resources\DynamicResource::collection($paginator);
        } else {
            // Create empty paginator for no permission case
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), // empty collection
                0,          // total
                $perPage,   // per page
                1,          // current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page'
                ]
            );

            // Create resource collection from empty paginator
            $records = class_exists($resourceClass)
                ? $resourceClass::collection($emptyPaginator)
                : \App\Http\Resources\DynamicResource::collection($emptyPaginator);
        }

        // Add meta data
        $meta = $this->getModelMetadata($modelClass);
        
        // Get pagination data
        $paginationData = $records->response()->getData(true);
        
        // Add additional data with consistent structure
        $records->additional([
            'meta' => array_merge($meta, [
                'current_page' => $paginationData['meta']['current_page'] ?? 1,
                'from' => $paginationData['meta']['from'] ?? null,
                'last_page' => $paginationData['meta']['last_page'] ?? 1,
                'links' => $paginationData['meta']['links'] ?? [],
                'path' => $paginationData['meta']['path'] ?? request()->url(),
                'per_page' => $paginationData['meta']['per_page'] ?? $perPage,
                'to' => $paginationData['meta']['to'] ?? null,
                'total' => $paginationData['meta']['total'] ?? 0,
            ])
        ]);

        return $records;
    }

    /**
     * Get page configuration
     */
    private function getPageConfiguration(string $module, string $table): array
    {
        return [
            'page' => \App\Models\Page::with('sections')
                ->where('title', "{$module}/{$table}")
                ->first(),
            'baseSections' => \App\Models\Section::where('page_id', '0')->get()
        ];
    }

    /**
     * Render the Inertia view
     */
    private function renderView(string $module, string $table, $records, array $pageConfig)
    {
        return Inertia::render("{$module}/" . Str::studly($table), [
            'records' => $records,
            'page' => $pageConfig['page'],
            'baseSections' => $pageConfig['baseSections']
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Check authorization for specific permission
        if (!auth()->check() || !auth()->user()->hasPermission('global-edit')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: global-edit permission required',
                'error_code' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

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