<?php

namespace App\Http\Controllers;

use App\Events\SessionFinished;
use App\Events\SessionStarted;
use App\Models\Answer;
use App\Models\GameSession;
use Illuminate\Http\Request;

class GameSessionController extends Controller
{
    public function index()
    {
        return GameSession::with('questions')->get();
    }

    public function show($code)
    {
        return GameSession::with('questions')->where('code', $code)->firstOrFail();
    }

    public function start($code)
    {
        $session = GameSession::where('code', $code)->firstOrFail();
        $session->status = 'started';
        $session->save();

        event(new SessionStarted($session));

        return response()->json(['status' => 'started']);
    }

    public function finish($code)
    {
        $session = GameSession::where('code', $code)->firstOrFail();
        $session->status = 'finished';
        $session->save();

        event(new SessionFinished($session));

        return response()->json(['status' => 'finished']);
    }

    public function answer(Request $request, $code)
    {
        $session = GameSession::where('code', $code)->firstOrFail();
        $questionId = $request->input('question_id');

        $answer = Answer::create([
            'game_session_id' => $session->id,
            'question_id' => $questionId,
            'nickname' => $request->input('nickname'),
            'answer' => $request->input('answer'),
        ]);

        return response()->json(['logged' => true, 'answer_id' => $answer->id]);
    }
}
