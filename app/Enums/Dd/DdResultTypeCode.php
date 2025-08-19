<?php

declare(strict_types=1);

namespace App\Enums\Dd;

use App\Enums\Traits\EnumToEqual;

/**
 * DD結果種別コード
 * nameとvalueは同じ値とする
 * Value側にDBの値が入る想定
 */
enum DdResultTypeCode: string
{
    use EnumToEqual;

    case StakeholderAcquisition = 'stakeholder_acquisition'; // 株主・役員情報取得
    case IndustryCheckReg = 'industry_check_reg'; // 業種業態チェック（登記簿）
    case IndustryCheckWeb = 'industry_check_web'; // 業種業態チェック（Web）
    case CustomerRiskLevel = 'customer_risk_level'; // 顧客リスクレベル判定
    case AddInfoEntry = 'add_info_entry'; // 顧客情報追加入力
    case AsfCheck = 'asf_check'; // 反社チェック
    case RepCheck = 'rep_check'; // 風評チェック
}
