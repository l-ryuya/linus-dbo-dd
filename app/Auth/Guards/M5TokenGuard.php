<?php

declare(strict_types=1);

namespace App\Auth\Guards;

use App\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class M5TokenGuard implements Guard
{
    protected Request $request;

    protected ?Authenticatable $user = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function guest(): bool
    {
        return !$this->check();
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->request->bearerToken();
        if (!$token) {
            return null;
        }

        $response = Http::post(
            config('m5.auth.token_validation_url'),
            [
                'token' => $token,
            ],
        );
        if ($response->ok()) {
            $data = $response->json();
            $data['token'] = $token;

            $this->user = new GenericUser($data);
            return $this->user;
        }

        return null;
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function id(): ?\App\Auth\GenericUser
    {
        /** @var \App\Auth\GenericUser|null $user */
        $user = $this->user();
        return $user?->getAuthIdentifier();
    }

    /**
     * @param array<string, mixed> $credentials
     */
    public function validate(array $credentials = []): bool
    {
        // このガードでは通常のバリデーションは不要
        return false;
    }

    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function hasUser(): bool
    {
        return $this->user !== null;
    }
}
