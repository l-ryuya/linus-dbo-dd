<?php

use App\Http\Controllers\Auth\TokenController;

Route::post('/auth/login', [TokenController::class, 'login']);
Route::get('/auth/logout', [TokenController::class, 'logout'])->middleware('auth:sanctum');
