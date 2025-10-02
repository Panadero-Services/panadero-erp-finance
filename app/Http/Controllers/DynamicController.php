<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Resources\Json\JsonResource;

class DynamicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $module, $table)
    {
        // Get and validate model class
        $modelClass = $this->getModelClass($table);
        
        // Build query with relationships and search
        $query = $this->buildQuery($modelClass, $request);
        
        // Get metadata from model
        $meta = $this->getModelMetadata($modelClass);
        
        // Get records with pagination - permission filtering is handled by scopeReadable()
        $records = $this->getRecords($query, $modelClass, $request);
        
        // Get page configuration
        $pageConfig = $this->getPageConfiguration($module, $table);
        
        // Return Inertia view
        return $this->renderView($module, $table, $records, $pageConfig);
    }

    /**
     * Get and validate model class
     */
    private function getModelClass(string $table): ?string
    {
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            return null;
        }
        
        return $modelClass;
    }

    /**
     * Build query with relationships and search
     */
    private function buildQuery(string $modelClass, Request $request)
    {

        $query = $modelClass::query();
        
        // Debug initial count
        \Log::info("Initial query count for {$modelClass}: " . $query->count());
        
        // Get all roles and their permissions for the current user
        $userRolePermissions = auth()->user()
            ->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique();
        
        \Log::info("User permissions: " . $userRolePermissions->implode(', '));
        
        // Apply readable scope with all user permissions
        if (method_exists($modelClass, 'scopeReadable')) {
            $query->readable($userRolePermissions);
            \Log::info("After readable scope count: " . $query->count());
        }

        // Get available permissions for this model
        $availablePermissions = $modelClass::getPermissionAccess();
        \Log::info("Available permissions for I1Order: " . json_encode($availablePermissions));

        // Add relationships
        $this->addRelationships($query, $modelClass);
        
        // Add search conditions
        $this->addSearchConditions($query, $modelClass, $request);
        
        // Add default ordering
        $query->orderBy('created_at', 'desc');
        
        // Debug final query
        \Log::info("Final SQL: " . $query->toSql());
        \Log::info("Final bindings: " . json_encode($query->getBindings()));
        \Log::info("Final count: " . $query->count());
        
        return $query;
    }

    /**
     * Add relationships to query
     */
    private function addRelationships($query, string $modelClass): void
    {
        // Generic relationships that most models might have
        if (method_exists($modelClass, 'user')) {
            $query->with('user');
        }
        if (method_exists($modelClass, 'project')) {
            $query->with('project');
        }
        
        // Check if the model has a method to define its relationships
        if (method_exists($modelClass, 'getApiRelationships')) {
            $relationships = $modelClass::getApiRelationships();
            if (!empty($relationships)) {
                $query->with($relationships);
            }
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
            'getContentFields' => 'content_fields',
            'getPermissionAccess' => 'permission_access',
            'getTitleColumn' => 'title_column',
            'getUserIdColumn' => 'user_id_column'
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
     * Get records with pagination
     */
    private function getRecords($query, string $modelClass, Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $resourceClass = "App\\Http\\Resources\\" . Str::studly(Str::singular($modelClass)) . "Resource";
        
        // Get paginated data
        $paginator = $query->paginate($perPage)->appends($request->query());
        
        // Get model metadata
        $modelMeta = $this->getModelMetadata($modelClass);
        
        // Create the resource collection
        $records = class_exists($resourceClass)
            ? $resourceClass::collection($paginator)
            : \App\Http\Resources\DynamicResource::collection($paginator);

        // Get clean pagination metadata
        $paginationLinks = $paginator->linkCollection()->unique('url')->values();
        
        // Add metadata with clean pagination data
        $records->additional([
            'meta' => array_merge($modelMeta, [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'path' => $paginator->path(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
                'links' => $paginationLinks->toArray()
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
     * Update a record for any model
     */
    public function update(Request $request, $table, $id)
    {
        // Convert table name to model class
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            return response()->json([
                'success' => false,
                'message' => "Model {$modelClass} not found"
            ], 404);
        }
        
        $model = $modelClass::findOrFail($id);
        
        // Use the standardized permission check (optional)
        if (auth()->check() && method_exists($model, 'canBeEditedBy') && !$model->canBeEditedBy(auth()->user())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: insufficient permissions to edit this record',
                'error_code' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }
        
        $fillableFields = $model->getFillable();
        $data = $request->only($fillableFields);

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
        
        // Validate data if validation rules exist
        if (method_exists($modelClass, 'validationRules')) {
            $rules = $modelClass::validationRules();
            
            // For updates, modify unique rules to exclude current record
            if ($id) {
                foreach ($rules as $field => $rule) {
                    if (str_contains($rule, 'unique:')) {
                        $rules[$field] = $rule . ',' . $id;
                    }
                }
                
                // Prevent identifier changes during updates (generic for all identifier fields)
                foreach ($data as $field => $value) {
                    if (str_ends_with($field, 'identifier')) {
                        unset($data[$field]);
                    }
                }
            }
            
            \Validator::make($data, $rules)->validate();
        }
        
        try {
            $model->update($data);
            
            return response()->json([
                'success' => true,
                'message' => Str::studly(Str::singular($table)) . ' updated successfully',
                'data' => $model->fresh()
            ]);
            
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
        
        // Add relationships using the model's getApiRelationships method
        if (method_exists($modelClass, 'getApiRelationships')) {
            $relationships = $modelClass::getApiRelationships();
            if (!empty($relationships)) {
                $query->with($relationships);
            }
        }
        
        // Get all fillable fields dynamically
        $modelInstance = new $modelClass();
        $fillableFields = $modelInstance->getFillable();
        
        // Always include ID, then all fillable fields
        $selectFields = array_merge(['id'], $fillableFields);
        
        // Select all available fields
        $query->select($selectFields);
        
        // Order by ID by default (or you can make this configurable)
        $query->orderBy('id', 'asc');
        
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

    /**
     * Remove the specified resource from storage (public version).
     */
    public function destroyPublic($table, $id)
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
            
            // Skip authentication check for public routes
            $model->delete();
            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($table, $id)
    {

        // Check for custom header
        $allowedHeaders = [
            'X-From-Products-Page',
            'X-From-Storages-Page',
            'X-From-Customers-Page',
            'X-From-Suppliers-Page'
            // Add more headers as needed
        ];

        $hasValidHeader = false;
        foreach ($allowedHeaders as $header) {
            if (request()->header($header)) {
                $hasValidHeader = true;
                break;
            }
        }

        if (!$hasValidHeader) {
            return response()->json([
                'success' => false, 
                'message' => 'Direct API calls not allowed'
            ], 403);
        }

        try {
            $modelClass = $this->getModelClass($table);
            if (!$modelClass) {
                return response()->json([
                    'success' => false,
                    'message' => "Model for table '{$table}' not found"
                ], 404);
            }
    
            $model = $modelClass::findOrFail($id);
            
            // Use the standardized permission check
            if (auth()->check() && method_exists($model, 'canBeDeletedBy') && !$model->canBeDeletedBy(auth()->user())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: insufficient permissions to delete this record',
                    'error_code' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }
            
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

    /**
     * Get a single record by ID
     */
    public function show(Request $request, $table, $id)
    {
        // Get and validate model class
        $modelClass = $this->getModelClass($table);
        
        // Get the record
        $record = $modelClass::findOrFail($id);
        
        // Check if user can read this record
        if (method_exists($record, 'canBeReadBy') && !$record->canBeReadBy(auth()->user())) {
            abort(403, 'Unauthorized');
        }
        
        // Get resource class
        $resourceClass = "App\\Http\\Resources\\" . Str::studly(Str::singular($table)) . "Resource";
        
        // Return the record
        return class_exists($resourceClass)
            ? new $resourceClass($record)
            : new \App\Http\Resources\DynamicResource($record);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $table)
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
            
            // Get fillable fields
            $model = new $modelClass();
            $fillableFields = $model->getFillable();
            $data = $request->only($fillableFields);
            
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
            
            // Add user_id if the model has it and user is authenticated
            if (in_array('user_id', $fillableFields) && auth()->check()) {
                $data['user_id'] = auth()->id();
            }
            
            // Validate data if validation rules exist
            if (method_exists($modelClass, 'validationRules')) {
                $rules = $modelClass::validationRules();
                
                // For erp_products, make identifier nullable for new records
                if ($table === 'erp_products' && isset($rules['identifier'])) {
                    $rules['identifier'] = 'nullable|string|max:16|unique:erp_products,identifier';
                }
                
                \Validator::make($data, $rules)->validate();
            }
            
            // Generate identifier for products if not provided
            if ($table === 'erp_products' && (empty($data['identifier']) || $data['identifier'] === '<AUTO>')) {
                $data['identifier'] = $modelClass::generateProductIdentifier(
                    $data['erp_brand_id'] ?? null, 
                    $data['erp_product_group_id'] ?? null
                );
            }

            // Create the record
            $record = $modelClass::create($data);
            
            return response()->json([
                'success' => true,
                'message' => Str::studly(Str::singular($table)) . ' created successfully',
                'data' => $record->fresh()
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating ' . Str::studly(Str::singular($table)) . ': ' . $e->getMessage()
            ], 500);
        }
    }

    public function getModelConfig(Request $request, $table)
    {
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            return response()->json([
                'success' => false,
                'message' => "Model {$modelClass} not found"
            ], 404);
        }
        
        $modelInstance = new $modelClass();
        
        $config = [
            'relationships' => method_exists($modelInstance, 'getDataTableRelationships') 
                ? $modelInstance->getDataTableRelationships() 
                : [],
            'formFields' => method_exists($modelInstance, 'formFields') 
                ? $modelInstance->formFields() 
                : [],
            'searchableColumns' => method_exists($modelInstance, 'getSearchableColumns') 
                ? $modelInstance->getSearchableColumns() 
                : []
        ];
        
        return response()->json($config);
    }
} 