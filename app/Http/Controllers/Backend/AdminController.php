<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $games = Game::with('questions')->get();
        return view('backend.dashboard', compact('games'));
    }

    public function create()
    {
        return view('backend.games.create');
    }

    public function store(Request $request, GameService $gameService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'questions' => 'nullable|array',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.options' => 'nullable|string',
            'questions.*.correct_answer' => 'required',
        ]);

        $gameService->createGame($validated);

        return redirect()->route('admin.index')->with('success', 'Game created successfully.');
    }

    public function edit($id)
    {
        $game = Game::with('questions')->findOrFail($id);
        return view('backend.games.edit', compact('game'));
    }

    public function update(Request $request, $id, GameService $gameService)
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
            'questions.*.options' => 'nullable|string',
            'questions.*.correct_answer' => 'required',
        ]);

        $gameService->updateGame($game, $validated);

        return redirect()->route('admin.index')->with('success', 'Game updated successfully.');
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return redirect()->route('admin.index')->with('success', 'Game deleted successfully.');
    }
}
