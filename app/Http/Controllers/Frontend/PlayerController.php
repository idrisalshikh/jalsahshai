<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Answer;
use App\Models\GameSession;
use App\Models\Player;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

class PlayerController extends Controller
{
    public function join(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|exists:game_sessions,code',
            'nickname' => 'required|string|max:255',
        ]);

        $session = GameSession::where('code', $validated['code'])->firstOrFail();

        $player = $session->players()->create([
            'nickname' => $validated['nickname'],
            'is_host' => false,
        ]);

        event(new \App\Events\PlayerJoined($session->code, $player));

        session(['player_id' => $player->id]);

        return redirect()->route('play', $session->code);
    }

    public function show($code)
    {
        $session = GameSession::with('game.questions')->where('code', $code)->firstOrFail();
        $player = Player::findOrFail(session('player_id'));
        $nickname = $player->nickname;

        if ($session->status === 'waiting') {
            return view('frontend.player.waiting', compact('session', 'nickname'));
        }

        return view('frontend.player.play', compact('session', 'nickname'));
    }

    public function answer(Request $request, $code)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string',
        ]);

        $session = GameSession::where('code', $code)->firstOrFail();
        $player = Player::findOrFail(session('player_id'));
        $question = Question::findOrFail($validated['question_id']);

        $isCorrect = $question->correct_answer == $validated['answer'];

        if ($isCorrect) {
            $player->score += 10; // Add 10 points for a correct answer
            $player->save();
        }

        Answer::create([
            'player_id' => $player->id,
            'question_id' => $question->id,
            'answer' => $validated['answer'],
        ]);

        event(new \App\Events\AnswerSubmitted($session->code, $player, $session->current_question_index, $validated['answer']));

        return response()->json(['message' => 'Answer submitted!', 'is_correct' => $isCorrect]);
    }
}
