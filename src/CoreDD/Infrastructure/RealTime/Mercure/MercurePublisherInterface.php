<?php

declare(strict_types=1);

namespace App\CoreDD\Infrastructure\RealTime\Mercure;

use App\CoreDD\Domain\Stream\Entity\Stream;
use App\CoreDD\Domain\User\Entity\User;

interface MercurePublisherInterface
{
    public function refreshStream(Stream $stream, ?string $context = null): void;

    public function refreshSearchStreams(Stream $stream, ?string $context = null): void;

    public function refreshSearchNotifications(User $user, ?string $context = null): void;

    public function refreshPlan(User $user, ?string $context = null): void;
}
