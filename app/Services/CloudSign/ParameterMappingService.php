<?php

declare(strict_types=1);

namespace App\Services\CloudSign;

use App\Models\Company;
use App\Models\ContractWidgetSetting;
use App\Models\CountryFieldDisplayOrder;
use App\Models\ServiceContract;
use Illuminate\Support\Collection;

class ParameterMappingService
{
    /**
     * 契約書ウィジェット設定から取得したテーブルとカラム名を元に、契約書ウィジェットの値を取得する
     *
     * @param \App\Models\ServiceContract $service_contracts
     * @param string                      $contractLanguage
     *
     * @return \Illuminate\Support\Collection<int, ContractWidgetSetting>
     */
    public function buildToWidget(
        ServiceContract $service_contracts,
        string $contractLanguage,
    ): Collection {
        // 可変変数でテーブル、カラム名から動的に取得するため変数名をテーブル名にしている
        $customers = $service_contracts->customer;
        $companies = $customers->company;
        $company_name_translations = $companies->companyNameTranslations()
            ->where('language_code', $service_contracts->contract_language)
            ->first();
        $services = $service_contracts->service;
        $service_translations = $services->nameTranslation($service_contracts->contract_language);
        $service_plans = $service_contracts->servicePlan;
        $service_plan_translations = $service_plans->nameTranslation($service_contracts->contract_language);

        $contractWidgetSettings = ContractWidgetSetting::where(
            'tenant_id',
            $service_contracts->tenant_id,
        )
        ->where('service_id', $service_contracts->service_id)
        ->where('service_plan_id', $service_contracts->service_plan_id)
        ->where('contract_language', $contractLanguage)
        ->get();

        foreach ($contractWidgetSettings as $widgetSetting) {
            $table = $widgetSetting->widget_source_table;
            $column = $widgetSetting->widget_source_column;
            if ($table === 'companies' && $column === 'address') {
                $widgetSetting->setAttribute('widget_source_value', $this->formatAddressFromTemplate($companies, $contractLanguage));
                continue;
            }

            $widgetSetting->setAttribute('widget_source_value', $$table->$column);
        }

        return $contractWidgetSettings;
    }

    /**
     * 法人住所を住所フォーマットテンプレートに従って整形
     *
     * @param Company $company
     * @param string $contractLanguage
     * @return string
     */
    private function formatAddressFromTemplate(
        Company $company,
        string $contractLanguage,
    ): string {
        if ($contractLanguage === 'jpn') {
            $countryCode = 'JPN';
        } else {
            $countryCode = 'USA';
        }
        // {{street}}, {{city}}, {{state}} {{postal}}{{building}}
        $formattedAddress = CountryFieldDisplayOrder::where('country_code_alpha3', $countryCode)->firstOrFail();

        $addressData = [
            'street' => $company->street,
            'city' => $company->city,
            'state' => $company->state,
            'postal' => $company->postal,
            'building' => $company->building,
        ];

        $replacements = [];
        foreach ($addressData as $key => $value) {
            $replacements["{{{$key}}}"] = $value;
        }

        return str_replace(array_keys($replacements), array_values($replacements), $formattedAddress->address_fields_order);
    }
}
