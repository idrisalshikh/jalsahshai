<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\GameSessionAdminController;

// Public API
Route::get('/sessions', [GameSessionController::class, 'index']);
Route::get('/sessions/{code}', [GameSessionController::class, 'show']);
Route::post('/sessions/{code}/start', [GameSessionController::class, 'start']);
Route::post('/sessions/{code}/finish', [GameSessionController::class, 'finish']);
Route::post('/sessions/{code}/answer', [GameSessionController::class, 'answer']);

// Admin API
Route::prefix('admin')->group(function () {
    Route::get('/sessions', [GameSessionController::class, 'index']);
    Route::post('/sessions', [GameSessionAdminController::class, 'store']);
    Route::put('/sessions/{id}', [GameSessionAdminController::class, 'update']);
    Route::delete('/sessions/{id}', [GameSessionAdminController::class, 'destroy']);
});
