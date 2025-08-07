<?php

declare(strict_types=1);

namespace App\Enums\Dd;

use App\Enums\Traits\EnumToEqual;

/**
 * DDステップ区分
 *
 * nameとvalueは同じ値とする
 * Value側にDBの値が入る想定
 */
enum DdStepCode: string
{
    use EnumToEqual;

    case PreDdAi = 'pre_dd_ai'; // DD準備AI処理
    case PreDdReview = 'pre_dd_review'; // DD準備再鑑
    case PreDdApproval = 'pre_dd_approval'; // DD準備承認
    case AddInfoInput = 'add_info_input'; // 追加入力
    case AddInfoReview = 'add_info_review'; // 追加入力再鑑
    case AddInfoApproval = 'add_info_approval'; // 追加入力承認
    case DdAi = 'dd_ai'; // DDAI処理
    case DdReview = 'dd_review'; // DD再鑑
    case DdApproval = 'dd_approval'; // DD承認
}
