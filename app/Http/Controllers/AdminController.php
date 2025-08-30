<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $games = Game::with('questions')->get();
        return view('admin', compact('games'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'questions' => 'nullable|array',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|string', // Comma-separated
            'questions.*.correct_answer' => 'required',
        ]);

        DB::transaction(function () use ($validated) {
            $game = Game::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'video_url' => $validated['video_url'],
            ]);

            if (isset($validated['questions'])) {
                foreach ($validated['questions'] as $questionData) {
                    $question = Question::create([
                        'text' => $questionData['text'],
                        'type' => $questionData['type'],
                        'options' => isset($questionData['options']) ? explode(',', $questionData['options']) : null,
                        'correct_answer' => $questionData['correct_answer'],
                    ]);
                    $game->questions()->attach($question->id);
                }
            }
        });

        return redirect()->route('admin.index')->with('success', 'Game created successfully.');
    }

    public function edit($id)
    {
        $game = Game::with('questions')->findOrFail($id);
        return view('admin.edit', compact('game'));
    }

    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|integer',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|string', // Comma-separated
            'questions.*.correct_answer' => 'required',
        ]);

        DB::transaction(function () use ($game, $validated) {
            $game->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'video_url' => $validated['video_url'],
            ]);

            $questionIds = [];
            if (isset($validated['questions'])) {
                foreach ($validated['questions'] as $questionData) {
                    $options = isset($questionData['options']) ? explode(',', $questionData['options']) : null;
                    if (isset($questionData['id'])) {
                        $question = Question::find($questionData['id']);
                        if ($question) {
                            $question->update([
                                'text' => $questionData['text'],
                                'type' => $questionData['type'],
                                'options' => $options,
                                'correct_answer' => $questionData['correct_answer'],
                            ]);
                        }
                    } else {
                        $question = Question::create([
                            'text' => $questionData['text'],
                            'type' => $questionData['type'],
                            'options' => $options,
                            'correct_answer' => $questionData['correct_answer'],
                        ]);
                    }
                    $questionIds[] = $question->id;
                }
            }

            $game->questions()->sync($questionIds);
        });

        return redirect()->route('admin.index')->with('success', 'Game updated successfully.');
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return redirect()->route('admin.index')->with('success', 'Game deleted successfully.');
    }
}
