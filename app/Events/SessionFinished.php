<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionFinished implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $session;
    public $code;

    public function __construct(array $session)
    {
        $this->session = $session;
        $this->code = $session['code'];
    }

    public function broadcastOn()
    {
        return new Channel('jalsah.' . $this->code);
    }

    public function broadcastWith()
    {
        return ['session' => $this->session];
    }

    public function broadcastAs()
    {
        return 'SessionFinished';
    }
}
