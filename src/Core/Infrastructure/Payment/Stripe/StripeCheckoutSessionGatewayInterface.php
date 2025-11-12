<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Payment\Stripe;

use App\Core\Domain\Plan\Entity\Plan;
use App\Core\Domain\User\Entity\User;

interface StripeCheckoutSessionGatewayInterface
{
    public function create(Plan $plan, User $user): string;
}
