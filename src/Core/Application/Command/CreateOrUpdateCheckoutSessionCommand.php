<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Core\Domain\Plan\Entity\Plan;
use App\Core\Domain\Subscription\Entity\Subscription;
use App\Core\Domain\User\Entity\User;
use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;

final class CreateOrUpdateCheckoutSessionCommand extends CommandAbstract implements SyncCommandInterface
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
