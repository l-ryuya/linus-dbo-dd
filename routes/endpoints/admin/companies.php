<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\CompaniesController;

Route::prefix('admin')->middleware(['auth', 'scope:admin,service_manager'])->group(function () {
    Route::get('/companies', [CompaniesController::class, 'index']);
});
