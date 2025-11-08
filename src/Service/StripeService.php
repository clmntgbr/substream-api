<?php

namespace App\Service;

use App\Entity\Plan;
use App\Entity\User;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService implements StripeServiceInterface
{
    public const CHECKOUT_SUCCESS_URL = '/subscribe/success';
    public const CHECKOUT_CANCEL_URL = '/pricing';

    public function __construct(
        private readonly string $frontendUrl,
        private readonly string $stripeApiPublicKey,
        private readonly string $stripeApiSecretKey,
    ) {
    }

    public function checkoutSession(Plan $plan, User $user): string
    {
        $stripe = new StripeClient($this->stripeApiSecretKey);

        $session = $stripe->checkout->sessions->create([
            'client_reference_id' => $user->getId(),
            'customer_email' => $user->getEmail(),
            'success_url' => $this->frontendUrl.self::CHECKOUT_SUCCESS_URL,
            'cancel_url' => $this->frontendUrl.self::CHECKOUT_CANCEL_URL,
            'line_items' => [[
                'price' => $plan->getStripePriceId(),
                'quantity' => 1,
            ]],
            'currency' => 'eur',
            'mode' => 'subscription',
            'metadata' => [
                'plan_id' => $plan->getId(),
                'plan_name' => $plan->getName(),
                'plan_price' => $plan->getPrice(),
                'currency' => 'eur',
                'plan_interval' => $plan->getInterval(),
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail(),
                'user_name' => $user->getName(),
            ],
        ]);

        return $session->url;
    }

    public function retrieveSession(string $sessionId): Session
    {
        $stripe = new StripeClient($this->stripeApiSecretKey);

        return $stripe->checkout->sessions->retrieve($sessionId, [
            'expand' => ['line_items'],
        ]);
    }
}
