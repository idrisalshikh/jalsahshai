<?php

use App\Http\Controllers\api\GameController;
use App\Http\Controllers\Frontend\PlayerApiController;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Player API
Route::get('/players/{id}', [PlayerApiController::class, 'show']);
Route::get('/games',function() {
    $games=Game::with('questions')->get()->map(function($game) {
        return [
            'id' => $game->id,
            'title' => $game->name,
            'description' => $game->description,
            'thumbnail' => $game->thumbnail ? asset('storage/' . $game->thumbnail) : null,
            'video' => ["id"=>0,"url"=>$game->video_url?:""],
            'is_timed' => $game->is_timed,
            'questions' => $game->questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'text' => $question->text,
                    'options' => $question->options, // Assuming options is already stored as an array
                    'answer' => $question->correct_answer,
                    'thumbnail' => $question->thumbnail ? asset('storage/' . $question->thumbnail) : null,
                    'time_limit' => $question->time_limit,
                ];
            }),
        ];
    });
    return response()->json($games);
});
Route::prefix('game')->group(function () {
    Route::get('/test', function() {
        User::first()->dd(); // Ensure DB connection is working
        return response()->json(['message' => 'API is working']);
    });
    Route::post('/session/create', [GameController::class, 'createSession']);
    Route::post('/session/{id}/join', [GameController::class, 'joinSession']);
    Route::post('/session/{id}/start', [GameController::class, 'startSession']);
    Route::post('/session/{id}/next-question', [GameController::class, 'nextQuestion']);
    Route::post('/session/{id}/answer', [GameController::class, 'submitAnswer']);
    Route::post('/session/{id}/close-question', [GameController::class, 'closeQuestion']);
    Route::post('/session/{id}/show-answers', [GameController::class, 'showAnswers']);    
    Route::post('/session/{id}/finish', [GameController::class, 'finishSession']);
});