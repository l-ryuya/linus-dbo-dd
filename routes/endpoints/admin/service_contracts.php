<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ServiceContractsController;

Route::prefix('admin')->middleware(['auth:sanctum', 'scope:admin,service_manager'])->group(function () {
    Route::get('/service-contracts', [ServiceContractsController::class, 'index']);
    Route::get('/service-contracts/{company_code}', [ServiceContractsController::class, 'show']);
});
