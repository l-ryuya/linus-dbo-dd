<?php
declare(strict_types = 1);

namespace App\UseCases\CountryRegion;

use App\Models\CountryRegion;

class IndexAction
{
    /**
     * 国・地域を取得する
     *
     * @param string      $languageCode 言語コード（ISO639-1）
     * @param string|null $countryCodeAlpha3
     * @param string|null $countryCodeAlpha2
     * @param int|null    $countryCodeNumeric
     * @param int         $displayedNumber 表示件数
     * @param int         $page ページ番号
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, CountryRegion>
     */
    public function __invoke(
        string $languageCode,
        ?string $countryCodeAlpha3,
        ?string $countryCodeAlpha2,
        ?int $countryCodeNumeric,
        int $displayedNumber,
        int $page,
    ): \Illuminate\Pagination\LengthAwarePaginator {
        return CountryRegion::select([
            'country_regions.country_code_alpha3',
            'country_regions.country_code_alpha2',
            'country_regions.country_code_numeric',
            'country_regions_translations.world_region',
            'country_regions_translations.country_region_name',
            'country_regions_translations.capital_name',
        ])
        ->join('country_regions_translations', function($join) use ($languageCode) {
            $join->on('country_regions.country_code_alpha3', '=', 'country_regions_translations.country_code_alpha3')
                ->where('country_regions_translations.language_code', $languageCode);
        })
        ->where('country_regions.world_region_type', 'world_region')
        ->when($countryCodeAlpha3, function ($query) use ($countryCodeAlpha3) {
            $query->where('country_regions.country_code_alpha3', $countryCodeAlpha3);
        })
        ->when($countryCodeAlpha2, function ($query) use ($countryCodeAlpha2) {
            $query->where('country_regions.country_code_alpha2', $countryCodeAlpha2);
        })
        ->when($countryCodeNumeric, function ($query) use ($countryCodeNumeric) {
            $query->where('country_regions.country_code_numeric', $countryCodeNumeric);
        })
        ->orderBy('country_regions.country_code_numeric')
        ->paginate(perPage: $displayedNumber, page: $page);
    }
}
