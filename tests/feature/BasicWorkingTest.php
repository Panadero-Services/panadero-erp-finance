<?php

/**
 * Basic working test to verify the setup
 */

test('can access the test environment', function () {
    expect(true)->toBeTrue();
});

test('can make a basic HTTP request', function () {
    $response = $this->get('/');
    
    // Should get some response (could be 200, 302, etc.)
    expect($response->status())->toBeGreaterThan(0);
});

test('can make a basic API request', function () {
    $response = $this->getJson('/api/posts');
    
    // Should get 401 because not authenticated
    $response->assertStatus(401);
});

test('returns 404 for non-existent endpoint', function () {
    $response = $this->getJson('/api/non_existent_table');
    
    $response->assertStatus(404);
});

test('can use Laravel helpers', function () {
    $hashed = bcrypt('password');
    expect($hashed)->toBeString();
    expect(strlen($hashed))->toBeGreaterThan(10);
});

test('can test with mock user', function () {
    $user = new \App\Models\User([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $this->actingAs($user);
    
    $response = $this->getJson('/api/posts');
    $response->assertStatus(200);
}); 