<?php

use App\Http\Controllers\Auth\TokenController;

// 無効化
Route::get('/sanctum/csrf-cookie', fn () => abort(404));
