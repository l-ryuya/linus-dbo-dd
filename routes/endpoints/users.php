<?php

declare(strict_types=1);

use App\Http\Controllers\UsersController;

Route::middleware(['auth'])->get('/users/me', [UsersController::class, 'me']);
