<?php

declare(strict_types=1);

namespace App\Enums\Dd;

use App\Enums\Traits\EnumToEqual;

/**
 * DD関係性種別コード
 * nameとvalueは同じ値とする
 * Value側にDBの値が入る想定
 */
enum DdRelationCode: string
{
    use EnumToEqual;

    case CounterpartyEntity = 'counterparty_entity'; // 取引対象法人
    case UltimateBeneficialOwner = 'ultimate_beneficial_owner'; // 実質的支配者
    case Executive = 'executive'; // 役員
    case Shareholder = 'shareholder'; // 株主
    case ExecutiveOfShareholder = 'executive_of_shareholder'; // 株主の役員
    case IndirectShareholder = 'indirect_shareholder'; // 間接株主
    case ExecutiveOfIndirectShareholder = 'executive_of_indirect_shareholder'; // 間接株主の役員
    case Investee = 'investee'; // 出資先
    case ExecutiveOfInvestee = 'executive_of_investee'; // 出資先の役員
    case IndirectInvestee = 'indirect_investee'; // 間接出資先
    case ExecutiveOfIndirectInvestee = 'executive_of_indirect_investee'; // 間接出資先の役員
}
