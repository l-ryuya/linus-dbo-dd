<?php

declare(strict_types=1);

namespace Tests;

use App\Auth\GenericUser;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * テナント管理者ユーザーを作成する
     *
     * @return \App\Auth\GenericUser
     */
    protected function createTenantManageUser(): GenericUser
    {
        $data = [
            'sub' => 'USR00000002',
            'aud' => 'PMX',
            'exp' => 'none',
            'iss' => 'M5',
            'address' => 'ORG00000010',
            'email' => 'ds_admin@dentsusoken.com',
            'token' => 'test_token',
        ];

        return new GenericUser($data);
    }

    /**
     * サービス管理者ユーザーを作成する
     *
     * @return \App\Auth\GenericUser
     */
    protected function createServiceManageUser(): GenericUser
    {
        $data = [
            'sub' => 'USR00000004',
            'aud' => 'PMX',
            'exp' => 'none',
            'iss' => 'M5',
            'address' => 'ORG00000015',
            'email' => 'securate_admin@dentsusoken.com',
            'token' => 'test_token',
        ];

        return new GenericUser($data);
    }
}
