<?php

use App\Http\Controllers\Frontend\GameController;
use App\Http\Controllers\Frontend\HostController;
use App\Http\Controllers\Frontend\PlayerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.welcome');
});

Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');

Route::post('/host', [HostController::class, 'store'])->name('host.store');
Route::get('/host/{code}/waiting', [HostController::class, 'waiting'])->name('host.waiting');
Route::post('/host/{code}/start', [HostController::class, 'start'])->name('host.start');
Route::get('/host/{code}/play', [HostController::class, 'play'])->name('host.play');
Route::post('/host/{code}/next', [HostController::class, 'next'])->name('host.next');
Route::post('/host/{code}/stop', [HostController::class, 'stop'])->name('host.stop');

Route::post('/join', [PlayerController::class, 'join'])->name('join');
Route::get('/play/{code}', [PlayerController::class, 'show'])->name('play');
Route::post('/play/{code}/answer', [PlayerController::class, 'answer'])->name('play.answer');
