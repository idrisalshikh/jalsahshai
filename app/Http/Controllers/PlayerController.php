<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function join(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|exists:game_sessions,code',
            'nickname' => 'required|string|max:255',
        ]);

        session(['nickname' => $validated['nickname']]);

        return redirect()->route('play', ['code' => $validated['code']]);
    }

    public function show($code)
    {
        $session = GameSession::with('questions')->where('code', $code)->firstOrFail();
        $nickname = session('nickname', 'guest');

        return view('play', compact('session', 'nickname'));
    }

    public function answer(Request $request, $code)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string',
        ]);

        $session = GameSession::where('code', $code)->firstOrFail();
        $nickname = session('nickname', 'guest');

        Answer::create([
            'game_session_id' => $session->id,
            'question_id' => $validated['question_id'],
            'nickname' => $nickname,
            'answer' => $validated['answer'],
        ]);

        return response()->json(['message' => 'Answer submitted!']);
    }
}
