// routes/game.php - Only for game server functionality
<?php

use App\Http\Controllers\GameServerController;

Route::prefix('game')->group(function () {
    Route::get('/status', [GameServerController::class, 'getStatus']);
    Route::post('/players/join', [GameServerController::class, 'playerJoin']);
    // ... other game server routes
});