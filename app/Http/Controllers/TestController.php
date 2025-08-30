<?php

namespace App\Http\Controllers;

use App\Events\AnswerSubmitted;
use App\Events\NextQuestion;
use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use App\Events\SessionFinished;
use App\Events\SessionStarted;
use App\Models\Player;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function showHostPage()
    {
        return view('frontend.test.host');
    }

    public function showPlayerPage()
    {
        return view('frontend.test.player');
    }

    public function fireStartGame(Request $request)
    {
        event(new SessionStarted($this->getTestSession()));
        return response()->json(['status' => 'ok']);
    }

    public function fireNextQuestion(Request $request)
    {
        event(new NextQuestion($request->input('session_code'), 1));
        return response()->json(['status' => 'ok']);
    }

    public function fireEndGame(Request $request)
    {
        event(new SessionFinished($this->getTestSession()));
        return response()->json(['status' => 'ok']);
    }

    public function firePlayerJoined(Request $request)
    {
        event(new PlayerJoined($request->input('session_code'), $this->getTestPlayer($request)));
        return response()->json(['status' => 'ok']);
    }

    public function fireAnswerSubmitted(Request $request)
    {
        event(new AnswerSubmitted($request->input('session_code'), $this->getTestPlayer($request), 0, $request->input('answer')));
        return response()->json(['status' => 'ok']);
    }

    public function firePlayerLeft(Request $request)
    {
        event(new PlayerLeft($request->input('session_code'), $this->getTestPlayer($request)));
        return response()->json(['status' => 'ok']);
    }

    private function getTestSession()
    {
        $session = new \App\Models\GameSession(['code' => 'TEST123']);
        $session->id = 1;
        return $session;
    }

    private function getTestPlayer(Request $request)
    {
        $player = new Player(['nickname' => $request->input('nickname', 'TestPlayer')]);
        $player->id = rand(1, 1000);
        return $player;
    }
}
