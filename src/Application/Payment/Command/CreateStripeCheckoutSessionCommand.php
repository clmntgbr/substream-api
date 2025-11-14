<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Domain\Plan\Entity\Plan;
use App\Domain\Subscription\Entity\Subscription;
use App\Domain\User\Entity\User;
use App\Shared\Application\Command\SynchronousInterface;

final class CreateStripeCheckoutSessionCommand implements SynchronousInterface
{
    public function __construct(
        private User $user,
        private Plan $plan,
        private ?Subscription $subscription = null,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }
}
