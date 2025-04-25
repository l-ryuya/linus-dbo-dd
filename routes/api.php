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

require $path . 'sanctum.php';
require $path . 'roots.php';
require $path . 'users.php';

require $path . 'admin' . DIRECTORY_SEPARATOR . 'companies.php';
require $path . 'admin' . DIRECTORY_SEPARATOR . 'due_diligences.php';
require $path . 'admin' . DIRECTORY_SEPARATOR . 'service_contracts.php';

Route::middleware(['auth:sanctum', 'scope:admin'])->group(function () {
    // テストメール送信ルート
    Route::post('/test/send-mail', [App\Http\Controllers\Test\MailController::class, 'sendMail']);
});
