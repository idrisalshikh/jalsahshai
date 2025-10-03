<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoined implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   public $sessionCode;
    public $player;

    /**
     * Create a new event instance.
     */
    public function __construct($id,$player)
    {
        $this->sessionCode = $id;
       $this->player = $player;
        
    }

     public function broadcastWith()
    {
        return [
            'id' => (string) $this->player->id,
            'name' => $this->player->nickname,
            'score' => $this->player->score
        ];
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('game-' . $this->sessionCode),
        ];
    }

    public function broadcastAs()
    {
        return 'player.joined';
    }
}
