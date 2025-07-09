<?php

declare(strict_types=1);

use App\Http\Controllers\UserController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users/me', [UserController::class, 'me']);
});
