<?php

declare(strict_types=1);

namespace App\Services\AiDd\PreDd;

use App\Enums\Dd\DdEntityTypeCode;
use App\Enums\Dd\DdRelationCode;
use App\Enums\Dd\DdStatusCode;
use App\Enums\Dd\DdStepCode;
use App\Models\Customer;
use App\Models\DdCase;
use App\Models\DdCompany;
use App\Models\DdEntity;
use App\Models\DdRelation;
use App\Models\DdStep;
use Illuminate\Support\Facades\DB;

class Step0Service
{
    /**
     * Step0 AI PRE DD 初期データを登録する
     *
     * @param int $tenantId
     * @param int $customerId
     * @param int $caseUserOptionId
     *
     * @return string
     * @throws \Throwable
     */
    public function createInitialData(
        int $tenantId,
        int $customerId,
        int $caseUserOptionId,
    ): string {
        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($customerId);

            $ddCase = $this->createCase($tenantId, $customerId, $caseUserOptionId);
            $this->createStep($tenantId, $ddCase);
            $ddEntity = $this->createEntity($tenantId);
            $this->createCompany($tenantId, $customer, $ddEntity);
            $this->createRelation($tenantId, $ddCase, $ddEntity);

            DB::commit();

            return $ddCase->dd_case_no;

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    private function createCase(
        int $tenantId,
        int $customerId,
        int $caseUserOptionId,
    ): DdCase {
        $ddCase = new DdCase();
        $ddCase->tenant_id = $tenantId;
        $ddCase->customer_id = $customerId;
        $ddCase->case_user_option_id = $caseUserOptionId;
        $ddCase->current_dd_step_type = 'dd_step';
        $ddCase->current_dd_step_code = DdStepCode::PreDdAi->value;
        $ddCase->current_dd_status_type = 'dd_status';
        $ddCase->current_dd_status_code = DdStatusCode::PreDdAiStarted->value;
        $ddCase->started_at = now();
        $ddCase->save();
        $ddCase->refresh();

        return $ddCase;
    }

    private function createStep(
        int $tenantId,
        DdCase $ddCase,
    ): void {
        $ddStep = new DdStep();
        $ddStep->tenant_id = $tenantId;
        $ddStep->dd_case_id = $ddCase->dd_case_id;
        $ddStep->dd_step_type = 'dd_step';
        $ddStep->dd_step_code = DdStepCode::PreDdAi->value;
        $ddStep->save();
    }

    private function createEntity(
        int $tenantId,
    ): DdEntity {
        $ddEntity = new DdEntity();
        $ddEntity->tenant_id = $tenantId;
        $ddEntity->dd_entity_type_type = 'dd_entity_type';
        $ddEntity->dd_entity_type_code = DdEntityTypeCode::Company->value;
        $ddEntity->save();

        return $ddEntity;
    }

    private function createCompany(
        int $tenantId,
        Customer $customer,
        DdEntity $ddEntity,
    ): void {
        $ddCompany = new DdCompany();
        $ddCompany->tenant_id = $tenantId;
        $ddCompany->dd_entity_id = $ddEntity->dd_entity_id;
        $ddCompany->company_name = $customer
            ->company
            ->nameTranslation($customer->company->default_language_code)
            ->company_legal_name;
        $ddCompany->save();
    }

    private function createRelation(
        int $tenantId,
        DdCase $ddCase,
        DdEntity $ddEntity,
    ): void {
        $ddRelation = new DdRelation();
        $ddRelation->tenant_id = $tenantId;
        $ddRelation->dd_case_id = $ddCase->dd_case_id;
        $ddRelation->dd_entity_id = $ddEntity->dd_entity_id;
        $ddRelation->dd_relation_type = 'dd_relation';
        $ddRelation->dd_relation_code = DdRelationCode::CounterpartyEntity->value;
        $ddRelation->dd_relation_status = 'CREATE';
        $ddRelation->save();
    }
}
