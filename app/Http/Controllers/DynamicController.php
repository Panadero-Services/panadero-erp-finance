<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class DynamicController extends Controller
{
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
        
        if (class_exists($resourceClass)) {
            $records = $resourceClass::collection($query->paginate($perPage)->appends($request->query()));
        } else {
            $records = $query->paginate($perPage)->appends($request->query());
        }

        // Add meta data
        $meta = [];
        
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
        // Get the table name from the route
        $table = $request->route()->getName();
        $table = str_replace('.update', '', $table); // Remove .update suffix
        
        // Convert table name to model class
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        
        if (!class_exists($modelClass)) {
            return response()->json([
                'success' => false,
                'message' => "Model {$modelClass} not found"
            ], 404);
        }
        
        $model = $modelClass::findOrFail($id);

        // Filter request data to only include record fields (no metadata)
        $fillableFields = $model->getFillable();
        $data = $request->only($fillableFields);
        
        // Always include the ID for the update
        $data['id'] = $id;

        // Convert json and links to arrays if they're strings
        if (isset($data['json']) && is_string($data['json'])) {
            $data['json'] = json_decode($data['json'], true) ?? [];
        }
        if (isset($data['links']) && is_string($data['links'])) {
            $data['links'] = json_decode($data['links'], true) ?? [];
        }
        
        // Ensure user_id is integer if it exists
        if (isset($data['user_id'])) {
            $data['user_id'] = (int) $data['user_id'];
        }
        
        // Convert boolean strings to actual booleans for all boolean fields
        $casts = $model->getCasts();
        foreach ($casts as $field => $cast) {
            if ($cast === 'boolean' && isset($data[$field])) {
                if (is_string($data[$field])) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }
        }

        try {
            // Validate using model's validation rules AFTER data conversion
            if (method_exists($modelClass, 'validationRules')) {
                \Validator::make($data, $modelClass::validationRules())->validate();
            }
            
            $model->update($data);
            
            // Return Inertia response
            return redirect()->back()->with('success', Str::studly(Str::singular($table)) . ' updated successfully');
            
        } catch (\Exception $e) {
            // Return Inertia response with errors
            return back()->withErrors(['error' => 'Error updating ' . Str::studly(Str::singular($table)) . ': ' . $e->getMessage()]);
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
} 