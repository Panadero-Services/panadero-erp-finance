<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanExpiredSessions extends Command
{
    protected $signature = 'session:gc';
    protected $description = 'Clean expired sessions from database';

    public function handle()
    {
        $lifetime = config('session.lifetime') * 60; // Convert to seconds
        $expired = now()->subSeconds($lifetime);
        
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', $expired->timestamp)
            ->delete();
            
        $this->info("Cleaned {$deleted} expired sessions.");
        
        return 0;
    }
}