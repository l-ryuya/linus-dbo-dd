<?php
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
