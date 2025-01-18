<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request; 
use App\Models\Project;

class ProjectController extends Controller
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
    public function store(StoreProjectRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }


    // if the record does not exist in the database it will be created
    // JaWsome.Orbit 18.01.25
    public function getProject(Request $request){
        //return $request;
            //$user = Auth::user();
            $id = $request->id;
            $project = Project::where('id', $id)->first();
            if($project==null){
                $notFound =[
                    'id' => $id,
                    'title' => 'notfound', 
                    'description' => 'this id is not found in the database', 
                    'json' => '{}', 
                    'is_active' => 0 ];
                //Project::create($notFound);
                sleep(3);
                return $notFound;
            }
            return $project;
    }





}
