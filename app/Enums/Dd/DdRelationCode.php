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
    case DirectShareholder = 'direct_shareholder'; // 直接株主
    case IndirectShareholder = 'indirect_shareholder'; // 間接株主
    case Investee = 'investee'; // 直接出資先
    case IndirectInvestee = 'indirect_investee'; // 間接出資先
    case ExecutiveOfDirectShareholder = 'executive_of_direct_shareholder'; // 直接株主の役員
    case ExecutiveOfIndirectShareholder = 'executive_of_indirect_shareholder'; // 間接株主の役員
    case ExecutiveOfInvestee = 'executive_of_investee'; // 直接出資先の役員
    case ExecutiveOfIndirectInvestee = 'executive_of_indirect_investee'; // 間接出資先の役員
}
