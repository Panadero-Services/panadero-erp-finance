<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorelogRequest;
use App\Http\Requests\UpdatelogRequest;
use App\Models\log;

use Illuminate\Http\Request;


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
    // NEEEDS WORK.. THIS IS THE AUTHENTICATED CALL
        // Bypass all auth just for testing
    // DO NOT USE IN PRODUCTION
//    public function store(StorelogRequest $request)
    public function store(Request $request)
    {

        $validated = $request->validate(Log::criteria());
        $log = Log::create($validated);
        
        return response()->json($log, 201);
        //
    }

    public function criteria()
    {
        return response()->json(Log::criteria());
    }

    /**
     * Display the specified resource.
     */
    public function show(log $log)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(log $log)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatelogRequest $request, log $log)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(log $log)
    {
        //
    }
}
