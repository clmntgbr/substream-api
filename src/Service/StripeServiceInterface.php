<?php

namespace App\Service;

use App\Entity\Plan;
use App\Entity\User;
use Stripe\Checkout\Session;

interface StripeServiceInterface
{
    public function checkoutSession(Plan $plan, User $user): string;

    public function retrieveSession(string $sessionId): Session;
}
