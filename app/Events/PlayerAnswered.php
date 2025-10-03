<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use Illuminate\Support\Facades\Log;
use App\Models\Player;
use App\Models\Question;
class PlayerAnswered implements ShouldBroadcastNow
{
use Dispatchable, InteractsWithSockets, SerializesModels;

    public $answer;
    public $playerId;
    public $score;
    public $session_id;
    public function __construct($session_id,$playerId,$score,$answer)
    {
        $this->playerId = $playerId;
        $this->score = $score;
        $this->answer = $answer;
        $this->session_id = $session_id;
        Log::info('__construct game-' . $this->session_id);
    }

    public function broadcastOn()
    {
        Log::info('Broadcasting PlayerAnswered to game-' . $this->session_id);
        return new Channel('game-' . $this->session_id);
    }

public function broadcastWith()
    {
        return [
            "id"=>0,
            "playerId"=>$this->playerId,
            "score"=>$this->score,
            "answer"=>$this->answer

            
        ];
    }

    public function broadcastAs()
    {
        return 'player.answered';
    }
}
