<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Resources\PostResource;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        /*
        if (Gate::denies('admin-access')){
            return response('UnAuthorized.',401);
        }
        */
        return inertia('Posts',[
            'posts'=> PostResource::collection(Post::with('user')->paginate())
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
    public function update(Request $request, Post $post)
    {
        $newPost = Post::firstOrNew(['id' =>  request('id')]);

        $newPost->title = $request->title;
        $newPost->body = $request->body;
        $newPost->save();
        sleep(1);
        //return 'PostController: update passed id:' . $request->id . ' title:'. $request->title;;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
