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

        return inertia('Projects',[
            'projects'=> PostResource::collection(Project::with('user')->paginate())
            //'formFields' => Project::formFields(),
            //'validationRules' => Project::validationRules()
        ]);


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
    //public function update(UpdateProjectRequest $request, Project $project)
 
 /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $newProject = Project::firstOrNew(['id' => $request->id]);
        $newProject->title = $request->title;
        $newProject->body = $request->body;
        $newProject->json = $request->json;
        $newProject->links = $request->links;
        $newProject->is_published = $request->is_published;
        $newProject->is_public = $request->is_public;
        $newProject->is_featured = $request->is_featured;
        $newProject->is_locked = $request->is_locked;
        $newProject->is_self = $request->is_self;
        $newProject->is_smart = $request->is_smart;
        $newProject->is_active = $request->is_active;
        $newProject->is_archived = $request->is_archived;
        $newProject->save();

        //return 'PostController: update passed id:' . $request->id . ' title:'. $request->title;
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateicon(Request $request, Project $project)
    {
        //        $request->validate([
        //            'title' => 'required|string|max:128',
        //            'image' => 'required|string|max:255',
        //            'href' => 'required|string|max:255',
        //            'self' => 'required|string|max:255',
        //        ]);
        $_id = $request->id;
        $_field = $request->field;
        $_value = $request->value;
        Project::where('id', $_id)->update([$_field=>$_value]);
        sleep(1);
        return 'ProjectController: updateicon passed: ' . $_id . ' set '.$_field. ' to :'.$_value ;
    }







 /**
     * Get labels from a model based on model, label and filter field
     */
    public function getLabels(Request $request)
    {
       
        $model = $request->query('model', 'project');
        $label = $request->query('label', 'title');
        $filter = $request->query('filter', '');

        try {
            // Get the model class name
            $modelClass = "App\\Models\\" . ucfirst($model);
            
            // Check if the class exists
            if (!class_exists($modelClass)) {
                return response()->json(['error' => 'Model not found'], 404);
            }

            // Get the model instance
            $modelInstance = new $modelClass();

            // Get searchable columns from model
            $searchableColumns = $modelInstance->getSearchableColumns();
            
            // Build the query
            $query = $modelInstance->newQuery();

            // Add filters if provided
            if ($filter) {
                $query->where(function ($q) use ($filter, $searchableColumns) {
                    foreach ($searchableColumns as $column) {
                        if ($column === 'id') {
                            $q->orWhere($column, $filter);
                        } else {
                            $q->orWhere($column, 'like', '%' . $filter . '%');
                        }
                    }
                });
            }

            // Get the results with only the label field
            $results = $query->get([$label]);

            // Format results as array of label values
            return response()->json($results->pluck($label)->toArray());

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching labels', 'message' => $e->getMessage()], 500);
        }
    }


    public function getrecordbyid(Request $request)
    {
        // default API CALL
        $caller =   $request->caller;       // this is the caller
        $model = $request->model;     // this is the model
        $id =    $request->id;        // this is the column
        
        $r=(object)NULL;
        // check Valid Provider (model) calles
        $model = 'App\Models\\'.$model;

        $r = $model::where('id',$id)->get();

        return response()->json($r);
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
                    'body' => 'this id is not found in the database', 
                    'json' => '{}', 
                    'is_active' => 0 ];
                //Project::create($notFound);
                sleep(3);
                return $notFound;
            }
            return $project;
    }





}
