<?php

namespace App\Core\Application\Command\Async;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class ExtractSoundFailureCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }
}
