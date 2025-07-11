<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Project;
use App\Models\BusinessService;
use App\Models\I1Order;
use App\Models\Future;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Team;
use App\Models\Provider;
use App\Models\Bot;
use App\Models\Role;
use App\Models\Permission;

/**
 * Pest-style tests for dynamic API entity fetching
 */

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can fetch all major entities via dynamic API routes', function () {
    $this->actingAs($this->user);

    // Define the most important entities to test
    $entities = [
        'posts' => Post::class,
        'projects' => Project::class,
        'business_services' => BusinessService::class,
        'i1_orders' => I1Order::class,
        'futures' => Future::class,
        'articles' => Article::class,
        'categories' => Category::class,
        'comments' => Comment::class,
        'tags' => Tag::class,
        'tasks' => Task::class,
        'teams' => Team::class,
        'providers' => Provider::class,
        'bots' => Bot::class,
        'roles' => Role::class,
        'permissions' => Permission::class,
    ];

    foreach ($entities as $tableName => $modelClass) {
        // Create test data
        try {
            if (class_exists($modelClass . 'Factory')) {
                $modelClass::factory()->create();
            }
        } catch (Exception $e) {
            // Skip if factory doesn't exist or fails
            continue;
        }

        // Test the API endpoint
        $response = $this->getJson("/api/{$tableName}");

        expect($response->status())->toBe(200);
        expect($response->headers()->get('content-type'))->toContain('application/json');

        $data = $response->json();
        expect($data)->toBeArray();
        
        // If we have data, check structure
        if (!empty($data)) {
            expect($data[0])->toHaveKey('id');
            expect($data[0]['id'])->toBeInt();
        }
    }
});

it('returns 404 for non-existent entities', function () {
    $this->actingAs($this->user);

    $nonExistentEntities = [
        'non_existent_table',
        'invalid_entity',
        'fake_model'
    ];

    foreach ($nonExistentEntities as $tableName) {
        $response = $this->getJson("/api/{$tableName}");
        
        expect($response->status())->toBe(404);
        expect($response->json('success'))->toBeFalse();
        expect($response->json('message'))->toContain('Model');
        expect($response->json('message'))->toContain('not found');
    }
});

it('rejects unauthenticated requests', function () {
    $entities = ['posts', 'projects', 'users'];

    foreach ($entities as $tableName) {
        $response = $this->getJson("/api/{$tableName}");
        
        expect($response->status())->toBe(401);
    }
});

it('returns proper JSON structure for entities', function () {
    $this->actingAs($this->user);

    // Create test data for posts
    $post = Post::factory()->create();

    $response = $this->getJson('/api/posts');

    expect($response->status())->toBe(200);
    
    $data = $response->json();
    expect($data)->toBeArray();
    expect($data)->not->toBeEmpty();
    
    // Check first record structure
    $firstRecord = $data[0];
    expect($firstRecord)->toHaveKey('id');
    expect($firstRecord['id'])->toBeInt();
});

it('handles empty results properly', function () {
    $this->actingAs($this->user);

    $response = $this->getJson('/api/web3_projects');
    
    expect($response->status())->toBe(200);
    
    $data = $response->json();
    expect($data)->toBeArray();
});

it('respects authentication middleware', function () {
    // Without authentication
    $response = $this->getJson('/api/posts');
    expect($response->status())->toBe(401);

    // With authentication
    $this->actingAs($this->user);
    $response = $this->getJson('/api/posts');
    expect($response->status())->toBe(200);
});

it('can fetch users entity', function () {
    $this->actingAs($this->user);

    // Create additional test users
    User::factory()->count(2)->create();

    $response = $this->getJson('/api/users');

    expect($response->status())->toBe(200);
    
    $data = $response->json();
    expect($data)->toBeArray();
    
    if (!empty($data)) {
        expect($data[0])->toHaveKey('id');
        expect($data[0]['id'])->toBeInt();
    }
});

it('can fetch posts with relationships', function () {
    $this->actingAs($this->user);

    // Create a post with user relationship
    $post = Post::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/posts');

    expect($response->status())->toBe(200);
    
    $data = $response->json();
    expect($data)->toBeArray();
    expect($data)->not->toBeEmpty();
    
    // Should find our created post
    $foundPost = collect($data)->firstWhere('id', $post->id);
    expect($foundPost)->not->toBeNull();
});

it('returns consistent data structure across entities', function () {
    $this->actingAs($this->user);

    $testEntities = ['posts', 'categories', 'tags'];

    foreach ($testEntities as $tableName) {
        $response = $this->getJson("/api/{$tableName}");
        
        expect($response->status())->toBe(200);
        
        $data = $response->json();
        expect($data)->toBeArray();
        
        // Each record should be an array
        foreach ($data as $record) {
            expect($record)->toBeArray();
        }
    }
}); 