<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\GameSession;
use App\Models\Player;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    public function join(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|exists:game_sessions,code',
            'nickname' => 'required|string|max:255',
        ]);

        $player = DB::transaction(function () use ($validated) {
            $session = GameSession::where('code', $validated['code'])->lockForUpdate()->firstOrFail();
            $isHost = is_null($session->host_id);

            $player = $session->players()->create([
                'nickname' => $validated['nickname'],
                'is_host' => $isHost,
            ]);

            if ($isHost) {
                $session->host_id = $player->id;
                $session->save();
            }

            // Broadcast PlayerJoined event
            event(new \App\Events\PlayerJoined($session->code, $player));

            return $player;
        });

        session(['player_id' => $player->id]);

        if ($player->is_host) {
            return redirect()->route('host.waiting', $validated['code']);
        }

        return redirect()->route('play', $validated['code']);
    }

    public function show($code)
    {
        $session = GameSession::with('game.questions')->where('code', $code)->firstOrFail();
        $player = Player::findOrFail(session('player_id'));
        $nickname = $player->nickname;

        return view('play', compact('session', 'nickname'));
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
