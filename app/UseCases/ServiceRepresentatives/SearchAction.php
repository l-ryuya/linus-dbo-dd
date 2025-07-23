<?php

declare(strict_types=1);

namespace App\UseCases\ServiceRepresentatives;

use App\Models\UserOption;

class SearchAction
{
    /**
     * サービス担当者を取得する
     *
     * @param int|null    $tenantId
     * @param int|null    $serviceId
     * @param string|null $name
     *
     * @return \Illuminate\Support\Collection<int, UserOption>
     */
    public function __invoke(
        ?int $tenantId,
        ?int $serviceId,
        ?string $name,
    ): \Illuminate\Support\Collection {
        return UserOption::select([
            'public_id',
            'user_name',
        ])
        ->when($tenantId, function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })
        ->when($serviceId, function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })
        ->when($name, function ($query) use ($name) {
            $query->where('user_name', 'ILIKE', "%{$name}%")
                ->orWhere('user_name_en', 'ILIKE', "%{$name}%");
        })
        ->get();
    }
}
