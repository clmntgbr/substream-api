<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;

class CreateStreamUrlCommand implements SyncCommandInterface, TrackableCommandInterface
{
    public function __construct(
        public string $url,
        public User $user,
    ) {
    }

    public function getData(): array
    {
        return [];
    }

    public function supports(): bool
    {
        return false;
    }
}
