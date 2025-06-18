<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\base\AddressFormatRulesSeeder;
use Database\Seeders\base\CompaniesSeeder;
use Database\Seeders\base\CompanyNameTranslationsSeeder;
use Database\Seeders\base\CompanyRoleAssignmentsSeeder;
use Database\Seeders\base\CompanyRolesSeeder;
use Database\Seeders\base\CompanyRoleTranslationsSeeder;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CountryRegionsTranslationsSeeder;
use Database\Seeders\base\CustomersSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\SelectionItemTranslationsSeeder;
use Database\Seeders\base\ServiceContractsSeeder;
use Database\Seeders\base\ServicePlansSeeder;
use Database\Seeders\base\ServicePlanTranslationsSeeder;
use Database\Seeders\base\ServicesSeeder;
use Database\Seeders\base\ServiceTranslationsSeeder;
use Database\Seeders\base\TenantsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SelectionItemsSeeder::class,
            SelectionItemTranslationsSeeder::class,
            CountryRegionsSeeder::class,
            CountryRegionsTranslationsSeeder::class,
            AddressFormatRulesSeeder::class,
            TenantsSeeder::class,
            CompaniesSeeder::class,
            CompanyNameTranslationsSeeder::class,
            CompanyRolesSeeder::class,
            CompanyRoleTranslationsSeeder::class,
            CompanyRoleAssignmentsSeeder::class,
            CustomersSeeder::class,
            ServicesSeeder::class,
            ServiceTranslationsSeeder::class,
            ServicePlansSeeder::class,
            ServicePlanTranslationsSeeder::class,
            ServiceContractsSeeder::class,
        ]);
    }
}
