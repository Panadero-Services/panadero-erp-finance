<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Http\Controllers\SomethingController;
use App\Http\Middleware\VerifyFastApiKey;
use App\Http\Middleware\EnsureTokenIsValid;

use App\Http\Controllers\BotController;
use App\Http\Controllers\ProviderController;
use OpenAI\OpenAI;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/adm', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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


Route::post('/example', [SomethingController::class, 'justAnExample'])
->middleware(VerifyFastApiKey::class);

// Model configuration
Route::get('/model-config/{module}/{table}', [ModelConfigController::class, 'getModelConfig'])
    ->middleware(['auth:sanctum', 'verified']);

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
    $message = $request->input('message');
    $context = $request->input('context', []);

    try {
        // Semantic search for relevant posts using full-text search
        $relevantPosts = \App\Models\Post::where(function($query) use ($message) {
            $query->whereFullText(['title', 'content'], $message)
                  ->orWhere('title', 'like', '%' . $message . '%')
                  ->orWhere('content', 'like', '%' . $message . '%');
        })
        ->with(['category', 'tags']) // Eager load relationships
        ->select('id', 'title', 'content', 'category_id', 'created_at', 'slug')
        ->orderByRaw('MATCH(title, content) AGAINST(? IN BOOLEAN MODE) DESC', [$message])
        ->limit(5)
        ->get();

        // Format posts for context with categories and tags
        $postsContext = $relevantPosts->map(function($post) {
            $categories = $post->category ? "Category: {$post->category->name}\n" : "";
            $tags = $post->tags->isNotEmpty() 
                ? "Tags: " . $post->tags->pluck('name')->join(', ') . "\n" 
                : "";
            return "Post ID: {$post->id}\nTitle: {$post->title}\n{$categories}{$tags}Content: {$post->content}";
        })->join("\n\n");

        $client = OpenAI::client(config('services.openai.api_key'));
        
        // Enhanced system prompt with post context
        $systemPrompt = "You are a helpful AI assistant for a business software platform. You can help with business process automation, data analysis, software recommendations, and technical support. Be concise but informative in your responses.\n\n";
        $systemPrompt .= "Here are some relevant posts from our knowledge base that might help you provide better answers. When referencing these posts, mention their titles and categories:\n\n{$postsContext}";

        // Prepare the conversation history with relevant posts
        $messages = array_merge([
            ['role' => 'system', 'content' => $systemPrompt]
        ], $context);

        // Add the current message
        $messages[] = ['role' => 'user', 'content' => $message];

        // Generate response
        $response = $client->chat()->create([
            'model' => 'gpt-4',
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
                    'content' => $post->content,
                    'category' => $post->category ? [
                        'id' => $post->category->id,
                        'name' => $post->category->name
                    ] : null,
                    'tags' => $post->tags->map(function($tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name
                        ];
                    }),
                    'created_at' => $post->created_at,
                    'slug' => $post->slug
                ];
            })
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to generate AI response'
        ], 500);
    }
});