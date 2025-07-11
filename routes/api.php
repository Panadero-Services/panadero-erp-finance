<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\Project;
use App\Models\Future;
use App\Models\BusinessService;
use App\Http\Controllers\SomethingController;
use App\Http\Middleware\VerifyFastApiKey;
use App\Http\Middleware\EnsureTokenIsValid;

use App\Http\Controllers\BotController;
use App\Http\Controllers\ProviderController;
use OpenAI\Client;
use OpenAI\Transporters\HttpTransporter;
use OpenAI\ValueObjects\Transporter\BaseUri;
use OpenAI\ValueObjects\Transporter\Headers;
use OpenAI\ValueObjects\ApiKey;
use OpenAI\ValueObjects\Transporter\QueryParams;
use OpenAI\ValueObjects\Transporter\Payload;
use GuzzleHttp\Client as GuzzleClient;
use App\Http\Resources\UserResource;
use App\Http\Controllers\ModelConfigController;
use App\Http\Controllers\DynamicController;
use App\Http\Controllers\UserPermissionController;
use App\Models\I1Order;

Route::apiResource('providers', ProviderController::class);

// Assuming you have a ProviderController
Route::get('/providers/{provider}/balance', [ProviderController::class, 'getContractBalance'])->name('providers.balance');

// ... other routes ...
Route::post('/providers/{provider}/test', [ProviderController::class, 'test'])->name('providers.test');

Route::prefix('bots')->group(function () {
    Route::get('/', [BotController::class, 'index']);
    Route::post('/', [BotController::class, 'store']);
    Route::put('/{id}', [BotController::class, 'update']);
    Route::delete('/{id}', [BotController::class, 'destroy']);
    Route::post('/{id}/start', [BotController::class, 'start']);
    Route::post('/{id}/stop', [BotController::class, 'stop']);
    Route::get('/{id}/status', [BotController::class, 'status']);
});


Route::get('test_user', function (){
    return Post::take(5)->get();
});

Route::get('whatever', function (){
    return Post::take(51)->get();
});

Route::get('projects', function (){
    return Project::take(25)->get();
});

//Route::get('futures', function (){
///    return Future::take(50)->get();
//});

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

Route::get('u', function (Request $request){
    $response = $request;
    return $response;
})->middleware('auth:sanctum');


Route::post('/example', [SomethingController::class, 'justAnExample'])
->middleware(VerifyFastApiKey::class);

// Model configuration
Route::get('/model-config/{module}/{table}', [ModelConfigController::class, 'getModelConfig'])
    ->middleware(['web', 'auth:sanctum']);

/*
Route::get('/examplez', [SomethingController::class, 'justAnExample']);

Route::group([
    'prefix' => 'v1',
    'middleware' => 'with_fast_api_key'
], function () {
    Route::post('/examplez', [SomethingController::class, 'justAnExample']);
    // ...
});
*/

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

// Add this at the end or in the appropriate section for API resources
Route::get('/resource/{table}', [\App\Http\Controllers\ResourceController::class, 'index']);

// Add these routes BEFORE the dynamic catch-all route
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

    // Dynamic entity routes
    // Comment out or remove these specific routes
    /*
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
    */


});

// Keep your existing dynamic catch-all route AFTER these specific routes
Route::get('/api/{table}', [DynamicController::class, 'api'])->name('api.table')->middleware('web');
// Add this with your other API routes
Route::patch('{table}/{id}/field', [DynamicController::class, 'updateField']);

// Add the missing API routes for updating records
// Comment out these specific update routes
/*
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
*/

// Keep only these generic routes
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // Generic dynamic routes
    Route::prefix('api')->group(function () {
        // List/Index
        Route::get('{table}', [DynamicController::class, 'api'])
            ->name('api.table');
            
        // Update
        Route::put('{table}/{id}', [DynamicController::class, 'update'])
            ->name('api.table.update');
            
        // Update single field
        Route::patch('{table}/{id}/field', [DynamicController::class, 'updateField'])
            ->name('api.table.field.update');
            
        // Delete
        Route::delete('{table}/{id}', [DynamicController::class, 'destroy'])
            ->name('api.table.destroy');
    });
});

Route::get('/auth/verify-session', function () {
    if (!auth()->check()) {
        return response()->json(['valid' => false], 401);
    }
    
    return response()->json([
        'valid' => true,
        'user' => auth()->user()->name,
        'session_id' => session()->getId()
    ]);
})->middleware('auth:sanctum');Route::get('i1_orders', function (){
    return I1Order::take(50)->get();
});

