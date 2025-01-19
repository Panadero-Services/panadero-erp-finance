<?php
namespace App\Http\Resources;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Post;
use App\Http\Controllers\PostController;
use App\Http\Resources\PostResource;

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
use App\Http\Controllers\ProjectController;
use App\Models\Section;




// custom token
use App\Http\Middleware\EnsureTokenIsValid;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('tiers', function () {
    return Inertia::render('Tiers', [
    ]);
})
->name('tiers');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('grid', function () { return Inertia::render('Grid', []);})->name('grid');
    Route::get('table', function () { return Inertia::render('Table', []);})->name('table');
    Route::get('mood', function () { 
        return Inertia::render('Mood', [
            'page'=> Page::with('sections')->where('title','mood')->first(),
            'baseSections' => Section::where('page_id','0')->get(),
        ]);
    })->name('mood');











    Route::get('config', function () { return Inertia::render('Config', []);})->name('config');
    Route::get('web3', function () { return Inertia::render('Web3', []);})->name('web3');
    Route::get('planning', function () { return Inertia::render('Planning', []);})->name('planning');
    Route::get('resources', function () { return Inertia::render('Resources', []);})->name('resources');
    Route::get('posts',[PostController::class,'index'])->name('posts');

    Route::get('bento', function () {
        return Inertia::render('Bento', [
            'page'=> Page::with('sections')->where('title','bento')->first(),
        ]);
    })->name('bento');

    Route::get('bots', function () {
        return Inertia::render('Bots', [
        ]);
    })->name('bots');

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard',[
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
    })->name('dashboard');

});

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

// web3RecordLine
Route::resource('web3RecordLines',Web3RecordLineController::class);

// page
Route::get('/getpage',[\App\Http\Controllers\PageController::class, 'getPage'])->name('getpage');

// project
Route::get('/getproject',[\App\Http\Controllers\ProjectController::class, 'getProject'])->name('getproject');











