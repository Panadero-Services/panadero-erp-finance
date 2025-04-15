<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        Gate::define('admin-access', function(User $user) {
            return $user->hasRole('admin') || $user->hasRole('master');
        });
        // permission model
        Gate::define('manage-posts', function(User $user, Post $post) {
            return $user->hasRole('admin') || $user->hasRole('master') || $user->id == $post->id;
        });
        
/*
        // permission model
        Gate::define('admin-access', function(User $user) {
            return $user->hasRole('admin') || $user->hasRole('master');
        });
        // permission model
        Gate::define('manage-posts', function(User $user, Post $post) {
            return $user->hasRole('admin') || $user->hasRole('master') || $user->id == $post->id;
        });
*/


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
