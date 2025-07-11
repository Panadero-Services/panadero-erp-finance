<?php

use App\Models\User;

/**
 * Working dynamic API test using correct Laravel testing methods
 */

test('can access the test environment', function () {
    expect(true)->toBeTrue();
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
    $user = new User([
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
    $user = new User([
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
    $user = new User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);

    $response = $this->getJson('/api/web3_projects');
    
    $response->assertStatus(200);
    expect($response->json())->toBeArray();
});

test('can test authentication flow', function () {
    // Test without authentication
    $response = $this->getJson('/api/posts');
    $response->assertStatus(401);
    
    // Test with authentication
    $user = new User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);
    
    $response = $this->getJson('/api/posts');
    $response->assertStatus(200);
});

test('can test specific entity responses', function () {
    $user = new User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);
    
    // Test posts endpoint
    $response = $this->getJson('/api/posts');
    $response->assertStatus(200);
    
    $data = $response->json();
    expect($data)->toBeArray();
    
    // If we have data, check structure
    if (!empty($data)) {
        expect($data[0])->toHaveKey('id');
    }
});

test('can test error responses', function () {
    $user = new User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);
    
    // Test non-existent entity
    $response = $this->getJson('/api/non_existent_table');
    $response->assertStatus(404);
    
    $errorData = $response->json();
    expect($errorData)->toHaveKey('success');
    expect($errorData['success'])->toBeFalse();
    expect($errorData)->toHaveKey('message');
}); 