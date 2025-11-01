<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Stream;
use App\Entity\User;

interface PublishServiceInterface
{
    public function refreshStream(Stream $stream, ?string $context = null): void;

    public function refreshSearchStreams(Stream $stream, ?string $context = null): void;

    public function refreshSearchNotifications(User $user, ?string $context = null): void;
}
