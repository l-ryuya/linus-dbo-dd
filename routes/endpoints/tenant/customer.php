<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\CustomerController;

Route::prefix('tenant')->middleware(['auth'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
});
