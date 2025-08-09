<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\DdCaseController;

Route::prefix('tenant')->middleware(['auth', 'roles:admin,tenant'])->group(function () {
    Route::get('/dd/case', [DdCaseController::class, 'index']);
//    Route::get('/dd/case/{public_id}/summary', [DdCaseController::class, 'show']);
});
