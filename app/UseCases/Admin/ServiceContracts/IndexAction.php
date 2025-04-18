<?php

declare(strict_types=1);

namespace App\UseCases\Admin\ServiceContracts;

use App\Models\Company;
use App\Models\ServicePlanTranslation;
use App\Models\ServiceTranslation;
use Illuminate\Support\Carbon;

class IndexAction
{
    /**
     * 管理者向けのサービス契約一覧を取得する
     *
     * @param string                          $languageCode 言語コード（ISO639-1）
     * @param string|null                     $companyName
     * @param string|null                     $companyStatusCode
     * @param \Illuminate\Support\Carbon|null $serviceSignupStartDate
     * @param \Illuminate\Support\Carbon|null $serviceSignupEndDate
     * @param int                             $displayedNumber 表示件数
     * @param int                             $page ページ番号
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, Company>
     */
    public function __invoke(
        string $languageCode,
        ?string $companyName,
        ?string $companyStatusCode,
        ?Carbon $serviceSignupStartDate,
        ?Carbon $serviceSignupEndDate,
        int $displayedNumber,
        int $page,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        $paginator = Company::select([
            'companies.company_id',
            'companies.company_code',
            'companies.company_name_en',
            'companies.company_status_type',
            'companies.company_status_code',
            'companies.created_at',
            'due_diligences.final_dd_completed_date',
        ])
        ->leftJoin('due_diligences', function ($join) {
            $join->on('companies.latest_dd_id', '=', 'due_diligences.dd_id')
                ->where('due_diligences.dd_status', 'Business Start/Continue');
        })
        ->when($companyName, function ($query) use ($companyName) {
            $query->where('companies.company_name_en', 'LIKE', "%{$companyName}%");
        })
        ->when($companyStatusCode, function ($query) use ($companyStatusCode) {
            $query->where('companies.company_status_type', 'company_status')
                ->where('companies.company_status_code', $companyStatusCode);
        })
        ->when($serviceSignupStartDate, function ($query) use ($serviceSignupStartDate) {
            $query->where('companies.created_at', '>=', $serviceSignupStartDate);
        })
        ->when($serviceSignupEndDate, function ($query) use ($serviceSignupEndDate) {
            $query->where('companies.created_at', '<=', $serviceSignupEndDate);
        })
        ->with('serviceContracts')
        ->orderBy('companies.company_id')
        ->paginate(perPage: $displayedNumber, page: $page);

        $services = ServiceTranslation::withLanguage($languageCode)->get();
        $servicePlans = ServicePlanTranslation::withLanguage($languageCode)->get();

        $paginator->map(function ($item) use ($services, $servicePlans) {
            $item->setAttribute('service_contracts', collect());
            foreach ($item->serviceContracts as $contract) {
                $item->getAttribute('service_contracts')->push((object) [
                    'service_code' => $contract->service_code,
                    'service_plan_code' => $contract->service_plan_code,
                    'service_contract_code' => $contract->service_contract_code,
                    'service_name' => $services->firstWhere('service_code', $contract->service_code)->service_name,
                    'service_plan_name' => $servicePlans->firstWhere('service_plan_code', $contract->service_plan_code)->service_plan_name,
                ]);
            }

            return $item;
        });

        return $paginator;
    }
}
