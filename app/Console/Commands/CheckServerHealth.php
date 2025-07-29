<?php
// app/Console/Commands/CheckServerHealth.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GameWorld;

class CheckServerHealth extends Command
{
    protected $signature = 'game:check-health';
    protected $description = 'Check health of game servers and mark unreachable ones';

    public function handle()
    {
        $threshold = now()->subMinutes(5);  // 5 minutes without heartbeat = unreachable

        GameWorld::where('status', 'online')
            ->where('last_heartbeat', '<', $threshold)
            ->each(function ($world) {
                $world->updateServerState(
                    GameWorld::STATUS_UNREACHABLE,
                    'No heartbeat for over 5 minutes'
                );
                $world->increment('consecutive_fails');
            });
    }
}