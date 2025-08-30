<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $session;
    public string $code;

    public function __construct(\App\Models\GameSession $session)
    {
        $this->session = $session->load('game.questions')->toArray();
        $this->code = $session->code;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('jalsah.session.' . $this->code),
        ];
    }

    public function broadcastWith()
    {
        return ['session' => $this->session];
    }

    public function broadcastAs()
    {
        return 'SessionStarted';
    }
}
