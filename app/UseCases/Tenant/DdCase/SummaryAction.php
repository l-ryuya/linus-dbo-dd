<?php

declare(strict_types=1);

namespace App\UseCases\Tenant\DdCase;

use App\Enums\Dd\DdRelationCode;
use App\Models\DdCase;
use App\Models\DdRelation;
use App\Models\DdStep;
use App\Services\AiDd\DdStepListService;
use App\Services\AiDd\DdStepResultService;
use Illuminate\Support\Collection;

class SummaryAction
{
    /**
     * デューデリジェンスケースのサマリー取得
     *
     * @param string $languageCode
     * @param int|null $tenantId
     * @param string $publicId
     *
     * @return object
     */
    public function __invoke(
        string $languageCode,
        ?int $tenantId,
        string $publicId,
    ): object {
        $ddCase = $this->getDdCase(
            $languageCode,
            $tenantId,
            $publicId,
        );
        $ddStep = $this->getDdStep(
            $ddCase->dd_case_id,
            $ddCase->current_dd_step_type,
            $ddCase->current_dd_step_code,
        );

        $stepList = DdStepListService::getStepList($languageCode);
        $targetCompany = $this->getTargetCompany($ddCase->tenant_id, $ddCase->dd_case_id);
        $executives = $this->getExecutives($ddCase->tenant_id, $ddCase->dd_case_id);
        $shareholders = $this->getShareholders($ddCase->tenant_id, $ddCase->dd_case_id);

        return (object) [
            'steps' => DdStepListService::mergeStepList($ddCase, $stepList),
            'ddStep' => $ddStep,
            'ddCase' => $ddCase,
            'targetCompany' => $targetCompany,
            'executives' => $executives,
            'directShareholders' => $shareholders,
        ];
    }

    /**
     * 取引先 役員情報の取得
     *
     * @param int $tenantId
     * @param int $ddCaseId
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function getExecutives(
        int $tenantId,
        int $ddCaseId,
    ): Collection {
        $ddRelations = DdRelation::select([
            'dd_relations.public_id',
            'dd_relations.dd_case_id',
            'dd_relations.dd_entity_id',
            'dd_individuals.full_name',
            'dd_individuals.position',
        ])
        ->join(
            'dd_individuals',
            'dd_individuals.dd_entity_id',
            '=',
            'dd_relations.dd_entity_id',
        )
        ->where('dd_relations.tenant_id', $tenantId)
        ->where('dd_relations.dd_case_id', $ddCaseId)
        ->Where('dd_relations.dd_relation_code', DdRelationCode::Executive->value)
        ->get();

        $executives = collect([]);
        foreach ($ddRelations as $ddRelation) {
            $latestList = DdStepResultService::fetchLatestCheckStatusList(
                $ddRelation->dd_case_id,
                $ddRelation->dd_entity_id,
            );

            $executive = DdStepResultService::buildLatestCheckStatusObject($latestList);
            $executive->executive_name = $ddRelation->full_name;
            $executive->position = $ddRelation->position;
            $executive->dd_relation_public_id = $ddRelation->public_id;

            $executives->push($executive);
        }

        return $executives;
    }

    /**
     * 取引先 株主情報の取得
     *
     * @param int $tenantId
     * @param int $ddCaseId
     *
     * @return Collection<int, object>
     */
    private function getShareholders(
        int $tenantId,
        int $ddCaseId,
    ): Collection {
        $ddRelations = DdRelation::select([
            'dd_relations.public_id',
            'dd_relations.dd_case_id',
            'dd_relations.dd_entity_id',
            'dd_relations.shareholding_ratio',
            'dd_entities.dd_entity_name AS shareholder_name',
        ])
        ->join(
            'dd_entities',
            'dd_entities.dd_entity_id',
            '=',
            'dd_relations.dd_entity_id',
        )
        ->where('dd_relations.tenant_id', $tenantId)
        ->where('dd_relations.dd_case_id', $ddCaseId)
        ->where(function ($query) {
            $query->where('dd_relations.dd_relation_code', DdRelationCode::DirectShareholder->value)
                ->orWhere('dd_relations.dd_relation_code', DdRelationCode::IndirectShareholder->value);
        })
        ->get();

        $shareholders = collect([]);
        foreach ($ddRelations as $ddRelation) {
            $latestList = DdStepResultService::fetchLatestCheckStatusList(
                $ddRelation->dd_case_id,
                $ddRelation->dd_entity_id,
            );

            $holder = DdStepResultService::buildLatestCheckStatusObject($latestList);
            $holder->shareholder_name = $ddRelation->shareholder_name;
            $holder->shareholding_ratio = $ddRelation->shareholding_ratio;
            $holder->dd_relation_public_id = $ddRelation->public_id;

            $shareholders->push($holder);
        }

        return $shareholders;
    }

    /**
     * 取引対象法人の取得
     *
     * @param int $tenantId
     * @param int $ddCaseId
     *
     * @return object
     */
    private function getTargetCompany(
        int $tenantId,
        int $ddCaseId,
    ): object {
        try {
            $ddRelation = DdRelation::select([
                'dd_relations.public_id',
                'dd_relations.dd_case_id',
                'dd_relations.dd_entity_id',
                'dd_companies.company_name AS company_name',
            ])
            ->join(
                'dd_companies',
                'dd_companies.dd_entity_id',
                '=',
                'dd_relations.dd_entity_id',
            )
            ->where('dd_relations.tenant_id', $tenantId)
            ->where('dd_relations.dd_case_id', $ddCaseId)
            ->where('dd_relations.dd_relation_code', DdRelationCode::CounterpartyEntity->value)
            ->firstOrFail();

            $latestList = DdStepResultService::fetchLatestCheckStatusList(
                $ddRelation->dd_case_id,
                $ddRelation->dd_entity_id,
            );

            $targetCompany = DdStepResultService::buildLatestCheckStatusObject($latestList);
            $targetCompany->company_name = $ddRelation->company_name;
            $targetCompany->dd_relation_public_id = $ddRelation->public_id;

            return $targetCompany;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \RuntimeException($e->getMessage(), 500, $e);
        }
    }

    /**
     * デューデリジェンスステップの取得
     *
     * @param int $ddCaseId
     * @param string $ddStepType
     * @param string $ddStepCode
     *
     * @return DdStep
     */
    private function getDdStep(
        int $ddCaseId,
        string $ddStepType,
        string $ddStepCode,
    ): DdStep {
        return DdStep::select([
            'dd_steps.step_comment',
            'dd_steps.step_completed_at',
            'user_options.user_name AS step_user_name',
        ])
        ->join(
            'user_options',
            'user_options.user_option_id',
            '=',
            'dd_steps.step_user_option_id',
        )
        ->where('dd_steps.dd_case_id', $ddCaseId)
        ->where('dd_steps.dd_step_type', $ddStepType)
        ->where('dd_steps.dd_step_code', $ddStepCode)
        ->firstOrFail();
    }

    private function getDdCase(
        string $languageCode,
        ?int $tenantId,
        string $publicId,
    ): DdCase {
        return DdCase::select([
            'dd_cases.dd_case_id',
            'dd_cases.tenant_id',
            'tenants.tenant_name',
            'dd_cases.public_id',
            'dd_cases.dd_case_no',
            'dd_cases.started_at',
            'dd_cases.ended_at',
            'case_user_option.user_name AS case_user_name',
            'current_dd_step_translation.selection_item_name AS current_dd_step',
            'dd_cases.current_dd_step_type',
            'dd_cases.current_dd_step_code',
            'current_dd_status_translation.selection_item_name AS current_dd_status',
            'dd_cases.current_dd_status_type',
            'dd_cases.current_dd_status_code',
            'dd_cases.overall_result',
            'dd_cases.industry_check_reg_result',
            'dd_cases.industry_check_web_result',
            'dd_cases.customer_risk_level',
            'dd_cases.asf_check_result',
            'dd_cases.rep_check_result',
            'last_process_user_option.user_name AS last_process_user_name',
            'dd_cases.last_process_datetime',
            'dd_cases.step_1_info',
            'dd_cases.step_2_info',
            'dd_cases.step_3_info',
            'dd_cases.step_4_info',
            'dd_cases.step_5_info',
            'dd_cases.step_6_info',
            'dd_cases.step_7_info',
            'dd_cases.step_8_info',
            'dd_cases.step_9_info',
        ])
        ->join('tenants', 'tenants.tenant_id', '=', 'dd_cases.tenant_id')
        ->join(
            'user_options AS case_user_option',
            'case_user_option.user_option_id',
            '=',
            'dd_cases.case_user_option_id',
        )
        ->join(
            'user_options AS last_process_user_option',
            'last_process_user_option.user_option_id',
            '=',
            'dd_cases.last_process_user_option_id',
        )
        ->join(
            'selection_item_translations AS current_dd_step_translation',
            function ($join) use ($languageCode) {
                $join->on(
                    'dd_cases.current_dd_step_code',
                    'current_dd_step_translation.selection_item_code',
                )
                ->where(
                    'current_dd_step_translation.selection_item_type',
                    'dd_step',
                )
                ->where(
                    'current_dd_step_translation.language_code',
                    $languageCode,
                );
            },
        )
        ->join(
            'selection_item_translations AS current_dd_status_translation',
            function ($join) use ($languageCode) {
                $join->on(
                    'dd_cases.current_dd_status_code',
                    'current_dd_status_translation.selection_item_code',
                )
                ->where(
                    'current_dd_status_translation.selection_item_type',
                    'dd_status',
                )
                ->where(
                    'current_dd_status_translation.language_code',
                    $languageCode,
                );
            },
        )
        ->when($tenantId, function ($query) use ($tenantId) {
            $query->where('dd_cases.tenant_id', $tenantId);
        })
        ->where('dd_cases.public_id', $publicId)
        ->firstOrFail();
    }
}
