<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

use Inertia\Inertia;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

  /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all();

        return Inertia::render('Tags/Index',[
            'tags' => $tags
 //          'tags' => Tag::paginate()
        ]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render(
            'Tags/Create'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:80',
        ]);
        Tag::create([
            'name' => $request->name,
            'content' => $request->content
        ]);
        sleep(1);

        return redirect()->route('tags.index')->with('message', 'Tag Created Successfully');
    }


    public function defaultTags(Request $request)
    {
        Tag::create(['name' => "sandbox",'content' => "tryouts"]);
        Tag::create(['name' => "about",'content' => "general overhead content"]);
        Tag::create(['name' => "version",'content' => "version management"]);
  
        sleep(1);

//        return redirect()->route('tags.index')->with('message', 'Tag Created Successfully');
    }




    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
       // return response()->json($tag);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag)
    {
        return Inertia::render(
            'Tags/Edit',
            [
                'tag' => $tag
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Tag $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'title' => 'required|string|max:80',
        ]);
        $tag->title = $request->title;
        $tag->content = $request->content;
        $tag->save();
        sleep(1);

        return redirect()->route('tags.index')->with('message', 'Tag Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        sleep(1);
        return redirect()->route('tags.index')->with('message', 'Tag Deleted Successfully');
    }
}