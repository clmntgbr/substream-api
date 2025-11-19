<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\Plan\Entity\Plan;
use App\Domain\User\Entity\User;

interface StripeCheckoutSessionGatewayInterface
{
    public function create(Plan $plan, User $user): string;
}
