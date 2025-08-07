<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Test\CompaniesSeeder;
use Database\Seeders\Test\CompanyNameTranslationsSeeder;
use Database\Seeders\Test\ContractWidgetSettingsSeeder;
use Database\Seeders\Test\CustomersSeeder;
use Database\Seeders\Test\ServiceContractsSeeder;
use Database\Seeders\Test\UserOptionsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompaniesSeeder::class,
            CompanyNameTranslationsSeeder::class,
            CustomersSeeder::class,
            UserOptionsSeeder::class,
            ServiceContractsSeeder::class,
            ContractWidgetSettingsSeeder::class,
        ]);
    }
}
