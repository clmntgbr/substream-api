<?php

declare(strict_types=1);

namespace App\Domain\Payment\Enum;

enum PaymentProviderEnum: string
{
    case STRIPE = 'stripe';
}
