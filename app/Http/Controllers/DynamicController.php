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

        return Inertia::render("{$module}/{$table}", [
            'records' => $records,
            'page' => $page,
            'baseSections' => $baseSections
        ]);
    }
} 