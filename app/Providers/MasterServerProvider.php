<?php
// app/Providers/MasterServerProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MasterServerService;
use App\Services\ServerRegistrationService;

class MasterServerProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register MasterServerService as a singleton
        $this->app->singleton(MasterServerService::class, function ($app) {
            return new MasterServerService(
                config('game.master_server.heartbeat_interval'),
                config('game.master_server.heartbeat_timeout')
            );
        });

        // Register ServerRegistrationService as a singleton
        $this->app->singleton(ServerRegistrationService::class, function ($app) {
            return new ServerRegistrationService(
                $app->make(MasterServerService::class)
            );
        });
    }

    public function boot(): void
    {
        if (config('game.roles.is_master_server')) {
            // Schedule heartbeat monitoring
            $this->app->booted(function () {
                $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
                
                // Monitor server heartbeats every minute
                $schedule->command('game:monitor-servers')->everyMinute();
                
                // Monitor player recovery every minute
                $schedule->command('game:monitor-recovery')->everyMinute();
            });
        }
    }
}