<?php
namespace App\Http\Resources;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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
use App\Http\Controllers\MasterGameServerController;

//use App\Http\Controllers\Auth\AuthCheckController;

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\GameStateController;

// Resource imports
use App\Http\Resources\PostResource;
use App\Http\Resources\ProjectResource;

// Middleware imports
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\RoleAccessMiddleware;

use App\Http\Controllers\GameServerController;

/*
Excellent! Now we understand exactly what makes it work:
The route is grouped under the master prefix
The critical part is ->withoutMiddleware(['web']) which completely bypasses the web middleware stack
The route matches the socket server's expected path /master/worlds/heartbeat
We get the expected {"success": true} response from the controller
Let's break down why this works:
The web middleware stack includes session handling, CSRF protection, and other browser-specific features
For server-to-server API calls (like the socket server's heartbeat), we don't need any of that
By using ->withoutMiddleware(['web']), we treat it as a pure API endpoint
The route group with prefix('master') keeps our routes organized
This matches the pattern we discovered with your test route - pure API endpoints that bypass web middleware work perfectly for server-to-server communication.
Would you like me to help document this solution or make any other improvements to the implementation?
*/



use App\Http\Controllers\FinanceInvoiceController;


// ============================================================================
// FINANCE INVOICE SYSTEM ROUTES
// ============================================================================
// Test route without authentication
Route::get('/finance/test', function () {
    return response()->json([
        'message' => 'Finance test route working',
        'timestamp' => now(),
        'cookies' => request()->cookies->all()
    ]);
});

// Test finance invoices route without authentication
Route::get('/finance/invoices/test', function () {
    return response()->json([
        'message' => 'Finance invoices test route working',
        'timestamp' => now(),
        'cookies' => request()->cookies->all(),
        'headers' => request()->headers->all()
    ]);
});

Route::prefix('finance/invoices')->middleware('auth')->group(function () {
    // Basic CRUD operations
    Route::get('/', [FinanceInvoiceController::class, 'index']);
    Route::post('/', [FinanceInvoiceController::class, 'store']);
    Route::get('/{invoice_number}', [FinanceInvoiceController::class, 'show']);
    Route::put('/{invoice_number}', [FinanceInvoiceController::class, 'update']);
    Route::delete('/{invoice_number}', [FinanceInvoiceController::class, 'destroy']);
    
    // Invoice workflow operations
    Route::post('/{invoice_number}/approve', [FinanceInvoiceController::class, 'approve']);
    Route::post('/{invoice_number}/complete', [FinanceInvoiceController::class, 'complete']);
    Route::post('/{invoice_number}/cancel', [FinanceInvoiceController::class, 'cancel']);
    
    // Reporting and statistics
    Route::get('/stats/summary', [FinanceInvoiceController::class, 'stats']);
    Route::get('/export/csv', [FinanceInvoiceController::class, 'export']);
    
    // Section-specific routes
    Route::prefix('section/{section}')->group(function () {
        Route::get('/', [FinanceInvoiceController::class, 'index']);
        Route::get('/stats', [FinanceInvoiceController::class, 'stats']);
        Route::get('/export', [FinanceInvoiceController::class, 'export']);
    });
    
    // Status-specific routes
    Route::prefix('status/{status}')->group(function () {
        Route::get('/', [FinanceInvoiceController::class, 'index']);
        Route::get('/export', [FinanceInvoiceController::class, 'export']);
    });
});











// ========================================
// MASTER GAME SERVER ROUTES
// ========================================

// GET routes - these work as-is
Route::get('/master/worlds', [MasterGameServerController::class, 'getWorldsStatus'])
    ->name('master.worlds');
Route::get('/master/game-state', [MasterGameServerController::class, 'getGameState'])
    ->name('master.game-state');
Route::get('/master/leaderboard', [MasterGameServerController::class, 'getLeaderboard'])
    ->name('master.leaderboard');
Route::get('/master/player-stats', [MasterGameServerController::class, 'getPlayerStats'])
    ->name('master.player-stats');
Route::get('/master/health', [MasterGameServerController::class, 'getServerHealth'])
    ->name('master.health');
Route::get('/master/worlds/available', [MasterGameServerController::class, 'getAvailableWorlds'])
    ->name('master.worlds.available');
Route::get('/master/player/resources', [MasterGameServerController::class, 'getPlayerResources'])
    ->name('master.player.resources.get');
Route::get('/master/events', [MasterGameServerController::class, 'getWorldEvents'])
    ->name('master.events.get');

// POST routes - each needs web middleware bypass
Route::post('/master/worlds/heartbeat', [MasterGameServerController::class, 'updateWorldHeartbeat'])
    ->name('master.worlds.heartbeat')
    ->withoutMiddleware(['web']);
Route::post('/master/worlds/register', [MasterGameServerController::class, 'registerWorld'])
    ->name('master.worlds.register')
    ->withoutMiddleware(['web']);
Route::post('/master/player/join-world', [MasterGameServerController::class, 'playerJoinWorld'])
    ->name('master.player.join-world')
    ->withoutMiddleware(['web']);
Route::post('/master/player/leave-world', [MasterGameServerController::class, 'playerLeaveWorld'])
    ->name('master.player.leave-world')
    ->withoutMiddleware(['web']);
Route::post('/master/player/transfer', [MasterGameServerController::class, 'transferToWorld'])
    ->name('master.player.transfer')
    ->withoutMiddleware(['web']);
Route::post('/master/player/resources', [MasterGameServerController::class, 'updatePlayerResources'])
    ->name('master.player.resources')
    ->withoutMiddleware(['web']);
Route::post('/master/update-score', [MasterGameServerController::class, 'updateScore'])
    ->name('master.update-score')
    ->withoutMiddleware(['web']);
Route::post('/master/events', [MasterGameServerController::class, 'createWorldEvent'])
    ->name('master.events.create')
    ->withoutMiddleware(['web']);
Route::post('/master/worlds/unregister', [MasterGameServerController::class, 'unregisterWorld'])
    ->name('master.worlds.unregister')
    ->withoutMiddleware(['web']);


// Add the missing shared state routes
Route::get('/master/players/state/{playerId}', [MasterGameServerController::class, 'getPlayerState'])
    ->name('master.players.state.get')
    ->withoutMiddleware(['web']);
Route::post('/master/players/state/{playerId}', [MasterGameServerController::class, 'updatePlayerState'])
    ->name('master.players.state.update')
    ->withoutMiddleware(['web']);


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

// Add the new logistics landing route
Route::get('home/logistics', function () {
    return Inertia::render('home/LogisticsLanding', [
        'page'=> Page::with('sections')->where('title','Logistics')->first(),
        'baseSections' => Section::where('page_id','0')->get(),
        'featuredPosts' => app(\App\Http\Controllers\PostController::class)->getFeaturedPosts()
    ]);
})->name('home/logistics');

Route::get('home', function () {
    return redirect()->route('home/welkom');
});

Route::get('home/welcome', function () {
    return Inertia::render('home/Welcome', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home/welcome');

Route::get('admin/permissions', function () {
    \Log::info('Accessing user permissions page');
    return Inertia::render('admin/Permissions', [
        'page' => Page::with('sections')->where('title', 'admin/Permissions')->first(),
        'baseSections' => Section::where('page_id', '0')->get()
    ]);
})->name('admin/permissions');

Route::get('admin/roles', function () {
    \Log::info('Accessing user roles page');
    return Inertia::render('admin/Roles', [
        'page' => Page::with('sections')->where('title', 'admin/Roles')->first(),
        'baseSections' => Section::where('page_id', '0')->get()
    ]);
})->name('admin/roles');

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
    // FUTURES TABLE - NOW HANDLED BY DYNAMIC ROUTE
    // ========================================
    // OLD: Route::get('/home/futures', function() {
    //     return app(DynamicController::class)->index(request(), 'home', 'futures');
    // })->name('home.futures');
    // NEW: Use dynamic route: /home/futures (handled by Route::get('/{module}/{table}', [DynamicController::class, 'index']))

    // ========================================
    // I1 ORDERS TABLE - NOW HANDLED BY DYNAMIC ROUTE
    // ========================================
    // OLD: Route::get('indigo1/orders', function() {
    //     return app(DynamicController::class)->index(request(), 'indigo1', 'i1_orders');
    // })->name('indigo1.orders');
    // NEW: Use dynamic route: /indigo1/i1_orders (handled by Route::get('/{module}/{table}', [DynamicController::class, 'index']))

    // ========================================
    // I1 FORWARDERS TABLE - NOW HANDLED BY DYNAMIC ROUTE
    // ========================================
    // OLD: Route::get('indigo1/forwarders', function() {
    //     return app(DynamicController::class)->index(request(), 'indigo1', 'i1_forwarders');
    // })->name('indigo1.forwarders');
    // NEW: Use dynamic route: /indigo1/i1_forwarders (handled by Route::get('/{module}/{table}', [DynamicController::class, 'index']))

    // ========================================
    // I1 BUSINESS CUSTOMERS TABLE - NOW HANDLED BY DYNAMIC ROUTE
    // ========================================
    // OLD: Route::get('indigo1/business-customers', function() {
    //     return app(DynamicController::class)->index(request(), 'indigo1', 'i1_business_customers');
    // })->name('indigo1.business-customers');
    // NEW: Use dynamic route: /indigo1/i1_business_customers (handled by Route::get('/{module}/{table}', [DynamicController::class, 'index']))

/*
    Route::get('indigo1/orders', function () {
        return Inertia::render('indigo1/Orders', [
            'page'=> Page::with('sections')->where('title','indigo1/Orders')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('indigo1.orders')->middleware(RoleAccessMiddleware::class.':admin,author');
*/



    // ========================================
    // PROJECTS TABLE - NOW HANDLED BY DYNAMIC ROUTE
    // ========================================
    // OLD: Route::get('/project/projects', function() {
    //     return app(DynamicController::class)->index(request(), 'project', 'projects');
    // })->name('project/projects');
    // NEW: Use dynamic route: /project/projects (handled by Route::get('/{module}/{table}', [DynamicController::class, 'index']))
    
    // ========================================
    // BUSINESS SERVICES TABLE - NOW HANDLED BY DYNAMIC ROUTE
    // ========================================
    // OLD: Route::get('/home/business_services', function() {
    //     return app(DynamicController::class)->index(request(), 'home', 'business_services');
    // })->name('home.business_services');
    // NEW: Use dynamic route: /home/business_services (handled by Route::get('/{module}/{table}', [DynamicController::class, 'index']))

    // ========================================
    // POSTS TABLE - NOW HANDLED BY DYNAMIC ROUTE
    // ========================================
    // OLD: Route::get('/content/posts', function() {
    //     return app(DynamicController::class)->index(request(), 'content', 'posts');
    // })->name('content/posts');
    // NEW: Use dynamic route: /content/posts (handled by Route::get('/{module}/{table}', [DynamicController::class, 'index']))

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
    // ERP FINANCE ROUTES
    // ========================================

    Route::get('erp/finance', function () {
        return Inertia::render('erp/Finance', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('epr/finance');


    // ========================================
    // ERP INVENTORY ROUTES
    // ========================================
    Route::get('erp/inventory', function () {
        return Inertia::render('erp/Inventory', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get(),
            'title' => 'ERP Inventory Management'
        ]);
    })->name('erp.inventory');




    Route::get('features/workflow', function () {
        return Inertia::render('features/Workflow', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('featurs/workflow');





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
    // SOLARSYS GAME ROUTES
    // ========================================
    Route::get('home/solarsys', function () {
        return Inertia::render('home/SolarSys', [
            'page'=> Page::with('sections')->where('title','SolarSys')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/solarsys');

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


    // ========================================
    // MIDDLEWARE
    // ========================================
    Route::get('admin/middleware', function () {
        return Inertia::render('admin/Middleware', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('admin/middleware');

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
    // Feature : workflow
    // ========================================
    Route::get('features/workflow/dashboard', function () {
        return Inertia::render('features/workflow/Dashboard', [
            'page'=> Page::with('sections')->where('title','Tiers')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('features/workflow/dashboard');





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

    // Add this route for auth check
    //Route::get('/auth/check', [AuthCheckController::class, 'check'])->middleware('auth:sanctum');

}); // This closes the authenticated middleware group


 





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

// Add this route outside the auth middleware group
Route::get('/auth-test', function () {
    return response()->json([
        'is_authenticated' => auth()->check(),
        'user' => auth()->user(),
        'session_id' => session()->getId(),
        'session_data' => session()->all()
    ]);
});

Route::post('/force-logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return response()->json(['success' => true]);
})->name('force.logout');

// Move this route outside the auth middleware group
Route::get('/getproject', function(Request $request) {
    return app(ProjectController::class)->getProject($request, 'projects', $request->get('id'));
})->name('getproject');

// Keep your existing dynamic catch-all route AFTER these specific routes
//Route::get('/api/{table}', [DynamicController::class, 'api'])->name('api.table')->middleware('web');
Route::patch('{table}/{id}/field', [DynamicController::class, 'updateField']);


    // ========================================
    // API UPDATE ROUTES
    // ========================================
    /*
    Route::put('/api/futures/{id}', function(Request $request, $id) {
        return app(DynamicController::class)->update($request, $id);
    })->name('futures.update');

    Route::put('/api/projects/{id}', function(Request $request, $id) {
        return app(DynamicController::class)->update($request, $id);
    })->name('projects.update');

    Route::put('/api/business_services/{id}', function(Request $request, $id) {
        return app(DynamicController::class)->update($request, $id);
    })->name('business_services.update');

    Route::put('/api/posts/{id}', function(Request $request, $id) {
        return app(DynamicController::class)->update($request, $id);
    })->name('posts.update');
    */

    // ========================================
    // GAME/SCORE ROUTES
    // ========================================
    Route::get('/getgame', [\App\Http\Controllers\GameController::class, 'get'])->name('getgame');
    Route::post('/setgame', [\App\Http\Controllers\GameController::class, 'setgame'])->name('setgame');
    Route::get('/getscore', [\App\Http\Controllers\ScoreController::class, 'getscore'])->name('getscore');
    Route::post('/setscore', [\App\Http\Controllers\ScoreController::class, 'setscore'])->name('setscore');

    // ========================================
    // SOLARSYS GAME ROUTES
    // ========================================
    Route::get('home/solarsys', function () {
        return Inertia::render('home/SolarSys', [
            'page'=> Page::with('sections')->where('title','SolarSys')->first(),
            'baseSections' => Section::where('page_id','0')->get()
        ]);
    })->name('home/solarsys');




    Route::post('/api/ai/call', [\App\Http\Controllers\AIServiceController::class, 'callAI'])->name('ai.call');
    Route::post('/api/ai/test', function() {
        return response()->json(['status' => 'working']);
    })->middleware('api');