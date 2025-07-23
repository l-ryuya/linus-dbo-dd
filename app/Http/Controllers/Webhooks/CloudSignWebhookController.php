<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\UseCases\Webhooks\CloudSignWebhookAction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CloudSignWebhookController extends Controller
{
    /**
     * Handle the incoming webhook request from CloudSign.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \App\UseCases\Webhooks\CloudSignWebhookAction $action
     *
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function handle(
        Request $request,
        CloudSignWebhookAction $action,
    ): Response {
        $action($request->json()->all());

        return response(null, 204);
    }
}
