<?php

//Route::middleware(['auth:sanctum', 'scope:admin'])->get('/test', function () {
//    echo 'Hello, World!';
//});

use App\Http\Controllers\NoAuthentication\MiscDataController;

Route::get('/misc-data', [MiscDataController::class, 'index']);
