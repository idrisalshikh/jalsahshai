<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameSessionController;

Route::get('/sessions', [GameSessionController::class, 'index']);
Route::get('/sessions/{code}', [GameSessionController::class, 'show']);
Route::post('/sessions/{code}/start', [GameSessionController::class, 'start']);
Route::post('/sessions/{code}/finish', [GameSessionController::class, 'finish']);
Route::post('/sessions/{code}/answer', [GameSessionController::class, 'answer']);
