<?php
// app/Jobs/PlayerTransferJob.php
class PlayerTransferJob implements ShouldQueue
{
    public function handle()
    {
        $session = $this->session;
        $fallbackServer = $this->fallbackServer;

        try {
            // 1. Connect to fallback server
            $connection = $this->connectToServer($fallbackServer);

            // 2. Transfer player data
            $connection->transferPlayer([
                'session_id' => $session->session_id,
                'player_state' => $session->recovery_data,
                'resources' => $session->resources
            ]);

            // 3. Update session record
            $session->update([
                'game_world_id' => $fallbackServer->id,
                'status' => 'active',
                'recovery_data' => null
            ]);

        } catch (Exception $e) {
            // If primary fallback fails, try secondary
            $this->trySecondaryFallback($session);
        }
    }
}
?>
