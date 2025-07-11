<?php

use App\Models\User;

test('can fetch all entities via dynamic API', function () {
    // Create a test user
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    
    $this->actingAs($user);
    
    // Test all major entities
    $entities = [
        'posts', 'projects', 'business_services', 'i1_orders', 
        'futures', 'articles', 'categories', 'users'
    ];
    
    foreach ($entities as $entity) {
        $response = $this->getJson("/api/{$entity}");
        $response->assertStatus(200);
        expect($response->json())->toBeArray();
    }
}); 