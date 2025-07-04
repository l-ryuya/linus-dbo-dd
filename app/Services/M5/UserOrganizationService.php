<?php

declare(strict_types=1);

namespace App\Services\M5;

use App\Models\Tenant;
use Illuminate\Support\Facades\Http;

class UserOrganizationService
{
    /**
     * ログインしているM5ユーザーの組織情報を取得し、最も高いレベルの組織を返す
     *
     * @param string $token
     * @param string $sysUserCode
     *
     * @return array<string, mixed>|null
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function getLowestLevelOrganization(string $token, string $sysUserCode): ?array
    {
        $response = Http::withToken($token)->get(
            config('m5.user.user_organization_url') . "/?sysUserCode={$sysUserCode}",
        );
        if (!$response->ok()) {
            return null;
        }

        $result = $response->json();
        if (empty($result)) {
            return null;
        }

        /** @var \Illuminate\Support\Collection<int, array<string, mixed>> $result */
        $organizations = collect($result);
        $lowestLevelOrganization = $organizations->sortByDesc('organizationLevelId')->first();

        return $lowestLevelOrganization ?? null;
    }

    /**
     * ログインしているM5ユーザーの組織情報から、紐づいたテナント情報を取得する
     *
     * @param string $token
     * @param string $sysUserCode
     *
     * @return \App\Models\Tenant|null
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function getTenantByOrganizationCode(string $token, string $sysUserCode): ?Tenant
    {
        $organization = $this->getLowestLevelOrganization($token, $sysUserCode);
        if (empty($organization['sysOrganizationCode'])) {
            abort(500, 'Invalid organization code');
        }

        $tenant = Tenant::where('sys_organization_code', $organization['sysOrganizationCode'])->first();
        if (empty($tenant)) {
            abort(500, 'Tenant not found for organization code');
        }

        return $tenant;
    }
}
