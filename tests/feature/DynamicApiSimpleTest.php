<?php

use App\Models\User;

/**
 * Simple test to verify dynamic API works without factories
 */

it('can access the test environment', function () {
    expect(true)->toBeTrue();
});

it('can make a basic API request', function () {
    $response = $this->getJson('/api/posts');
    
    // Should get 401 because not authenticated
    expect($response->status())->toBe(401);
});

it('can create a user manually', function () {
    // Create user without factory
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    
    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Test User');
});

it('can authenticate and access API', function () {
    // Create user manually
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test2@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    
    $this->actingAs($user);
    
    $response = $this->getJson('/api/posts');
    
    // Should get 200 because authenticated
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
});

it('returns 404 for non-existent entity', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test3@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    
    $this->actingAs($user);
    
    $response = $this->getJson('/api/non_existent_table');
    
    expect($response->status())->toBe(404);
    expect($response->json('success'))->toBeFalse();
}); 