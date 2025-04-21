<?php

declare(strict_types=1);

namespace App\Http\Resources\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait CamelCasePaginationMeta
{
    /**
     * ページネーションから生成されるメタ情報をキャメルケースに変換する
     *
     * @param \Illuminate\Http\Request $request
     * @param array<string, mixed>  $paginated
     * @param array<string, mixed> $default
     *
     * @return array<string, mixed>
     */
    public function paginationInformation(
        Request $request,
        array $paginated,
        array $default,
    ): array {
        $default['meta'] = $this->camelCaseMeta($default['meta']);

        return $default;
    }

    /**
     * メタ情報のキーをキャメルケースに変換する
     *
     * @param array<string, mixed> $meta
     * @return array<string, mixed>
     */
    protected function camelCaseMeta(array $meta): array
    {
        return collect($meta)->mapWithKeys(function ($value, $key) {
            return [Str::camel($key) => $value];
        })->toArray();
    }
}
