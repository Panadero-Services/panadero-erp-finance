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
 * Core entity tests for dynamic API
 * Tests the most important entities to ensure the system works
 */

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can fetch core entities via dynamic API', function () {
    $this->actingAs($this->user);

    // Test the most important entities
    $coreEntities = [
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

    foreach ($coreEntities as $tableName => $modelClass) {
        // Try to create test data
        try {
            if (class_exists($modelClass . 'Factory')) {
                $modelClass::factory()->create();
            }
        } catch (Exception $e) {
            // Skip if factory doesn't exist
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

    $response = $this->getJson('/api/non_existent_table');
    
    expect($response->status())->toBe(404);
    expect($response->json('success'))->toBeFalse();
});

it('rejects unauthenticated requests', function () {
    $response = $this->getJson('/api/posts');
    expect($response->status())->toBe(401);
});

it('can fetch posts with proper structure', function () {
    $this->actingAs($this->user);

    // Create a test post
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

it('handles empty results properly', function () {
    $this->actingAs($this->user);

    $response = $this->getJson('/api/web3_projects');
    
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
});