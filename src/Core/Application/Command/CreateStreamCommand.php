<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class CreateStreamCommand implements SyncCommandInterface, TrackableCommandInterface
{
    public function __construct(
        public Uuid $streamId,
        public User $user,
        public ?string $fileName = null,
        public ?string $originalFileName = null,
        public ?string $url = null,
    ) {
    }
}
