<?php

declare(strict_types=1);

namespace App\Services\Role;

use App\Models\UserOption;

/**
 * 本システム内でのテナントユーザの権限を判定するサービス
 *
 * プラットフォーム管理者は全ての権限を持つため、nullを返す
 * nullによりwhere句の条件が無くなるため、全てのテナントを対象にする
 */
class TenantUserRoleService
{
    /**
     * @see https://www.php.net/manual/ja/language.oop5.decon.php#language.oop5.decon.constructor.promotion
     */
    public function __construct(
        private readonly UserOption $userOption,
    ) {}

    public function getTenantId(): ?int
    {
        if ($this->userOption->isAdmin()) {
            return null;
        }

        return $this->userOption->tenant_id;
    }

    public function getServiceId(): ?int
    {
        if ($this->userOption->isAdmin()) {
            return null;
        }

        return $this->userOption->service_id;
    }
}
