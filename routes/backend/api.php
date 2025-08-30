<?php

use App\Http\Controllers\Backend\GameAdminController;
use Illuminate\Support\Facades\Route;

Route::post('/games', [GameAdminController::class, 'store']);
Route::put('/games/{id}', [GameAdminController::class, 'update']);
Route::delete('/games/{id}', [GameAdminController::class, 'destroy']);
