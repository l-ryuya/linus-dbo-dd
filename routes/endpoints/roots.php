<?php

declare(strict_types=1);

use App\Http\Controllers\MasterData\CountryRegionController;
use App\Http\Controllers\MasterData\MiscDataController;

Route::middleware(['auth'])->group(function () {
    Route::get('/country-regions', [CountryRegionController::class, 'index']);
    Route::get('/misc-data', [MiscDataController::class, 'index']);
});
