<?php

/**
 * Simple working test that doesn't rely on Laravel test methods
 */

test('can access the test environment', function () {
    expect(true)->toBeTrue();
});

test('can use basic PHP functions', function () {
    $result = 2 + 2;
    expect($result)->toBe(4);
});

test('can use Laravel helpers when available', function () {
    // Test if we can use basic Laravel helpers
    $hashed = bcrypt('password');
    expect($hashed)->toBeString();
    expect(strlen($hashed))->toBeGreaterThan(10);
});

test('can test string operations', function () {
    $string = 'Hello World';
    expect($string)->toBeString();
    expect(strlen($string))->toBe(11);
    expect($string)->toContain('Hello');
});

test('can test array operations', function () {
    $array = ['a', 'b', 'c'];
    expect($array)->toBeArray();
    expect(count($array))->toBe(3);
    expect($array[0])->toBe('a');
});

test('can test boolean operations', function () {
    expect(true)->toBeTrue();
    expect(false)->toBeFalse();
    expect(1 === 1)->toBeTrue();
    expect(1 === 2)->toBeFalse();
}); 