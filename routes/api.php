<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Project;
use App\Models\Future;
use App\Models\BusinessService;
use App\Models\I1Order;
use App\Models\User;
use App\Http\Controllers\SomethingController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ModelConfigController;
use App\Http\Controllers\DynamicController;
use App\Http\Controllers\MasterGameServerController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\ResourceController;
use App\Http\Middleware\VerifyFastApiKey;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Resources\UserResource;
use OpenAI\Client;
use OpenAI\Transporters\HttpTransporter;
use OpenAI\ValueObjects\Transporter\BaseUri;
use OpenAI\ValueObjects\Transporter\Headers;
use OpenAI\ValueObjects\ApiKey;
use OpenAI\ValueObjects\Transporter\QueryParams;
use GuzzleHttp\Client as GuzzleClient;

use App\Http\Controllers\Finance\FixedAssetController;
use App\Http\Controllers\Finance\BudgetController;
use App\Http\Controllers\Finance\AuditLogController;

/*
|--------------------------------------------------------------------------
| SPECIFIC API ROUTES
|--------------------------------------------------------------------------
| These are dedicated routes for specific functionality that cannot be
| handled by the dynamic system
*/

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

// Add this to api.php


// ============================================================================
// PROVIDER ROUTES
// ============================================================================
Route::apiResource('providers', ProviderController::class);
Route::get('/providers/{provider}/balance', [ProviderController::class, 'getContractBalance'])
    ->name('providers.balance');
Route::post('/providers/{provider}/test', [ProviderController::class, 'test'])
    ->name('providers.test');

// ============================================================================
// BOT ROUTES
// ============================================================================
Route::prefix('bots')->group(function () {
    Route::get('/', [BotController::class, 'index']);
    Route::post('/', [BotController::class, 'store']);
    Route::delete('/{id}', [BotController::class, 'destroy']);
    Route::post('/{id}/start', [BotController::class, 'start']);
    Route::post('/{id}/stop', [BotController::class, 'stop']);
    Route::get('/{id}/status', [BotController::class, 'status']);
    // Note: Bot updates now use dynamic route: PUT /api/bots/{id}
});

// ============================================================================
// AI CHAT ROUTES
// ============================================================================
Route::post('/ai/chat', function (Request $request) {
    try {
        // Get the user's message
        $message = $request->input('message');
        
        // Search for relevant posts
        $searchTerms = explode(' ', $message);
        $query = Post::query();
        
        foreach ($searchTerms as $term) {
            if (strlen($term) > 2) {
                $query->orWhere(function($q) use ($term) {
                    $q->where('title', 'like', '%' . $term . '%')
                      ->orWhere('body', 'like', '%' . $term . '%');
                });
            }
        }
        
        // Get relevant posts with their relationships
        $relevantPosts = $query->with(['user'])
            ->select(['id', 'title', 'body', 'created_at', 'user_id'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Format posts for context
        $context = '';
        if ($relevantPosts->isNotEmpty()) {
            $context = "Here are some relevant posts from our knowledge base:\n\n";
            foreach ($relevantPosts as $post) {
                $context .= "Title: {$post->title}\n";
                $context .= "Content: {$post->body}\n";
                if ($post->user) {
                    $context .= "Author: {$post->user->name}\n";
                }
                $context .= "\n";
            }
        }

        $httpClient = new GuzzleClient();
        $baseUri = BaseUri::from('https://api.openai.com/v1');
        $apiKey = ApiKey::from(config('services.openai.api_key'));
        $headers = Headers::withAuthorization($apiKey);
        $queryParams = QueryParams::create();
        $streamHandler = function ($response) {
            return $response;
        };
        $transporter = new HttpTransporter($httpClient, $baseUri, $headers, $queryParams, $streamHandler);
        $client = new Client($transporter);
        
        // Enhanced system prompt with post context
        $systemPrompt = "You are a helpful AI assistant for a business software platform. You can help with business process automation, data analysis, software recommendations, and technical support. Be concise but informative in your responses.\n\n";
        $systemPrompt .= "Here are some relevant posts from our knowledge base that might help you provide better answers. When referencing these posts, mention their titles and categories:\n\n{$context}";

        // Prepare the conversation history
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $message]
        ];

        // Generate response
        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        return response()->json([
            'message' => $response->choices[0]->message->content,
            'relevant_posts' => $relevantPosts->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->body,
                    'author' => $post->user ? [
                        'id' => $post->user->id,
                        'name' => $post->user->name
                    ] : null,
                    'created_at' => $post->created_at
                ];
            })
        ]);
    } catch (\Exception $e) {
        \Log::error('AI Chat Error: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to generate AI response',
            'message' => $e->getMessage()
        ], 500);
    }
});

// ============================================================================
// AUTHENTICATION & SESSION ROUTES
// ============================================================================
Route::get('/auth/verify-session', function () {
    if (!auth()->check()) {
        return response()->json(['valid' => false], 401);
    }
    
    return response()->json([
        'valid' => true,
        'user' => auth()->user()->name,
        'session_id' => session()->getId()
    ]);
})->middleware('auth:sanctum');

// ============================================================================
// USER PERMISSIONS ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // User permissions management routes
    Route::get('/user-permissions/users', [UserPermissionController::class, 'index']);
    Route::get('/user-permissions/users/search', [UserPermissionController::class, 'search']);
    Route::get('/user-permissions/roles', [UserPermissionController::class, 'roles']);
    Route::put('/user-permissions/users/{user}/roles', [UserPermissionController::class, 'updateRoles']);
    
    // Role permissions management routes
    Route::get('/permissions', [UserPermissionController::class, 'permissions']);
    Route::get('/user-permissions/roles/search', [UserPermissionController::class, 'searchRoles']);
    Route::put('/user-permissions/roles/{role}/permissions', [UserPermissionController::class, 'updateRolePermissions']);
});

// ============================================================================
// MODEL CONFIGURATION ROUTES
// ============================================================================
Route::get('/model-config/{module}/{table}', [ModelConfigController::class, 'getModelConfig'])
    ->middleware(['web', 'auth:sanctum']);

// ============================================================================
// RESOURCE ROUTES
// ============================================================================
Route::get('/resource/{table}', [ResourceController::class, 'index']);

// ============================================================================
// EXAMPLE & TEST ROUTES
// ============================================================================
Route::post('/example', [SomethingController::class, 'justAnExample'])
    ->middleware(VerifyFastApiKey::class);

Route::get('test_user', function (){
    return Post::take(5)->get();
});

Route::get('whatever', function (){
    return Post::take(51)->get();
});

Route::get('u', function (Request $request){
    $response = $request;
    return $response;
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| DYNAMIC API ROUTES
|--------------------------------------------------------------------------
| These routes handle ALL entity operations dynamically and should be placed LAST
| to avoid conflicts with specific routes above
|
| ALL entity updates now use: PUT /api/{table}/{id}
| ALL entity field updates use: PATCH /api/{table}/{id}/field
| ALL entity deletions use: DELETE /api/{table}/{id}
| ALL entity fetching use: GET /api/{table}
|
| Examples:
| - PUT /api/posts/123 -> Updates post with ID 123
| - PUT /api/projects/456 -> Updates project with ID 456
| - PUT /api/users/789 -> Updates user with ID 789
| - PUT /api/bots/101 -> Updates bot with ID 101
| - PUT /api/providers/202 -> Updates provider with ID 202
| - etc. for ALL entities
*/

Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // Generic dynamic routes for ALL entities
    Route::get('{table}', [DynamicController::class, 'api'])
        ->name('api.table');
        
    // CREATE - Create new record
    Route::post('{table}', [DynamicController::class, 'store'])
        ->name('api.table.store');
        
    // READ - Get single record
    Route::get('{table}/{id}', [DynamicController::class, 'show'])
        ->name('api.table.show');
        
    // UPDATE - Update entire record
    Route::put('{table}/{id}', [DynamicController::class, 'update'])
        ->name('api.table.update');
        
    // UPDATE - Update single field
    Route::patch('{table}/{id}/field', [DynamicController::class, 'updateField'])
        ->name('api.table.field.update');
        
    // DELETE - Delete record
    Route::delete('{table}/{id}', [DynamicController::class, 'destroy'])
        ->name('api.table.destroy');
});


/*
Complete CRUD Operations Summary
After adding the missing operations, you'll have complete CRUD functionality for all entities:
✅ CREATE (Create new record)
Route: POST /api/{table}
Example: POST /api/posts → Creates new post
Method: DynamicController@store
✅ READ (List all records)
Route: GET /api/{table}
Example: GET /api/posts → Lists all posts
Method: DynamicController@api
✅ READ (Get single record)
Route: GET /api/{table}/{id}
Example: GET /api/posts/123 → Gets post with ID 123
Method: DynamicController@show
✅ UPDATE (Update entire record)
Route: PUT /api/{table}/{id}
Example: PUT /api/posts/123 → Updates post with ID 123
Method: DynamicController@update
✅ UPDATE (Update single field)
Route: PATCH /api/{table}/{id}/field
Example: PATCH /api/posts/123/field → Updates single field
Method: DynamicController@updateField
✅ DELETE (Delete record)
Route: DELETE /api/{table}/{id}
Example: DELETE /api/posts/123 → Deletes post with ID 123
Method: DynamicController@destroy
Entities That Will Work
This will work for ALL entities in your system:
✅ posts, projects, futures, users, bots, providers
✅ i1_orders, i1_forwarders, i1_business_customers
✅ business_services, categories, comments, tags
✅ tasks, teams, roles, permissions
✅ articles, article_groups, features
✅ And any other model in your app/Models/ directory
Would you like me to implement the missing store method and add the missing routes to complete the CRUD functionality?
*/


/*
|--------------------------------------------------------------------------
| COMMENTED OUT ROUTES (For Reference)
|--------------------------------------------------------------------------
| These routes are commented out but kept for reference

Route::get('/examplez', [SomethingController::class, 'justAnExample']);

Route::group([
    'prefix' => 'v1',
    'middleware' => 'with_fast_api_key'
], function () {
    Route::post('/examplez', [SomethingController::class, 'justAnExample']);
    // ...
});

Route::get('futures', function (){
    return Future::take(50)->get();
});

// Specific dynamic routes (replaced by generic dynamic routes above)
Route::prefix('futures')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('futures.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('futures.update');
});

Route::prefix('projects')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('projects.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('projects.update');
});

Route::prefix('business_services')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('business_services.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('business_services.update');
});

Route::prefix('posts')->group(function () {
    Route::get('/', [DynamicController::class, 'api'])
        ->name('posts.index');
    Route::put('/{id}', [DynamicController::class, 'update'])
        ->name('posts.update');
});

// Specific update routes (replaced by generic dynamic routes above)
Route::put('/api/futures/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('futures.update')->middleware('auth:sanctum');

Route::put('/api/projects/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('projects.update')->middleware('auth:sanctum');

Route::put('/api/business_services/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('business_services.update')->middleware('auth:sanctum');

Route::put('/api/posts/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('posts.update')->middleware('auth:sanctum');

Route::put('/api/i1_orders/{id}', function(Request $request, $id) {
    return app(DynamicController::class)->update($request, $id);
})->name('i1_orders.update')->middleware('auth:sanctum');

// Legacy data routes (migrated to dynamic routes above)
Route::get('projects', function (){
    return Project::take(25)->get();
});

Route::get('business_services', function (){
    return BusinessService::take(50)->get();
});

Route::get('posts', function (){
    return Post::take(50)->get();
});

Route::get('users', function (Request $request) {
    if (!$request->user()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    return UserResource::collection(User::take(25)->get());
})->middleware('auth:sanctum');

Route::get('i1_orders', function (){
    return I1Order::take(50)->get();
});
*/



// Test route to check authentication
Route::get('/test-auth', function () {
    if (auth()->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user()->email,
            'message' => 'Authentication working'
        ]);
    }
    return response()->json([
        'authenticated' => false,
        'message' => 'Not authenticated'
    ], 401);
})->middleware('auth:sanctum');

Route::prefix('finance')->middleware('auth:sanctum')->group(function () {
    // Fixed Assets
    Route::get('/assets', [FixedAssetController::class, 'index']);
    Route::post('/assets', [FixedAssetController::class, 'store']);
    Route::post('/assets/depreciate', [FixedAssetController::class, 'depreciate']);

    // Budgets
    Route::get('/budgets/{period}', [BudgetController::class, 'show']);
    Route::post('/budgets', [BudgetController::class, 'upsert']);

    // Audit
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::post('/audit-logs', [AuditLogController::class, 'store']);
});



