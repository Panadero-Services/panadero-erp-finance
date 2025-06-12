<?php

namespace App\Http\Controllers;

use App\Models\Future;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FutureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $futures = Future::with(['user', 'project'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($futures);
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
    public function update(Request $request, Future $future)
    {
        $validated = $request->validate(Future::validationRules());
        
        $future->update($validated);

        return response()->json($future);
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