<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Models\Section;


use Illuminate\Http\Request;



class SectionController extends Controller
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
    public function store(StoreSectionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        //
    }

    /**\
     * Update the specified resource in storage.
     */
//    public function update(UpdateSectionRequest $request, Section $section)
    public function update(Request $request, Section $section)
    {
        //
        $request->validate(['title' => 'required|string|max:128']);
        $request->validate(['file' => 'required|string|max:128']);
        $request->validate(['subtitle' => 'required|string|max:512']);
        $request->validate(['icon' => 'required|string|max:128']);
        $request->validate(['image' => 'required|string|max:128']);
        $request->validate(['slogan' => 'required|string|max:256']);
        $request->validate(['css' => 'required|string|max:256']);
        $request->validate(['self' => 'required|string|max:256']);
        $request->validate(['self_admin' => 'required|string|max:256']);

        $section->page_id = $request->page_id;
        $section->title = $request->title;
        $section->file = $request->file;
        $section->subtitle = $request->subtitle;
        $section->icon = $request->icon;
        $section->image = $request->image;
        $section->slogan = $request->slogan;
        $section->html = $request->html;
        $section->css = $request->css;
        $section->features = $request->features;
        $section->settings = $request->settings;
        $section->self = $request->self;
        $section->self_admin = $request->self_admin;
        $section->priority = $request->priority;
        $section->is_active = $request->is_active;
        $section->self_auth = $request->self_auth;
        $section->animate = $request->animate;
        $section->save();
        sleep(1);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        //
    }
}
