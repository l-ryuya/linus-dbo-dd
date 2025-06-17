<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\CustomersController;

Route::prefix('tenant')->middleware(['auth'])->group(function () {
    Route::get('/customers', [CustomersController::class, 'index']);
});
