<?php

//Route::middleware(['auth:sanctum', 'scope:admin'])->get('/test', function () {
//    echo 'Hello, World!';
//});

use App\Http\Controllers\NoAuthentication\CountryRegionController;
use App\Http\Controllers\NoAuthentication\MiscDataController;
use App\Http\Controllers\NoAuthentication\ServicePlanController;

Route::get('/country-regions', [CountryRegionController::class, 'index']);
Route::get('/misc-data', [MiscDataController::class, 'index']);
Route::get('/service-plans', [ServicePlanController::class, 'index']);
