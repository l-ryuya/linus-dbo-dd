<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$path = __DIR__ . DIRECTORY_SEPARATOR . 'endpoints' . DIRECTORY_SEPARATOR;

require $path . 'roots.php';

Route::middleware(['auth'])->group(function () {
    // テストメール送信ルート
    Route::post('/test/send-mail', [App\Http\Controllers\Test\MailController::class, 'sendMail']);
    // ENV変数出力ルート
    Route::get('/test/show-env', [App\Http\Controllers\Test\EnvController::class, 'showEnvVariables']);
});
