<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\DdCase;

use App\Enums\Dd\DdRelationCode;
use App\Models\DdCase;

class IndexAction
{
    /**
     * デューデリジェンスケース一覧取得
     *
     * @param string $languageCode
     * @param int|null $tenantId
     * @param string|null $tenantPublicId
     * @param string|null $companyName
     * @param string|null $caseNo
     * @param string|null $currentDdStepCode
     * @param string|null $overallResult
     * @param string|null $customerRiskLevel
     * @param string|null $startedAtFrom
     * @param string|null $startedAtTo
     * @param string|null $endedAtFrom
     * @param string|null $endedAtTo
     * @param int $displayedNumber
     * @param int $page
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, DdCase>
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        ?string $tenantPublicId,
        ?string $companyName,
        ?string $caseNo,
        ?string $currentDdStepCode,
        ?string $overallResult,
        ?string $customerRiskLevel,
        ?string $startedAtFrom,
        ?string $startedAtTo,
        ?string $endedAtFrom,
        ?string $endedAtTo,
        int $displayedNumber,
        int $page,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        return DdCase::select([
            'dd_cases.public_id',
            'tenants.tenant_name',
            'dd_companies.company_name',
            'dd_cases.dd_case_no',
            'selection_item_translations.selection_item_name AS current_dd_step',
            'dd_cases.overall_result',
            'dd_cases.customer_risk_level',
            'dd_cases.started_at',
            'dd_cases.ended_at',
        ])
        ->join('tenants', 'tenants.tenant_id', '=', 'dd_cases.tenant_id')
        ->join('dd_relations', function ($join) {
            $join->on('dd_cases.tenant_id', '=', 'dd_relations.tenant_id')
                ->whereColumn('dd_cases.dd_case_id', 'dd_relations.dd_case_id')
                ->where('dd_relations.dd_relation_code', DdRelationCode::CounterpartyEntity->value);
        })
        ->join('dd_companies', 'dd_companies.dd_entity_id', '=', 'dd_relations.dd_entity_id')
        ->join('selection_item_translations', function ($join) use ($languageCode) {
            $join->on('dd_cases.current_dd_step_code', 'selection_item_translations.selection_item_code')
                ->where('selection_item_translations.selection_item_type', 'dd_step')
                ->where('selection_item_translations.language_code', $languageCode);
        })
        ->when($tenantId, function ($query) use ($tenantId) {
            $query->where('dd_cases.tenant_id', $tenantId);
        })
        ->when($tenantPublicId, function ($query) use ($tenantPublicId) {
            $query->where('tenants.public_id', $tenantPublicId);
        })
        ->when($companyName, function ($query) use ($companyName) {
            $query->where('dd_companies.company_name', 'ILIKE', "%{$companyName}%");
        })
        ->when($caseNo, function ($query) use ($caseNo) {
            $query->where('dd_cases.dd_case_no', $caseNo);
        })
        ->when($currentDdStepCode, function ($query) use ($currentDdStepCode) {
            $query->where('dd_cases.current_dd_step_type', 'dd_step')
                ->where('dd_cases.current_dd_step_code', $currentDdStepCode);
        })
        ->when($overallResult, function ($query) use ($overallResult) {
            $query->where('dd_cases.overall_result', $overallResult);
        })
        ->when($customerRiskLevel, function ($query) use ($customerRiskLevel) {
            $query->where('dd_cases.customer_risk_level', $customerRiskLevel);
        })
        ->when($startedAtFrom, function ($query) use ($startedAtFrom) {
            $query->whereDate('dd_cases.started_at', '>=', $startedAtFrom);
        })
        ->when($startedAtTo, function ($query) use ($startedAtTo) {
            $query->whereDate('dd_cases.started_at', '<=', $startedAtTo);
        })
        ->when($endedAtFrom, function ($query) use ($endedAtFrom) {
            $query->whereDate('dd_cases.ended_at', '>=', $endedAtFrom);
        })
        ->when($endedAtTo, function ($query) use ($endedAtTo) {
            $query->whereDate('dd_cases.ended_at', '<=', $endedAtTo);
        })
        ->orderBy('dd_cases.dd_case_id', 'DESC')
        ->paginate(perPage: $displayedNumber, page: $page);
    }
}
