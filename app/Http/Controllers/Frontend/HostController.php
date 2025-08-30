<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;

class HostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::findOrFail($validated['game_id']);

        $session = GameSession::create([
            'game_id' => $game->id,
            'code' => Str::upper(Str::random(6)),
            'status' => 'waiting',
            // host_id will be set when the host joins
        ]);

        return redirect()->route('host.waiting', $session->code);
    }

    public function waiting($code)
    {
        $session = GameSession::with('game')->where('code', $code)->firstOrFail();
        return view('frontend.host.waiting', compact('session'));
    }

    public function start(Request $request, $code)
    {
        $session = GameSession::where('code', $code)->firstOrFail();
        $session->status = 'started';
        $session->save();

        event(new \App\Events\SessionStarted($session));

        return redirect()->route('host.play', $code);
    }

    public function play($code)
    {
        $session = GameSession::with('game.questions')->where('code', $code)->firstOrFail();
        return view('frontend.host.play', compact('session'));
    }

    public function next(Request $request, $code)
    {
        $session = GameSession::with('game.questions')->where('code', $code)->firstOrFail();
        $nextQuestionIndex = $session->current_question_index + 1;

        if ($nextQuestionIndex < $session->game->questions->count()) {
            $session->current_question_index = $nextQuestionIndex;
            $session->save();
            event(new \App\Events\NextQuestion($session->code, $nextQuestionIndex));
        } else {
            // No more questions, finish the game
            $session->status = 'finished';
            $session->save();
            event(new \App\Events\SessionFinished($session));
        }

        return back();
    }

    public function stop(Request $request, $code)
    {
        $session = GameSession::where('code', $code)->firstOrFail();
        $session->status = 'finished';
        $session->save();

        event(new \App\Events\SessionFinished($session));

        return redirect()->route('host.play', $code);
    }
}
