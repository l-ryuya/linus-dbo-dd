<?php

namespace App\Http\Resources\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait CamelCasePaginationMeta
{
    /**
     * ページネーションから生成されるメタ情報をキャメルケースに変換する
     *
     * @param \Illuminate\Http\Request $request
     * @param $paginated
     * @param array $default
     *
     * @return array
     */
    public function paginationInformation(
        Request $request,
        $paginated,
        array $default,
    ): array {
        $default['meta'] = $this->camelCaseMeta($default['meta']);

        return $default;
    }

    protected function camelCaseMeta(array $meta): array
    {
        return collect($meta)->mapWithKeys(function ($value, $key) {
            return [Str::camel($key) => $value];
        })->toArray();
    }
}