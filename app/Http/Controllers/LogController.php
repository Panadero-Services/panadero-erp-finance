<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorelogRequest;
use App\Http\Requests\UpdatelogRequest;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
        $validated = $request->validate(Log::criteria());
            
            // Ensure user_id is set from authenticated user
            $validated['user_id'] = Auth::id();
            
        $log = Log::create($validated);
        
        return response()->json($log, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function criteria()
    {
        return response()->json(Log::criteria());
    }

    /**
     * Display the specified resource.
     */
    public function show(Log $log)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Log $log)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatelogRequest $request, Log $log)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Log $log)
    {
        //
    }
}
