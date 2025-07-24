<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Score;

class ScoreUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $score;

    /**
     * Create a new event instance.
     */
    public function __construct(Score $score)
    {
        $this->score = $score;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('game-scores'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'score_id' => $this->score->id,
            'player_name' => $this->score->self,
            'score' => $this->score->score,
            'stage' => $this->score->stage,
            'bonus' => $this->score->bonus,
            'created_at' => $this->score->created_at,
            'is_new_high_score' => $this->isNewHighScore()
        ];
    }

    /**
     * Check if this is a new high score
     */
    private function isNewHighScore(): bool
    {
        $currentHigh = Score::max('score') ?? 0;
        return $this->score->score > $currentHigh;
    }
} 