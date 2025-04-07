<?php

use App\Http\Controllers\NoAuthentication\CountryRegionController;
use App\Http\Controllers\NoAuthentication\MiscDataController;
use App\Http\Controllers\NoAuthentication\ServicePlanController;
use App\Http\Controllers\NoAuthentication\ServiceSignupController;

Route::get('/country-regions', [CountryRegionController::class, 'index']);
Route::get('/misc-data', [MiscDataController::class, 'index']);
Route::get('/service-plans', [ServicePlanController::class, 'index']);

Route::post('/service-signup', [ServiceSignupController::class, 'store']);
