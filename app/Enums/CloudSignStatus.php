<?php

declare(strict_types=1);

namespace App\Enums;

enum CloudSignStatus: int
{
    case UnderReview = 1;
    case Executed = 2;
    case Cancelled = 3;

    public function getStatusText(): string
    {
        return match ($this) {
            self::UnderReview => __('cloudsign.status.under_review'),
            self::Executed => __('cloudsign.status.executed'),
            self::Cancelled => __('cloudsign.status.cancelled'),
        };
    }
}
