<?php

declare(strict_types=1);

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;

class LogTestController extends Controller
{
    public function putStdout(): \Illuminate\Http\Response
    {
        \Log::channel('stdout')->info("Stdout: log test message");

        return response('Successfully output log to stdout.', 200);
    }

    public function putStderr(): \Illuminate\Http\Response
    {
        \Log::channel('stderr')->info("Stderr: log test message");

        return response('Successfully output log to stderr.', 200);
    }
}
