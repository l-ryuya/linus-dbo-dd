<?php

declare(strict_types=1);

namespace App\Services\Role;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\UserOption;
use stdClass;

/**
 * 本システム内でのユーザの権限を判定するサービス
 */
readonly class UserRoleService
{
    /**
     * @see https://www.php.net/manual/ja/language.oop5.decon.php#language.oop5.decon.constructor.promotion
     */
    public function __construct(
        private UserOption $userOption,
    ) {}

    public function getRole(): object
    {
        $role = new StdClass();
        if ($this->userOption->isAdmin()) {
            $role->name = 'admin';
            $company = Company::where('company_id', $this->userOption->company_id)->first();
            $role->companyName = $company->nameTranslation($this->userOption->language_code)->company_legal_name;
            $role->serviceName = null;
        } elseif ($this->userOption->isTenant()) {
            $role->name = 'tenant';
            $tenant = Tenant::where('tenant_id', $this->userOption->tenant_id)->first();
            $role->companyName = $tenant->tenant_name;
            $role->serviceName = null;
            if (!empty($this->userOption->service_id)) {
                $service = Service::where('service_id', $this->userOption->service_id)->first();
                $role->serviceName = $service->nameTranslation($this->userOption->language_code)->service_name;
            }
        } elseif ($this->userOption->isCustomer()) {
            $role->name = 'customer';
            $customer = Customer::where('customer_id', $this->userOption->customer_id)->firstOrFail();
            $company = Company::where('company_id', $customer->company_id)->first();
            $role->companyName = $company->nameTranslation($this->userOption->language_code)->company_legal_name;
            $role->serviceName = null;
        }

        return $role;
    }
}
