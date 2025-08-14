<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant\DdCase;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Illuminate\Support\Collection<int, object> $steps
 * @property object $ddStep
 * @property object $ddCase
 * @property object $targetCompany
 * @property \Illuminate\Support\Collection<int, object> $directShareholders
 * @property \Illuminate\Support\Collection<int, object> $executives
 */
class SummaryResource extends JsonResource
{
    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     *
     * @return array{
     *     ddStep: array{
     *         current: array{
     *             ddStep: string|null,
     *             ddStepCode: string|null,
     *             stepComment: string|null,
     *             stepCompletedAt: string|null,
     *             stepUserName: string|null
     *         },
     *         steps: \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *     },
     *     ddCase: array{
     *         ddCasePublicId: string|null,
     *         tenantName: string|null,
     *         ddCaseNo: string|null,
     *         startedAt: string|null,
     *         endedAt: string|null,
     *         caseUserName: string|null,
     *         currentDdStep: string|null,
     *         currentDdStepCode: string|null,
     *         currentDdStatus: string|null,
     *         currentDdStatusCode: string|null,
     *         overallResult: string|null,
     *         industryCheckRegResult: string|null,
     *         industryCheckWebResult: string|null,
     *         customerRiskLevel: string|null,
     *         asfCheckResult: string|null,
     *         repCheckResult: string|null,
     *         lastProcessUserName: string|null,
     *         lastProcessDatetime: string|null
     *     },
     *     targetCompany: array{
     *         ddRelationPublicId: string|null,
     *         companyName: string|null,
     *         industryCheckReg: string|null,
     *         industryCheckWeb: string|null,
     *         customerRiskLevel: string|null,
     *         asfCheckResult: string|null,
     *         repCheckResult: string|null,
     *         exchangeName: string|null,
     *         securitiesCode: string|null
     *     },
     *     executives: \Illuminate\Http\Resources\Json\AnonymousResourceCollection,
     *     directShareholders: \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Auth\GenericUser $user */
        $user = $request->user();
        $userTimezone = $user->getUserOption()->timeZone->tz_name;

        return [
            'ddStep' => [
                'current' => [
                    'ddStep' => $this->ddCase->current_dd_step,
                    'ddStepCode' => $this->ddCase->current_dd_step_code,
                    'stepComment' => $this->ddStep->step_comment,
                    'stepCompletedAt' => $this->ddStep->step_completed_at?->setTimezone($userTimezone)->format('Y-m-d H:i:s'),
                    'stepUserName' => $this->ddStep->step_user_name,
                ],
                'steps' => StepResource::collection($this->steps),
            ],
            'ddCase' => [
                'ddCasePublicId' => $this->ddCase->public_id,
                'tenantName' => $this->ddCase->tenant_name,
                'ddCaseNo' => $this->ddCase->dd_case_no,
                'startedAt' => $this->ddCase->started_at?->setTimezone($userTimezone)->format('Y-m-d H:i:s'),
                'endedAt' => $this->ddCase->ended_at?->setTimezone($userTimezone)->format('Y-m-d H:i:s'),
                'caseUserName' => $this->ddCase->case_user_name,
                'currentDdStep' => $this->ddCase->current_dd_step,
                'currentDdStepCode' => $this->ddCase->current_dd_step_code,
                'currentDdStatus' => $this->ddCase->current_dd_status,
                'currentDdStatusCode' => $this->ddCase->current_dd_status_code,
                'overallResult' => $this->ddCase->overall_result,
                'industryCheckRegResult' => $this->ddCase->industry_check_reg_result,
                'industryCheckWebResult' => $this->ddCase->industry_check_web_result,
                'customerRiskLevel' => $this->ddCase->customer_risk_level,
                'asfCheckResult' => $this->ddCase->asf_check_result,
                'repCheckResult' => $this->ddCase->rep_check_result,
                'lastProcessUserName' => $this->ddCase->last_process_user_name,
                'lastProcessDatetime' => $this->ddCase->last_process_datetime?->setTimezone($userTimezone)->format('Y-m-d H:i:s'),
            ],
            'targetCompany' => [
                'ddRelationPublicId' => $this->targetCompany->dd_relation_public_id,
                'companyName' => $this->targetCompany->company_name,
                'industryCheckReg' => $this->targetCompany->industry_check_reg ?? null,
                'industryCheckWeb' => $this->targetCompany->industry_check_web ?? null,
                'customerRiskLevel' => $this->targetCompany->customer_risk_level ?? null,
                'asfCheckResult' => $this->targetCompany->asf_check ?? null,
                'repCheckResult' => $this->targetCompany->rep_check ?? null,
                'exchangeName' => $this->targetCompany->exchange_name ?? null,
                'securitiesCode' => $this->targetCompany->securities_code ?? null,
            ],
            'executives' => ExecutivesResource::collection($this->executives),
            'directShareholders' => DirectShareholderResource::collection($this->directShareholders),
        ];
    }
}
