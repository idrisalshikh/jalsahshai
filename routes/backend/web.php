<?php

use App\Http\Controllers\Backend\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminController::class, 'index'])->name('admin.index');
Route::get('/games/create', [AdminController::class, 'create'])->name('admin.games.create');
Route::post('/games', [AdminController::class, 'store'])->name('admin.games.store');
Route::get('/games/{id}/edit', [AdminController::class, 'edit'])->name('admin.games.edit');
Route::put('/games/{id}', [AdminController::class, 'update'])->name('admin.games.update');
Route::delete('/games/{id}', [AdminController::class, 'destroy'])->name('admin.games.destroy');
