<?php
namespace App\Http\Resources;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
use Illuminate\Support\Str;

// Model imports
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use App\Models\Comment;
use App\Models\Page;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Section;
use App\Models\Role;

// Controller imports
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Web3RecordController;
use App\Http\Controllers\Web3RecordLineController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TestPhotoUploadController;
use App\Http\Controllers\BusinessServiceController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\DynamicController;

// Resource imports
use App\Http\Resources\PostResource;
use App\Http\Resources\ProjectResource;

// Middleware imports
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\RoleAccessMiddleware;

// ========================================
// PUBLIC ROUTES (No Authentication Required)
// ========================================

Route::get('/', function () {
    return redirect()->route('home/welkom');
});

Route::get('home/landing', function () {
    return Inertia::render('home/Landing', [
        'page'=> Page::with('sections')->where('title','Tiers')->first(),
        'baseSections' => Section::where('page_id','0')->get(),
        'featuredPosts' => app(\App\Http\Controllers\PostController::class)->getFeaturedPosts()
    ]);
})->name('home/landing');

Route::get('home', function () {
    return redirect()->route('home/welkom');
});

Route::get('home/welcome', function () {
    return Inertia::render('home/Welcome', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home/welcome');

Route::get('home/welkom', function () {
    return Inertia::render('home/Welkom', [
        'page'=> Page::with('sections')->where('title','home/Welkom')->first(),
        'baseSections' => Section::where('page_id','0')->get(),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home/welkom');

// ========================================
// AUTHENTICATED ROUTES
// ========================================

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // ========================================
    // API UTILITY ROUTES
    // ========================================
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::get('/adm', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/test-upload', [TestPhotoUploadController::class, 'store'])->name('test.upload');

    Route::get('/photo-test', function () {
        return inertia('PhotoTest');
    });

    Route::post('/photo-test', function (Request $request) {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $file = $request->file('photo');
        $path = $file->store('test-photos', 'public');

        Log::info('Upload success', [
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
        ]);

        return back()->with('success', 'Upload gelukt: ' . $path);
    });

    // ========================================
    // FUTURES TABLE
    // ========================================
    Route::get('/home/futures', function() {
        return app(DynamicController::class)->index(request(), 'home', 'futures');
    })->name('home.futures');

    Route::put('/api/futures/{id}', function(Request $request, $id) {
        return app(DynamicController::class)->update($request, $id);
    })->name('futures.update')->middleware('web');

    // ========================================
    // PROJECTS TABLE
    // ========================================
    Route::get('/project/projects', function() {
        return app(DynamicController::class)->index(request(), 'project', 'projects');
    })->name('project/projects');
    
    Route::put('/api/projects/{id}', function(Request $request, $id) {
        return app(DynamicController::class)->update($request, $id);
    })->name('projects.update')->middleware('web');

    Route::get('/getproject', function() {
        return app(DynamicController::class)->index(request(), 'project', 'projects');
    })->name('getproject');


    // ========================================
    // BUSINESS SERVICES TABLE
    // ========================================
    Route::get('/business-services', [BusinessServiceController::class, 'index'])->name('business-services.index');

    // ========================================
    // BUSINESS_SERVICES TABLE
    // ========================================
    Route::get('/home/businessservices', function() {
        return app(DynamicController::class)->index(request(), 'home', 'businessservices');
    })->name('home.businessservices');

    Route::put('/api/business_services/{id}', function(Request $request, $id) {
        return app(DynamicController::class)->update($request, $id);
    })->name('business_services.update')->middleware('web');


    

    // ========================================
    // POSTS TABLE
    // ========================================
    Route::get('/content/posts', [PostController::class, 'index'])->name('content/posts');
    Route::put('/posts.update', [PostController::class, 'update'])->name('posts.update');
    Route::post('/postupdateicon', [PostController::class, 'updateicon'])->name('postupdateicon');
    Route::get('/getrecordbyid', [PostController::class, 'getrecordbyid'])->name('getrecordbyid');
    Route::get('/getLabels', [PostController::class, 'getLabels'])->name('getLabels');

    // ========================================
    // USERS TABLE
    // ========================================
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('can:admin-access');
    Route::post('/updateuserprofile', [UserController::class, 'updateProfile'])->name('updateuserprofile');
    Route::post('/photo-upload', [UserController::class, 'upload'])->name('photo.upload');
    Route::resource('users', UserController::class);

    // ========================================
    // PAGES TABLE
    // ========================================
    Route::get('/getpage', [PageController::class, 'getPage'])->name('getpage');
    Route::get('/defaultPages', [PageController::class, 'defaultPages'])->name('defaultPages');
    Route::resource('pages', PageController::class);

    // ========================================
    // SECTIONS TABLE
    // ========================================
    Route::resource('sections', SectionController::class);

    // ========================================
    // WEB3RECORDS TABLE
    // ========================================
    Route::resource('web3Records', Web3RecordController::class);
    Route::get('/getrecord', [Web3RecordController::class, 'getrecord'])->name('getrecord');
    Route::get('/getweb3recordline', [Web3RecordController::class, 'getweb3recordline'])->name('getweb3recordline');
    Route::get('/getweb3records', [Web3RecordController::class, 'getweb3records'])->name('getweb3records');
    Route::post('/setweb3record', [Web3RecordController::class, 'setweb3record'])->name('setweb3record');
    Route::post('/setweb3recordcomplete', [Web3RecordController::class, 'setweb3recordcomplete'])->name('setweb3recordcomplete');
    Route::post('/setweb3recordline', [Web3RecordController::class, 'setweb3recordline'])->name('setweb3recordline');
    Route::get('/getrecords', [Web3RecordController::class, 'getrecords'])->name('getrecords');
    Route::post('/insertTeam', [Web3RecordController::class, 'insertTeam'])->name('insertTeam');
    Route::post('/insertProject', [Web3RecordController::class, 'insertProject'])->name('insertProject');

    // ========================================
    // WEB3RECORDLINES TABLE
    // ========================================
    Route::resource('web3RecordLines', Web3RecordLineController::class);

    // ========================================
    // LOGS TABLE
    // ========================================
    Route::resource('logs', LogController::class);
    Route::post('/logs', [LogController::class, 'store'])->name('logs.store');
    Route::get('/logs/criteria', [LogController::class, 'criteria'])->name('logs.criteria');





    // ========================================
    // FEATURES TABLE
    // ========================================
    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');

    // ========================================
    // STATE DATASET ROUTES
    // ========================================
    Route::post('/setstate', [\App\Http\Controllers\StateDatasetController::class, 'setstate'])->name('setstate');
    Route::get('/getstate', [\App\Http\Controllers\StateDatasetController::class, 'getstate'])->name('getstate');
    Route::get('/gettables', [\App\Http\Controllers\StateDatasetController::class, 'gettables'])->name('gettables');

    // ========================================
    // GAME/SCORE ROUTES
    // ========================================
    Route::get('/getgame', [\App\Http\Controllers\GameController::class, 'get'])->name('getgame');
    Route::post('/setgame', [\App\Http\Controllers\GameController::class, 'setgame'])->name('setgame');
    Route::get('/getscore', [\App\Http\Controllers\ScoreController::class, 'getscore'])->name('getscore');
    Route::post('/setscore', [\App\Http\Controllers\ScoreController::class, 'setscore'])->name('setscore');

    // ========================================
    // PRICE ROUTES
    // ========================================
    Route::get('/getprice', [\App\Http\Controllers\PriceController::class, 'get'])->name('getprice');
    Route::post('/setprice', [\App\Http\Controllers\PriceController::class, 'setprice'])->name('setprice');

    // ========================================
    // PAGE ROUTES (Non-API)
    // ========================================
    Route::get('home/dashboard', function () {
        return Inertia::render('home/Dashboard', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/dashboard');

    Route::get('home/sandbox', function () {
        return Inertia::render('home/Sandbox', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/sandbox');

    Route::get('home/tiers', function () {
        return Inertia::render('home/Tiers', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/tiers');

    Route::get('home/administration', function () {
        return Inertia::render('home/Administration', [
            'page'=> Page::with('sections')->where('title','home/Administration')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/administration');

    Route::get('erp/dashboard', function () {
        return Inertia::render('erp/ErpDashboard', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('erp/dashboard');

    Route::get('erp/mood', function () { 
        return Inertia::render('erp/Mood', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('erp/mood')->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('erp/resources', function () {
        return Inertia::render('erp/Resources', [
            'page'=> Page::with('sections')->where('title','Resources')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('erp/resources');

    Route::get('erp/sand', function () {
        return Inertia::render('Sandbox', [
            'page'=> Page::with('sections')->where('title','Sandbox')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('erp/sand');

    Route::get('project/plan', function () { 
        return Inertia::render('project/Plan', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('project/plan')->middleware(RoleAccessMiddleware::class.':admin,author');

 Route::get('project/work', function () { 
        return Inertia::render('project/Work', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('project/work')->middleware(RoleAccessMiddleware::class.':admin,author');

 Route::get('project/budget', function () { 
        return Inertia::render('project/Budget', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('project/budget')->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('design/mind', function () { 
        return Inertia::render('design/Diagram', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get(),
            'item' => 'mind'
        ]);
    })->name('design/mind')->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('design/pert', function () { 
        return Inertia::render('design/Diagram', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get(),
            'item' => 'pert'
        ]);
    })->name('design/pert')->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('design/lane', function () { 
        return Inertia::render('design/Diagram', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get(),
            'item' => 'lane'
        ]);
    })->name('design/lane')->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('ecommerce/dashboard', function () {
        return Inertia::render('ecommerce/EcommerceDashboard', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('ecommerce/dashboard');

    Route::get('ecommerce/storefront', function () {
        return Inertia::render('Storefront', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('ecommerce/storefront');

    Route::get('bento', function () {
        return Inertia::render('Bento', [
            'page'=> Page::with('sections')->where('title','Bento')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('bento')->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('grid', function () {
        return Inertia::render('Grid', [
            'page'=> Page::with('sections')->where('title','Grid')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('grid');

    Route::get('ai/bots', function () {
        return Inertia::render('ai/Bots', [
            'page'=> Page::with('sections')->where('title','Bots')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('ai/bots');

    Route::get('ai/providers', function () {
        return Inertia::render('ai/Providers', [
            'page'=> Page::with('sections')->where('title','Bots')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('ai/providers');

    Route::get('ai/processors', function () {
        return Inertia::render('ai/Processors', [
            'page'=> Page::with('sections')->where('title','Bots')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('ai/processors');

    Route::get('ai/executors', function () {
        return Inertia::render('ai/Executors', [
            'page'=> Page::with('sections')->where('title','Bots')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('ai/executors');

    Route::get('project', function () {
        return Inertia::render('Project', [
            'page'=> Page::with('sections')->where('title','Project')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('project');

    Route::get('web3', function () {
        return Inertia::render('Web3', [
            'page'=> Page::with('sections')->where('title','Web3')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('web3');

    Route::get('config', function () {
        return Inertia::render('Config', [
            'page'=> Page::with('sections')->where('title','Config')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('config');

    Route::get('table', function () {
        return Inertia::render('Table', [
            'page'=> Page::with('sections')->where('title','Table')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('table');

    Route::get('planning', function () { 
        return Inertia::render('Planning', []); 
    })->name('planning');

    // ========================================
    // TEST ROUTES
    // ========================================
Route::get('test/{id?}', function($id=1){
    return [
    UserResource::make(User::where('id',$id)->first()),
        PostResource::make(Post::where('id',$id)->first()),
        CommentResource::make(Comment::where('id',$id)->first())
    ];
});

Route::get('test_user', function (){
    return Post::take(5)->get();
});

    // ========================================
    // DEBUGGING ROUTES
    // ========================================
    Route::get('/api/users', function () {
        \Log::info('Session ID: ' . session()->getId());
        \Log::info('User authenticated: ' . auth()->check());
        \Log::info('Session data: ', session()->all());
        
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'session_present' => session()->has('auth'),
                'session_id' => session()->getId()
            ], 401);
        }
        
        return \App\Models\User::all();
    });

    // ========================================
    // DYNAMIC API ROUTES
    // ========================================
    Route::get('/api/{table}', [DynamicController::class, 'api'])->name('api.table')->middleware('web');

    // ========================================
    // DYNAMIC ROUTES
    // ========================================
    Route::get('/{module}/{table}', [DynamicController::class, 'index'])->name('dynamic.table');

    // Add these routes for dropdown options
    Route::get('/api/color', function() {
        return response()->json([
            'blue' => 'Blue',
            'green' => 'Green', 
            'red' => 'Red',
            'yellow' => 'Yellow',
            'purple' => 'Purple',
            'orange' => 'Orange',
            'gray' => 'Gray',
            'pink' => 'Pink'
        ]);
    })->name('api.color');

    Route::get('/api/status', function() {
        return response()->json([
            'idle' => 'Idle',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'blocked' => 'Blocked',
            'review' => 'Review'
        ]);
    })->name('api.status');

});

// ========================================
// CATCH-ALL ROUTE (Keep at the end)
// ========================================
// Route::get('{any}', function () {return view('app'); })->where('any', '.*');

/*
plan

Functies
--------
1 home
1.0. dashboard
    1.1. tiers
    1.2. bento
    1.3. posts
    1.4. overview
    1.5. users
    1.6. config
2 cms
    1.0. dashboard
    1.1. articles
    1.2. categories
    1.3. posts
3 erp
    3.0. dashboard
    3.1. project
    3.2. resource
    3.3. kanban
    3.4. todo
    3.5. rca
4 logistics
    4.0. dashboard
    4.1. closeout
    4.2. in
    4.3. out
    4.4. planning
    4.5. forecast
5 production
    5.0. dashboard
    5.1. rawMaterials
    5.2. procesA
    5.3. procesB
    5.4. finishedProd
6 sales
7 web3
8 bots
9 socials
10 indigo1
    10.0. dashboard
    10.1. sim
    10.2. report
11 indigo2
    11.0. dashboard.

Modules
1 dashboard
2 grid
3 kanban
4 gantt
5 landing
6 adminSection
7 
*/