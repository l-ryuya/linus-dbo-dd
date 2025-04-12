<?php
declare(strict_types = 1);

namespace App\Http\Controllers\NoAuthentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceSignup\StoreRequest;
use App\Http\Resources\ServiceSignup\StoreResource;
use App\UseCases\ServiceSignup\StoreAction;

class ServiceSignupController extends Controller
{
    /**
     * サービス申込
     *
     * @param \App\Http\Requests\ServiceSignup\StoreRequest $request
     * @param \App\UseCases\ServiceSignup\StoreAction       $action
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(
        StoreRequest $request,
        StoreAction $action,
    ) {
        return (new StoreResource(
            $action($request->toStoreInput()),
        ))
        ->response()
        ->setStatusCode(201);
    }
}
