<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStateDatasetRequest;
use App\Http\Requests\UpdateStateDatasetRequest;
use Illuminate\Http\Request;

use App\Models\StateDataset;

use Illuminate\Support\Facades\DB;


class StateDatasetController extends Controller
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
    public function store(StoreStateDatasetRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StateDataset $stateDataset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StateDataset $stateDataset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStateDatasetRequest $request, StateDataset $stateDataset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function setstate(Request $request)
    {

        $request->validate([
            'json' => 'required|string|min:2',
        ]);

/*
       $create = StateDataset::updateOrInsert(
            [
            'type' =>  'mood',
            'path' => $request->path,
            'json' => $request->json,
            'project_id' => $request->projectId,
            'is_active' => $request->isActive
            ]);
*/

        $create = StateDataset::updateOrInsert(
            [   'type' =>  $request->type,
                'path' => $request->path,
                'project_id'=> $request->projectId
            ],
            [
            'json' => $request->json,
            'is_active' => $request->isActive
            ]);
        //return $create;

        return $request;
    }

    public function getstate(Request $request) {
        $state = StateDataset::where('type',$request->type)
            ->where('path',$request->path)
            ->where('project_id',$request->projectId)
            ->first();
        return $state;
    }

    public function gettables(Request $request) {
        $tables = DB::select('SHOW TABLES');
        return $tables;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StateDataset $stateDataset)
    {
        //
    }
}
