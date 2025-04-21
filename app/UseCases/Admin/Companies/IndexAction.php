<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Companies;

use App\Models\Company;
use Illuminate\Support\Carbon;

class IndexAction
{
    /**
     * 管理者向けの法人一覧を取得する
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
        return Company::select([
            'companies.company_id',
            'companies.company_code',
            'companies.company_name_en',
            'companies.company_status_type',
            'companies.company_status_code',
            'companies.created_at',
        ])
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
        ->orderBy('companies.company_id')
        ->paginate(perPage: $displayedNumber, page: $page);
    }
}
