<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $sessions = GameSession::with('questions')->get();
        return view('admin', compact('sessions'));
    }

    public function create()
    {
        return view('admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:game_sessions,code',
            'video_url' => 'nullable|url',
            'questions' => 'nullable|array',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|string', // Comma-separated
            'questions.*.correct_answer' => 'required',
        ]);

        DB::transaction(function () use ($validated) {
            $session = GameSession::create([
                'code' => $validated['code'],
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
                    $session->questions()->attach($question->id);
                }
            }
        });

        return redirect()->route('admin.index')->with('success', 'Game session created successfully.');
    }

    public function edit($id)
    {
        $session = GameSession::with('questions')->findOrFail($id);
        return view('admin.edit', compact('session'));
    }

    public function update(Request $request, $id)
    {
        $session = GameSession::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|unique:game_sessions,code,' . $session->id,
            'video_url' => 'nullable|url',
            'questions' => 'nullable|array',
            'questions.*.id' => 'nullable|integer',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|string', // Comma-separated
            'questions.*.correct_answer' => 'required',
        ]);

        DB::transaction(function () use ($session, $validated) {
            $session->update([
                'code' => $validated['code'],
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

            $session->questions()->sync($questionIds);
        });

        return redirect()->route('admin.index')->with('success', 'Game session updated successfully.');
    }

    public function destroy($id)
    {
        $session = GameSession::findOrFail($id);
        $session->delete();

        return redirect()->route('admin.index')->with('success', 'Game session deleted successfully.');
    }

    public function start($id)
    {
        $session = GameSession::findOrFail($id);
        $session->status = 'started';
        $session->save();

        event(new \App\Events\SessionStarted($session));

        return redirect()->route('admin.index')->with('success', 'Game session started.');
    }

    public function finish($id)
    {
        $session = GameSession::findOrFail($id);
        $session->status = 'finished';
        $session->save();

        event(new \App\Events\SessionFinished($session));

        return redirect()->route('admin.index')->with('success', 'Game session finished.');
    }
}
