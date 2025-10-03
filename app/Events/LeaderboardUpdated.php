<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LeaderboardUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $players;
    public $sessionId;

    public function __construct(Collection $players, $sessionId)
    {
        $this->players = $players;
        $this->sessionId = $sessionId;
        Log::info('LeaderboardUpdated event created with ' . $players->count() . ' players.');
    }

    public function broadcastOn()
    {
        
        return new Channel('game-' . $this->sessionId);
    }

    public function broadcastWith()
    {
        return $this->players->map(function ($player) {
        return [
            'id' => $player->id,
            'name' => $player->nickname,
            'score' => $player->score,
        ];
    })->values()->toArray();
    }
    public function broadcastAs()
    {
        return 'leaderboard.updated';
    }
}
