<?php

declare(strict_types=1);

namespace App\Enums\Dd;

use App\Enums\Traits\EnumToEqual;

/**
 * DDステータス区分
 *
 * nameとvalueは同じ値とする
 * Value側にDBの値が入る想定
 */
enum DdStatusCode: string
{
    use EnumToEqual;

    case CddAwaiting = 'cdd_awaiting'; // 継続DD準備中
    case PreDdAiStarted = 'pre_dd_ai_started'; // DD準備AI処理開始
    case PreDdAiCompleted = 'pre_dd_ai_completed'; // DD準備AI処理完了
    case PreDdReviewCompleted = 'pre_dd_review_completed'; // DD準備再鑑完了
    case PreDdApprovalCompleted = 'pre_dd_approval_completed'; // DD準備承認完了
    case AddInfoInputRequested = 'add_info_input_requested'; // 追加入力依頼完了
    case AddInfoInputCompleted = 'add_info_input_completed'; // 追加入力完了
    case AddInfoReviewCompleted = 'add_info_review_completed'; // 追加入力再鑑完了
    case AddInfoApprovalCompleted = 'add_info_approval_completed'; // 追加入力承認完了
    case DdAiStarted = 'dd_ai_started'; // DDAI処理開始
    case DdAiCompleted = 'dd_ai_completed'; // DDAI処理完了
    case DdReviewCompleted = 'dd_review_completed'; // DD再鑑完了
    case DdApprovalCompleted = 'dd_approval_completed'; // DD承認完了
}
