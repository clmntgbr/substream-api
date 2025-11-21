<?php

declare(strict_types=1);

namespace App\Domain\Plan\Enum;

enum PlanTypeEnum: string
{
    case FREE = 'free';
    case PAID = 'paid';
}
