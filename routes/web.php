<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/admin/sessions/create', [AdminController::class, 'create'])->name('admin.sessions.create');
Route::post('/admin/sessions', [AdminController::class, 'store'])->name('admin.sessions.store');
use App\Http\Controllers\PlayerController;

Route::get('/admin/sessions/{id}/edit', [AdminController::class, 'edit'])->name('admin.sessions.edit');
Route::put('/admin/sessions/{id}', [AdminController::class, 'update'])->name('admin.sessions.update');
Route::delete('/admin/sessions/{id}', [AdminController::class, 'destroy'])->name('admin.sessions.destroy');
Route::post('/admin/sessions/{id}/start', [AdminController::class, 'start'])->name('admin.sessions.start');
Route::post('/admin/sessions/{id}/finish', [AdminController::class, 'finish'])->name('admin.sessions.finish');

Route::post('/join', [PlayerController::class, 'join'])->name('join');
Route::get('/play/{code}', [PlayerController::class, 'show'])->name('play');
Route::post('/play/{code}/answer', [PlayerController::class, 'answer'])->name('play.answer');
