<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/host', [TestController::class, 'showHostPage'])->name('test.host');
Route::get('/player', [TestController::class, 'showPlayerPage'])->name('test.player');

Route::prefix('fire')->group(function () {
    Route::post('/start', [TestController::class, 'fireStartGame'])->name('test.fire.start');
    Route::post('/next', [TestController::class, 'fireNextQuestion'])->name('test.fire.next');
    Route::post('/end', [TestController::class, 'fireEndGame'])->name('test.fire.end');
    Route::post('/join', [TestController::class, 'firePlayerJoined'])->name('test.fire.join');
    Route::post('/answer', [TestController::class, 'fireAnswerSubmitted'])->name('test.fire.answer');
    Route::post('/leave', [TestController::class, 'firePlayerLeft'])->name('test.fire.leave');
});
