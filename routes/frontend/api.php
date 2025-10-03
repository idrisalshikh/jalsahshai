<?php

use App\Http\Controllers\api\GameController;
use App\Http\Controllers\Frontend\PlayerApiController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Player API
Route::get('/players/{id}', [PlayerApiController::class, 'show']);
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