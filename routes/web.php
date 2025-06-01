<?php
namespace App\Http\Resources;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


use Illuminate\Foundation\Application;
use Inertia\Inertia;

use App\Models\Post;
use App\Http\Controllers\PostController;
use App\Http\Resources\PostResource;

use App\Models\Project;
use App\Http\Controllers\ProjectController;
use App\Http\Resources\ProjectResource;

use App\Models\User;
use App\Models\Comment;

use App\Models\Page;
use App\Http\Controllers\PageController;

use App\Models\Tag;
use App\Http\Controllers\TagController;
use App\Models\Task;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Web3RecordController;
use App\Http\Controllers\Web3RecordLineController;
use App\Models\Section;
use App\Models\Role;

use App\Http\Controllers\UserController;
//use Laravel\Jetstream\Http\Controllers\Inertia\TeamController;

// custom token
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\RoleAccessMiddleware;

use App\Http\Controllers\SectionController;
use App\Http\Controllers\LogController;


use App\Http\Controllers\TestPhotoUploadController;


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



Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Gebruikerslijst alleen voor admins
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('can:admin-access');

    // Profiel bijwerken
    Route::post('/updateuserprofile', [UserController::class, 'updateProfile'])
        ->name('updateuserprofile');

    // Profielfoto uploaden
    Route::post('/photo-upload', [UserController::class, 'upload'])
        ->name('photo.upload');
});

//Route::post('/photo-upload', [UserController::class, 'store'])->name('photo.upload');

// Route::get('{any}', function () {return view('app'); })->where('any', '.*');
Route::get('/getLabels', [\App\Http\Controllers\PostController::class, 'getLabels'])->name('getLabels');


Route::get('/', function () {
    return redirect()->route('home/welkom');
});

Route::get('home', function () {
    return redirect()->route('home/welkom');
});

Route::get('home/welcome', function () {
    return Inertia::render('home/Welcome', [
        //'canLogin' => Route::has('login'),
        //'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home/welcome');//->middleware(RoleAccessMiddleware::class.':admin,author');


Route::get('home/welkom', function () {
    return Inertia::render('home/Welkom', [
        'page'=> Page::with('sections')->where('title','home/Welkom')->first(),
        'baseSections' => Section::where('page_id','0')->get(),
        //'canLogin' => Route::has('login'),
        //'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home/welkom');//->middleware(RoleAccessMiddleware::class.':admin,author');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('home/dashboard', function () {
        return Inertia::render('home/Dashboard', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/dashboard');//->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('home/sandbox', function () {
        return Inertia::render('home/Sandbox', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/sandbox');//->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('home/tiers', function () {
        return Inertia::render('home/Tiers', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/tiers');//->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('erp/dashboard', function () {
        return Inertia::render('erp/ErpDashboard', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('erp/dashboard');//->middleware(RoleAccessMiddleware::class.':admin,author');

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


    // Business Function : Poject Management Software
    // project
    Route::get('project/plan', function () { 
        return Inertia::render('project/Plan', [
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('project/plan')->middleware(RoleAccessMiddleware::class.':admin,author');

    // Business Function : Poject Management Software
    // project
    Route::get('project/projects', function () { 
        return Inertia::render('project/Projects', [
            'projects'=> ProjectResource::collection(Project::with('user')->paginate()),
            'page'=> Page::with('sections')->where('title','Mood')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('project/projects')->middleware(RoleAccessMiddleware::class.':admin,author');

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

    // Business Function : Design  Software
    // design
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

});

    Route::get('home/tiers', function () {
        return Inertia::render('home/Tiers', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/tiers')->middleware(RoleAccessMiddleware::class.':admin,author');

    Route::get('bento', function () {
        return Inertia::render('Bento', [
            'page'=> Page::with('sections')->where('title','Bento')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('bento')->middleware(RoleAccessMiddleware::class.':admin,author');

//  Gate function
//  Route::middleware('can:admin-access')->get('posts', function () {
    Route::get('content/posts', function () {
        return Inertia::render('content/Posts', [
             'posts'=> PostResource::collection(Post::with('user')->paginate()),
            'page'=> Page::with('sections')->where('title','Posts')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('content/posts')->middleware(RoleAccessMiddleware::class.':admin,member');


    Route::get('grid', function () {
        return Inertia::render('Grid', [
            'page'=> Page::with('sections')->where('title','Grid')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('grid');

    Route::get('bots', function () {
        return Inertia::render('Bots', [
            'page'=> Page::with('sections')->where('title','Bots')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('bots');

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



   // Route::get('grid', function () { return Inertia::render('Grid', []);})->name('grid');
  //  Route::get('table', function () { return Inertia::render('Table', []);})->name('table');

  //  Route::get('config', function () { return Inertia::render('Config', []);})->name('config');
  //  Route::get('web3', function () { return Inertia::render('Web3', []);})->name('web3');
    Route::get('planning', function () { return Inertia::render('Planning', []);})->name('planning');
  //  Route::get('resources', function () { return Inertia::render('Resources', []);})->name('resources');
  //  Route::get('posts',[PostController::class,'index'])->name('posts');


//Route::get('posts',[PostController::class,'index'])->name('posts');
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

// price
Route::get('/getprice',[\App\Http\Controllers\PriceController::class, 'get'])->name('getprice');
Route::post('/setprice',[\App\Http\Controllers\PriceController::class, 'setprice'])->name('setprice');

// game
Route::get('/getgame',[\App\Http\Controllers\GameController::class, 'get'])->name('getgame');
Route::post('/setgame',[\App\Http\Controllers\GameController::class, 'setgame'])->name('setgame');

// score
Route::get('/getscore',[\App\Http\Controllers\ScoreController::class, 'getscore'])->name('getscore');
Route::post('/setscore',[\App\Http\Controllers\ScoreController::class, 'setscore'])->name('setscore');

// stateDataSet
Route::post('/setstate',[\App\Http\Controllers\StateDatasetController::class, 'setstate'])->name('setstate');
Route::get('/getstate',[\App\Http\Controllers\StateDatasetController::class, 'getstate'])->name('getstate');

Route::get('/gettables',[\App\Http\Controllers\StateDatasetController::class, 'gettables'])->name('gettables');


Route::get('/defaultPages',[\App\Http\Controllers\PageController::class, 'defaultPages'])->name('defaultPages');

// web3Record
Route::resource('web3Records',Web3RecordController::class);

Route::get('/getrecord',[\App\Http\Controllers\Web3RecordController::class, 'getrecord'])->name('getrecord');
Route::get('/getweb3recordline',[\App\Http\Controllers\Web3RecordController::class, 'getweb3recordline'])->name('getweb3recordline');
Route::get('/getweb3records',[\App\Http\Controllers\Web3RecordController::class, 'getweb3records'])->name('getweb3records');
Route::post('/setweb3record',[\App\Http\Controllers\Web3RecordController::class, 'setweb3record'])->name('setweb3record');
Route::post('/setweb3recordcomplete',[\App\Http\Controllers\Web3RecordController::class, 'setweb3recordcomplete'])->name('setweb3recordcomplete');
Route::post('/setweb3recordline',[\App\Http\Controllers\Web3RecordController::class, 'setweb3recordline'])->name('setweb3recordline');

Route::get('/getrecords',[\App\Http\Controllers\Web3RecordController::class, 'getrecords'])->name('getrecords');

// web3RecordLine
Route::resource('web3RecordLines',Web3RecordLineController::class);

// page
Route::get('/getpage',[\App\Http\Controllers\PageController::class, 'getPage'])->name('getpage');

// project
Route::get('/getproject',[\App\Http\Controllers\ProjectController::class, 'getProject'])->name('getproject');

// user
Route::resource('users',UserController::class);
Route::post('/updateuserprofile',[UserController::class, 'updateProfile'])->name('updateuserprofile');

// sections
Route::resource('sections',SectionController::class);

// team
Route::post('/insertTeam',[Web3RecordController::class, 'insertTeam'])->name('insertTeam');
Route::post('/insertProject',[Web3RecordController::class, 'insertProject'])->name('insertProject');

Route::resource('pages',PageController::class);
Route::put('posts.update',[PostController::class, 'update'])->name('posts.update');

Route::post('/postupdateicon',[\App\Http\Controllers\PostController::class, 'updateicon'])->name('postupdateicon');

Route::resource('logs',LogController::class);

Route::get('/getrecordbyid',[\App\Http\Controllers\PostController::class, 'getrecordbyid'])->name('getrecordbyid');

//Route::get('/logs/criteria', [LogController::class, 'criteria']);

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

