<?php

declare(strict_types=1);

namespace App\Infrastructure\RealTime\Mercure;

use App\Domain\Stream\Entity\Stream;
use App\Domain\User\Entity\User;

interface MercurePublisherInterface
{
    public function refreshStream(Stream $stream, ?string $context = null): void;

    public function refreshStreams(User $user, ?string $context = null): void;

    public function refreshNotifications(User $user, ?string $context = null): void;

    public function refreshPlans(User $user, ?string $context = null): void;
}
