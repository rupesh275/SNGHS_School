<?php

use App\Http\Controllers\Login;
use App\Http\Controllers\Home;
Route::get('dashboard', [Home::class, 'index']);
Route::get('login', [Login::class, 'login']);
