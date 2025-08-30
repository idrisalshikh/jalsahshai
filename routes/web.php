<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/admin/games/create', [AdminController::class, 'create'])->name('admin.games.create');
Route::post('/admin/games', [AdminController::class, 'store'])->name('admin.games.store');
use App\Http\Controllers\PlayerController;

Route::get('/admin/games/{id}/edit', [AdminController::class, 'edit'])->name('admin.games.edit');
Route::put('/admin/games/{id}', [AdminController::class, 'update'])->name('admin.games.update');
Route::delete('/admin/games/{id}', [AdminController::class, 'destroy'])->name('admin.games.destroy');

Route::post('/join', [PlayerController::class, 'join'])->name('join');
use App\Http\Controllers\GameController;

Route::get('/games', [GameController::class, 'index'])->name('games.index');
use App\Http\Controllers\HostController;

Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');

Route::post('/host', [HostController::class, 'store'])->name('host.store');
Route::get('/host/{code}/waiting', [HostController::class, 'waiting'])->name('host.waiting');
Route::post('/host/{code}/start', [HostController::class, 'start'])->name('host.start');
Route::get('/host/{code}/play', [HostController::class, 'play'])->name('host.play');
Route::post('/host/{code}/next', [HostController::class, 'next'])->name('host.next');

Route::get('/play/{code}', [PlayerController::class, 'show'])->name('play');
Route::post('/play/{code}/answer', [PlayerController::class, 'answer'])->name('play.answer');
