<?php

declare(strict_types=1);

use App\Http\Controllers\NoAuthentication\CountryRegionController;
use App\Http\Controllers\NoAuthentication\MiscDataController;

Route::get('/country-regions', [CountryRegionController::class, 'index']);
Route::get('/misc-data', [MiscDataController::class, 'index']);
