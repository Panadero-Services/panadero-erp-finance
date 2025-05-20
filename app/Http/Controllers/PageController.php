<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use Illuminate\Http\Request; 
use App\Models\Page;


use Illuminate\Support\Facades\Auth;



class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $slogans =[
    'Dream, Create, Inspire',
    'Driving the Communication Revolution',
    'Experience and innovation in a single touch',
    'How Communication Happens',
    'Imagination turns to innovation',
    'Innovating for a less vulnerable World',
    'Innovation is the key to everything the future can be',
    'Innovation we want more, let creativity soar',
    'Innovation, Responsibility, and Humanism',
    'Inspiration Technology',
    'Inspire the Next',
    'Inspiring innovation and discovery',
    'Leading through innovation',
    'Let your ideas soar with wings',
    'Living innovation',
    'Logistics through innovation, dedication, and technology',
    'Materials that Create Solutions',
    'More Intelligence Solutions',
    'Our challenge is our progress',
    'Powered by innovation',
    'Pushing limits and see what we mean',
    'Simple Solutions for Complex Connections',
    'Simple Solutions for Complex Challenges',
    'Simple Solutions for Complex Dreams',
    'Technology at the Speed of Life',
    'The Future of Memory',
    'The Miracles of Science',
    'To create and invest is time well spent',
    'We Make IT Happen',
    'Great things can be designed'
    ];

    public function index()
    {
        $pages = Page::all();

        return Inertia::render('Pages/Index',[
            'pages' => $pages
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePageRequest $request)
    {
        // check slogan input
        $slogan = $request->slogan;
        if (strlen($slogan) < 5 ) $slogan = $this->slogans[array_rand($this->slogans)];

        $request->validate([
            'title' => 'required|string|max:80',
        ]);

        $create = Page::create([
            'title' => $request->title,
            'file' => $request->file,
            'icon' => $request->icon,
            'image' => $request->image,
            'slogan' => $slogan,
            'type' => $request->type,
            'settings' => $request->settings,
            'is_active' => $request->is_active,
            'header' => $request->header,
            'footer' => $request->footer,
            'sidebar' => $request->sidebar,
            'public' => $request->public,
            'animate' => $request->animate,
            'max_width' => $request->max_width
        ]);
    }



    // this is an alternative routine to do some frontend testing
    public function defaultPages(Request $request){
            //$user = Auth::user();

            $user = $request->user()->tokens;

            // $ip = $request->cookie();
            return $user;
    }

    // this is the other way of getting a Page Record onMounted
    // if the record does not exist in the database it will be created
    // JaWsome.Orbit 9.12.4
    public function getPage(Request $request){
            //$user = Auth::user();
            $name = $request->name;
            $page = Page::with('sections')->where('title', $name)->first();

            if($page==null){
                $newRec =['title' => $name, 'file' => $name.'.vue', 'image' => 'new2.jpg','icon' => 'view-dashboard-outline', 'slogan' => 'Manual: Sandbox sustainability focus accelerates innovation at the heart of your businesss', 'type' => 'content', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ];
                Page::create($newRec);
                sleep(3);
                return $newRec;
            }
            //$page = $request->user()->tokens;
            // $ip = $request->cookie();
            return $page;
    }

    // this is the original routine to create default pages
    public function defaultPagesOld()
    {
        Page::create(['title' => 'Sandbox', 'file' => 'Sandbox.vue', 'image' => 'new2.jpg','icon' => 'view-dashboard-outline', 'slogan' => 'Sandbox sustainability focus accelerates innovation at the heart of your businesss', 'type' => 'content', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ]);
        Page::create(['title' => 'About', 'file' => 'About.vue','image' => 'new2.jpg','icon' => 'view-dashboard-outline', 'slogan' => 'How sustainability focus accelerates innovation at the heart of your businesss', 'type' => 'content', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ]);
        Page::create(['title' => 'Version', 'file' => 'Version.vue','image' => 'new2.jpg','icon' => 'view-dashboard-outline', 'slogan' => 'Verson: how sustainability focus accelerates innovation at the heart of your businesss', 'type' => 'content', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ]);
        Page::create(['title' => 'Dashboard', 'file' => 'Dashboard.vue','image' => 'new2.jpg','icon' => 'monitor-dashboard', 'slogan' => 'Construct the dashboard so you can manage your KPIs', 'type' => 'dashboard', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ]);
        Page::create(['title' => 'Dashboard1', 'file' => 'Dashboard1.vue','image' => 'new2.jpg','icon' => 'monitor-dashboard', 'slogan' => 'Construct the dashboard so you can manage your KPIs', 'type' => 'dashboard', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ]);
        Page::create(['title' => 'Dashboard2', 'file' => 'Dashboard2.vue','image' => 'new2.jpg','icon' => 'monitor-dashboard', 'slogan' => 'Construct the dashboard so you can manage your KPIs', 'type' => 'dashboard', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ]);
        Page::create(['title' => 'Dashboard3', 'file' => 'Dashboard3.vue','image' => 'new2.jpg','icon' => 'monitor-dashboard', 'slogan' => 'Construct the dashboard so you can manage your KPIs', 'type' => 'dashboard', 'user_id' => 1, 'project_id' => 1, 'settings' => '{}', 'is_active'=> 1, 'sidebar'=> false, 'header'=> false, 'footer'=> false, 'public' => false, 'animate' => false, 'max_width' => false ]);
        sleep(1);
//        return redirect()->route('tags.index')->with('message', 'Tag Created Successfully');
    }
/*
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
//    public function update(UpdatePageRequest $request, Page $page)
    public function update(Request $request, Page $page)
    {
        //
        $request->validate([
            'title' => 'required|string|max:80',
        ]);
        $page->title = $request->title;
        $page->file = $request->file;
        $page->icon = $request->icon;
        $page->image = $request->image;
        $page->slogan = $request->slogan;
        $page->type = $request->type;
        $page->settings = $request->settings;
        $page->is_active = $request->is_active;
        $page->header = $request->header;
        $page->footer = $request->footer;
        $page->sidebar = $request->sidebar;
        $page->public = $request->public;
        $page->animate = $request->animate;
        $page->max_width = $request->max_width;
        $page->save();
        sleep(1);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        //
    }
}