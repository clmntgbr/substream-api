<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class ExtractSoundCommand implements AsyncCommandInterface, TrackableCommandInterface
{
    public function __construct(
        public Uuid $streamId,
    ) {
    }

    public function getData(): array
    {
        return [
            'streamId' => $this->streamId,
        ];
    }

    public function supports(): bool
    {
        return true;
    }
}
