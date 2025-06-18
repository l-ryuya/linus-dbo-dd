<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Http;

/**
 * @property string $id
 * @property string $token
 * @property string $sub
 */
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
        $this->attributes['id'] = $attributes['sub'] ?? null;
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function tokenCan(string $scope): bool
    {
        $response = Http::withToken($this->attributes['token'])->get(
            config('m5.auth.token_functions_verify_url') . '/' . $scope,
        );
        if ($response->ok()) {
            return true;
        }

        return false;
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
