<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\User\Entity\User;

interface StripeSubscriptionGatewayInterface
{
    public function getBillingPortalUrl(User $user): string;
}
