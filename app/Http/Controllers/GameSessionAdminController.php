<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameSessionAdminController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:game_sessions,code',
            'video_url' => 'nullable|url',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'required',
        ]);

        $session = DB::transaction(function () use ($validated) {
            $session = GameSession::create([
                'code' => $validated['code'],
                'video_url' => $validated['video_url'],
            ]);

            foreach ($validated['questions'] as $questionData) {
                $question = Question::create([
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'options' => $questionData['options'] ?? null,
                    'correct_answer' => $questionData['correct_answer'],
                ]);
                $session->questions()->attach($question->id);
            }

            return $session;
        });

        return response()->json($session->load('questions'), 201);
    }

    public function update(Request $request, $id)
    {
        $session = GameSession::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:game_sessions,code,' . $session->id,
            'video_url' => 'nullable|url',
            'questions' => 'required|array',
            'questions.*.id' => 'nullable|integer',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'required',
        ]);

        $session = DB::transaction(function () use ($session, $validated) {
            $session->update([
                'code' => $validated['code'],
                'video_url' => $validated['video_url'],
            ]);

            $questionIds = [];
            foreach ($validated['questions'] as $questionData) {
                if (isset($questionData['id'])) {
                    $question = Question::find($questionData['id']);
                    if ($question) {
                        $question->update([
                            'text' => $questionData['text'],
                            'type' => $questionData['type'],
                            'options' => $questionData['options'] ?? null,
                            'correct_answer' => $questionData['correct_answer'],
                        ]);
                    }
                } else {
                    $question = Question::create([
                        'text' => $questionData['text'],
                        'type' => $questionData['type'],
                        'options' => $questionData['options'] ?? null,
                        'correct_answer' => $questionData['correct_answer'],
                    ]);
                }
                $questionIds[] = $question->id;
            }

            $session->questions()->sync($questionIds);

            $session->questions()->sync($questionIds);

            // Delete questions that are no longer attached to this session
            $session->questions()->whereNotIn('questions.id', $questionIds)->delete();

            return $session;
        });

        return response()->json($session->load('questions'));
    }

    public function destroy($id)
    {
        $session = GameSession::findOrFail($id);
        $session->delete();

        return response()->json(null, 204);
    }
}
