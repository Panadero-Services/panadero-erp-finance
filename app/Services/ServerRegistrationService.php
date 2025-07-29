<?php
// 3.1 Create Registration Service
// app/Services/ServerRegistrationService.php
class ServerRegistrationService
{
    public function register(array $data): GameWorld
    {
        return DB::transaction(function () use ($data) {
            $server = GameWorld::create([
                'server_id' => $data['server_id'],
                'url' => $data['url'],
                'state' => ServerState::OFFLINE->value,
            ]);

            // Initialize monitoring
            $this->initializeServerMonitoring($server);

            return $server;
        });
    }

    public function unregister(GameWorld $server): void
    {
        DB::transaction(function () use ($server) {
            // Graceful shutdown
            $this->transitionState(
                $server, 
                ServerState::SHUTDOWN, 
                'Planned unregistration'
            );

            // Handle active sessions
            $this->handleActiveSessions($server);

            // Archive server data
            $this->archiveServerData($server);
        });
    }
}