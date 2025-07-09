<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Users\MeResource;
use App\UseCases\Users\MeAction;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * ログインユーザの情報を取得する
     *
     * @param \Illuminate\Http\Request     $request
     * @param \App\UseCases\Users\MeAction $action
     *
     * @return \App\Http\Resources\Users\MeResource
     */
    public function me(
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
