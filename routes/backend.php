<?php

use App\Http\Controllers\Backend\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/admin/games/create', [AdminController::class, 'create'])->name('admin.games.create');
Route::post('/admin/games', [AdminController::class, 'store'])->name('admin.games.store');
Route::get('/admin/games/{id}/edit', [AdminController::class, 'edit'])->name('admin.games.edit');
Route::put('/admin/games/{id}', [AdminController::class, 'update'])->name('admin.games.update');
Route::delete('/admin/games/{id}', [AdminController::class, 'destroy'])->name('admin.games.destroy');
