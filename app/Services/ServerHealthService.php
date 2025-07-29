<?php
// app/Services/ServerHealthService.php
class ServerHealthService
{
    public function handleServerFailure(GameWorld $server)
    {
        DB::transaction(function () use ($server) {
            // 1. Mark server as down
            $server->updateServerState('down', 'Server failure detected');

            // 2. Get all active player sessions
            $activeSessions = $server->activeSessions;

            // 3. Find fallback server
            $fallbackServer = $this->getFallbackServer();

            // 4. Initiate player transfers
            foreach ($activeSessions as $session) {
                $this->initiatePlayerRecovery($session, $fallbackServer);
            }

            // 5. Notify all connected clients
            event(new ServerFailureEvent($server, $fallbackServer));
        });
    }

    private function initiatePlayerRecovery(PlayerSession $session, GameWorld $fallbackServer)
    {
        // 1. Save current state
        $session->recovery_data = [
            'last_position' => $session->player_state['position'],
            'inventory' => $session->player_state['inventory'],
            'timestamp' => now()
        ];

        // 2. Mark for transfer
        $session->status = 'transferring';
        $session->fallback_server_id = $fallbackServer->id;
        $session->save();

        // 3. Queue transfer job
        PlayerTransferJob::dispatch($session, $fallbackServer);
    }
}
?>
