<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\ServiceContractController;

Route::prefix('tenant')->middleware(['auth', 'roles:admin,tenant'])->group(function () {
    Route::get('/service-contracts', [ServiceContractController::class, 'index']);
    Route::get('/service-contracts/{public_id}', [ServiceContractController::class, 'show']);
    Route::post('/service-contracts/{public_id}/cloudsign-status/sync', [ServiceContractController::class, 'cloudsignStatusSync']);
});

// adminはテナントに所属しない為
Route::prefix('tenant')->middleware(['auth', 'roles:tenant'])->group(function () {
    Route::post('/service-contracts', [ServiceContractController::class, 'store']);
    Route::put('/service-contracts/{public_id}', [ServiceContractController::class, 'update']);
});
