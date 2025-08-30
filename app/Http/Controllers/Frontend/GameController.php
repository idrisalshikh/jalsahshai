<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Game;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::all();
        return view('frontend.games.index', compact('games'));
    }

    public function show($id)
    {
        $game = Game::findOrFail($id);
        return view('frontend.games.show', compact('game'));
    }
}
