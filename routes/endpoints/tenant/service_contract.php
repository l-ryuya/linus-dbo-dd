<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\ServiceContractController;

// Route::prefix('tenant')->middleware(['auth', 'roles:admin,tenant'])->group(function () {
    Route::get('/service-contracts', [ServiceContractController::class, 'index']);
    Route::get('/service-contracts/{public_id}', [ServiceContractController::class, 'show']);
// });

// Route::prefix('tenant')->middleware(['auth', 'roles:tenant'])->group(function () {
    Route::post('/service-contracts', [ServiceContractController::class, 'store']);
    Route::post('/service-contracts/draft', [ServiceContractController::class, 'storeDraft']);
    Route::put('/service-contracts/{public_id}', [ServiceContractController::class, 'update']);
// });
