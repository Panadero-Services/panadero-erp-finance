<?php
class Kernel extends HttpKernel
{
    // ...

    protected $middlewareAliases = [
        'game.server' => \App\Http\Middleware\GameServerAuth::class,
        // ...

        'with_fast_api_key' => \App\Http\Middleware\VerifyFastApiKey::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Clean expired sessions every 5 minutes
        $schedule->command('session:gc')->everyMinute();
        
        // Or if you want it more frequent:
        // $schedule->command('session:gc')->everyFiveMinutes();
    }

}
 ?>