<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::view('/admin', 'admin');
Route::view('/play', 'play');
