<?php

declare(strict_types=1);

namespace App\CoreDD\Infrastructure\Payment\Stripe;

use App\CoreDD\Domain\User\Entity\User;
use Stripe\StripeClient;

class StripeSubscriptionGateway implements StripeSubscriptionGatewayInterface
{
    public function __construct(
        private readonly string $frontendUrl,
        private readonly string $stripeApiSecretKey,
    ) {
    }

    public function getBillingPortalUrl(User $user): string
    {
        $stripe = new StripeClient($this->stripeApiSecretKey);

        $billingPortalSession = $stripe->billingPortal->sessions->create([
            'customer' => $user->getStripeCustomerId(),
            'return_url' => $this->frontendUrl.'/billing',
        ]);

        return $billingPortalSession->url;
    }
}
