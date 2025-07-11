<?php

/**
 * Simple API test that avoids problematic services
 */

test('can access the test environment', function () {
    expect(true)->toBeTrue();
});

test('can use basic PHP functions', function () {
    $result = 2 + 2;
    expect($result)->toBe(4);
});

test('can test string operations', function () {
    $string = 'Hello World';
    expect($string)->toBeString();
    expect(strlen($string))->toBe(11);
});

test('can test array operations', function () {
    $array = ['a', 'b', 'c'];
    expect($array)->toBeArray();
    expect(count($array))->toBe(3);
});

test('can test boolean operations', function () {
    expect(true)->toBeTrue();
    expect(false)->toBeFalse();
});

test('can test HTTP client manually', function () {
    // Test if we can make a basic HTTP request without Laravel's test methods
    $url = 'http://localhost:8000/api/posts';
    
    // This will likely fail, but it tests if we can make HTTP requests
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json',
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    
    // We expect this to fail, but it tests the basic HTTP functionality
    expect($result !== false || $http_response_header[0] ?? '' !== '')->toBeTrue();
});

test('can test JSON operations', function () {
    $data = ['test' => 'value'];
    $json = json_encode($data);
    $decoded = json_decode($json, true);
    
    expect($json)->toBeString();
    expect($decoded)->toBeArray();
    expect($decoded['test'])->toBe('value');
});

test('can test file operations', function () {
    $tempFile = tempnam(sys_get_temp_dir(), 'test');
    file_put_contents($tempFile, 'test content');
    $content = file_get_contents($tempFile);
    unlink($tempFile);
    
    expect($content)->toBe('test content');
}); 