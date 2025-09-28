<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoByUrlCommand implements AsyncCommandInterface, TrackableCommandInterface
{
    public function __construct(
        public Uuid $streamId,
        public User $user,
        public string $url,
    ) {
    }

    public function getData(): array
    {
        return [
            'streamId' => $this->streamId,
            'url' => $this->url,
        ];
    }

    public function supports(): bool
    {
        return true;
    }
}
