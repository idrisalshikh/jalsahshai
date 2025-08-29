<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\AdminController;

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::get('/admin/sessions/create', [AdminController::class, 'create'])->name('admin.sessions.create');
Route::post('/admin/sessions', [AdminController::class, 'store'])->name('admin.sessions.store');
Route::get('/admin/sessions/{id}/edit', [AdminController::class, 'edit'])->name('admin.sessions.edit');
Route::put('/admin/sessions/{id}', [AdminController::class, 'update'])->name('admin.sessions.update');
Route::delete('/admin/sessions/{id}', [AdminController::class, 'destroy'])->name('admin.sessions.destroy');

Route::view('/play', 'play');
