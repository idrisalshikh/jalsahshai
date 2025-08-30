<?php

namespace App\Http\Controllers\Backend;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

class GameAdminController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'required',
        ]);

        $game = DB::transaction(function () use ($validated) {
            $game = Game::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'video_url' => $validated['video_url'],
            ]);

            foreach ($validated['questions'] as $questionData) {
                $question = Question::create([
                    'text' => $questionData['text'],
                    'type' => $questionData['type'],
                    'options' => $questionData['options'] ?? null,
                    'correct_answer' => $questionData['correct_answer'],
                ]);
                $game->questions()->attach($question->id);
            }

            return $game;
        });

        return response()->json($game->load('questions'), 201);
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'questions' => 'required|array',
            'questions.*.id' => 'nullable|integer',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'required',
        ]);

        $game = DB::transaction(function () use ($game, $validated) {
            $game->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
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

            $game->questions()->sync($questionIds);

            return $game;
        });

        return response()->json($game->load('questions'));
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return response()->json(null, 204);
    }
}
