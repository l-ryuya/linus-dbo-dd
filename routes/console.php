<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Log::channel('stdout')->info("task is running");
})->hourly();
