<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\GameSessionAdminController;

use App\Http\Controllers\PlayerApiController;

// Admin API
Route::prefix('admin')->group(function () {
    Route::post('/games', [GameAdminController::class, 'store']);
    Route::put('/games/{id}', [GameAdminController::class, 'update']);
    Route::delete('/games/{id}', [GameAdminController::class, 'destroy']);
});

// Player API
Route::get('/players/{id}', [PlayerApiController::class, 'show']);
Route::get('/sessions/{code}/scores', [GameSessionController::class, 'scores']);
