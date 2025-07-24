<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\WorldTransfer;

class WorldTransferCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transfer;

    /**
     * Create a new event instance.
     */
    public function __construct(WorldTransfer $transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('world-transfers'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'transfer_id' => $this->transfer->id,
            'player_name' => $this->transfer->player->name,
            'from_world' => $this->transfer->fromWorld?->name,
            'to_world' => $this->transfer->toWorld->name,
            'transfer_type' => $this->transfer->transfer_type,
            'resources_transferred' => $this->transfer->resources_transferred,
            'transfer_time' => $this->transfer->transfer_time
        ];
    }
} 