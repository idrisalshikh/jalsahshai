<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NextQuestion implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessionCode;
    public $questionIndex;

    /**
     * Create a new event instance.
     */
    public function __construct($sessionCode, $questionIndex)
    {
        $this->sessionCode = $sessionCode;
        $this->questionIndex = $questionIndex;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('jalsah.session.' . $this->sessionCode),
        ];
    }

    public function broadcastAs()
    {
        return 'NextQuestion';
    }
}
