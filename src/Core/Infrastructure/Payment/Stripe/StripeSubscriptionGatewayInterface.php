<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Payment\Stripe;

use App\Core\Domain\User\Entity\User;

interface StripeSubscriptionGatewayInterface
{
    public function getBillingPortalUrl(User $user): string;
}
