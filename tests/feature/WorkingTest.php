<?php

/**
 * Simple working test with correct Pest configuration
 */

test('can access the test environment', function () {
    expect(true)->toBeTrue();
});

test('can use basic PHP functions', function () {
    $result = 2 + 2;
    expect($result)->toBe(4);
});

test('can use Laravel helpers', function () {
    $hashed = bcrypt('password');
    expect($hashed)->toBeString();
    expect(strlen($hashed))->toBeGreaterThan(10);
});

test('can make a basic API request', function () {
    $response = $this->getJson('/api/posts');
    
    // Should get 401 because not authenticated
    $response->assertStatus(401);
});

test('returns 404 for non-existent entity', function () {
    $response = $this->getJson('/api/non_existent_table');
    
    $response->assertStatus(404);
    expect($response->json('success'))->toBeFalse();
});

test('can test with mock user', function () {
    // Create a mock user without database
    $user = new \App\Models\User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);
    
    $response = $this->getJson('/api/posts');
    
    // Should get 200 because authenticated
    $response->assertStatus(200);
    expect($response->json())->toBeArray();
});

test('can test multiple entities', function () {
    $user = new \App\Models\User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);
    
    // Test core entities
    $entities = ['posts', 'users', 'categories'];
    
    foreach ($entities as $entity) {
        $response = $this->getJson("/api/{$entity}");
        
        $response->assertStatus(200);
        expect($response->json())->toBeArray();
    }
});

test('handles empty results properly', function () {
    $user = new \App\Models\User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);

    $response = $this->getJson('/api/web3_projects');
    
    $response->assertStatus(200);
    expect($response->json())->toBeArray();
}); 