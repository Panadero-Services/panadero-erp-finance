<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use App\Models\Section;
use App\Http\Resources\PostResource;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        /*
        if (Gate::denies('admin-access')){
            return response('UnAuthorized.',401);
        }
        */
        
        $query = Post::with('user');
        
        // Handle search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $searchFields = $request->get('search_fields', 'title');
            $fieldsArray = explode(',', $searchFields);
            
            $query->where(function ($q) use ($searchTerm, $fieldsArray) {
                foreach ($fieldsArray as $field) {
                    switch (trim($field)) {
                        case 'title':
                            $q->orWhere('title', 'like', '%' . $searchTerm . '%');
                            break;
                        case 'content':
                            $q->orWhere('body', 'like', '%' . $searchTerm . '%');
                            break;
                        case 'author':
                            $q->orWhereHas('user', function ($subQ) use ($searchTerm) {
                                $subQ->where('name', 'like', '%' . $searchTerm . '%');
                            });
                            break;
                    }
                }
            });
        }
        
        // Order by most recent first
        $query->orderBy('created_at', 'desc');
        
        return inertia('content/Posts',[
            'posts'=> PostResource::collection($query->paginate(12)->appends($request->query())),
            'page'=> Page::with('sections')->where('title','Posts')->first(),
            'baseSections' => Section::where('page_id','0')->get()
            //'formFields' => Post::formFields(),
            //'validationRules' => Post::validationRules()
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateicon(Request $request, Post $post)
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
        Post::where('id', $_id)->update([$_field=>$_value]);
        sleep(1);
        return 'PostController: updateicon passed: ' . $_id . ' set '.$_field. ' to :'.$_value ;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $newPost = Post::firstOrNew(['id' => $request->id]);
        $newPost->title = $request->title;
        $newPost->body = $request->body;
        $newPost->json = $request->json;
        $newPost->links = $request->links;
        $newPost->is_published = $request->is_published;
        $newPost->is_public = $request->is_public;
        $newPost->is_featured = $request->is_featured;
        $newPost->is_locked = $request->is_locked;
        $newPost->is_self = $request->is_self;
        $newPost->is_smart = $request->is_smart;
        $newPost->is_active = $request->is_active;
        $newPost->is_archived = $request->is_archived;
        $newPost->save();


    return redirect()->back()->with('flash', [
        'banner' => 'PostController: update passed id:' . $request->id,
        'bannerStyle' => 'success'
    ]);

        //return 'PostController: update passed id:' . $request->id . ' title:'. $request->title;
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }

    /**
     * Get labels from a model based on model, label and filter field
     */
    public function getLabels(Request $request)
    {
       
        $model = $request->query('model', 'post');
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

    public function getFeaturedPosts()
    {
        $featuredPosts = Post::with(['user', 'category'])
            ->where('is_featured', true)
            ->where('is_public', true)
            ->where('post_type_id', 1)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->get();

        return PostResource::collection($featuredPosts);
    }

}
