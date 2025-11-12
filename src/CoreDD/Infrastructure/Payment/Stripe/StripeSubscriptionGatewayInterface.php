<?php

declare(strict_types=1);

namespace App\CoreDD\Infrastructure\Payment\Stripe;

use App\CoreDD\Domain\User\Entity\User;

interface StripeSubscriptionGatewayInterface
{
    public function getBillingPortalUrl(User $user): string;
}
