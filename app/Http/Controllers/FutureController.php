<?php

namespace App\Http\Controllers;

use App\Models\Future;
use App\Models\Page;
use App\Models\Section;
use App\Http\Resources\FutureResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FutureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Future::with(['user', 'project']);

        // Get allowed searchable columns from the model
        $allowedSearchFields = \App\Models\Future::getSearchableColumns();

        // Handle search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            // Use provided search_fields or default to all allowed fields
            $searchFields = $request->get('search_fields', implode(',', $allowedSearchFields));
            $fieldsArray = array_filter(array_map('trim', explode(',', $searchFields)));

            // Only keep valid fields
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

        // Add meta data only once, not per record
        $records = FutureResource::collection($query->paginate($perPage)->appends($request->query()))
            ->additional([
                'meta' => [
                    'validation_rules' => \App\Models\Future::validationRules(),
                    'form_fields' => \App\Models\Future::formFields(),
                    'links_table' => \App\Models\Future::linksTable(),
                    'searchable_columns' => \App\Models\Future::getSearchableColumns(),
                    'table_columns' => \App\Models\Future::getTableColumns(),
                    'status_mapping' => \App\Models\Future::getStatusMapping(),
                ]
            ]);

        return Inertia::render('home/Futures', [
            'records' => $records,
            'page' => Page::with('sections')->where('title', 'home/futures')->first(),
            'baseSections' => Section::where('page_id', '0')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(Future::validationRules());

        $future = Future::create($validated);

        return response()->json($future, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Future $future)
    {
        return response()->json($future->load(['user', 'project']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $future = Future::findOrFail($request->id);
        
        // Convert json and links to arrays if they're strings
        $data = $request->all();
        if (isset($data['json']) && is_string($data['json'])) {
            $data['json'] = json_decode($data['json'], true) ?? [];
        }
        if (isset($data['links']) && is_string($data['links'])) {
            $data['links'] = json_decode($data['links'], true) ?? [];
        }
        
        // Ensure user_id and project_id are integers
        if (isset($data['user_id'])) {
            $data['user_id'] = (int) $data['user_id'];
        }
        if (isset($data['project_id'])) {
            $data['project_id'] = (int) $data['project_id'];
        }
        
        // Validate using model's validation rules
        $validated = $request->validate(Future::validationRules());
        
        $future->update($validated);
        
        return redirect()->back()->with('flash', [
            'banner' => 'FutureController: update passed id:' . $request->id,
            'bannerStyle' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Future $future)
    {
        $future->delete();
        return response()->json(['message' => 'Future deleted successfully']);
    }

    /**
     * Get a specific future by ID
     */
    public function getFuture(Request $request)
    {
        $id = $request->id;
        $future = Future::where('id', $id)->first();
        
        if ($future == null) {
            $notFound = [
                'id' => $id,
                'title' => 'notfound',
                'description' => 'this id is not found in the database',
                'json' => '{}',
                'is_active' => 0
            ];
            sleep(3);
            return $notFound;
        }
        
        return $future;
    }

    /**
     * Update a specific field of a future
     */
    public function updateField(Request $request)
    {
        $_id = $request->id;
        $_field = $request->field;
        $_value = $request->value;
        
        Future::where('id', $_id)->update([$_field => $_value]);
        sleep(1);
        
        return 'FutureController: updateField passed: ' . $_id . ' set ' . $_field . ' to: ' . $_value;
    }
} 