// routes/master.php - Only for master server functionality
<?php

use App\Http\Controllers\MasterGameServerController;

Route::prefix('master')->group(function () {
    Route::get('/worlds', [MasterGameServerController::class, 'getWorldsStatus']);
    Route::get('/game-state', [MasterGameServerController::class, 'getGameState']);
    Route::post('/worlds/heartbeat', [MasterGameServerController::class, 'updateWorldHeartbeat'])
        ->withoutMiddleware(['web']);
    // ... other master server routes
});