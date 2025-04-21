<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ServiceContract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceContract>
 */
class ServiceContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ServiceContract>
     */
    protected $model = ServiceContract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_contract_code' => ServiceContract::generateNewServiceContractId(),
            'company_id' => null,
            'department_name_en' => $this->faker->randomElement([
                'Sales',
                'Marketing',
                'Development',
                'HR',
                'Finance',
            ]),
            'service_code' => 'SV-00003',
            'service_plan_code' => 'SP-000001',
            'service_usage_status_type' => 'service_usage_status',
            'service_usage_status_code' => 'Under DD',
            'service_contract_status_type' => 'service_contract_status',
            'service_contract_status_code' => 'Not Requested',
            'payment_cycle_type' => 'payment_cycle',
            'payment_cycle_code' => 'Monthly',
            'payment_method_type' => 'payment_method',
            'payment_method_code' => 'Card',
            'service_application_date' => $this->faker->date(),
            'service_start_date' => null,
            'service_end_date' => null,
            'service_contract_url' => null,
            'responsible_user_id' => null,
            'contract_manager_user_id' => null,
            'created_by' => null,
            'created_at' => now(),
            'updated_by' => null,
            'updated_at' => now(),
        ];
    }
}
