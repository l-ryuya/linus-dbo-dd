<?php

declare(strict_types=1);


// 無効化
Route::get('/sanctum/csrf-cookie', fn() => abort(404));
