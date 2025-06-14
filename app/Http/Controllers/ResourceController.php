<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource for any table/model.
     * @param Request $request
     * @param string $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $table)
    {
        // Convert table name to StudlyCase Model name
        $modelClass = 'App\\Models\\' . Str::studly(Str::singular($table));
        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Model not found: ' . $modelClass], 404);
        }

        $query = $modelClass::query();

        // Optional: search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $searchFields = $request->get('search_fields', 'title');
            $fieldsArray = explode(',', $searchFields);
            $query->where(function ($q) use ($searchTerm, $fieldsArray) {
                foreach ($fieldsArray as $field) {
                    $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                }
            });
        }

        $perPage = $request->get('per_page', 12);
        $results = $query->paginate($perPage)->appends($request->query());

        // Try to use a Resource if it exists
        $resourceClass = 'App\\Http\\Resources\\' . Str::studly(Str::singular($table)) . 'Resource';
        if (class_exists($resourceClass)) {
            return $resourceClass::collection($results);
        }

        // Fallback: return raw data
        return response()->json($results);
    }
} 