<?php
// 2.1 Create State Enum
// app/Enums/ServerState.php
enum ServerState: string
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case UNREACHABLE = 'unreachable';
    case MAINTENANCE = 'maintenance';
    case SHUTDOWN = 'shutdown';

    public function canTransitionTo(ServerState $newState): bool
    {
        return match($this) {
            self::ONLINE => true,
            self::OFFLINE => $newState !== self::UNREACHABLE,
            self::UNREACHABLE => $newState !== self::ONLINE,
            self::MAINTENANCE => $newState !== self::UNREACHABLE,
            self::SHUTDOWN => false
        };
    }
}

// 2.2 Add State Migration
// database/migrations/add_server_state_tracking.php
Schema::table('game_worlds', function (Blueprint $table) {
    $table->string('state')->default(ServerState::OFFLINE->value);
    $table->json('state_history')->nullable();
    $table->timestamp('last_state_change')->nullable();
    $table->integer('consecutive_failures')->default(0);
});

// 2.3 Create State Manager
// app/Services/ServerStateManager.php
class ServerStateManager
{
    public function transitionState(
        GameWorld $server, 
        ServerState $newState, 
        string $reason
    ): void {
        DB::transaction(function () use ($server, $newState, $reason) {
            $oldState = ServerState::from($server->state);
            
            if (!$oldState->canTransitionTo($newState)) {
                throw new InvalidStateTransitionException();
            }

            $server->state_history = array_merge(
                $server->state_history ?? [],
                [[
                    'from' => $oldState->value,
                    'to' => $newState->value,
                    'timestamp' => now(),
                    'reason' => $reason
                ]]
            );
            
            $server->state = $newState->value;
            $server->last_state_change = now();
            $server->save();

            event(new ServerStateChanged($server, $oldState, $newState));
        });
    }
}