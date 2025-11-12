<?php

namespace App\Service;

use App\Core\Domain\Plan\Entity\Plan;
use App\Core\Domain\User\Entity\User;

interface StripeServiceInterface
{
    public function createCheckoutSession(Plan $plan, User $user): string;

    public function getManageSubscriptionUrl(User $user): string;

    public function cancelSubscription(User $user): void;
}
