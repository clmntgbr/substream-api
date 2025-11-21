<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment\Stripe;

use App\Domain\User\Entity\User;

interface StripeBillingPortalGatewayInterface
{
    public function getUrl(User $user): string;
}
