<?php

use App\Http\Controllers\Frontend\PlayerApiController;
use Illuminate\Support\Facades\Route;

// Player API
Route::get('/players/{id}', [PlayerApiController::class, 'show']);
