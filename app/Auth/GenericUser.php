<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class GenericUser implements Authenticatable
{
    /** @var array<string, mixed> */
    protected array $attributes;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function tokenCan(string $scope): bool
    {
        // 権限情報はM5のAPIから取得して検証する必要がある

        return true;

//        $scopes = $this->attributes['scopes'] ?? [];
//
//        return in_array($scope, $scopes);
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->attributes['id'] ?? null;
    }

    public function getAuthPassword(): ?string
    {
        return null;
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void {}

    public function getRememberTokenName(): ?string
    {
        return null;
    }

    public function getAuthPasswordName(): ?string
    {
        return null;
    }

    // 任意の属性アクセス
    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }
}
