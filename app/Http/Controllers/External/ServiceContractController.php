<?php

declare(strict_types=1);

namespace App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use App\Http\Resources\External\InvoiceInfoResource;
use App\UseCases\External\InvoiceInfoAction;

class ServiceContractController extends Controller
{
    /**
     * 請求情報を取得する
     * dbo_billing向け
     *
     * @param string                                   $publicId
     * @param \App\UseCases\External\InvoiceInfoAction $action
     *
     * @return \App\Http\Resources\External\InvoiceInfoResource
     */
    public function invoiceInfo(
        string $publicId,
        InvoiceInfoAction $action,
    ): InvoiceInfoResource {
        return new InvoiceInfoResource($action($publicId));
    }
}
