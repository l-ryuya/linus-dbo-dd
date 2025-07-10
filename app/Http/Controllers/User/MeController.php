<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\MeResource;
use App\UseCases\Users\MeAction;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * ログインユーザの情報を取得する
     *
     * @param \Illuminate\Http\Request     $request
     * @param \App\UseCases\Users\MeAction $action
     *
     * @return \App\Http\Resources\Users\MeResource
     */
    public function show(
        Request $request,
        MeAction $action,
    ): MeResource {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();

        return new MeResource(
            $action(
                $user->id,
            ),
        );
    }
}
