<?php

declare(strict_types=1);

namespace App\Infrastructure\RealTime\Mercure;

use App\Domain\User\Entity\User;

interface MercurePublisherInterface
{
    public function refreshStreams(User $user, ?string $context = null): void;

    public function refreshUser(User $user, ?string $context = null): void;

    public function refreshPlan(User $user, ?string $context = null): void;

    public function refreshSubscription(User $user, ?string $context = null): void;

    public function refreshNotifications(User $user, ?string $context = null): void;

    public function refreshPlans(User $user, ?string $context = null): void;
}
