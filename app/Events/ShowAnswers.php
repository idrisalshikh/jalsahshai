<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShowAnswers implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessionCode;
    public $question;

    /**
     * Create a new event instance.
     */
    public function __construct($sessionCode)
    {
        $this->sessionCode = $sessionCode;
       
    }



//  public function broadcastWith()
//     {
//         return [
//             'id' =>  $this->question->id,
//             'text' => $this->question->text,
//             'options' => $this->question->options
//         ];
//     }

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
        return 'show.answers';
    }
}
