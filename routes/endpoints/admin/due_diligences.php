<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DueDiligencesController;

Route::prefix('admin')->middleware(['auth:sanctum', 'scope:admin,service_manager'])->group(function () {
    Route::get('/due-diligences/{dd_code}', [DueDiligencesController::class, 'show']);
    Route::get('/due-diligences/{dd_code}/result-summary', [DueDiligencesController::class, 'resultSummary']);
});
