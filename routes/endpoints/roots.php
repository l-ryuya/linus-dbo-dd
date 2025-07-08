<?php

declare(strict_types=1);

use App\Http\Controllers\MasterData\CountryRegionController;
use App\Http\Controllers\MasterData\MiscDataController;
use App\Http\Controllers\MasterData\ServicePlanController;

Route::middleware(['auth'])->group(function () {
    Route::get('/country-regions', [CountryRegionController::class, 'index']);
    Route::get('/misc-data', [MiscDataController::class, 'index']);
});

Route::middleware(['auth', 'roles:admin,tenant'])->group(function () {
    Route::get('/service-plans', [ServicePlanController::class, 'index']);
});
