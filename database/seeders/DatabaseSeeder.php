<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\base\CompaniesSeeder;
use Database\Seeders\base\CountryRegionsSeeder;
use Database\Seeders\base\CountryRegionsTranslationsSeeder;
use Database\Seeders\base\CurrenciesSeeder;
use Database\Seeders\base\SelectionItemsSeeder;
use Database\Seeders\base\SelectionItemTranslationsSeeder;
use Database\Seeders\base\ServicePlansSeeder;
use Database\Seeders\base\ServicePlanTranslationsSeeder;
use Database\Seeders\base\ServicesSeeder;
use Database\Seeders\base\ServiceTranslationsSeeder;
use Database\Seeders\base\SystemUserSeeder;
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
            CurrenciesSeeder::class,
            CountryRegionsSeeder::class,
            CountryRegionsTranslationsSeeder::class,
            CompaniesSeeder::class,
            SystemUserSeeder::class,
            ServicesSeeder::class,
            ServiceTranslationsSeeder::class,
            ServicePlansSeeder::class,
            ServicePlanTranslationsSeeder::class,
        ]);
    }
}
