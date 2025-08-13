<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::useCache('database');

Schedule::call(function () {
    Log::channel('stdout')->info("schedule:work is running");
})
    ->name('log-schedule-work')
    ->everyFifteenMinutes()
    ->onOneServer()
    ->withoutOverlapping();
