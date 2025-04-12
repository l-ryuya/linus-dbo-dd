<?php
declare(strict_types = 1);

namespace App\UseCases\Users;

use App\Models\User;

class MeAction
{
    /**
     * ユーザー情報を取得する
     *
     * @param int $user_id
     *
     * @return \App\Models\User
     */
    public function __invoke(
        int $user_id,
    ): User {
        return User::select([
            'user_code',
            'last_name_en',
            'middle_name_en',
            'first_name_en',
            'email',
            'roles',
        ])
        ->where('user_id', $user_id)
        ->where('user_status_type', 'user_status')
        ->where('user_status', 'Active')
        ->firstOrFail();
    }
}
