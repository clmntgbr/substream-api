<?php

declare(strict_types=1);

namespace App\Enum;

enum SubscriptionStatusEnum: string
{
    case ACTIVE = 'active';
    case CANCELED = 'canceled';
    case EXPIRED = 'expired';
    case PAUSED = 'paused';
}
