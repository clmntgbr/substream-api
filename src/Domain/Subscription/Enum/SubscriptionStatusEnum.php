<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Enum;

enum SubscriptionStatusEnum: string
{
    case ACTIVE = 'active';
    case CANCELED = 'canceled';
    case PENDING_CANCEL = 'pending_cancel';
    case EXPIRED = 'expired';
    case PAUSED = 'paused';
}
