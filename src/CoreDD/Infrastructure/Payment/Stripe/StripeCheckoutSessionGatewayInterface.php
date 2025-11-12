<?php

declare(strict_types=1);

namespace App\CoreDD\Infrastructure\Payment\Stripe;

use App\CoreDD\Domain\Plan\Entity\Plan;
use App\CoreDD\Domain\User\Entity\User;

interface StripeCheckoutSessionGatewayInterface
{
    public function create(Plan $plan, User $user): string;
}
