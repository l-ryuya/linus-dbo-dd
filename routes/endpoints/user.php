<?php

declare(strict_types=1);

use App\Http\Controllers\User\MeController;

Route::middleware(['auth'])->group(function () {
    Route::get('/users/me', [MeController::class, 'show']);
});
