<?php

/**
 * Clean API test - minimal and focused
 */

it('can access the test environment', function () {
    expect(true)->toBeTrue();
});

it('can make a basic API request', function () {
    $response = test()->getJson('/api/posts');
    
    // Should get 401 because not authenticated
    expect($response->status())->toBe(401);
});

it('returns 404 for non-existent entity', function () {
    $response = test()->getJson('/api/non_existent_table');
    
    expect($response->status())->toBe(404);
    expect($response->json('success'))->toBeFalse();
});

it('can test with mock user', function () {
    // Create a mock user without database
    $user = new \App\Models\User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    test()->actingAs($user);
    
    $response = test()->getJson('/api/posts');
    
    // Should get 200 because authenticated
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
});

it('can test multiple entities', function () {
    $user = new \App\Models\User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    test()->actingAs($user);
    
    // Test core entities
    $entities = ['posts', 'users', 'categories'];
    
    foreach ($entities as $entity) {
        $response = test()->getJson("/api/{$entity}");
        
        expect($response->status())->toBe(200);
        expect($response->json())->toBeArray();
    }
});

it('handles empty results properly', function () {
    $user = new \App\Models\User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    test()->actingAs($user);

    $response = test()->getJson('/api/web3_projects');
    
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
}); 