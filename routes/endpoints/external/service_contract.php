<?php

declare(strict_types=1);

use App\Http\Controllers\External\ServiceContractController;

Route::prefix('external')->middleware(['auth.external'])->group(function () {
    Route::get('/service-contracts/{public_id}/invoice-info', [ServiceContractController::class, 'invoiceInfo']);
});
