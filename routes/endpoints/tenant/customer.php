<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\CustomerController;

Route::prefix('tenant')->middleware(['auth', 'roles:admin,tenant'])->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{public_id}', [CustomerController::class, 'show']);
});

Route::prefix('tenant')->middleware(['auth', 'roles:tenant'])->group(function () {
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{public_id}', [CustomerController::class, 'update']);
});
