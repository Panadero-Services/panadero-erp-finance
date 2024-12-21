<?php
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\get;
use Illuminate\Support\Facades\Route;



it('returns a correct json response', function() {
    $url = route('posts');
    dd($url);
});


/*
it ('should returnz the correct component', function(){ 
	get(route('post'))
	->assertInertia(fn(AssertableInertia $inertia)=> $inertia
		->component('Posts', true)
	);
});
/*
it ('passes posts to the', function(){ 
	get(route('posts.index'))
	->assertInertia(fn(AssertableInertia $inertia)=> $inertia
		->has("posts")
	);
});
*/