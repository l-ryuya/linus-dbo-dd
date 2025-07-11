<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumToEqual;
use App\Enums\Traits\EnumToGet;

/**
 * サービス契約ステータス
 *
 * nameとvalueはいったん同じ値とする
 * Value側にDBの値が入る想定
 */
enum ServiceContractStatus: String
{
    use EnumToGet;
    use EnumToEqual;

    case ContractInfoRegistered = 'contract_info_registered';
    case ContractDocumentSent = 'contract_document_sent';
    case ContractExecuted = 'contract_executed';
}
