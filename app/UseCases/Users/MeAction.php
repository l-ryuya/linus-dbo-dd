<?php

declare(strict_types=1);

namespace App\UseCases\Users;

use App\Models\UserOption;
use App\Services\Role\UserRoleService;

class MeAction
{
    /**
     * ユーザー情報を取得する
     *
     * @param string $sysUserCode
     *
     * @return \App\Models\UserOption
     */
    public function __invoke(
        string $sysUserCode,
    ): UserOption {
        $userOption = UserOption::select([
            'user_options.public_id',
            'user_options.company_id',
            'user_options.tenant_id',
            'user_options.customer_id',
            'user_options.service_id',
            'user_options.platform_user',
            'user_options.user_name',
            'user_options.user_mail',
            'user_options.user_icon_url',
            'user_options.country_code_alpha3',
            'user_options.language_code',
            'user_options.time_zone_id',
            'user_options.date_format',
            'user_options.phone_number',
            'country_regions_translations.country_region_name',
            'selection_item_translations.selection_item_name AS language_name',
            'time_zones.display_label AS time_zone_name',
        ])
        ->join('selection_item_translations', function ($join) {
            $join->on('user_options.language_code', '=', 'selection_item_translations.selection_item_code')
                ->where('selection_item_translations.selection_item_type', 'language_code')
                ->whereColumn('selection_item_translations.language_code', 'user_options.language_code');
        })
        ->join('country_regions_translations', function ($join) {
            $join->on('user_options.country_code_alpha3', '=', 'country_regions_translations.country_code_alpha3')
                ->whereColumn('country_regions_translations.language_code', 'user_options.language_code');
        })
        ->join('time_zones', 'user_options.time_zone_id', '=', 'time_zones.time_zone_id')
        ->where('user_options.sys_user_code', $sysUserCode)
        ->firstOrFail();

        $userRoleService = new UserRoleService($userOption);
        $userRole = $userRoleService->getRole();

        $userOption->setAttribute('role_name', $userRole->name);
        $userOption->setAttribute('company_name', $userRole->companyName);
        $userOption->setAttribute('service_name', $userRole->serviceName);

        return $userOption;
    }
}
