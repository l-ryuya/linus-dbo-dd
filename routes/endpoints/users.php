<?php

use App\Http\Controllers\UsersController;

Route::middleware(['auth:sanctum'])->get('/users/me', [UsersController::class, 'me']);
