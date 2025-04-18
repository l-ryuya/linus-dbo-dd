<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Log::channel('stdout')->info("task is running");
})->hourly();

Schedule::command('sanctum:prune-expired')->daily();
